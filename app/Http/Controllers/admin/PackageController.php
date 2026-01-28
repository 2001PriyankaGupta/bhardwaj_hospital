<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Package;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PackageController extends Controller
{
    // public function index()
    // {
    //     $packages = Package::all();
    //     return view('admin.packages.index', compact('packages'));
    // }

    public function store(Request $request)
    {
         $user = Auth::user();
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'included_services' => 'required|array',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['included_services'] = json_encode($request->included_services);

        Package::create($data);

        return redirect()->route($user->user_type.'.services.index')
            ->with('success', 'Package created successfully.');
    }

    public function update(Request $request, $id)
    {
            $user = Auth::user();
        $package = Package::findOrFail($id);
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'currency' => 'required|string|max:3',
            'included_services' => 'required|array',
            'is_active' => 'boolean',
        ]);

        $data = $request->all();
        $data['included_services'] = json_encode($request->included_services);

        $package->update($data);

        return redirect()->route($user->user_type.'.services.index')
            ->with('success', 'Package updated successfully.');
    }

    public function destroy($id)
    {
        $user = Auth::user();
        $package = Package::findOrFail($id);
        $package->delete();

        return redirect()->route($user->user_type.'.services.index')
            ->with('success', 'Package deleted successfully.');
    }
}