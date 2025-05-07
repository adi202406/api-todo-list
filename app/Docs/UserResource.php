<?php

namespace App\Docs;

/**
 * @OA\Schema(
 *     schema="UserResource",
 *     @OA\Property(
 *         property="data",
 *         type="object",
 *         ref="#/components/schemas/User"
 *     )
 * )
 */
class UserResource  {}