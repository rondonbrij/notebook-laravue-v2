<?php

namespace App\Http\Controllers;

use App\Models\Note;
use Illuminate\Http\Request;
use Inertia\Inertia;

class NoteController extends Controller
{
    //This block retrieves all notes from the database and passes them to the Note.vue component
    public function index()
    {
        $notes = Note::all(); //retrieves all notes from the database
        return Inertia::render('Note', ['notes' => $notes]); //pass
    }//End of block

    //This block creates a new note in the database, validates, and returns to the main page again
    public function store(Request $request)
    {

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]); //End of block for validation

        //Note::create(attributes: $request->only(['title', 'content'])); //creates a new note in the database
        Note::create([
            'title' => $request->input('title'),
            'content' => $request->input('content')

        ]);
        return redirect()->back(); //returns to the previous page

    } //end of block

    //This block shows a single note
    public function show(Note $note)
    {
        return Inertia::render('OneNote', ['note' => $note]);
    } //block ends


    //This block updates a note in the database
    public function update(Request $request, Note $note)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]); //End of block for validation

        $note->update($request->only(['title', 'content'])); //updates a note in the database

        return redirect()->back(); //returns to the previous page

    } //block ends

    //delete a note
    public function destroy(Note $note)
    {
        $note->delete(); //deletes a note from the database
        return redirect()->back(); //returns to the previous page
    } //block ends 
}