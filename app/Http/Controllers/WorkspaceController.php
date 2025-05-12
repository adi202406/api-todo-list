<?php
namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\WorkspaceRequest;
use App\Http\Resources\WorkspaceResource;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class WorkspaceController extends Controller
{
    // GET /api/workspaces
    public function index()
    {
        $workspaces = Workspace::where('owner_id', Auth::id())
            ->withCount('users')
            ->get();

        if ($workspaces->isEmpty()) {
            return response()->json(['message' => 'You do not have any workspaces.'], 404);
        }
        return WorkspaceResource::collection($workspaces);
    }

    // POST /api/workspaces
    public function store(WorkspaceRequest $request)
    {
        $validated = $request->validated();

        try {
            DB::beginTransaction();

            // Generate unique slug if not provided
            $slug = Str::slug($validated['title']);
            $slug = $this->generateUniqueSlug($slug);

            $workspace = Workspace::create([
                'title'       => $validated['title'],
                'description' => $validated['description'],
                'slug'        => $slug,
                'visibility'  => $validated['visibility'],
                'owner_id'    => Auth::id(),
            ]);

            if ($request->hasFile('banner_image')) {
                // Upload new banner to Cloudinary
                $uploadResult = Cloudinary::upload($request->file('banner_image')->getRealPath());

                // Get the secure URL and public ID
                $uploadedFileUrl = $uploadResult->getSecurePath();
                $publicId        = $uploadResult->getPublicId();

                // Update workspace with banner image info
                $workspace->banner_image           = $uploadedFileUrl;
                $workspace->banner_image_public_id = $publicId;
                $workspace->save();
            }

            // Sync workspace creator as owner in pivot table
            $workspace->users()->attach(Auth::id(), [
                'role'      => 'owner',
                'status'    => 'active',
                'joined_at' => now(),
            ]);

            DB::commit();

            return new WorkspaceResource($workspace);

        } catch (\Throwable $th) {
            DB::rollBack();
            // Log::error($th); // optional: log error for debugging
            return response()->json([
                'message' => 'Gagal menyimpan workspace.',
                'error'   => $th->getMessage(),
            ], 500);
        }
    }

    // GET /api/workspaces/{id}
    public function show($slug)
    {
        $workspace = Workspace::where('slug', $slug)
            ->where(function ($query) {
                $query->where('owner_id', Auth::id())
                    ->orWhereHas('workspaceUsers', function ($q) {
                        $q->where('user_id', Auth::id());
                    });
            })
            ->with(['workspaceUsers'])
            ->firstOrFail();

        return new WorkspaceResource($workspace);
    }

    // PUT /api/workspaces/{id}
    public function update(WorkspaceRequest $request, $id)
    {
        $workspace = Workspace::where('owner_id', Auth::id())->findOrFail($id);

        $validated = $request->validated();

        // Generate unique slug if not provided
        $slug = Str::slug($validated['title']);
        $slug = $this->generateUniqueSlug($slug);

        $workspace->update([
            'title'       => $validated['title'],
            'description' => $validated['description'],
            'slug'        => $slug,
            'visibility'  => $validated['visibility'],
        ]);

        if ($request->hasFile('banner_image')) {
            // If user already has an banner_image, delete the old one
            if ($workspace->banner_image_public_id) {
                // Delete old image from Cloudinary
                Cloudinary::destroy($workspace->banner_image_public_id);
            }
            // Upload new banner_image to Cloudinary
            $uploadResult = Cloudinary::upload($request->file('banner_image')->getRealPath());

            // Get the secure URL and public ID
            $uploadedFileUrl = $uploadResult->getSecurePath();
            $publicId        = $uploadResult->getPublicId();

            // Update wo$workspace's banner_image and banner_image_public_id
            $workspace->banner_image           = $uploadedFileUrl;
            $workspace->banner_image_public_id = $publicId;
            $workspace->save();
        }

        return new WorkspaceResource($workspace);
    }

    // DELETE /api/workspaces/{id}
    public function destroy($id)
    {
        $workspace = Workspace::where('owner_id', Auth::id())->findOrFail($id);

        $workspace->delete();

        return response()->json(['message' => 'Workspace deleted successfully.']);
    }

    public function inviteUser(Request $request, $id)
    {
        // Authorize workspace ownership
        $workspace = Workspace::where('owner_id', Auth::id())->findOrFail($id);

        // Validate input
        $validated = $request->validate([
            'email' => 'required|email|exists:users,email',
            'role'  => 'required|in:editor,viewer',
        ]);

        // Get the invited user
        $user = User::where('email', $validated['email'])->first();

        // Include soft-deleted pivot record in the check
        $existingMember = $workspace->users()
            ->withPivot('status', 'deleted_at')
            ->wherePivot('user_id', $user->id)
            ->withTrashed() // Include soft-deleted pivot if using custom pivot model with SoftDeletes
            ->first();

        // Case: user still in workspace
        if ($existingMember && is_null($existingMember->pivot->deleted_at)) {
            if ($existingMember->pivot->status === 'pending') {
                return response()->json([
                    'message' => 'User has already been invited and the invitation is pending',
                ], 422);
            }

            if ($existingMember->pivot->status === 'active') {
                return response()->json([
                    'message' => 'User is already an active member of this workspace',
                ], 422);
            }
        }

        // Case: user was removed (soft deleted) before — restore and update data
        if ($existingMember && ! is_null($existingMember->pivot->deleted_at)) {
            $workspace->users()->updateExistingPivot($user->id, [
                'role'       => $validated['role'],
                'status'     => 'pending',
                'joined_at'  => null,
                'deleted_at' => null, // restore soft delete
            ]);

            return response()->json([
                'message' => 'User has been re-invited successfully',
            ], 200);
        }

        // New invite
        $workspace->users()->attach($user->id, [
            'role'       => $validated['role'],
            'status'     => 'pending',
            'invited_by' => Auth::id(),
            'joined_at'  => null,
        ]);

        return response()->json([
            'message' => 'Invitation sent successfully',
        ], 201);
    }

    public function acceptInvitation($id)
    {
        $workspace = Workspace::findOrFail($id);

        // Check if user has pending invitation
        $membership = $workspace->users()
            ->where('user_id', Auth::id())
            ->wherePivot('status', 'pending')
            ->firstOrFail();

        // Update status to active
        $workspace->users()->updateExistingPivot(Auth::id(), [
            'status'    => 'active',
            'joined_at' => now(),
        ]);

        return response()->json([
            'message' => 'You have successfully joined the workspace',
        ], 200);
    }

    public function removeUser(Request $request, $id)
    {
        // Authorize workspace ownership
        $workspace = Workspace::where('owner_id', Auth::id())->findOrFail($id);

        // Validate input
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
        ]);

        // Check if user exists in workspace
        $membership = $workspace->users()
            ->where('user_id', $validated['user_id'])
            ->wherePivot('status', 'active')
            ->firstOrFail();

        // Prevent removing workspace owner
        if ($membership->pivot->role === 'owner') {
            return response()->json([
                'message' => 'Cannot remove workspace owner',
            ], 403);
        }

        // Soft delete the user from workspace and update status
        $workspace->users()->updateExistingPivot($validated['user_id'], [
            'status'     => 'removed',
            'deleted_at' => now(),
        ]);

        return response()->json([
            'message' => 'User has been removed from workspace successfully',
        ], 200);
    }

    // Generate a unique slug if already exists
    private function generateUniqueSlug($baseSlug)
    {
        $slug = $baseSlug;
        $i    = 1;
        while (Workspace::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $i++;
        }
        return $slug;
    }
}
