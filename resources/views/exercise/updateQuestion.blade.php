@extends('admin.layouts.app')

@section('content')
    @php
        use App\Enums\QuestionTypeEnums;
        use App\Models\Exercise;
    @endphp
    <div class="row">
        <div class="col-md-8 offset-md-2">
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


            <form action="{{route('question.updateQuestion', [$question, $question->exercise_id])}}" method="post" id="question_update_form">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="question_text">Question Text:</label>
                    <textarea id="question_text" name="question_text" class="form-control">{{$question->text}}</textarea>
                </div>

                <div class="form-group">
                    <label for="question_type">Question Type:</label>
                    <select id="question_type" name="question_type" class="form-control" onchange="handleQuestionTypeChange()">
                        <option value="{{ QuestionTypeEnums::BLANK }}" {{$question->question_type === QuestionTypeEnums::BLANK ? 'selected' : ''}}>Blank</option>
                        <option value="{{ QuestionTypeEnums::TRUEorFALSE }}" {{$question->question_type === QuestionTypeEnums::TRUEorFALSE ? 'selected' : ''}}>True/False</option>
                        <option value="{{ QuestionTypeEnums::MULTIPLE_CHOICE }}" {{$question->question_type === QuestionTypeEnums::MULTIPLE_CHOICE ? 'selected' : ''}}>Multiple Choice</option>
                    </select>
                </div>


                <div class="form-group my-3" id="blank" style="display: {{$question->question_type === QuestionTypeEnums::BLANK ? 'block' : 'none'}};">
                    <label for="blank" class="mb-3">Correct Answer</label> <br>
                    <input type="text" id="blank" name="answers[]" class="form-control" value="{{$question->answers->first()->text}}" required>
                </div>

                <div class="form-group my-3" id="multiple_choice_options" style="display: {{$question->question_type === QuestionTypeEnums::MULTIPLE_CHOICE ? 'block' : 'none'}};">  {{-- Initially hidden --}}
                    @foreach($question->answers as $index => $answer)
                        <label for="answer_{{$index+1}}">Answer {{$index+1}}:</label>
                        <input type="text" id="answer_{{$index+1}}" name="answers[]" class="form-control" value="{{$answer->text}}" required>

                        <input type="checkbox" id="correct_answer_{{$index+1}}" name="correct_answers[]" value="{{$index}}" {{$answer->is_correct ? 'checked' : ''}}>
                        <label for="correct_answer_{{$index+1}}" class="mb-3">Correct Answer</label> <br>
                    @endforeach

                    <div id="dynamic_answer_options"></div>  {{-- Container for dynamic options --}}

                    <button type="button" class="btn btn-primary" onclick="addAnswerOption()">Add Answer Option</button>
                </div>

                <div class="form-group my-3" id="true_or_false_options" style="display: {{$question->question_type === QuestionTypeEnums::TRUEorFALSE ? 'block' : 'none'}};">
                    <label for="true_answer">Is the statement true?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="true_answer" name="correct_answer" value="{{ QuestionTypeEnums::TRUE }}" {{$question->answers->count() > 0 && $question->answers[0]->is_correct ? 'checked' : ''}} required>
                        <label class="form-check-label" for="true_answer">True</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="false_answer" name="correct_answer" value="{{ QuestionTypeEnums::FALSE }}" {{$question->answers->count() > 1 && !$question->answers[1]->is_correct ? 'checked' : ''}} required>
                        <label class="form-check-label" for="false_answer">False</label>
                    </div>
                </div>

                <div class="form-group my-3 d-flex flex-row-reverse">
                    <button class="btn btn-info mx-3" type="submit" id="submit_question_form">{{__('btnText.save')}}</button>
                    <a href="{{route('exercise.show',[$question->exercise_id])}}" class="btn btn-secondary">Cancel</a>
                </div>

                <script>
                    const QuestionSubmitBtn = document.getElementById('submit_question_form');
                    const QuestionForm = document.getElementById('question_update_form');
                    QuestionSubmitBtn.addEventListener('click', function (){
                        QuestionForm.submit();
                    });


                    function handleQuestionTypeChange() {
                        const questionType = document.getElementById('question_type').value;
                        const multipleChoiceOptions = document.getElementById('multiple_choice_options');
                        const trueOrFalseOptions = document.getElementById('true_or_false_options');
                        const blankOption = document.getElementById('blank');

                        if (questionType === '{{ QuestionTypeEnums::MULTIPLE_CHOICE }}') {
                            multipleChoiceOptions.style.display = 'block';
                            trueOrFalseOptions.style.display = 'none';
                            blankOption.style.display = 'none';
                        } else if (questionType === '{{ QuestionTypeEnums::TRUEorFALSE }}') {
                            multipleChoiceOptions.style.display = 'none';
                            trueOrFalseOptions.style.display = 'block';
                            blankOption.style.display = 'none';
                        } else {
                            multipleChoiceOptions.style.display = 'none';
                            trueOrFalseOptions.style.display = 'none';
                            blankOption.style.display = 'block';
                            // Clear dynamic answer options (if any)
                            document.getElementById('dynamic_answer_options').innerHTML = '';
                        }
                    }

                    function addAnswerOption() {
                        const dynamicAnswerOptions = document.getElementById('dynamic_answer_options');
                        const currentAnswerCount = dynamicAnswerOptions.children.length + 3;  {{-- Account for initial two options --}}

                        if (currentAnswerCount > 10) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: 'Maximum 10 answer options allowed.'
                            });
                            return;
                        }

                        const newOption = document.createElement('div');
                        newOption.classList.add('form-group');  {{-- Add class for styling --}}

                        const labelText = `Answer ${currentAnswerCount}:`;
                        const label = document.createElement('label');
                        label.setAttribute('for', `answer_${currentAnswerCount}`);
                        label.textContent = labelText;

                        const input = document.createElement('input');
                        input.type = 'text';
                        input.id = `answer_${currentAnswerCount}`;
                        input.name = 'answers[]';
                        input.classList.add('form-control');
                        input.required = true;

                        const checkbox = document.createElement('input');
                        checkbox.type = 'checkbox';
                        checkbox.id = `correct_answer_${currentAnswerCount}`;
                        checkbox.name = 'correct_answers[]';  {{-- Array to store multiple correct answers --}}
                            checkbox.value = currentAnswerCount-1;  {{-- Value to identify the answer --}}

                        const checkboxLabel = document.createElement('label');
                        checkboxLabel.setAttribute('for', `correct_answer_${currentAnswerCount}`);
                        checkboxLabel.textContent = 'Correct Answer';

                        newOption.appendChild(label);
                        newOption.appendChild(input);
                        newOption.appendChild(checkbox);
                        newOption.appendChild(checkboxLabel);

                        dynamicAnswerOptions.appendChild(newOption);
                    }
                </script>

            </form>
        </div>
    </div>
@endsection
