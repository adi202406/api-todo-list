<?php

namespace App\Http\Controllers;

use App\Models\Label;
use Illuminate\Http\Request;
use App\Http\Requests\LabelRequest;
use App\Http\Resources\LabelResource;

class LabelController extends Controller
{
    public function index()
    {
        $labels = Label::all();
        return LabelResource::collection($labels);
    }

    public function store(LabelRequest $request)
    {
        $label = Label::create($request->validated());
        return new LabelResource($label);
    }

    public function show(Label $label)
    {
        return new LabelResource($label);
    }

    public function update(LabelRequest $request, Label $label)
    {
        $label->update($request->validated());
        return new LabelResource($label);
    }

    public function destroy(Label $label)
    {
        $label->delete();
        return response()->json([
            'message' => 'Label deleted successfully'
        ], 204);
    }
}
