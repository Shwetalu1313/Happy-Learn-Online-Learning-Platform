@extends('layouts.app')

@section('content')
    <div class="container py-5">
        {{--alert--}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(Session::has('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-bag-x me-3"></i> {{ Session::pull('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(Session::has('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check2-circle text-success me-3"></i> {{ Session::pull('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        {{--end alert--}}

        <div class="row justify-content-center">
            <div class="col-md-8">

                <button class="btn btn-secondary mb-3" onclick="window.location='{{route('exercise.list', $exercise->lesson_id)}}'">
                    Back
                </button>

                <div class="card">
                    <div class="card-header">{{ $exercise->title }}</div>

                    <div class="card-body">
                        <form id="exercise_answer_form" method="post" action="{{ route('exercise.submit', $exercise) }}">
                            @csrf
                            @method('POST')
                            <input type="hidden" name="exercise_id" value="{{ $exercise->id }}">
                            @foreach($exercise->questions as $question)
                                <div class="card mb-3">
                                    <div class="card-body">
                                        <h4 class="">{{$question->text}}</h4>
                                        @if($question->question_type === App\Enums\QuestionTypeEnums::BLANK)
                                            <input type="text" name="answers[{{$question->id}}]" class="form-control mb-3">
                                        @else
                                            <div class="row">
                                                @foreach($question->answers as $answer)
                                                    <div class="col-md-6">
                                                        <div class="form-check mb-2">
                                                            <input class="form-check-input" type="radio" name="answers[{{$question->id}}]" id="answer_{{$answer->id}}" value="{{$answer->id}}">
                                                            <label class="form-check-label" for="answer_{{$answer->id}}">{{$answer->text}}</label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                            <button type="submit" class="btn btn-primary">Submit Answers</button>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
