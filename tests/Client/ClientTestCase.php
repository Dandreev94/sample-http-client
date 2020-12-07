<?php

use PHPUnit\Framework\TestCase;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use GuzzleHttp\Exception\ConnectException;

use AwesomeServiceClient\Client\Client;
use AwesomeServiceClient\Model\Comment;
use AwesomeServiceClient\Transport\GuzzleTransport;
use AwesomeServiceClient\Exception\AwesomeException;

class ClientTestCase extends TestCase
{
    private $client;

    private $mock;

    protected function setUp(): void
    {
        $this->mock = new MockHandler([
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode($this->responseBodyForGet())
            ),
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                json_encode([])
            )
        ]);
        $handlerStack = HandlerStack::create($this->mock);

        $this->client = new Client(new GuzzleTransport([
            'handler' => $handlerStack
        ]));
    }

    public function testSuccessGetCommentsAndGetEmptySet()
    {
        $response = $this->client->fetchComments();

        $this->assertIsArray($response->data, 'Results must be array');
        $this->assertEquals(200, $response->statusCode);
        $this->assertEquals('OK', $response->message);

        foreach ($response->data as $result) {
            $this->assertInstanceOf(Comment::class, $result);
            $this->assertObjectHasAttribute('id', $result);
            $this->assertObjectHasAttribute('name', $result);
            $this->assertObjectHasAttribute('text', $result);
        }

        $response = $this->client->fetchComments();

        $this->assertEquals([], $response->data, 'Request should returns empty set');
        $this->assertEquals(200, $response->statusCode);
        $this->assertEquals('OK', $response->message);

    }

    public function testSuccessPostComment()
    {
        $this->mock->reset();
        $this->mock->append(new Response(201));
        $response = $this->client->postComment(new Comment('text value', 'name value'));
        $this->assertEquals(201, $response->statusCode);
        $this->assertEquals('Created', $response->message);
    }

    public function testSuccessPutComment()
    {
        $this->mock->reset();
        $this->mock->append(new Response(201));
        $response = $this->client->updateComment(new Comment('text value', 'name value', 12));
        $this->assertEquals(201, $response->statusCode);
        $this->assertEquals('Created', $response->message);
    }

    public function testInputArgumentsPutMethod()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->client->updateComment(new Comment('', '', 12));
    }

    public function testClientServerRequestError()
    {
        $this->mock->reset();
        $this->mock->append(new Response(400));
        try {
            $this->client->fetchComments();
        } catch (AwesomeException $e) {
            $this->assertInstanceOf(ClientException::class, $e->getPrevious());
        }

        $this->mock->reset();
        $this->mock->append(new Response(500));
        try {
            $this->client->fetchComments();
        } catch (AwesomeException $e) {
            $this->assertInstanceOf(ServerException::class, $e->getPrevious());
        }
    }

    public function testConnectionError()
    {
        $this->mock->reset();
        $this->mock->append(new ConnectException('Connection Error', new Request('GET', 'comments')));
        try {
            $this->client->fetchComments();
        } catch (AwesomeException $e) {
            $this->assertInstanceOf(ConnectException::class, $e->getPrevious());
        }
    }

    public function responseBodyForGet()
    {
        return [
            [
                'id' => 1,
                'name' => 'One',
                'text' => 'Text of One'
            ],
            [
                'id' => 2,
                'name' => 'Two',
                'text' => 'Text of Two'
            ],
            [
                'id' => 3,
                'name' => 'Three',
                'text' => 'Text of Three'
            ]
        ];
    }
}