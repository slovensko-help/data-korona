<?php

namespace App\QueryBuilder;

/**
 * Useful info: https://github.com/rina-andria/PowerBI-BrushChart/blob/master/lib/powerbi-visuals.d.ts
 */
class PowerBiQueryBuilder
{
    const SELECT_TYPE_COLUMN = 0;
    const SELECT_TYPE_MEASURE = 1;

    const AGGREGATION_SUM = 0;
    const AGGREGATION_AVG = 1;
    const AGGREGATION_COUNT = 2;
    const AGGREGATION_MIN = 3;
    const AGGREGATION_MAX = 4;

    const COMPARISON_EQUAL = 0;
    const COMPARISON_GREATER_THAN = 1;
    const COMPARISON_GREATER_THAN_OR_EQUAL = 2;
    const COMPARISON_LESS_THAN = 3;
    const COMPARISON_LESS_THAN_OR_EQUAL = 4;

    const ORDER_ASC = 1;
    const ORDER_DESC = 2;

    private $froms = [];
    private $selects = [];
    private $andWheres = [];
    private $orderBys = [];
    private $limit = 5000;

    private $modelId;
    private $datasetId;
    private $reportId;
    private $fieldIndices = [];

    public function __construct(int $modelId, string $datasetId, string $reportId)
    {
        $this->modelId = $modelId;
        $this->datasetId = $datasetId;
        $this->reportId = $reportId;
    }

    public function selectColumn(string $entityName, string $propertyName, ?int $aggregationFunction = null): self
    {
        return $this->addSelect($entityName, $propertyName, self::SELECT_TYPE_COLUMN, $aggregationFunction);
    }

    public function selectMeasure(string $entityName, string $propertyName): self
    {
        return $this->addSelect($entityName, $propertyName, self::SELECT_TYPE_MEASURE);
    }

    public function setLimit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function andWhere(string $entityName, string $propertyName, int $comparisonKind, string $value): self
    {
        $comparison = [
            'Comparison' => $this->leftRight(
                $this->columnReference($entityName, $propertyName),
                [
                    'Literal' => [
                        'Value' => $value,
                    ],
                ]
            )
        ];

        $comparison['Comparison']['ComparisonKind'] = $comparisonKind;

        $this->andWheres[] = $comparison;

        return $this;
    }

    public function orderBy(string $entityName, string $propertyName, int $direction = self::ORDER_ASC): self
    {
        $this->orderBys[] = [
            'Direction' => $direction,
            'Expression' => $this->columnReference($entityName, $propertyName)
        ];

        return $this;
    }

    public function build(): array
    {
        return [
            'version' => '1.0.0',
            'queries' => [[
                'Query' => [
                    'Commands' => [[
                        'SemanticQueryDataShapeCommand' => [
                            'Query' => [
                                'Version' => 2,
                                'From' => array_values($this->froms),
                                'Select' => $this->selects,
                                'Where' => $this->buildAndWhere($this->andWheres, true),
                                'OrderBy' => $this->orderBys,
                            ],
                            'Binding' => [
                                'Primary' => [
                                    'Groupings' => [[
                                        'Projections' => array_keys($this->selects),
                                    ]]
                                ],
                                'DataReduction' => [
                                    'DataVolume' => count($this->selects),
                                    'Primary' => [
                                        'Window' => [
                                            'Count' => $this->limit,
                                        ],
                                    ],
                                ],
                                'Version' => 1,
                            ],
                        ],
                    ]]
                ],
                'QueryId' => '',
                'ApplicationContext' => [
                    'DatasetId' => $this->datasetId,
                    'Sources' => [
                        [
                            'ReportId' => $this->reportId,
                        ],
                    ],
                ],
            ]],
            'modelId' => $this->modelId,
        ];
    }

    public function fieldIndex(string $entityName, string $propertyName): ?int
    {
        return $this->fieldIndices[$this->fieldKey($entityName, $propertyName)] ?? null;
    }

    private function leftRight(array $left, array $right): array
    {
        return [
            'Left' => $left,
            'Right' => $right,
        ];
    }

    private function buildAndWhere(array $andWheres, bool $isTop = false): array
    {
        if (empty($andWheres)) {
            return [];
        }

        if (1 === count($andWheres)) {
            $result = $andWheres[0];
        } else {
            $result = [
                'And' => $this->leftRight(
                    $andWheres[0],
                    $this->buildAndWhere(array_slice($andWheres, 1))
                ),
            ];
        }


        if ($isTop) {
            $result = [[
                'Condition' => $result,
            ]];
        }

        return $result;
    }

    private function columnReference(string $entityName, string $propertyName, int $selectType = self::SELECT_TYPE_COLUMN): array
    {
        return [
            (self::SELECT_TYPE_MEASURE === $selectType ? 'Measure' : 'Column') => [
                'Expression' => [
                    'SourceRef' => [
                        'Source' => $this->getOrAddEntityName($entityName)['Name'],
                    ]
                ],
                'Property' => $propertyName,
            ]
        ];
    }

    private function addSelect(string $entityName, string $propertyName, int $selectType = self::SELECT_TYPE_COLUMN, ?int $aggregationFunction = null): self
    {
        $select = $this->columnReference($entityName, $propertyName, $selectType);

        if (null !== $aggregationFunction) {
            $select = [
                'Aggregation' => [
                    'Expression' => $select,
                    'Function' => $aggregationFunction,
                ]
            ];
        }

        $select['Name'] = 'N' . (count($this->selects) + 1);

        $this->selects[] = $select;

        $this->fieldIndices[$this->fieldKey($entityName, $propertyName)] = count($this->selects) - 1;

        return $this;
    }

    private function fieldKey(string $entityName, string $propertyName) {
        return $entityName . '~~~' . $propertyName;
    }

    private function getOrAddEntityName(string $entityName): array
    {
        return $this->froms[$entityName] = $this->froms[$entityName] ?? [
                'Name' => 'f' . (count($this->froms) + 1),
                'Entity' => $entityName,
                'Type' => 0,
            ];
    }
}