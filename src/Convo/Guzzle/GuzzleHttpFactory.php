<?php declare(strict_types=1);

namespace Convo\Guzzle;

use \GuzzleHttp\Psr7\Request;
use \GuzzleHttp\Psr7\Response;
use \GuzzleHttp\Psr7\Uri;
use function GuzzleHttp\Psr7\stream_for;

use Convo\Core\Util\IHttpFactory;

class GuzzleHttpFactory implements IHttpFactory
{   
    public function __construct()
    {
    }

    public function getHttpClient(array $config = array()): \Psr\Http\Client\ClientInterface
    {
        return new GuzzleHttpClient($config);
    }

    public function buildResponse($data, $status = 200, $headers = []): \Psr\Http\Message\ResponseInterface
    {
        if (!is_string($data)) {
            $data = json_encode($data);
        }

        $response = new Response();

        foreach ($headers as $name => $value) {
            $response = $response->withAddedHeader($name, $value);
        }
            
        return $response
            ->withStatus($status)
            ->withBody(stream_for($data));
    }

    /**
     * @var string $method
     * @var string|\Psr\Http\Message\UriInterface $uri
     */
    public function buildRequest($method, $uri, array $headers = [], $body = null, $version = '1.1'): \Psr\Http\Message\RequestInterface
    {
        $request = new Request($method, $uri);

        if (!empty($headers)) {
            foreach ($headers as $header => $value) {
                $request = $request->withAddedHeader($header, $value);
            }
        }

        if ($method === self::METHOD_POST || $method === self::METHOD_PUT) {
            if (is_array($body)) {
                $encoded = json_encode($body);

                $request = $request
                    ->withBody(\GuzzleHttp\Psr7\stream_for($encoded))
                    ->withHeader('Content-Type', 'application/json');
			} else {
                $request = $request
                    ->withBody(\GuzzleHttp\Psr7\stream_for($body));
            }
        }

        return $request;
    }

    public function buildUri($url, $queryParams = []): \Psr\Http\Message\UriInterface
    {
        $uri = new Uri($url);

        return $uri->withQueryValues($uri, $queryParams);
    }
}