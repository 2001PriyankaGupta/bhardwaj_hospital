<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\HealthTip;
use App\Services\LinkPreviewService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class HealthTipController extends Controller
{
    private $linkPreviewService;

    public function __construct()
    {
        $this->linkPreviewService = new LinkPreviewService();
    }

    public function index()
    {
       $Loginuser = Auth::user();
        $healthTips = HealthTip::all();
        return view($Loginuser->user_type.'.health.index', compact('healthTips'));
    }

    public function create()
    {
        $Loginuser = Auth::user();
        return view($Loginuser->user_type.'.health.create');
    }

    public function store(Request $request)
    {
        $Loginuser = Auth::user();
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'link' => 'nullable|url',
            'thumbnail_image' => 'nullable|image',
        ]);

        $data = $request->all();

        // If no thumbnail uploaded but link exists, try to generate thumbnail
        if (!$request->hasFile('thumbnail_image') && $request->filled('link')) {
            $thumbnail = $this->linkPreviewService->generateThumbnailFromUrl($request->link);
            if ($thumbnail) {
                $data['thumbnail_image'] = $thumbnail;
            }
        }
        // If thumbnail is uploaded normally
        elseif ($request->hasFile('thumbnail_image')) {
            $data['thumbnail_image'] = $request->file('thumbnail_image')->store('health_tips', 'public');
        }

        HealthTip::create($data);

        return redirect()->route($Loginuser->user_type.'.healthtips.index')->with('success', 'Health Tip created successfully.');
    }

    public function edit(HealthTip $healthTip)
    {
        $Loginuser = Auth::user();
        return view($Loginuser->user_type.'.health.edit', compact('healthTip'));
    }

    public function update(Request $request, HealthTip $healthTip)
    {
        $Loginuser = Auth::user();
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required',
            'link' => 'nullable|url',
            'thumbnail_image' => 'nullable|image',
            'regenerate_thumbnail' => 'nullable|boolean', // Optional: Add a checkbox in form
        ]);

        $data = $request->all();

        // If regenerate thumbnail is requested and link exists
        if ($request->has('regenerate_thumbnail') && $request->filled('link')) {
            $thumbnail = $this->linkPreviewService->generateThumbnailFromUrl($request->link);
            if ($thumbnail) {
                // Delete old thumbnail if exists
                if ($healthTip->thumbnail_image) {
                    Storage::disk('public')->delete($healthTip->thumbnail_image);
                }
                $data['thumbnail_image'] = $thumbnail;
            }
        }
        // If new thumbnail is uploaded
        elseif ($request->hasFile('thumbnail_image')) {
            // Delete old thumbnail if exists
            if ($healthTip->thumbnail_image) {
                Storage::disk('public')->delete($healthTip->thumbnail_image);
            }
            $data['thumbnail_image'] = $request->file('thumbnail_image')->store('health_tips', 'public');
        }

        $healthTip->update($data);

        return redirect()->route($Loginuser->user_type.'.healthtips.index')->with('success', 'Health Tip updated successfully.');
    }

    public function destroy(HealthTip $healthTip)
    {
        $Loginuser = Auth::user();
        // Delete thumbnail if exists
        if ($healthTip->thumbnail_image) {
            Storage::disk('public')->delete($healthTip->thumbnail_image);
        }

        $healthTip->delete();

        return redirect()->route($Loginuser->user_type.'.healthtips.index')->with('success', 'Health Tip deleted successfully.');
    }

    public function fetchLinkDetails(Request $request)
    {
        $request->validate([
            'link' => 'required|url',
        ]);

        $metadata = $this->linkPreviewService->extractMetadata($request->link);

        if ($metadata) {
            return response()->json([
                'success' => true,
                'data' => $metadata
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'Could not fetch metadata'
        ], 404);
    }
}