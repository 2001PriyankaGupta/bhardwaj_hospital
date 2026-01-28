<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Bed;
use App\Models\Room;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class BedController extends Controller
{


   public function index()
    {
         $user = Auth::user();
        $rooms = Room::where('is_active', 1)->get();
        $beds = Bed::with('room')->orderBy('id', 'desc')->get();

        // Bed counts
        $totalBeds = Bed::count();
        $availableBeds = Bed::where('status', 'available')->count();
        $occupiedBeds = Bed::where('status', 'occupied')->count();
        $maintenanceBeds = Bed::where('status', 'maintenance')->count();

        return view($user->user_type.'.bed.index', compact(
            'beds',
            'rooms',
            'totalBeds',
            'availableBeds',
            'occupiedBeds',
            'maintenanceBeds'
        ));
    }


    public function store(Request $request)
    {
         $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'bed_number' => 'required|string',
            'room_id' => 'required|exists:rooms,id',
            'status' => 'required|in:available,occupied,maintenance,reserved',
            'last_occupancy_date' => 'nullable|date',
            'next_availability_date' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string|max:500'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors below.');
        }

        try {
            Bed::create($request->all());
            return redirect()->route($user->user_type.'.beds.index')
                ->with('success', 'Bed added successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to add bed: ' . $e->getMessage());
        }
    }

    public function update(Request $request, $id)
    {
         $user = Auth::user();
        $bed = Bed::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'bed_number' => 'required|string' ,
            'room_id' => 'required|exists:rooms,id',
            'status' => 'required|in:available,occupied,maintenance,cleaning,reserved',
            'last_occupancy_date' => 'nullable|date',
            'next_availability_date' => 'nullable|date|after_or_equal:today',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'sometimes|boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput()
                ->with('error', 'Please correct the errors below.');
        }

        try {
            $data = $request->all();
            $data['is_active'] = $request->has('is_active') ? $request->is_active : true;

            $bed->update($data);

            return redirect()->route($user->user_type.'.beds.index')
                ->with('success', 'Bed updated successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update bed: ' . $e->getMessage());
        }
    }

    public function destroy($id)
    {
            $user = Auth::user();
        try {
            $bed = Bed::findOrFail($id);
            $bed->delete();

            return redirect()->route($user->user_type.'.beds.index')
                ->with('success', 'Bed deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Failed to delete bed: ' . $e->getMessage());
        }
    }

    
    public function show($id)
    {
        $bed = Bed::with('room')->findOrFail($id);
        return view('admin.bed.show', compact('bed'));
    }
}

