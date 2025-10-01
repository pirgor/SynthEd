<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Notification::announcements()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->get();
            
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

        $announcement->update([
            'title' => $request->title,
            'message' => $request->message,
        ]);

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

        $announcement->delete();

        return redirect()->route('instructor.announcements.index')
            ->with('success', 'Announcement deleted successfully!');
    }
}