<?php

namespace App\Repository;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\Cache\CacheInterface;

class AbstractRemoteRepository
{
    protected $cache;
    private $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag, CacheInterface $cache)
    {
        $this->parameterBag = $parameterBag;
        $this->cache = $cache;
    }

    protected function fileContent(string $filePathOrUrl, $bodyData = null, ?array $headers = null)
    {
        if (false !== strpos($filePathOrUrl, '@project_dir')) {
            return file_get_contents(str_replace('@project_dir', $this->parameterBag->get('kernel.project_dir'), $filePathOrUrl));
        }

        if (null === $headers && is_array($bodyData)) {
            $headers['Content-Type'] = 'application/x-www-form-urlencoded';
        }

        $http = [
            'method' => null === $bodyData ? 'GET' : 'POST',
        ];

        if (null !== $headers) {
            $http['header'] = implode("\r\n", array_map(function ($name, $value) {
                return "$name: $value";
            }, array_keys($headers), $headers));
        }

        if (null !== $bodyData) {
            $http['content'] = is_array($bodyData) ? http_build_query($bodyData) : $bodyData;
        }

        // TODO: refactor this mess

        $fp = fopen($filePathOrUrl, 'r', false, stream_context_create([
            'http' => $http
        ]));

        $content = stream_get_contents($fp);
        fclose($fp);

        foreach($http_response_header as $c => $h)
        {
            if(stristr($h, 'content-encoding') and stristr($h, 'gzip'))
            {
                //Now lets uncompress the compressed data
                $content = gzinflate( substr($content,10,-8) );
            }
        }

        return $content;
    }
}