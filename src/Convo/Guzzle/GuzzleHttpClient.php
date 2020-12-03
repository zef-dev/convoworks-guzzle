<?php declare(strict_types=1);

namespace Convo\Guzzle;

class GuzzleHttpClient implements \Psr\Http\Client\ClientInterface
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $_guzzleClient;

    public function __construct(array $config = array())
    {
        $this->_guzzleClient = new \GuzzleHttp\Client($config);
    }

    public function sendRequest(\Psr\Http\Message\RequestInterface $request): \Psr\Http\Message\ResponseInterface
    {
        try {
            $response = $this->_guzzleClient->send($request);
        } catch ( \GuzzleHttp\Exception\ClientException $e) {
            throw new \Convo\Core\Util\HttpClientException( $e->getResponse()->getBody()->getContents(), $e->getCode(), $e);
        }

        return $response;
    }
}