<?php

namespace App\Http\Controllers;

use App\Models\CourseEnrollUser;
use Illuminate\Http\Request;
use App\Enums\CoursePaymentTypeEnums;
use Illuminate\Support\Facades\Auth;

class CourseEnrollController extends Controller
{
    public function PtsPayment(Request $request){
        $data = $request->validate([
            'pts' => 'required|integer',
            'course_id' => 'required',
            'amount' => 'required|integer'
        ]);

        // Calculate the final points after deduction
        $finalPoint = Auth::user()->points - $data['pts'];

        // Check if the user has enough points
        if ($finalPoint < 0){
            return redirect()->back()->with('error', 'Not Enough Points');
        }

        // Create the enrollment record
        $enroll = CourseEnrollUser::create([
            'user_id' => auth()->id(),
            'course_id' => $data['course_id'],
            'amount' => $data['amount'], // Use the provided amount instead of final points
            'payment_type' => CoursePaymentTypeEnums::POINT->value,
        ]);

        if ($enroll) {
            // Update user's points after successful enrollment
            Auth::user()->update(['points' => $finalPoint]);
            return redirect()->route('course.detail', $enroll->id)->with('success', 'You enrolled in a new course.');
        } else {
            return redirect()->back()->with('error', 'Failed to enroll in the course.');
        }
    }

    public function cardPayment(Request $request){
        $data = $request->validate([
            'course_id' => 'required',
            'amount' => 'required|integer',
            'card_number' => 'required|numeric|digits:16',
            'expired_date' => 'required',
            'cvv' => 'required|numeric|digits:3',
            'cardHolderName' => 'required',
        ]);

        $enroll = CourseEnrollUser::create([
            'user_id' => auth()->id(),
            'course_id' => $data['course_id'],
            'amount' => $data['amount'],
            'payment_type' => CoursePaymentTypeEnums::CARD->value,
            'card_number' => $data['card_number'],
            'expired_date' => $data['expired_date'],
            'cvv' => $data['cvv'],
            'cardHolderName' => $data['cardHolderName'],
        ]);

        if ($enroll) {
            return redirect()->route('course.detail', $enroll->id)->with('success', 'You enrolled in a new course.');
        } else {
            return redirect()->back()->with('error', 'Failed to enroll in the course.');
        }
    }

}
