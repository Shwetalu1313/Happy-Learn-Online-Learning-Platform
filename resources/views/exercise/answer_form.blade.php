@extends('layouts.app')
@section('content')
    @php
        use App\Models\UserAnswer;
        use App\Enums\QuestionTypeEnums;

        $titlePage = $exercise->title . ' Result';

        // Extract data from the session
        $percentage = session('percentage');
        $totalCorrect = session('totalCorrect');
        $Calculate_points = session('Calculate_points');
        $userAnswers = session('userAnswer');
        $userFalseAnswers = session('userFalseAnswers');

    @endphp
    <div class="container mt-5">
        <div class="card shadow mb-3">
            <div class="card-header text-center">
                <h1>{{$exercise->title}} Result</h1>
            </div>
            <div class="card-body">
                @if (session('message'))
                    <div class="alert alert-danger" role="alert">
                        {{ session('message') }}
                    </div>
                @endif

                <p><b>Exercise:</b> {{ $exercise->title }}</p>
                <p><b>Total Questions:</b> {{ $exercise->questions->count() }}</p>
                <p><b>Your Score:</b> {{ $percentage }}% ({{ $totalCorrect }} correct)</p>
                <p><b>Points Award:</b> {{ $Calculate_points }}</p>
                <hr>
                @foreach ($userFalseAnswers as $i => $userFalseAnswer)
                    <div class="mb-3">
                        You answered <button class="btn btn-danger bg-opacity-50">{{' '. $userFalseAnswer.' '}}</button> in question Number {{' '.$i. '.'}}
                    </div>
                @endforeach
{{--                TODO::show question type of each section instead of each answer--}}
            </div>
        </div>

        Official Correct Answers
        @foreach($exercise->questions as $i => $question)
            <div class="card mb-3">
                <div class="card-body">
                    <h5>{{$question->text}}</h5>
                    @if($question->question_type === QuestionTypeEnums::BLANK)
                        @foreach($question->answers as $j => $answer)
                            <div>
                                <i class="bi bi-check-circle-fill text-success"></i> {{$answer->text}}
                            </div>
                        @endforeach
                    @elseif($question->question_type === QuestionTypeEnums::TRUEorFALSE || $question->question_type === QuestionTypeEnums::MULTIPLE_CHOICE)
                        <div class="d-flex flex-row">
                            @foreach($question->answers as $j => $answer)
                                <div class="me-2"> <!-- Add margin to separate buttons -->
                                    <button class="btn btn-outline-light ">
                                        @if($answer->is_correct)
                                            <i class="bi bi-check-circle-fill text-success"></i>
                                        @endif
                                        {{$answer->text}}
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="text-end">
                        <small class="text-secondary">{{$question->question_type}}</small>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
