<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomTypeController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $roomTypes = RoomType::withCount('rooms')->get();
        return view($user->user_type.'.room.types.index', compact('roomTypes'));
    }

    public function edit($id)
    {
        $user = Auth::user();
        $roomType = RoomType::findOrFail($id);
        return view($user->user_type.'.room.types.edit-form', compact('roomType'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        // Validation
        $request->validate([
            'name' => 'required|unique:room_types,name',
            'base_price' => 'required|numeric|min:0',
            'max_capacity' => 'required|integer|min:1',
            'available_rooms' => 'nullable|integer|min:0',
            'current_utilization' => 'nullable|integer|min:0'
        ]);

        $request->merge([
            'amenities' => json_encode($request->amenities ?? []),
            'seasonal_pricing' => json_encode($request->seasonal_pricing ?? []),
            'discounts' => json_encode($request->discounts ?? []),
            'capacity_forecast' => json_encode($request->capacity_forecast ?? []),
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        RoomType::create($request->all());

        return redirect()->route($user->user_type.'.room-types.index')
                        ->with('success', 'Room type created successfully!');
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $roomType = RoomType::findOrFail($id);

        // Validation
        $request->validate([
            'name' => 'required|unique:room_types,name,' . $id,
            'base_price' => 'required|numeric|min:0',
            'max_capacity' => 'required|integer|min:1',
            'available_rooms' => 'nullable|integer|min:0',
            'current_utilization' => 'nullable|integer|min:0'
        ]);

        $request->merge([
            'amenities' => json_encode($request->amenities ?? []),
            'seasonal_pricing' => json_encode($request->seasonal_pricing ?? []),
            'discounts' => json_encode($request->discounts ?? []),
            'capacity_forecast' => json_encode($request->capacity_forecast ?? []),
            'is_active' => $request->has('is_active') ? 1 : 0
        ]);

        $roomType->update($request->all());
        
        return redirect()->route($user->user_type.'.room-types.index')
                        ->with('success', 'Room type updated successfully!');
    }

    public function destroy($id)
    {
        $user = Auth::user();   
        $roomType = RoomType::findOrFail($id);
        
        // Check if room type has rooms
        if ($roomType->rooms()->count() > 0) {
            return redirect()->route($user->user_type.'.room-types.index')
                            ->with('error', 'Cannot delete room type. It has associated rooms.');
        }
        
        $roomType->delete();
        
        return redirect()->route($user->user_type.'.room-types.index')
                        ->with('success', 'Room type deleted successfully!');
    }
}