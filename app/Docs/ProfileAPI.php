<?php

namespace App\Docs;

/**
 * @OA\OpenApi(
 *     @OA\Info(
 *         version="1.0.0",
 *         title="Todo List API Documentation",
 *         description="API documentation for Todo List application",
 *         @OA\Contact(
 *             email="admin@example.com"
 *         )
 *     ),
 *     @OA\Server(
 *         url="/api",
 *         description="API Server"
 *     )
 * )
 */

/**
 * @OA\Tag(
 *     name="Profile",
 *     description="API Endpoints for user profile management"
 * )
 */
/**
 * @OA\SecurityScheme(
 *     securityScheme="bearerAuth",
 *     type="http",
 *     scheme="bearer",
 *     bearerFormat="JWT"
 * )
 */

/**
 * @OA\Schema(
 *     schema="ErrorResponse",
 *     @OA\Property(property="message", type="string", example="Error message")
 * )
 */

/**
 */
class ProfileAPI
{
    /**
     * @OA\Get(
     *     path="/auth/profile",
     *     summary="Get user profile",
     *     tags={"Profile"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Profile retrieved successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function getProfile() {}

    /**
     * @OA\Put(
     *     path="/auth/profile",
     *     summary="Update user profile",
     *     tags={"Profile"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="name", type="string", example="John Doe"),
     *                 @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *                 @OA\Property(property="avatar", type="string", format="binary", description="Profile picture")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Profile updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/UserResource")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 @OA\Property(property="email", type="array", @OA\Items(type="string", example="The email has already been taken.")),
     *                 @OA\Property(property="avatar", type="array", @OA\Items(type="string", example="The avatar must be an image."))
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function updateProfile() {}

    /**
     * @OA\Put(
     *     path="/auth/profile/password",
     *     summary="Update user password",
     *     tags={"Profile"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password", "new_password", "new_password_confirmation"},
     *             @OA\Property(property="current_password", type="string", format="password", example="current123"),
     *             @OA\Property(property="new_password", type="string", format="password", example="newpass123"),
     *             @OA\Property(property="new_password_confirmation", type="string", format="password", example="newpass123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password berhasil diperbarui.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or incorrect current password",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Password lama salah.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Unauthenticated")
     *         )
     *     )
     * )
     */
    public function updatePassword() {}
}
