<?php

namespace App\Http\Controllers;

use App\Enums\CoursePaymentTypeEnums;
use App\Enums\CourseTypeEnums;
use App\Enums\UserRoleEnums;
use App\Models\Course;
use App\Models\CourseEnrollUser;
use App\Models\CurrencyExchange;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class CourseEnrollController extends Controller
{
    public function ListPage(): View
    {
        $titlePage = 'Course Enroll User Lists';

        return view('course.enroll.enroll_list', compact('titlePage'));
    }

    public function PtsPayment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'course_id' => 'required|integer|exists:courses,id',
        ]);

        $course = Course::findOrFail((int) $data['course_id']);
        if ($course->courseType === CourseTypeEnums::BASIC->value) {
            return redirect()->back()->with('error', 'This course is free. Please use free enrollment.');
        }

        $amount = (int) $course->fees;
        $requiredPoints = MoneyExchange($amount, CurrencyExchange::getPts());
        $finalPoint = Auth::user()->points - $requiredPoints;

        if ($finalPoint < 0) {
            return redirect()->back()->with('error', 'Not Enough Points');
        }

        $enroll = CourseEnrollUser::create([
            'user_id' => auth()->id(),
            'course_id' => $course->id,
            'amount' => $amount,
            'receive_amount' => 0,
            'payment_type' => CoursePaymentTypeEnums::POINT->value,
        ]);

        if ($enroll) {
            Auth::user()->update(['points' => $finalPoint]);

            return redirect()->back();
        } else {
            return redirect()->back()->with('error', 'Failed to enroll in the course.');
        }
    }

    public function cardPayment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'course_id' => 'required|integer|exists:courses,id',
            'card_number' => 'required|numeric|digits:16',
            'expired_date' => 'required',
            'cvv' => 'required|numeric|digits:3',
            'cardHolderName' => 'required',
        ]);

        $course = Course::findOrFail((int) $data['course_id']);
        if ($course->courseType === CourseTypeEnums::BASIC->value) {
            return redirect()->back()->with('error', 'This course is free. Please use free enrollment.');
        }

        $amount = (int) $course->fees;
        $lastFour = substr((string) $data['card_number'], -4);

        $enroll = CourseEnrollUser::create([
            'user_id' => auth()->id(),
            'course_id' => $course->id,
            'amount' => $amount,
            'receive_amount' => $amount,
            'payment_type' => CoursePaymentTypeEnums::CARD->value,
            'card_last_four' => $lastFour,
            'expired_date' => $data['expired_date'],
            'cardHolderName' => $data['cardHolderName'],
        ]);

        if ($enroll) {
            return redirect()->back();
        } else {
            return redirect()->back()->with('error', 'Failed to enroll in the course.');
        }
    }

    public function FreePayment(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'course_id' => 'required|integer|exists:courses,id',
        ]);

        $course = Course::findOrFail((int) $data['course_id']);
        if ($course->courseType !== CourseTypeEnums::BASIC->value) {
            return redirect()->back()->with('error', 'This is a paid course. Please complete payment.');
        }

        $enroll = CourseEnrollUser::create([
            'user_id' => auth()->id(),
            'course_id' => $course->id,
            'amount' => 0,
            'receive_amount' => 0,
            'payment_type' => CoursePaymentTypeEnums::FREE->value,
        ]);

        if ($enroll) {
            return redirect()->back();
        } else {
            return redirect()->back()->with('error', 'Failed to enroll in the course.');
        }
    }

    public function deleteEnroll(CourseEnrollUser $enrollCourse): RedirectResponse
    {
        $this->authorizeDeleteEnrollment($enrollCourse);

        try {
            if ($enrollCourse->delete()) {
                return redirect()->back()->with('success', 'Enroll Record was successfully removed.');
            } else {
                throw new \Exception('Unable to delete enroll record.');
            }
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    private function authorizeDeleteEnrollment(CourseEnrollUser $enrollCourse): void
    {
        $authUser = auth()->user();
        if (! $authUser) {
            abort(403, 'Unauthorized');
        }

        if ($authUser->role->value === UserRoleEnums::ADMIN->value) {
            return;
        }

        if ((int) $enrollCourse->user_id === (int) $authUser->id) {
            return;
        }

        if ((int) $enrollCourse->course?->createdUser_id === (int) $authUser->id) {
            return;
        }

        abort(403, 'You are not allowed to delete this enrollment.');
    }
}
