<?php

namespace AwesomeServiceClient\Client;

use AwesomeServiceClient\Model\Comment;
use AwesomeServiceClient\Model\Response;

interface ClientInterface
{
    /**
     * @return Response
     */
    public function fetchComments(): Response;

    /**
     * @param Comment $comment
     * @return Response
     */
    public function postComment(Comment $comment): Response;

    /**
     * @param Comment $comment
     * @return Response
     */
    public function updateComment(Comment $comment): Response;
}
