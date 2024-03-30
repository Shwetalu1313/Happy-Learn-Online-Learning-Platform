<?php

namespace App\Http\Controllers;

use App\Enums\QuestionTypeEnums;
use App\Models\Answer;
use App\Models\Exercise;
use App\Models\Lesson;
use App\Models\Question;
use App\Models\UserAnswer;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AnswerResult;

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

    public function answerForm(Exercise $exercise)
    {
        // Ensure exercise has questions
        if ($exercise->questions->count() === 0) {
            return abort(404, 'Exercise has no questions');
        }

        $user = Auth::user();
        $hasAnswered = UserAnswer::where('user_id', $user->id)
            ->where('exercise_id', $exercise->id)
            ->exists();

        return view('exercise.answer_form', compact('exercise', 'hasAnswered'));
    }

    public function submitAnswers(Request $request, Exercise $exercise)
    {
        $user = Auth::user(); // Get authenticated user


        $validated = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'required', // Removed integer rule (allows strings for Blank)
        ]);
        $message = 'Good Job';
        $isAnswer = UserAnswer::where('user_id', $user->id)->where('exercise_id', $exercise->id)->exists();
        if ($isAnswer) {
            $message = 'You are already answered this question';
        }

        $totalCorrect = 0;
        $totalQuestions = $exercise->questions->count();
        $userFalseAnswers = [];
        $i = 0;


        foreach ($validated['answers'] as $questionId => $userInput) {
            $question = $exercise->questions->find($questionId);
            $correctAnswerIds = $question->answers->where('is_correct', true)->pluck('id')->toArray();
            ++$i;

            if (!$question) {
                continue; // Skip if question not found
            }
            $correctAnswer = null;
            $answerText = null;

            if ($question->question_type === QuestionTypeEnums::BLANK) {
                $answerText = $userInput; // User input is the answer text for Blank

                // Get the correct answer text for the question
                $correctAnswerText = $question->answers->where('is_correct', true)->pluck('text')->first();

                // Compare the user's input with the correct answer text
                if ($answerText === $correctAnswerText) {
                    $totalCorrect++;
                } else {
                    // Add the user's false answer to the array
                    $userFalseAnswers[$i] = $userInput;
                }

            } else {
                // Attempt to convert user input to integer (might be answer ID)
                $answerId = (int) $userInput; // Type casting to integer

                if (is_int($userInput)) {
                    // Assume it's an answer ID
                    $answer_data = Answer::findOrFail($userInput);
                    $answerText = $answer_data->text;
                } else {
                    // Assume it's already answer text (for Blank questions)
                    $answerText = $userInput;
                }

                if (in_array($answerId, $correctAnswerIds)){
                    $totalCorrect++;
                } else {
                    // Add the user's false answer to the array
                    $answer_data = Answer::findOrFail($userInput);
                    $answerText = $answer_data->text;
                    $userFalseAnswers[$i] = $answerText;
                }

            }

            // Handle multiple correct answers:
            if (count($correctAnswerIds) > 1) {
                $userAnswers = [];
                foreach ($correctAnswerIds as $correctAnswerId) {
                    $userAnswers[] = [
                        'user_id' => $user->id,
                        'exercise_id' => $exercise->id,
                        'question_id' => $question->id,
                        'answer_id' => $correctAnswerId,
                        'answer_text' => $answerText, // Use the same answer text for all records
                        'is_correct' => in_array($correctAnswerId, $userInput) ? 1 : 0, // Check if user selected the correct answer
                    ];
                }
                UserAnswer::insert($userAnswers); // Create multiple records at once
            } else {
                // Single correct answer:
                $userAnswer = UserAnswer::create([
                    'user_id' => $user->id,
                    'exercise_id' => $exercise->id,
                    'question_id' => $question->id,
                    'answer_id' => $correctAnswerIds[0],
                    'answer_text' => $answerText,
                    'is_correct' => (string) $correctAnswerIds[0] === $userInput ? 1 : 0,
                ]);
            }
        }

        $percentage = round(($totalCorrect / $totalQuestions) * 100, 2);
        $Calculate_points = $this->calculatePoints($percentage);


        // Update user points (implementation depends on your user model)
        if (!$isAnswer){
            $user->points += $Calculate_points;
            $user->save();
            $Calculate_points = 'Thank You';
        }

        $request->session()->put('exercise', $exercise);
        $request->session()->put('percentage', $percentage);
        $request->session()->put('Calculate_points', $Calculate_points);
        $request->session()->put('totalCorrect', $totalCorrect);
        $request->session()->put('userAnswer', $userAnswer);
        $request->session()->put('userFalseAnswers', $userFalseAnswers);

        return redirect()->route('exercise.answer_form', [$exercise])->with('message',$message);

        //return view('exercise.answer_form',compact('exercise','percentage', 'Calculate_points','message','totalCorrect','userAnswer'));

    }




    private function calculatePoints($percentage)
    {
        if ($percentage >= 100) {
            return 10;
        } elseif ($percentage >= 80) {
            return 7;
        } elseif ($percentage >= 50) {
            return 5;
        } elseif ($percentage >= 30) {
            return 3;
        } elseif ($percentage >= 10) {
            return 2;
        } else {
            return 1;
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate(['content' => 'required|string']);

        $exercise_data = Exercise::createExercise($data['content'], $request->lesson_id);

        if ($exercise_data) {
            return redirect()->route('exercise.show',[$exercise_data->id])->with(['success' => 'You created a new exercise']);
        } else {
            return redirect()->back()->with('error', 'Failed to create exercise');
        }
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $exercise = Exercise::findOrFail($id);
        $titlePage = $exercise->title;
        return view('exercise.QuestionList', compact('exercise', 'titlePage'));
    }

    public function showToLearners(Exercise $exercise){
        $titlePage = $exercise->title;
        return view('exercise.QuestionforStudentsForms', compact('titlePage','exercise'));
    }

    public function showQuestionCreateForm(string $id)
    {
        $exercise = Exercise::findOrFail($id);
        $titlePage = $exercise->title;
        return view('exercise.createQuestion', compact('exercise', 'titlePage'));
    }

    public function showExerciseList(string $lesson_id)
    {
        $lesson = Lesson::findOrFail($lesson_id);
        $titlePage = $lesson->title;
        return view('exercise.ExerciseList', compact('lesson', 'titlePage'));
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
        try {
            $exercise = $this->exercise->updateExercise($id, $request->input('content'));
            return redirect()->back()->with('success', 'Exercise updated successfully.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to update exercise: ' . $e->getMessage());
        }
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
            return redirect()->back()->with('error', 'Failed to delete exercise: ');
        }
    }

    public function forceDelete(string $id)
    {
        try {
            $this->exercise->forceDeleteExercise($id);
            return redirect()->back()->with('success', 'Exercise was deleted permanently and numbering updated.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to delete exercise: ' );
        }
    }

    public function restore(string $id)
    {
        try {
            $this->exercise->restoreExercise($id);
            return redirect()->back()->with('success', 'Exercise restored successfully and numbering updated.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Failed to restore exercise: ' );
        }
    }
}
