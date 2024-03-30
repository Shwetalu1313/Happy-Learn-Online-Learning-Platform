<?php

namespace App\Http\Controllers;

use App\Enums\QuestionTypeEnums;
use App\Models\Answer;
use App\Models\Exercise;
use App\Models\Question;
use Illuminate\Http\Request;
use Mockery\Exception;

class QuestionController extends Controller
{
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
        return view('exercise.createQuestion');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    public function storeQuestion(Request $request, Exercise $exercise)
    {

        $validatedData = $request->validate([
            'question_text' => 'required',
            'question_type' => 'required',
            'answers.*' => 'nullable|string', // Allow null for non-selected answers
            'correct_answers.*' => 'nullable', // Allow null for non-selected correct answers
            'correct_answer' => 'nullable'
        ]);
        $question = Question::create([
            'exercise_id' => $exercise->id,
            'text' => $validatedData['question_text'],
            'question_type' => $validatedData['question_type'],
        ]);

        // Handle answer data based on question type:
        if ($question->question_type === QuestionTypeEnums::MULTIPLE_CHOICE) {
            $answers = array_slice($validatedData['answers'], 1);
            $correctAnswers = $validatedData['correct_answers'];

            foreach ($answers as $key => $answerText) {
                // Check if the current answer index is in the array of correct answers indices
                $isCorrect = in_array($key, $correctAnswers);
                $question->answers()->create([
                    'text' => $answerText,
                    'is_correct' => (bool)$isCorrect, // Convert the result to boolean
                ]);
            }
        }

        elseif ($question->question_type === QuestionTypeEnums::BLANK)
        {
            // Save blank answer
            $answer = new Answer;
            $answer->question_id = $question->id;
            $answer->text = $validatedData['answers'][0] ?? ''; // Use first answer if provided, otherwise empty string
            $answer->save();
        }
        elseif ($question->question_type === QuestionTypeEnums::TRUEorFALSE)
        {
            // Create both True and False answer options for True/False questions
            $question->answers()->createMany([
                ['question_id' => $question->id, 'text' => $validatedData['correct_answer'], 'is_correct' => true],
                ['question_id' => $question->id, 'text' => $validatedData['correct_answer'] === QuestionTypeEnums::TRUE->value ? QuestionTypeEnums::FALSE->value : QuestionTypeEnums::TRUE->value, 'is_correct' => false],
            ]);
        }

        // Handle errors (if any) during question/answer creation
        if ($question->errors) {
            return back()->withErrors($question->errors)->withInput();  // Redirect back with errors and form data
        }


        return redirect()->route('exercise.show',[$exercise->id])->with('success', 'Question created successfully!');
    }

    private function saveMultipleChoiceAnswers(Question $question, array $answers, array $correctAnswers)
    {
        foreach ($answers as $index => $answerText) {
            $answer = new Answer;
            $answer->question_id = $question->id;
            $answer->text = $answerText;
            $answer->is_correct = in_array($index + 1, $correctAnswers);  // Check if answer is marked correct (index + 1 for array offset)
            $answer->save();

            // Consider adding error handling here:
            // if ($answer->errors->any()) {
            //     // Handle individual answer creation errors
            // }
        }
    }

    public function updateQuestion(Request $request, Question $question, $exercise_id)
    {
        $validatedData = $request->validate([
            'question_text' => 'required',
            'question_type' => 'required',
            'answers.*' => 'nullable|string', // Allow null for non-selected answers
            'correct_answers.*' => 'nullable', // Allow null for non-selected correct answers
            'correct_answer' => 'nullable'
        ]);

        $question->update([
            'text' => $validatedData['question_text'],
            'question_type' => $validatedData['question_type'],
        ]);

        // Handle answer data based on question type:
        if ($question->question_type === QuestionTypeEnums::MULTIPLE_CHOICE) {
            $answers = array_slice($validatedData['answers'], 1);
            $correctAnswers = $validatedData['correct_answers'];

            $question->answers()->delete(); // Delete existing answers before updating

            foreach ($answers as $key => $answerText) {
                // Check if the current answer index is in the array of correct answers indices
                $isCorrect = in_array($key, $correctAnswers);
                $question->answers()->create([
                    'text' => $answerText,
                    'is_correct' => (bool)$isCorrect, // Convert the result to boolean
                ]);
            }
        } elseif ($question->question_type === QuestionTypeEnums::BLANK) {
            $answer = $question->answers->first(); // Assuming there's only one answer for blank type
            $answer->update([
                'text' => $validatedData['answers'][0] ?? '' // Use first answer if provided, otherwise empty string
            ]);
        } elseif ($question->question_type === QuestionTypeEnums::TRUEorFALSE) {
            // Update both True and False answer options for True/False questions
            $trueAnswer = $question->answers->where('text', QuestionTypeEnums::TRUE->value)->first();
            $falseAnswer = $question->answers->where('text', QuestionTypeEnums::FALSE->value)->first();
            if ($validatedData['correct_answer'] === QuestionTypeEnums::TRUE->value) {
                $trueAnswer->update(['is_correct' => true]);
                $falseAnswer->update(['is_correct' => false]);
            } else {
                $trueAnswer->update(['is_correct' => false]);
                $falseAnswer->update(['is_correct' => true]);
            }
        }

        // Handle errors (if any) during question/answer update
        if ($question->errors) {
            return back()->withErrors($question->errors)->withInput();  // Redirect back with errors and form data
        }

        return redirect()->route('exercise.show', $exercise_id)->with('success', 'Question updated successfully!');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $question = Question::findOrFail($id);
        $titlePage = $question->exercise->title.'| Question Update';
        return view('exercise.updateQuestion', compact('titlePage', 'question'));
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
        //
    }
}
