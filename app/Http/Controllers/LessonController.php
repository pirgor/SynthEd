<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Lesson;
use App\Models\LessonUpload;
use Smalot\PdfParser\Parser;
use Illuminate\Support\Facades\Log;
use App\Models\ProgressTracking;

class LessonController extends Controller
{
    public function markRead(Lesson $lesson)
    {
        ProgressTracking::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'lesson_id' => $lesson->id,
                'type' => 'reading',
            ],
            [
                'completed' => true,
                'completed_at' => now(),
            ]
        );

        return response()->json([
            'status' => 'success',
            'message' => 'Lesson marked as read!',
        ]);
    }
    public function index()
    {
        $lessons = Lesson::with('uploads', 'quizzes')->orderBy('id')->get();
        return view('instructor.lessons.index', compact('lessons'));
    }

    public function create()
    {
        return view('instructor.lessons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'       => 'required|string|max:255',
            'description' => 'nullable|string',
            'file'        => 'nullable|file|mimes:pdf,docx,pptx,txt,mp4|max:20480', // 20MB
        ]);

        // Create Lesson
        $lesson = Lesson::create([
            'title'       => $request->title,
            'description' => $request->description,
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('lesson_uploads', 'public');
            $text = null;

            if ($file->getClientOriginalExtension() === 'pdf') {
                try {
                    $parser = new Parser();
                    $pdf    = $parser->parseFile($file->getRealPath());
                    $text   = $pdf->getText();
                } catch (\Exception $e) {
                    Log::error("PDF parsing failed: " . $e->getMessage());
                }
            }

            LessonUpload::create([
                'lesson_id'     => $lesson->id,
                'file_path'     => $path,
                'file_name'     => $file->getClientOriginalName(),
                'extracted_text' => $text, // new column
            ]);
        }

        // Flash lesson ID for Add Quiz button
        session()->flash('new_lesson_id', $lesson->id);

        return redirect()->route('instructor.lessons.create')
            ->with('success', 'Lesson created successfully! You can now add a quiz.');
    }

    public function viewUpload(LessonUpload $upload)
    {
        $lesson = $upload->lesson;
        return view('instructor.lessons.view', compact('upload', 'lesson'));
    }
}
