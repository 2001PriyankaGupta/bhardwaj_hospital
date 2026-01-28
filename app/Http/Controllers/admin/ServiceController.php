<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\RateCard;
use App\Models\Package;
use App\Models\Discount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $services = Service::all();
        $rateCards = RateCard::all();
        $packages = Package::all();
        $discounts = Discount::all();
        return view($user->user_type.'.services.index', compact('services', 'rateCards', 'packages', 'discounts'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        Service::create($request->all());

        return redirect()->route($user->user_type.'.services.index')
            ->with('success', 'Service created successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'description' => 'required|string',
            'status' => 'required|in:active,inactive',
        ]);

        $service = Service::findOrFail($id);

        $service->update([
            'name' => $request->name,
            'category' => $request->category,
            'description' => $request->description,
            'status' => $request->status,
        ]);

        return redirect()->route($user->user_type.'.services.index')
            ->with('success', 'Service updated successfully.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $service = Service::findOrFail($id);
        $service->delete();

        return redirect()->route($user->user_type.'.services.index')
            ->with('success', 'Service deleted successfully.');
    }

    public function edit($id)
    {
        echo "id=".$id;
        die;
        try {
            $item = Service::findOrFail($id);
            return response()->json([
                'success' => true,
                'data' => $item
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Item not found'
            ], 404);
        }
}
}