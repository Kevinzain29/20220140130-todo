<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Todo;

class TodoController extends Controller
{
    public function index()
    {
        $todos = Todo::where('user_id', Auth::id())
        // ->orderBy('is_done', 'asc')
        ->orderBy('created_at', 'desc')
        ->get();
        // dd($todos);
        $todosCompleted = Todo::where('user_id', auth()->user()->id)
            ->where('is_done', true)
            ->count();
        return view('todo.index', compact('todos', 'todosCompleted'));
    }

    public function create()
    {
        return view('todo.create');
    }
    public function store(Request $request)
     {
         $request->validate([
             'title' => 'required|string|max:255',
         ]);
 
         $todo = todo::create([
             'title' => ucfirst($request->title),
             'user_id' => Auth::id(),
         ]);
         return redirect()->route('todo.index')->with('success', 'todo created successfully.');
     }
     public function complete(Todo $todo)
     {
         if ($todo->user_id != Auth::user()->id) {
             abort(403);
         }
 
         $todo->is_done = true;
         $todo->save();
 
         return redirect()->route('todo.index')->with('success', 'Todo completed successfully!');
     }
 
     public function uncomplete(Todo $todo)
     {
         if ($todo->user_id != Auth::user()->id) {
             abort(403);
         }
 
         $todo->is_done = false;
         $todo->save();
 
         return redirect()->route('todo.index')->with('success', 'Todo uncompleted successfully!');
     }
    public function edit(Todo $todo) {
        if (auth()->user()->id == $todo->user_id){
            return view('todo.edit', compact('todo'));
        }else{
            return redirect()->route('todo.index')->with('danger', 'You are not authorized to edit this todo!');
        }
    }

    public function update(Request $request, Todo $todo){
        $request->validate([
            'title' => 'required|max:255',
        ]);
        $todo->update([
            'title' => ucfirst($request->title),
        ]);
        return redirect()->route('todo.index')->with('success', 'Todo updated successfully!');
    }
    public function destroy(Todo $todo){
        if (auth()  ->user()->id == $todo->user_id){
            $todo->delete();
            return redirect()->route('todo.index')->with('success', 'Todo deleted successfully!');
        }else{
            return redirect()->route('todo.index')->with('danger', 'You are not authorized to delete this todo!');
        }
    }

    public function destroyCompleted(){
        $todosCompleted = Todo::where('user_id', auth()->user()->id)
            ->where('is_done', true)
            ->get();
        foreach ($todosCompleted as $todo){
            $todo->delete();
        }
        return redirect()->route('todo.index')->with('success', 'All completed todos deleted successfully!');
    }
  
}
