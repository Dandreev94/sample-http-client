<?php

namespace AwesomeServiceClient\Transport;

use AwesomeServiceClient\Model\Comment;
use AwesomeServiceClient\Model\Response;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;
use AwesomeServiceClient\Exception\AwesomeException;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

class GuzzleTransport implements TransportInterface
{
    private const GET = 'GET';

    private const PUT = 'PUT';

    private const POST = 'POST';

    /**
     * @var GuzzleClient
     */
    private $client;

    /**
     * GuzzleTransport constructor.
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $conf = [
            'base_uri' => self::BASE_URL
        ];

        if (!empty($params)) {
            $conf = array_merge($conf, $params);
        }
        $this->client = new GuzzleClient($conf);
    }

    /**
     * @return Response
     * @throws AwesomeException
     */
    public function get(): Response
    {
        $comments = [];
        $response = $this->send(self::GET, self::COMMENTS_URL);
        $items = json_decode($response->getBody()->getContents(), true);
        foreach ($items as $item) {
            $comments[] = new Comment(
                $item['text'],
                $item['name'],
                $item['id']
            );
        }

        return new Response($response->getStatusCode(), $response->getReasonPhrase(), $comments);
    }

    /**
     * @param int $commentId
     * @param string $name
     * @param string $text
     * @return Response
     * @throws AwesomeException
     */
    public function put(int $commentId, string $name = '', string $text = ''): Response
    {
        if (!$name && !$text) {
            throw new InvalidArgumentException('The method should take at least one argument for update');
        }

        $body = [];
        if ($name) {
            $body['json']['name'] = $name;
        }

        if ($text) {
            $body['json']['text'] = $text;
        }

        $url = self::COMMENT_URL . "/{$commentId}";
        $response = $this->send(self::PUT, $url, $body);

        return new Response($response->getStatusCode(), $response->getReasonPhrase());
    }

    /**
     * @param string $name
     * @param string $text
     * @return Response
     * @throws AwesomeException
     */
    public function post(string $name, string $text): Response
    {
        $body = [
            'json' => [
                'name' => $name,
                'text' => $text
            ]
        ];
        $response = $this->send(self::POST, self::COMMENT_URL, $body);

        return new Response($response->getStatusCode(), $response->getReasonPhrase());
    }

    /**
     * @param string $cmd
     * @param string $url
     * @param array $body
     * @return ResponseInterface
     * @throws AwesomeException
     */
    protected function send (string $cmd, string $url, array $body = []): ResponseInterface
    {
        $headers = [
            'headers' => ['Content-Type' => 'application/json']
        ];
        $options = array_merge($headers, $body);

        try {
            return $this->client->request($cmd, $url, $options);
        } catch (GuzzleException $e) {
            throw new AwesomeException($e->getMessage(), $e->getCode(), $e);
        }
    }
}