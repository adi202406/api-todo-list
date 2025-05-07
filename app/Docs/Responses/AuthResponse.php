<?php

namespace App\Docs\Responses;

/**
 * @OA\Schema(
 *     schema="AuthResponse",
 *     required={"token", "user"},
 *     @OA\Property(property="token", type="string", example="1|laravel_sanctum_token"),
 *     @OA\Property(
 *         property="user",
 *         ref="#/components/schemas/User"
 *     )
 * )
 */
class AuthResponse {}