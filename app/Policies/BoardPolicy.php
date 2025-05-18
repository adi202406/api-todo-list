<?php

namespace App\Policies;

use App\Models\Board;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Auth\Access\Response;

class BoardPolicy
{
    /**
     * Get the user's role for the board's workspace
     */
    private function getUserRole(User $user, Board $board): ?string
    {
        return $board->workspace->users()
            ->where('user_id', $user->id)
            ->first()
            ?->pivot
            ?->role;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // User can view any boards in workspaces they're a member of
        return $user->workspaces()->exists();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Board $board): bool
    {
        $role = $this->getUserRole($user, $board);
        
        // Only members of the workspace can view the board
        return in_array($role, ['owner', 'editor', 'viewer']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Workspace $workspace): bool
    {
        $role = $workspace->users()
            ->where('user_id', $user->id)
            ->first()
            ?->pivot
            ?->role;
            
        // Only owners and editors can create boards
        return in_array($role, ['owner', 'editor']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Board $board): bool
    {
        $role = $this->getUserRole($user, $board);
        
        // Only owners and editors can update boards
        return in_array($role, ['owner', 'editor']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Board $board): bool
    {
        $role = $this->getUserRole($user, $board);
        
        // Only owners can delete boards
        return $role === 'owner';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Board $board): bool
    {
        $role = $this->getUserRole($user, $board);
        
        // Only owners can restore boards
        return $role === 'owner';
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Board $board): bool
    {
        $role = $this->getUserRole($user, $board);
        
        // Only owners can permanently delete boards
        return $role === 'owner';
    }
}