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

            <form action="{{route('question.storeQuestion', $exercise)}}" method="post" id="question_store_form">
                @csrf
                @method('POST')

                <div class="form-group">
                    <label for="question_text">Question Text:</label>
                    <textarea id="question_text" name="question_text" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label for="question_type">Question Type:</label>
                    <select id="question_type" name="question_type" class="form-control" onchange="handleQuestionTypeChange()">
                        <option value="{{ QuestionTypeEnums::BLANK }}">Blank</option>
                        <option value="{{ QuestionTypeEnums::TRUEorFALSE }}">True/False</option>
                        <option value="{{ QuestionTypeEnums::MULTIPLE_CHOICE }}">Multiple Choice</option>
                    </select>
                </div>


                <div class="form-group my-3" id="blank" style="display: block;">  {{-- Initially hidden --}}
                    <label for="blank">Correct Answer</label>
                    <input type="text" id="blank" name="answers[]" class="form-control" required>
                </div>

                <div class="form-group my-3" id="multiple_choice_options" style="display: none;">  {{-- Initially hidden --}}
                    <label for="answer_1">Answer 1:</label>
                    <input type="text" id="answer_1" name="answers[]" class="form-control" required>

                    <input type="checkbox" id="correct_answer_1" name="correct_answers[]" value="0">  {{-- Correctly associate with answer 1 --}}
                    <label for="correct_answer_1">Correct Answer</label><br>

                    <label for="answer_2">Answer 2:</label>
                    <input type="text" id="answer_2" name="answers[]" class="form-control" required>

                    <input type="checkbox" id="correct_answer_2" name="correct_answers[]" value="1">  {{-- Correctly associate with answer 2 --}}
                    <label for="correct_answer_2">Correct Answer</label>

                    <div id="dynamic_answer_options"></div>  {{-- Container for dynamic options --}}

                    <button type="button" class="btn btn-primary" onclick="addAnswerOption()">Add Answer Option</button>
                </div>

                <div class="form-group my-3" id="true_or_false_options" style="display: none;">
                    <label for="true_answer">Is the statement true?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="true_answer" name="correct_answer" value="{{ QuestionTypeEnums::TRUE }}" required>
                        <label class="form-check-label" for="true_answer">True</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="false_answer" name="correct_answer" value="{{ QuestionTypeEnums::FALSE }}" required>
                        <label class="form-check-label" for="false_answer">False</label>
                    </div>
                </div>


                <div class="form-group my-3 d-flex flex-row-reverse">
                    <button class="btn btn-info mx-3" type="submit" id="submit_question_form">{{__('btnText.save')}}</button>
                    <input type="reset" value="Reset" class="btn btn-secondary">
                </div>

                <script>
                    const QuestionSubmitBtn = document.getElementById('submit_question_form');
                    const QuestionForm = document.getElementById('question_store_form');
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








































{{--
@extends('admin.layouts.app')

@section('content')
    @php
        use App\Enums\QuestionTypeEnums;
        use App\Models\Exercise;
    @endphp
    <div class="row">
        <div class="col-md-8 offset-md-2">
            --}}
{{--alert--}}{{--

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
            --}}
{{--end alert--}}{{--



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
            --}}
{{--exercise update form--}}{{--


            <form action="{{route('question.storeQuestion', $exercise)}}" method="post" id="question_store_form">
                @csrf
                @method('POST')

                <div class="form-group">
                    <label for="question_text">Question Text:</label>
                    <textarea id="question_text" name="question_text" class="form-control"></textarea>
                </div>

                <div class="form-group">
                    <label for="question_type">Question Type:</label>
                    <select id="question_type" name="question_type" class="form-control" onchange="handleQuestionTypeChange()">
                        <option value="{{ QuestionTypeEnums::BLANK }}">Blank</option>
                        <option value="{{ QuestionTypeEnums::TRUEorFALSE }}">True/False</option>
                        <option value="{{ QuestionTypeEnums::MULTIPLE_CHOICE }}">Multiple Choice</option>
                    </select>
                </div>


                <div class="form-group my-3" id="blank" style="display: block;">  --}}
{{-- Initially hidden --}}{{--

                        <label for="blank">Correct Answer</label>
                        <input type="text" id="blank" name="answers[]" class="form-control" required>
                </div>

                <div class="form-group my-3" id="multiple_choice_options" style="display: none;">  --}}
{{-- Initially hidden --}}{{--

                    <label for="answer_1">Answer 1:</label>
                    <input type="text" id="answer_1" name="answers[]" class="form-control" required>

                    <input type="checkbox" id="correct_answer_1" name="correct_answers[]" value="1">  --}}
{{-- Correctly associate with answer 1 --}}{{--

                    <label for="correct_answer_1">Correct Answer</label>

                    <label for="answer_2">Answer 2:</label>
                    <input type="text" id="answer_2" name="answers[]" class="form-control" required>

                    <input type="checkbox" id="correct_answer_2" name="correct_answers[]" value="2">  --}}
{{-- Correctly associate with answer 2 --}}{{--

                    <label for="correct_answer_2">Correct Answer</label>

                    <div id="dynamic_answer_options"></div>  --}}
{{-- Container for dynamic options --}}{{--


                    <button type="button" class="btn btn-primary" onclick="addAnswerOption()">Add Answer Option</button>
                </div>

                <div class="form-group my-3" id="true_or_false_options" style="display: none;">
                    <label for="true_answer">Is the statement true?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="true_answer" name="correct_answer" value="{{ QuestionTypeEnums::TRUE }}" required>
                        <label class="form-check-label" for="true_answer">True</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="false_answer" name="correct_answer" value="{{ QuestionTypeEnums::FALSE }}" required>
                        <label class="form-check-label" for="false_answer">False</label>
                    </div>
                </div>


                <div class="form-group my-3 d-flex flex-row-reverse">
                    <button class="btn btn-info mx-3" type="submit" id="submit_question_form">{{__('btnText.save')}}</button>
                    <input type="reset" value="Reset" class="btn btn-secondary">
                </div>

                <script>
                    const QuestionSubmitBtn = document.getElementById('submit_question_form');
                    const QuestionForm = document.getElementById('question_store_form');
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
                        const currentAnswerCount = dynamicAnswerOptions.children.length + 2;  --}}
{{-- Account for initial two options --}}{{--


                        if (currentAnswerCount > 10) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops!',
                                text: 'Maximum 10 answer options allowed.'
                            });
                            return;
                        }


                        const newOption = document.createElement('div');
                        newOption.classList.add('form-group');  --}}
{{-- Add class for styling --}}{{--


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
                        checkbox.name = 'correct_answers[]';  --}}
{{-- Array to store multiple correct answers --}}{{--

                            checkbox.value = currentAnswerCount;  --}}
{{-- Value to identify the answer --}}{{--


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
--}}
