<?php

namespace App\Http\Controllers;

use App\Http\Requests\LocationRequest;
use App\Models\Department;
use App\Models\Location;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::with('department')
            ->withCount('equipment')
            ->orderBy('name')
            ->paginate(15);

        return view('locations.index', compact('locations'));
    }

    public function create()
    {
        return view('locations.create', [
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function store(LocationRequest $request)
    {
        Location::create($request->validated());

        return redirect()
            ->route('locations.index')
            ->with('success', 'Location created successfully.');
    }

    public function show(Location $location)
    {
        $location->load('department');

        $equipment = $location->equipment()
            ->with('category')
            ->whereNull('archived_at')
            ->latest()
            ->paginate(15);

        return view('locations.show', compact('location', 'equipment'));
    }

    public function edit(Location $location)
    {
        return view('locations.edit', [
            'location' => $location,
            'departments' => Department::orderBy('name')->get(),
        ]);
    }

    public function update(LocationRequest $request, Location $location)
    {
        $location->update($request->validated());

        return redirect()
            ->route('locations.show', $location)
            ->with('success', 'Location updated successfully.');
    }

    public function destroy(Location $location)
    {
        if ($location->equipment()->exists()) {
            return redirect()
                ->route('locations.index')
                ->with('error', 'Location cannot be deleted because equipment is still assigned to it.');
        }

        $location->delete();

        return redirect()
            ->route('locations.index')
            ->with('success', 'Location deleted successfully.');
    }
}
