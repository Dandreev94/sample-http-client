<?php

namespace AwesomeServiceClient\Transport;

use AwesomeServiceClient\Exception\AwesomeException;
use AwesomeServiceClient\Model\Response;

interface TransportInterface
{
    public const BASE_URL = 'https://example.com';

    public const COMMENT_URL = '/comment';

    public const COMMENTS_URL = '/comments';

    /**
     * @return Response
     * @throws AwesomeException
     */
    public function get(): Response;

    /**
     * @param int $commentId
     * @param string $name
     * @param string $text
     * @return Response
     * @throws AwesomeException
     */
    public function put(int $commentId, string $name = '', string $text = ''): Response;

    /**
     * @param string $name
     * @param string $text
     * @return Response
     * @throws AwesomeException
     */
    public function post(string $name, string $text): Response;
}

