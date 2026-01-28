<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
         $user = Auth::user();
        $query = Event::query();
        
        // Search functionality
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }
        
        // Filter by type
        if ($request->has('type') && $request->type != 'all') {
            $query->where('type', $request->type);
        }
        
        // Filter by status
        if ($request->has('status') && $request->status != 'all') {
            $query->where('status', $request->status);
        }
        
        $events = $query->latest()->paginate(10);
        
        return view($user->user_type.'.events.index', compact('events'));
    }

    public function create()
    {
            $user = Auth::user();
        $eventTypes = [
            'blood_donation' => 'Blood Donation',
            'health_camp' => 'Health Camp',
            'seminar' => 'Seminar',
            'workshop' => 'Workshop',
            'awareness_program' => 'Awareness Program',
            'vaccination_drive' => 'Vaccination Drive',
            'other' => 'Other'
        ];
        
        $statuses = [
            'upcoming' => 'Upcoming',
            'ongoing' => 'Ongoing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];
        
        return view($user->user_type.'.events.create', compact('eventTypes', 'statuses'));
    }

    public function store(Request $request)
    {
            $user = Auth::user();
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'venue' => 'required|string|max:255',
            'target_participants' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $data = $request->all();
        $data['slug'] = Str::slug($request->title) . '-' . Str::random(5);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('events', 'public');
            $data['image'] = $imagePath;
        }
        
        // Handle social media links
        $socialLinks = [];
        if ($request->filled('facebook_url')) $socialLinks['facebook'] = $request->facebook_url;
        if ($request->filled('twitter_url')) $socialLinks['twitter'] = $request->twitter_url;
        if ($request->filled('instagram_url')) $socialLinks['instagram'] = $request->instagram_url;
        if ($request->filled('linkedin_url')) $socialLinks['linkedin'] = $request->linkedin_url;
        if ($request->filled('website_url')) $socialLinks['website'] = $request->website_url;
        
        if (!empty($socialLinks)) {
            $data['social_media_links'] = json_encode($socialLinks);
        }
        
        Event::create($data);
        
        return redirect()->route($user->user_type.'.events.index')
                        ->with('success', 'Event created successfully.');
    }

    public function show(Event $event)
    {
            $user = Auth::user();
        return view($user->user_type.'.events.show', compact('event'));
    }

    public function edit(Event $event)
    {
            $user = Auth::user();
        $eventTypes = [
            'blood_donation' => 'Blood Donation',
            'health_camp' => 'Health Camp',
            'seminar' => 'Seminar',
            'workshop' => 'Workshop',
            'awareness_program' => 'Awareness Program',
            'vaccination_drive' => 'Vaccination Drive',
            'other' => 'Other'
        ];
        
        $statuses = [
            'upcoming' => 'Upcoming',
            'ongoing' => 'Ongoing',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled'
        ];
        
        return view($user->user_type.'.events.edit', compact('event', 'eventTypes', 'statuses'));
    }

    public function update(Request $request, Event $event)
    {
            $user = Auth::user();
        $request->validate([
            'title' => 'required|string|max:255',
            'type' => 'required|string',
            'description' => 'required|string',
            'event_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required',
            'venue' => 'required|string|max:255',
            'target_participants' => 'nullable|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);
        
        $data = $request->all();
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($event->image) {
                Storage::disk('public')->delete($event->image);
            }
            
            $imagePath = $request->file('image')->store('events', 'public');
            $data['image'] = $imagePath;
        }
        
        // Handle social media links
        $socialLinks = [];
        if ($request->filled('facebook_url')) $socialLinks['facebook'] = $request->facebook_url;
        if ($request->filled('twitter_url')) $socialLinks['twitter'] = $request->twitter_url;
        if ($request->filled('instagram_url')) $socialLinks['instagram'] = $request->instagram_url;
        if ($request->filled('linkedin_url')) $socialLinks['linkedin'] = $request->linkedin_url;
        if ($request->filled('website_url')) $socialLinks['website'] = $request->website_url;
        
        if (!empty($socialLinks)) {
            $data['social_media_links'] = json_encode($socialLinks);
        } else {
            $data['social_media_links'] = null;
        }
        
        $event->update($data);
        
        return redirect()->route($user->user_type.'.events.index')
                        ->with('success', 'Event updated successfully.');
    }

    public function destroy(Event $event)
    {
            $user = Auth::user();
        if ($event->image) {
            Storage::disk('public')->delete($event->image);
        }
        
        $event->delete();
        
        return redirect()->route($user->user_type.'.events.index')
                        ->with('success', 'Event deleted successfully.');
    }
    
    public function updateStatus(Request $request, Event $event)
    {
        $request->validate([
            'status' => 'required|in:upcoming,ongoing,completed,cancelled'
        ]);
        
        $event->update(['status' => $request->status]);
        
        return response()->json(['success' => true]);
    }
    
    public function toggleFeature(Request $request, Event $event)
    {
        $event->update(['is_featured' => !$event->is_featured]);
        
        return response()->json([
            'success' => true,
            'is_featured' => $event->is_featured
        ]);
    }
    
    public function togglePublish(Request $request, Event $event)
    {
        $event->update(['is_published' => !$event->is_published]);
        
        return response()->json([
            'success' => true,
            'is_published' => $event->is_published
        ]);
    }
}