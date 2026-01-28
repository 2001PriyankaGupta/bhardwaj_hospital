<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Room;
use App\Models\RoomType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoomController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $rooms = Room::with('roomType')->get();
        $roomTypes = RoomType::where('is_active', true)->get();
        return view($user->user_type.'.room.index', compact('rooms','roomTypes'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        // Validation
        $request->validate([
            'room_number' => 'required|unique:rooms,room_number',
            'room_type_id' => 'required|exists:room_types,id',
            'floor_number' => 'required|integer|min:0',
            'bed_count' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,maintenance,cleaning',
            'current_occupancy' => 'integer|min:0'
        ]);

        Room::create($request->all());

        return redirect()->route($user->user_type.'.rooms.index')
                        ->with('success', 'Room created successfully!');
    }

    public function edit($id)
    {
        $user = Auth::user();
        $room = Room::findOrFail($id);
        $roomTypes = RoomType::where('is_active', true)->get();

        return view($user->user_type.'.room.create', compact('room','roomTypes'));
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $room = Room::findOrFail($id);

        // Validation
        $request->validate([
            'room_number' => 'required|unique:rooms,room_number,' . $id,
            'room_type_id' => 'required|exists:room_types,id',
            'floor_number' => 'required|integer|min:0',
            'bed_count' => 'required|integer|min:1',
            'status' => 'required|in:available,occupied,maintenance,cleaning',
            'current_occupancy' => 'integer|min:0'
        ]);

        $room->update($request->all());

        return redirect()->route($user->user_type.'.rooms.index')
                        ->with('success', 'Room updated successfully!');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $room = Room::findOrFail($id);
        $room->delete();

        return redirect()->route($user->user_type.'.rooms.index')
                        ->with('success', 'Room deleted successfully!');
    }
}
