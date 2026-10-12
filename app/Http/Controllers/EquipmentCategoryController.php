<?php

namespace App\Http\Controllers;

use App\Http\Requests\EquipmentCategoryRequest;
use App\Models\EquipmentCategory;

class EquipmentCategoryController extends Controller
{
    public function index()
    {
        $categories = EquipmentCategory::withCount('equipment')
            ->orderBy('name')
            ->paginate(15);

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(EquipmentCategoryRequest $request)
    {
        EquipmentCategory::create($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Equipment category created successfully.');
    }

    public function edit(EquipmentCategory $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(EquipmentCategoryRequest $request, EquipmentCategory $category)
    {
        $category->update($request->validated());

        return redirect()
            ->route('categories.index')
            ->with('success', 'Equipment category updated successfully.');
    }

    public function destroy(EquipmentCategory $category)
    {
        if ($category->equipment()->exists()) {
            return back()->with('error', 'This category cannot be deleted because equipment is assigned to it.');
        }

        $category->delete();

        return redirect()
            ->route('categories.index')
            ->with('success', 'Equipment category deleted successfully.');
    }
}
