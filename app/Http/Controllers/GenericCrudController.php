<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class GenericCrudController extends Controller
{
    // Fetch all records with options for hierarchical filtering
    public function index(Request $request)
    {
        try {
            $modelName = $this->getModelName($request);

            // Check if the model exists
            if (!class_exists($modelName)) {
                return response()->json(['message' => 'Model does not exist'], 400);
            }

            // Special handling for the Category model
            if ($modelName === 'App\Models\Category') {
                $select = $request->query('select'); // Check for 'select' query parameter
                $parentId = $request->query('parent_id'); // Check for 'parent_id'

                // Fetch main categories
                if ($select === 'main' && !$parentId) {
                    $categories = $modelName::whereNull('parent_id')->get();
                    return response()->json($categories, 200);
                }

                // Fetch subcategories of a given parent category
                if ($select === 'sub' && $parentId) {
                    $categories = $modelName::where('parent_id', $parentId)->get();
                    return response()->json($categories, 200);
                }

                // Default: Fetch all categories (with descendants if needed)
                $categories = $modelName::with('descendants')->get();
                return response()->json($categories, 200);
            }

            // Default behavior for non-Category models
            $records = $modelName::all();
            return response()->json($records, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch records', 'error' => $e->getMessage()], 500);
        }
    }


    // Create a new record
    public function store(Request $request)
    {
        try {
            $modelName = $this->getModelName($request);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|exists:categories,id', // Validate parent_id for categories
                'details.name' => 'nullable|string|max:255',
                'details.description' => 'nullable|string',
            ]);

            // Create the main record
            $record = $modelName::create($validatedData);

            // Create associated details for categories
            if ($modelName === 'App\Models\Category' && isset($validatedData['details'])) {
                $record->details()->create($validatedData['details']);
            }

            return response()->json(['message' => 'Record created successfully', 'record' => $record], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to create record', 'error' => $e->getMessage()], 500);
        }
    }

    // Fetch a single record
    public function show(Request $request, $id)
    {
        try {
            $modelName = $this->getModelName($request);

            $record = $modelName::findOrFail($id);
            return response()->json($record, 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to fetch record', 'error' => $e->getMessage()], 500);
        }
    }

    // Update a record
    public function update(Request $request, $id)
    {
        try {
            $modelName = $this->getModelName($request);

            $record = $modelName::findOrFail($id);

            $validatedData = $request->validate([
                'name' => 'required|string|max:255',
                'parent_id' => 'nullable|exists:categories,id', // Validate parent_id for categories
                'details.name' => 'nullable|string|max:255',
                'details.description' => 'nullable|string',
            ]);

            // Update the main record
            $record->update($validatedData);

            // Update or create associated details for categories
            if ($modelName === 'App\Models\Category' && isset($validatedData['details'])) {
                $record->details()->updateOrCreate(['category_id' => $record->id], $validatedData['details']);
            }

            return response()->json(['message' => 'Record updated successfully', 'record' => $record], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to update record', 'error' => $e->getMessage()], 500);
        }
    }

    // Delete a record (soft delete)
    public function destroy(Request $request, $id)
    {
        try {
            $modelName = $this->getModelName($request);

            $record = $modelName::findOrFail($id);
            $record->delete();

            return response()->json(['message' => 'Record soft deleted successfully'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to delete record', 'error' => $e->getMessage()], 500);
        }
    }

    // Restore a soft-deleted record
    public function restore(Request $request, $id)
    {
        try {
            $modelName = $this->getModelName($request);

            $record = $modelName::withTrashed()->findOrFail($id);
            $record->restore();

            return response()->json(['message' => 'Record restored successfully', 'record' => $record], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to restore record', 'error' => $e->getMessage()], 500);
        }
    }

    // Helper function to get the model name
    private function getModelName(Request $request)
    {
        $model = $request->input('model'); // Model name from the request
        $fullModelName = "App\\Models\\" . ucfirst($model);

        if (!class_exists($fullModelName)) {
            throw new \Exception("Model {$model} does not exist.");
        }

        return $fullModelName;
    }
}
