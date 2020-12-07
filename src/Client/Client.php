<?php

namespace AwesomeServiceClient\Client;

use AwesomeServiceClient\Model\Comment;
use AwesomeServiceClient\Model\Response;
use AwesomeServiceClient\Transport\TransportInterface;
use AwesomeServiceClient\Exception\AwesomeException;

class Client implements ClientInterface
{
    /**
     * @var TransportInterface
     */
    private $transport;

    /**
     * Client constructor.
     * @param TransportInterface $transport
     */
    public function __construct(TransportInterface $transport)
    {
        $this->transport = $transport;
    }

    /**
     * @return Response
     * @throws AwesomeException
     */
    public function fetchComments(): Response
    {
        return $this->transport->get();
    }

    /**
     * @param Comment $comment
     * @return Response
     * @throws AwesomeException
     */
    public function postComment(Comment $comment): Response
    {
        return $this->transport->post($comment->getName(), $comment->getText());
    }

    /**
     * @param Comment $comment
     * @return Response
     * @throws AwesomeException
     */
    public function updateComment(Comment $comment): Response
    {
        return $this->transport->put($comment->getId(), $comment->getName(), $comment->getText());
    }


}