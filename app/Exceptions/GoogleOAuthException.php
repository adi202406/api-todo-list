<?php

namespace App\Exceptions;

use Exception;

class GoogleOAuthException extends Exception
{
    /**
     * Report the exception.
     *
     * @return bool|null
     */
    public function report(): ?bool
    {
        // Log the exception
        return true;
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function render()
    {
        return response()->json([
            'status' => 'error',
            'message' => $this->getMessage(),
        ], 500);
    }
}