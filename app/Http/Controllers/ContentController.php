<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ContentController extends Controller
{
    public function index()
    {
        $uploads = \App\Models\QuizUpload::with('quiz')->latest()->get();
        return view('instructor.content.index', compact('uploads'));
    }
    
    public function create()
    {
        return view('instructor.content.create');
    }
    
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'file' => 'required|file|mimes:pdf,docx,txt|max:10240',
        ]);
        
        $file = $request->file('file');
        $path = $file->store('content');
        
        \App\Models\QuizUpload::create([
            'quiz_id' => null,
            'filename' => $file->getClientOriginalName(),
            'filepath' => $path,
            'title' => $request->title,
        ]);
        
        return redirect()->route('instructor.content.index')
            ->with('success', 'Content uploaded successfully!');
    }
    
    public function download($id)
    {
        $upload = \App\Models\QuizUpload::findOrFail($id);
        return Storage::download($upload->filepath, $upload->filename);
    }
}