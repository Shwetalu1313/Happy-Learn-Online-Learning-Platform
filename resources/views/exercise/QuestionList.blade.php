@extends('admin.layouts.app')
@section('content')
    @php use App\Enums\QuestionTypeEnums; @endphp
    <div class="my-3">
        <button class="btn btn-secondary" onclick="window.location = '{{url('lesson/'.$exercise->lesson_id.'/review')}}' "><i class="bi bi-arrow-bar-left me-3"></i>Back</button>
    </div>
    <div class="card p-5">
        {{--alert--}}
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>
                            {{$error}}
                        </li>
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

        <form action="{{ route('exercise.update', $exercise->id) }}" class="mb-3" method="post" id="exercise_form">
            @csrf
            @method('PUT')
            <input class="form-control mb-3" type="text" name="content" id="exercise_content_input" value="{{ $exercise->content }}">
            <button type="submit" id="update_button" class="btn btn-primary d-none mb-3">{{ __('btnText.update') }}</button>
        </form>
        <script>
            const contentInput = document.getElementById('exercise_content_input');
            const updateButton = document.getElementById('update_button');
            const originalValue = contentInput.value;

            contentInput.addEventListener('input', function (){
                // If input field value changes and it's different from the original value, show the submit button
                if (contentInput.value !== originalValue) {
                    updateButton.classList.remove('d-none');
                } else {
                    // If input field value is the same as the original value, hide the submit button
                    updateButton.classList.add('d-none');
                }
            })

            document.getElementById('exercise_form').addEventListener('submit', function() {
                // Before form submission, hide the submit button
                updateButton.classList.add('d-none');
            });
        </script>

        <hr>
        {{--exercise update form--}}


        <div class="mb-3">
            <button class="btn btn-primary" onclick="window.location='{{url('question/'.$exercise->id.'/form')}}'">Create a Question</button>
        </div>
        Total Questions - {{$exercise->questions->count()}}
        @if($exercise->questions->count() === 0)
            <div class="card">
                <div class="card-body">
                    <div class="card-title text-center">No Question Found</div>
                </div>
            </div>
        @else
            @foreach($exercise->questions as $i => $question)
                <div class="card">
                    <div class="card-header d-flex flex-row-reverse">
                        <button class="btn btn-secondary mx-3" onclick="window.location='{{route('question.edit', $question->id)}}'"><i class="bi bi-pencil-square"></i></button>
                        <button class="btn btn-danger" onclick="document.getElementById('delete-question-form-{{$question->id}}').submit()"><i class="bi bi-trash"></i></button>
                        <form action="{{route('question.destroy', $question->id)}}" id="delete-question-form-{{$question->id}}" method="post" class="d-none">
                            @csrf
                            @method('DELETE')
                        </form>
                    </div>
                    <div class="card-body">
                        <h5></h5>
                        <textarea name="" id="" class="form-control disabled mb-3">{{$question->text}}</textarea>
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
                                        <button class="btn btn-outline-secondary ">
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
        @endif


    </div>
@endsection
