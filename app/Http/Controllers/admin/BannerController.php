<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class BannerController extends Controller
{
   
    public function index()
    {
        $Loginuser = Auth::user();
        $banners = Banner::latest()->paginate(10);
        return view($Loginuser->user_type.'.banner.index', compact('banners'));
    }

   
    public function create()
    {
         $Loginuser = Auth::user();
        return view($Loginuser->user_type.'.banner.create');
    }

   
    public function store(Request $request)
    {
        $Loginuser = Auth::user();

        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'status' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $banner = new Banner();
            $banner->title = $request->title;
            $banner->description = $request->description;
            $banner->status = $request->status ?? true;
            $banner->sort_order = $request->sort_order ?? 0;

            // Handle image upload
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = 'banner_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('banners', $imageName, 'public');
                $banner->image = $imagePath;
            }

            $banner->save();

            return redirect()->route($Loginuser->user_type.'.banner.index')
                ->with('success', 'Banner created successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating banner: ' . $e->getMessage())
                ->withInput();
        }
    }

   
    public function show(Banner $banner)
    {
        $Loginuser = Auth::user();
        return view($Loginuser->user_type.'.banner.show', compact('banner'));
    }

   
    public function edit(Banner $banner)
    {
        $Loginuser = Auth::user();
        return view($Loginuser->user_type.'.banner.edit', compact('banner'));
    }

    
    public function update(Request $request, Banner $banner)
    {
        $Loginuser = Auth::user();
        $validator = Validator::make($request->all(), [
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'status' => 'boolean',
            'sort_order' => 'integer|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $banner->title = $request->title;
            $banner->description = $request->description;
            $banner->status = $request->status ?? $banner->status;
            $banner->sort_order = $request->sort_order ?? $banner->sort_order;

            // Handle image upload if new image is provided
            if ($request->hasFile('image')) {
                // Delete old image if exists
                if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                    Storage::disk('public')->delete($banner->image);
                }

                $image = $request->file('image');
                $imageName = 'banner_' . time() . '_' . Str::random(10) . '.' . $image->getClientOriginalExtension();
                $imagePath = $image->storeAs('banners', $imageName, 'public');
                $banner->image = $imagePath;
            }

            $banner->save();

            return redirect()->route($Loginuser->user_type.'.banner.index')
                ->with('success', 'Banner updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating banner: ' . $e->getMessage())
                ->withInput();
        }
    }

    
    public function destroy(Banner $banner)
    {
        $Loginuser = Auth::user();
        try {
            // Delete image from storage
            if ($banner->image && Storage::disk('public')->exists($banner->image)) {
                Storage::disk('public')->delete($banner->image);
            }

            $banner->delete();

            return redirect()->route($Loginuser->user_type.'.banner.index')
                ->with('success', 'Banner deleted successfully.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error deleting banner: ' . $e->getMessage());
        }
    }

   
    public function updateStatus(Request $request, Banner $banner)
    {
        $Loginuser = Auth::user();
        try {
            $banner->status = $request->status;
            $banner->save();

            return response()->json([
                'success' => true,
                'message' => 'Status updated successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating status: ' . $e->getMessage()
            ], 500);
        }
    }
}