<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class Content
{
    private $parameterBag;
    private $httpClient;

    public function __construct(ParameterBagInterface $parameterBag, HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
        $this->parameterBag = $parameterBag;
    }

    public function load(string $filePathOrUrl, $body = null, ?array $headers = null)
    {
        if (false !== strpos($filePathOrUrl, '@project_dir')) {
            return file_get_contents(str_replace('@project_dir', $this->parameterBag->get('kernel.project_dir'), $filePathOrUrl));
        }

        return $this->response($filePathOrUrl, $body, $headers)->getContent();
    }

    public function response(string $url, $body = null, ?array $headers = null): ResponseInterface {
        $method = 'GET';
        $options = [];

        if (isset($body['json'])) {
            $method = 'POST';
            $options['json'] = $body['json'];
        }

        if (isset($body['form'])) {
            $method = 'POST';
            $options['body'] = $body['form'];
        }

        if (null !== $headers) {
            $options['headers'] = $headers;
        }

        return $this->httpClient->request($method, $url, $options);
    }
}