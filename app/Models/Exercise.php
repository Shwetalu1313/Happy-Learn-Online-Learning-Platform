<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

// Include SoftDeletes trait

class Exercise extends Model
{
    use HasFactory, SoftDeletes; // Use both HasFactory and SoftDeletes traits

    protected $fillable = ['title', 'content', 'lesson_id'];

    public function Lessons(): BelongsTo
    {
        return $this->belongsTo(Lesson::class, 'lesson_id');
    }

    public static function createExercise(string $content, $lesson_id)
    {
        $original_counter = Exercise::where('lesson_id', $lesson_id)->count();
        $title = 'Exercise ' . ($original_counter + 1);
        return Exercise::create([
            'title' => $title,
            'content' => $content,
            'lesson_id' => $lesson_id,
        ]);
    }

    public function destroyExercise($id)
    {
        return DB::transaction(function () use ($id) {
            $exercise = Exercise::findOrFail($id);
            $exercise->delete();

            $this->updateRemainingExerciseTitles($exercise->lesson_id, $exercise->id);

            // Check and fix exercise titles serialization only when needed
            if ($this->needsTitleSerializationFix($exercise->lesson_id)) {
                $this->fixExerciseTitlesSerialization($exercise->lesson_id);
            }

            return true;
        });
    }

    public function forceDeleteExercise($id)
    {
        return DB::transaction(function () use ($id) {
            $exercise = Exercise::onlyTrashed()->findOrFail($id);

            // Force delete the exercise
            $exercise->forceDelete();

            // Update the titles of subsequent exercises
            $this->updateRemainingExerciseTitles($exercise->lesson_id, $exercise->id, false);

            // Check and fix exercise titles serialization if needed
            if ($this->needsTitleSerializationFix($exercise->lesson_id)) {
                $this->fixExerciseTitlesSerialization($exercise->lesson_id);
            }

            return true; // Indicate success
        });
    }

    public function restoreExercise($id)
    {
        return DB::transaction(function () use ($id) {
            $exercise = Exercise::onlyTrashed()->findOrFail($id);

            // Get the ID of the last exercise (before soft deletion) for the same lesson
            $lastExerciseId = Exercise::where('lesson_id', $exercise->lesson_id)
                ->where('id', '<', $id)
                ->orderBy('id', 'desc')
                ->value('id');

            if ($exercise->restore()) {
                // Update the title of the restored exercise
                $exercise->title = 'Exercise ' . ($lastExerciseId + 1);
                $exercise->save();

                // Update the titles of subsequent exercises
                $this->updateRemainingExerciseTitles($exercise->lesson_id, $exercise->id, true);

                // Check and fix exercise titles serialization only when needed
                if ($this->needsTitleSerializationFix($exercise->lesson_id)) {
                    $this->fixExerciseTitlesSerialization($exercise->lesson_id);
                }

                return true; // Indicate success
            } else {
                return false; // Indicate failure
            }
        });
    }


    private function updateRemainingExerciseTitles($lesson_id, $id, $isRestore = false)
    {
        $operator = $isRestore ? '+' : '-';
        Exercise::where('lesson_id', $lesson_id)
            ->where('id', '>', $id)
            ->update(['title' => DB::raw("CONCAT('Exercise ', id $operator 1)")]);
    }



    public function shiftNumberingForRemainingExercises($lessonId, $deletedId)
    {
        $remainingExercises = Exercise::where('lesson_id', $lessonId)
            ->where('id', '>', $deletedId)
            ->get();

        foreach ($remainingExercises as $remainingExercise) {
            $newTitle = preg_replace('/\d+$/', $remainingExercise->id - 1, $remainingExercise->title);
            $remainingExercise->title = $newTitle;
            $remainingExercise->save();
        }
    }

    private function needsTitleSerializationFix($lesson_id)
    {
        $count = Exercise::where('lesson_id', $lesson_id)->count();
        $maxId = Exercise::where('lesson_id', $lesson_id)->max('id');

        return $count !== $maxId;
    }


    private function fixExerciseTitlesSerialization($lesson_id)
    {
        $exercises = Exercise::where('lesson_id', $lesson_id)
            ->orderBy('id')
            ->get();

        $expectedTitle = 'Exercise ';
        $currentNumber = 1;

        foreach ($exercises as $exercise) {
            // Generate the expected title for the exercise
            $expectedTitleWithNumber = $expectedTitle . $currentNumber;

            // If the title does not match the expected title, update it
            if ($exercise->title !== $expectedTitleWithNumber) {
                $exercise->title = $expectedTitleWithNumber;
                $exercise->save();
            }

            // Increment the current number for the next exercise
            $currentNumber++;
        }
    }

}
