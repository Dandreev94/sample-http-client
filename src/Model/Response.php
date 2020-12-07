<?php

namespace AwesomeServiceClient\Model;

use AwesomeServiceClient\Model\Comment;

class Response
{
    /**
     * @var int
     */
    public $statusCode;

    /**
     * @var string
     */
    public $message;

    /**
     * @var Comment []
     */
    public $data;

    /**
     * Response constructor.
     * @param int $statusCode
     * @param string $message
     * @param Comment [] $data
     */
    public function __construct(int $statusCode, string $message, array $data = [])
    {
        $this->statusCode = $statusCode;
        $this->message = $message;
        $this->data = $data;
    }
}