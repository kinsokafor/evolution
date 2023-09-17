<?php

namespace EvoPhp\Api;

final class ErrorHandler
{

    public static function handleException(\Throwable $exception): void
    {
        http_response_code(500);
        // var_dump(get_class_methods($exception));
        echo json_encode([
            "code" => $exception->getCode(),
            "message" => $exception->getMessage(),
            "file" => $exception->getFile(),
            "line" => $exception->getLine()
        ]);
    }
    
    public static function handleError(
        int $errno,
        string $errstr,
        string $errfile,
        int $errline
    ): bool
    {
        throw new \ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}
