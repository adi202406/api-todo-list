<?php
namespace App\Http\Controllers;

use App\Models\Workspace;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\WorkspaceRequest;
use App\Http\Resources\WorkspaceResource;

class WorkspaceController extends Controller
{
    // GET /api/workspaces
    public function index()
    {
        $workspaces = Workspace::where('owner_id', Auth::id())->get();
        return WorkspaceResource::collection($workspaces);
    }

    // POST /api/workspaces
    public function store(WorkspaceRequest $request)
    {
        $validated = $request->validated();

        // Generate unique slug if not provided
        $slug = Str::slug($validated['title']);
        $slug = $this->generateUniqueSlug($slug);

        $workspace = Workspace::create([
            'title'       => $validated['title'],
            'slug'       => $slug,
            'visibility' => $validated['visibility'],
            'owner_id'   => Auth::id(),
        ]);

        return new WorkspaceResource($workspace);
    }

    // GET /api/workspaces/{id}
    public function show($slug)
    {
        $workspace = Workspace::where('owner_id', Auth::id())
                                ->where('slug', $slug)
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
            'title' => $validated['title'],
            'slug'  => $slug,
            'visibility' => $validated['visibility'],
        ]);

        return new WorkspaceResource($workspace);
    }

    // DELETE /api/workspaces/{id}
    public function destroy($id)
    {
        $workspace = Workspace::where('owner_id', Auth::id())->findOrFail($id);

        if(!$workspace) {
            return response()->json(['message' => 'Workspace cannot be deleted.'], 403);
        }
        $workspace->delete();

        return response()->json(['message' => 'Workspace deleted successfully.']);
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
