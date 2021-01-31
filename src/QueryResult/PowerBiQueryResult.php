<?php

namespace App\QueryResult;

use App\Exception\PowerBiResponseException;
use App\Tool\ArrayChain;
use Exception;
use Generator;

class PowerBiQueryResult
{
    const PROPERTY_TYPE_NULL = 0;
    const PROPERTY_TYPE_TEXT = 1;
    const PROPERTY_TYPE_DECIMAL = 2;
    const PROPERTY_TYPE_DOUBLE = 3;
    const PROPERTY_TYPE_INTEGER = 4;
    const PROPERTY_TYPE_BOOLEAN = 5;
    const PROPERTY_TYPE_DATE = 6;
    const PROPERTY_TYPE_DATETIME = 7;
    const PROPERTY_TYPE_DATETIME_ZONE = 8;
    const PROPERTY_TYPE_TIME = 9;
    const PROPERTY_TYPE_DURATION = 10;
    const PROPERTY_TYPE_BINARY = 11;
    const PROPERTY_TYPE_NONE = 12;
    const PROPERTY_TYPE_NAMES = [
        self::PROPERTY_TYPE_NULL => 'null',
        self::PROPERTY_TYPE_TEXT => 'text',
        self::PROPERTY_TYPE_DECIMAL => 'decimal',
        self::PROPERTY_TYPE_DOUBLE => 'double',
        self::PROPERTY_TYPE_INTEGER => 'integer',
        self::PROPERTY_TYPE_BOOLEAN => 'boolean',
        self::PROPERTY_TYPE_DATE => 'date',
        self::PROPERTY_TYPE_DATETIME => 'datetime',
        self::PROPERTY_TYPE_DATETIME_ZONE => 'datetimezone',
        self::PROPERTY_TYPE_TIME => 'time',
        self::PROPERTY_TYPE_DURATION => 'duration',
        self::PROPERTY_TYPE_BINARY => 'binary',
        self::PROPERTY_TYPE_NONE => 'none',
    ];

    private $rawResponse;
    private $selectedFields;
    private $dictionaries;
    private $rawItems;
    private $runtimeObjectConstants = [];

    public function __construct(array $rawResponse)
    {
        $this->rawResponse = $rawResponse;

        $data = $this->data();
        $window = $this->window($data);

        $this->selectedFields = $this->selectedFields($data);
        $this->rawItems = $this->rawItems($window);
        $this->dictionaries = $this->dictionaries($window);
    }

    public function items(): Generator
    {
        $attributesCount = count($this->selectedFields);
        $isScalarResultItem = 1 === $attributesCount;
        $scalarResultItemKey = current($this->selectedFields)['Value'];

        foreach ($this->rawItems as $i => $dataItem) {
            if (0 === $i) {
                $fieldIndicesMap = $this->fieldsIndicesMap($this->selectedFields, $dataItem['S']);
                $itemStructure = $dataItem['S'];
            }

            if ($isScalarResultItem) {
                if ($i > 0) {
                    // if the result item is scalar and more than row is returned Power BI returns the first value
                    // in the second dataItem
                    $item = [$dataItem[$scalarResultItemKey]];
                } else {
                    continue;
                }
            } else {
                if (count($dataItem['C']) === $attributesCount) {
                    $item = $dataItem['C'];
                } else {
                    $item = $this->expandItem($dataItem, $previousItem ?? array_fill(0, $attributesCount, null));
                }

                $previousItem = $item;
            }

            yield $this->resolveItem($item, $fieldIndicesMap, $itemStructure);
        }

        // if the result item is scalar and only one row is returned Power BI bundles the value
        // to the first dataItem along with the structure
        if ($isScalarResultItem && 0 === $i) {
            yield $this->resolveItem([$dataItem[$scalarResultItemKey]], $fieldIndicesMap, $itemStructure);
        }
    }

    private function runtimeObjectConstant(string $name, callable $valueCallback)
    {
        if (!isset($this->runtimeObjectConstants[$name])) {
            $this->runtimeObjectConstants[$name] = $valueCallback();
        }

        return $this->runtimeObjectConstants[$name];
    }

    private function expandItem(array $dataItem, array $previousItem): array
    {
        $defaultCharMap = $this->runtimeObjectConstant('defaultCharMap', function () use ($previousItem) {
            return array_fill(0, count($previousItem), 'U');
        });

        $charMap = $this->charMap((int)($dataItem['R'] ?? 0), 'R',
            $this->charMap((int)($dataItem['Ø'] ?? 0), 'Ø', $defaultCharMap));

        $partialIndex = 0;
        $item = [];
        foreach ($charMap as $index => $char) {
            switch ($char) {
                case 'U':
                    $item[$index] = $dataItem['C'][$partialIndex++];
                    break;
                case 'R':
                    $item[$index] = $previousItem[$index];
                    break;
                case 'Ø':
                    $item[$index] = 0;
                    break;
                default:
                    throw new Exception('Unsupported transformation character "' . $char . '".');
            }
        }

        return $item;
    }

    private function fieldsIndicesMap(array $select, array $structure): array
    {
        $selectIndices = [];

        foreach ($select as $index => $selectField) {
            $selectIndices[$selectField['Value']] = $index;
        }

        $result = array_map(function (array $structureItem) use ($selectIndices) {
            return $selectIndices[$structureItem['N']];
        }, $structure);

        asort($result);

        return array_flip($result);
    }

    private function data(): array
    {
        return ArrayChain::value($this->rawResponse, 'results', 0, 'result', 'data');
    }

    private function selectedFields(array $data): array
    {
        $descriptor = ArrayChain::value($data, 'descriptor');

        if (empty($descriptor)) {
            $error = ArrayChain::value($data, 'dsr', 'DataShapes', 0, 'odata.error');

            throw new PowerBiResponseException(ArrayChain::value($error, 'message', 'value'));
        }

        if (!isset($descriptor['Select'])) {
            throw new PowerBiResponseException('Descriptor Select is missing in response.');
        }

        if (empty($descriptor['Select'])) {
            return [];
        }

        return $descriptor['Select'];
    }

    private function window(array $data): array
    {
        $dsr = ArrayChain::value($data, 'dsr');

        if (isset($dsr['DataShapes'])) {
            $error = ArrayChain::value($data, 'dsr', 'DataShapes', 0, 'odata.error', 'message', 'value');
            throw new PowerBiResponseException(false === strpos($error, 'how these fields are related') ?
                $error : 'Selected tables are not related (could not be joined).');
        }

        return ArrayChain::value($dsr, 'DS', 0);
    }

    private function dictionaries(array $window): array
    {
        if (isset($window['ValueDicts'])) {
            return $window['ValueDicts'];
        }

        return [];
    }

    private function rawItems(array $window): Generator
    {
        foreach ($window['PH'] ?? [] as $page) {
            foreach ($page as $grouping) {
                foreach ($grouping as $dataItem) {
                    yield $dataItem;
                }
            }
        }
    }

    private function resolveItem(array $item, array $fieldIndicesMap, array $itemStructure): array
    {
        $result = [];

        foreach ($fieldIndicesMap as $valueIndex => $index) {
            $value = $item[$index];

            if (isset($itemStructure[$index]['DN']) && isset($this->dictionaries[$itemStructure[$index]['DN']][$value])) {
                $result[] = $this->dictionaries[$itemStructure[$index]['DN']][$value];
            } else {
                $result[] = $value;
            }
        }

        return $result;
    }

    private function charMap(int $number, string $mapChar, array $charMap): array
    {
        $bitMapString = str_pad(decbin($number), count($charMap), 0, STR_PAD_LEFT);

        $bitArray = array_map(function ($bit) {
            return (bool)$bit;
        }, array_reverse(str_split($bitMapString)));

        foreach ($bitArray as $index => $isOne) {
            if ($isOne) {
                $charMap[$index] = $mapChar;
            }
        }

        return $charMap;
    }
}