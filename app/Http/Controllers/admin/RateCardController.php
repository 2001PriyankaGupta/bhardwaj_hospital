<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\RateCard;
use App\Models\Service;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class RateCardController extends Controller
{
    // public function index()
    // {
    //     $rateCards = RateCard::with('service')->get();
    //     return view('admin.rate-cards.index', compact('rateCards'));
    // }

    public function store(Request $request)
    {
         $user = Auth::user();
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'billing_cycle' => 'required|string|max:50',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['features'] = $request->has('features') ? json_encode($request->features) : null;

        RateCard::create($data);

        return redirect()->route($user->user_type.'.services.index')
            ->with('success', 'Rate card created successfully.');
    }

    public function update(Request $request, $id)
    {
            $user = Auth::user();
        $rateCard = RateCard::findOrFail($id);
        $request->validate([
            'service_id' => 'required|exists:services,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'billing_cycle' => 'required|string|max:50',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['features'] = $request->has('features') ? json_encode($request->features) : null;

        $rateCard->update($data);

        return redirect()->route($user->user_type.'.services.index')
            ->with('success', 'Rate card updated successfully.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $rateCard = RateCard::findOrFail($id);
        $rateCard->delete();

        return redirect()->route($user->user_type.'.services.index')
            ->with('success', 'Rate card deleted successfully.');
    }
}