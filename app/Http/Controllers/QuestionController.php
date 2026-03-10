<?php

namespace App\Http\Controllers;

use App\Enums\QuestionTypeEnums;
use App\Models\Exercise;
use App\Models\Question;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index() {}

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
    public function store(Request $request) {}

    public function storeQuestion(Request $request, Exercise $exercise): RedirectResponse
    {
        $validated = $this->validateQuestionPayload($request);

        DB::transaction(function () use ($exercise, $validated): void {
            $question = Question::query()->create([
                'exercise_id' => $exercise->id,
                'text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
            ]);

            $this->syncAnswers($question, $validated);
        });

        return redirect()
            ->route('exercise.show', [$exercise->id])
            ->with('success', 'Question created successfully.');
    }

    public function updateQuestion(Request $request, Question $question, string $exercise_id): RedirectResponse
    {
        $validated = $this->validateQuestionPayload($request);

        DB::transaction(function () use ($question, $validated): void {
            $question->update([
                'text' => $validated['question_text'],
                'question_type' => $validated['question_type'],
            ]);

            $question->answers()->delete();
            $this->syncAnswers($question, $validated);
        });

        return redirect()
            ->route('exercise.show', $exercise_id)
            ->with('success', 'Question updated successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id) {}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $question = Question::query()
            ->with(['exercise:id,title', 'answers:id,question_id,text,is_correct'])
            ->findOrFail($id);
        $titlePage = $question->exercise->title.' | Question Update';

        return view('exercise.updateQuestion', compact('titlePage', 'question'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): RedirectResponse
    {
        $question = Question::findOrFail($id);
        $question->delete();

        return redirect()->back()->with('success', 'Question deleted successfully.');
    }

    /**
     * @return array<string, mixed>
     */
    private function validateQuestionPayload(Request $request): array
    {
        /** @var array<string, mixed> $validated */
        $validated = $request->validate([
            'question_text' => ['required', 'string', 'max:4000'],
            'question_type' => ['required', Rule::in([
                QuestionTypeEnums::BLANK->value,
                QuestionTypeEnums::TRUEorFALSE->value,
                QuestionTypeEnums::MULTIPLE_CHOICE->value,
            ])],
            'blank_answer' => ['nullable', 'string', 'max:1000'],
            'answers' => ['nullable', 'array'],
            'answers.*' => ['nullable', 'string', 'max:1000'],
            'correct_answers' => ['nullable', 'array'],
            'correct_answers.*' => ['nullable', 'integer', 'min:0', 'max:20'],
            'correct_answer' => ['nullable', Rule::in([
                QuestionTypeEnums::TRUE->value,
                QuestionTypeEnums::FALSE->value,
            ])],
        ]);

        $questionType = (string) $validated['question_type'];
        if ($questionType === QuestionTypeEnums::BLANK->value && empty(trim((string) ($validated['blank_answer'] ?? '')))) {
            throw ValidationException::withMessages([
                'blank_answer' => 'Blank answer is required.',
            ]);
        }

        if ($questionType === QuestionTypeEnums::TRUEorFALSE->value && empty($validated['correct_answer'])) {
            throw ValidationException::withMessages([
                'correct_answer' => 'Please choose true or false.',
            ]);
        }

        if ($questionType === QuestionTypeEnums::MULTIPLE_CHOICE->value) {
            $answers = collect($validated['answers'] ?? [])
                ->map(fn ($answer) => trim((string) $answer))
                ->filter()
                ->values()
                ->all();

            if (count($answers) < 2) {
                throw ValidationException::withMessages([
                    'answers' => 'Multiple choice needs at least 2 options.',
                ]);
            }

            $correctIndexes = array_map('intval', $validated['correct_answers'] ?? []);
            if (empty($correctIndexes)) {
                throw ValidationException::withMessages([
                    'correct_answers' => 'Select at least one correct answer.',
                ]);
            }

            $validated['answers'] = $answers;
            $validated['correct_answers'] = $correctIndexes;
        }

        return $validated;
    }

    /**
     * @param  array<string, mixed>  $validated
     */
    private function syncAnswers(Question $question, array $validated): void
    {
        if ($question->question_type === QuestionTypeEnums::BLANK) {
            $question->answers()->create([
                'text' => trim((string) ($validated['blank_answer'] ?? '')),
                'is_correct' => true,
            ]);

            return;
        }

        if ($question->question_type === QuestionTypeEnums::TRUEorFALSE) {
            $correctAnswer = (string) ($validated['correct_answer'] ?? QuestionTypeEnums::TRUE->value);
            $question->answers()->createMany([
                ['text' => QuestionTypeEnums::TRUE->value, 'is_correct' => $correctAnswer === QuestionTypeEnums::TRUE->value],
                ['text' => QuestionTypeEnums::FALSE->value, 'is_correct' => $correctAnswer === QuestionTypeEnums::FALSE->value],
            ]);

            return;
        }

        $correctIndexes = collect($validated['correct_answers'] ?? [])->map(fn ($index) => (int) $index)->all();
        foreach ($validated['answers'] as $index => $answerText) {
            $question->answers()->create([
                'text' => (string) $answerText,
                'is_correct' => in_array((int) $index, $correctIndexes, true),
            ]);
        }
    }
}
