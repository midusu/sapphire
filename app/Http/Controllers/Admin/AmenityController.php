<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Amenity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Helpers\AuditHelper;

class AmenityController extends Controller
{
    public function index()
    {
        $amenities = Amenity::orderBy('display_order')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.amenities.index', compact('amenities'));
    }

    public function create()
    {
        return view('admin.amenities.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:5120',
            'category' => 'required|in:room,hotel,activity,dining,general',
            'display_order' => 'nullable|integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('amenities', 'public');
        }

        $amenity = Amenity::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'icon' => $validated['icon'] ?? null,
            'image_path' => $imagePath,
            'category' => $validated['category'],
            'display_order' => $validated['display_order'] ?? 0,
            'is_featured' => $validated['is_featured'] ?? false,
            'is_active' => $validated['is_active'] ?? true,
        ]);

        AuditHelper::log('create', "Amenity created: {$amenity->name}", $amenity);

        return redirect()->route('admin.amenities.index')->with('success', 'Amenity created successfully.');
    }

    public function edit(Amenity $amenity)
    {
        return view('admin.amenities.edit', compact('amenity'));
    }

    public function update(Request $request, Amenity $amenity)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'icon' => 'nullable|string|max:255',
            'image' => 'nullable|image|max:5120',
            'category' => 'required|in:room,hotel,activity,dining,general',
            'display_order' => 'nullable|integer',
            'is_featured' => 'boolean',
            'is_active' => 'boolean',
        ]);

        $oldValues = $amenity->toArray();

        if ($request->hasFile('image')) {
            if ($amenity->image_path) {
                Storage::disk('public')->delete($amenity->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('amenities', 'public');
        }

        $amenity->update($validated);

        AuditHelper::log('update', "Amenity updated: {$amenity->name}", $amenity, $oldValues, $amenity->toArray());

        return redirect()->route('admin.amenities.index')->with('success', 'Amenity updated successfully.');
    }

    public function destroy(Amenity $amenity)
    {
        if ($amenity->image_path) {
            Storage::disk('public')->delete($amenity->image_path);
        }

        AuditHelper::log('delete', "Amenity deleted: {$amenity->name}", $amenity);

        $amenity->delete();

        return redirect()->route('admin.amenities.index')->with('success', 'Amenity deleted successfully.');
    }
}
