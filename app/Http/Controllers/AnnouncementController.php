<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnouncementController extends Controller
{
    public function index()
    {
        // Get unique announcements by grouping them
        $announcements = Notification::announcements()
            ->orderBy('created_at', 'desc')
            ->get()
            ->unique(function ($notification) {
                return $notification->data['announcement_id'] ?? $notification->id;
            });
            
        return view('instructor.announcements.index', compact('announcements'));
    }

    public function create()
    {
        $students = User::where('user_role', 'student')->get();
        return view('instructor.announcements.create', compact('students'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
            'recipients' => 'required|string|in:all,specific',
            'student_ids' => 'required_if:recipients,specific|array',
            'student_ids.*' => 'exists:users,id',
        ]);

        // Generate a unique ID for this announcement to link all notifications
        $announcementId = uniqid('announcement_' . time());

        // Get all students if "all" is selected
        $recipients = [];
        if ($request->recipients === 'all') {
            $recipients = User::where('user_role', 'student')->pluck('id')->toArray();
        } else {
            $recipients = $request->student_ids;
        }

        // Create a notification for each recipient
        foreach ($recipients as $userId) {
            Notification::create([
                'user_id' => $userId,
                'type' => Notification::TYPE_ANNOUNCEMENT,
                'title' => $request->title,
                'message' => $request->message,
                'is_read' => false,
                'data' => [
                    'sender_id' => auth()->id(),
                    'sender_name' => auth()->user()->name,
                    'recipients' => $request->recipients,
                    'announcement_id' => $announcementId,
                ]
            ]);
        }

        return redirect()->route('instructor.announcements.index')
            ->with('success', 'Announcement sent successfully!');
    }

    public function edit(Notification $announcement)
    {
        // Only allow editing announcements created by the current user
        if (!isset($announcement->data['sender_id']) || $announcement->data['sender_id'] !== auth()->id()) {
            return redirect()->route('instructor.announcements.index')
                ->with('error', 'You are not authorized to edit this announcement.');
        }

        return view('instructor.announcements.edit', compact('announcement'));
    }

    public function update(Request $request, Notification $announcement)
    {
        // Only allow updating announcements created by the current user
        if (!isset($announcement->data['sender_id']) || $announcement->data['sender_id'] !== auth()->id()) {
            return redirect()->route('instructor.announcements.index')
                ->with('error', 'You are not authorized to update this announcement.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        // Update all notifications with the same announcement_id
        if (isset($announcement->data['announcement_id'])) {
            $announcementId = $announcement->data['announcement_id'];
            
            Notification::where('type', Notification::TYPE_ANNOUNCEMENT)
                ->whereJsonContains('data->announcement_id', $announcementId)
                ->update([
                    'title' => $request->title,
                    'message' => $request->message,
                ]);
        }

        return redirect()->route('instructor.announcements.index')
            ->with('success', 'Announcement updated successfully!');
    }

    public function destroy(Notification $announcement)
    {
        // Only allow deleting announcements created by the current user
        if (!isset($announcement->data['sender_id']) || $announcement->data['sender_id'] !== auth()->id()) {
            return redirect()->route('instructor.announcements.index')
                ->with('error', 'You are not authorized to delete this announcement.');
        }

        // Find and delete all notifications with the same announcement_id
        if (isset($announcement->data['announcement_id'])) {
            $announcementId = $announcement->data['announcement_id'];
            
            Notification::where('type', Notification::TYPE_ANNOUNCEMENT)
                ->whereJsonContains('data->announcement_id', $announcementId)
                ->delete();
        } else {
            // If no announcement_id is found, just delete this notification
            $announcement->delete();
        }

        return redirect()->route('instructor.announcements.index')
            ->with('success', 'Announcement deleted successfully!');
    }
}