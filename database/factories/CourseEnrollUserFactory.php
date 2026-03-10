<?php

namespace Database\Factories;

use App\Enums\CoursePaymentTypeEnums;
use App\Enums\CourseTypeEnums;
use App\Enums\UserRoleEnums;
use App\Models\Course;
use App\Models\CourseEnrollUser;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseEnrollUserFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        $studentIDs = User::where('role', UserRoleEnums::STUDENT)->pluck('id')->toArray();

        $courseTypeFreeOrPoint = [CourseTypeEnums::BASIC->value]; // Define course types where payment type should be 'free' or 'point'
        $courseTypes = [CourseTypeEnums::BASIC->value, CourseTypeEnums::ADVANCED->value];
        $courseType = fake()->randomElement($courseTypes);
        $paymentType = in_array($courseType, $courseTypeFreeOrPoint) ? CoursePaymentTypeEnums::FREE->value : CoursePaymentTypeEnums::CARD->value;

        $cardNumber = $paymentType === CoursePaymentTypeEnums::CARD->value ? fake()->creditCardNumber : null;
        $cardLastFour = $cardNumber ? substr($cardNumber, -4) : null;
        $expiredDate = $paymentType === CoursePaymentTypeEnums::CARD->value ? fake()->creditCardExpirationDate : null;
        $cardHolderName = $paymentType === CoursePaymentTypeEnums::CARD->value ? fake()->name : null;

        $studentID = fake()->randomElement($studentIDs);

        // Retrieve courses that have lessons
        $courseIDs = Course::has('lessons')->pluck('id')->toArray();

        // If no courses have lessons, return null (skipping the creation of this enrollment)
        if (empty($courseIDs)) {
            return null;
        }

        $courseID = fake()->randomElement($courseIDs);

        // Check if the enrollment already exists for this student and course
        $existingEnrollment = CourseEnrollUser::where('user_id', $studentID)
            ->where('course_id', $courseID)
            ->exists();

        // If enrollment already exists, return null (skipping the creation of this enrollment)
        if ($existingEnrollment) {
            return null;
        }

        return [
            'user_id' => $studentID,
            'course_id' => $courseID,
            'amount' => function (array $attributes) {
                $course = Course::find($attributes['course_id']);

                return $course->fees;
            },
            'payment_type' => $paymentType,
            'card_last_four' => $cardLastFour,
            'expired_date' => $expiredDate,
            'cardHolderName' => $cardHolderName,
            'created_at' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
