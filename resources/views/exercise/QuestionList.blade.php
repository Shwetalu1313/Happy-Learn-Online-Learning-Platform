@extends('admin.layouts.app')
@section('content')
    @php use App\Enums\QuestionTypeEnums; @endphp
    <div class="card p-5">
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
                        <button class="btn btn-danger"><i class="bi bi-trash"></i></button>
                    </div>
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
