<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DiscountController extends Controller
{

    public function store(Request $request)
    {
         $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'applicable_to' => 'required|in:services,packages,all',
            'applicable_ids' => 'nullable|array',
            'valid_from' => 'required|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['applicable_ids'] = $request->has('applicable_ids') ? json_encode($request->applicable_ids) : null;

        Discount::create($data);

        return redirect()->route($user->user_type.'.services.index')
            ->with('success', 'Discount created successfully.');
    }

    public function update(Request $request, $id)
    {
        $user = Auth::user();
        $discount =  Discount::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:percentage,fixed',
            'value' => 'required|numeric|min:0',
            'applicable_to' => 'required|in:services,packages,all',
            'applicable_ids' => 'nullable|array',
            'valid_from' => 'required|date',
            'valid_until' => 'nullable|date|after:valid_from',
            'usage_limit' => 'nullable|integer|min:1',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['applicable_ids'] = $request->has('applicable_ids') ? json_encode($request->applicable_ids) : null;

        $discount->update($data);

        return redirect()->route($user->user_type.'.services.index')
            ->with('success', 'Discount updated successfully.');
    }

    public function destroy($id)
    {  
        $user = Auth::user();
       $discount =  Discount::findOrFail($id);
        $discount->delete();

        return redirect()->route($user->user_type.'.services.index')
            ->with('success', 'Discount deleted successfully.');
    }
}