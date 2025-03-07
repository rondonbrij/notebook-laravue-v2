<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoteController extends Controller
{
    public function index()
    {
        $query = Note::latest();
        
        if (request('search')) {
            $query->where('title', 'like', '%' . request('search') . '%')
                  ->orWhere('content', 'like', '%' . request('search') . '%');
        }
        
        // $notes = $query->paginate(9)->withQueryString();
        $notes = $query->paginate(perPage: 9)->onEachSide(1)->withQueryString();

        return Inertia::render('Dashboard', [
            'notes' => $notes,
            'filters' => request()->only('search')
        ]);
    }

    public function show(Note $note)
    {
        return Inertia::render('Notes/Show', [
            'note' => $note
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        Note::create([
            'title' => $request->input('title'),
            'content' => $request->input('content')
        ]);

        return redirect()->back();
    }

    public function update(Request $request, Note $note)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $note->update($request->only(['title', 'content']));

        return redirect()->back();
    }

    public function destroy(Note $note)
    {
        $note->delete();
        return redirect()->route('dashboard');    }
}