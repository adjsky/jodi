<?php

declare(strict_types=1);

namespace App\Support\Exceptions;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Throwable;

class DisplayException extends JodiException
{
    public function __construct(
        string $message,
        int $statusCode = Response::HTTP_BAD_REQUEST,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $statusCode, $previous);
    }

    public function render(): JsonResponse
    {
        return response()->json(
            ['message' => $this->getMessage()],
            $this->getCode(),
        );
    }
}
