<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use Exception;
use Illuminate\Http\Request;

class ExerciseController extends Controller
{

    private Exercise $exercise;

    public function __construct(Exercise $exercise)
    {
        $this->exercise = $exercise;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate(['content' => 'required|string']);

        $exercise = Exercise::createExercise($data['content'],$request->lesson_id);

        if ($exercise){
            dd($exercise->id . ' is created');
        }
        else dd('fail');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $this->exercise->destroyExercise($id);
            return redirect()->back()->with('success', 'Exercise deleted successfully and numbering updated.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete exercise: ' . $e->getMessage());
        }
    }

    public function forceDelete(string $id)
    {
        try {
            $this->exercise->forceDeleteExercise($id);
            return redirect()->back()->with('success', 'Exercise was deleted permanently and numbering updated.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete exercise: ' . $e->getMessage());
        }
    }

    public function restore(string $id)
    {
        try {
            $this->exercise->restoreExercise($id);
            return redirect()->back()->with('success', 'Exercise restored successfully and numbering updated.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete exercise: ' . $e->getMessage());
        }
    }
}
