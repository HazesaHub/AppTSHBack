<?php

class Controller 
{
    public function __construct()
    {
        
    }
    public function response(int $statusCode, bool $error, string $message, array $data = []): object
    {
        return (object) array('statusCode' => $statusCode, 'error' => $error, 'message' => $message, 'data' => $data);
    }
}