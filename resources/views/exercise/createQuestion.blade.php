@extends('admin.layouts.app')

@section('content')
    @php use App\Enums\QuestionTypeEnums; @endphp
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <form action="" method="post">
                @csrf
                @method('POST')

                <div class="form-group">
                    <label for="question_text">Question Text:</label>
                    <textarea id="question_text" name="question_text" class="form-control" required></textarea>
                </div>

                <div class="form-group">
                    <label for="question_type">Question Type:</label>
                    <select id="question_type" name="question_type" class="form-control" onchange="handleQuestionTypeChange()">
                        <option value="{{ QuestionTypeEnums::BLANK }}">Blank</option>
                        <option value="{{ QuestionTypeEnums::TRUEorFALSE }}">True/False</option>
                        <option value="{{ QuestionTypeEnums::MULTIPLE_CHOICE }}">Multiple Choice</option>
                    </select>
                </div>

                <div class="form-group" id="multiple_choice_options" style="display: none;">  {{-- Initially hidden --}}
                    <label for="answer_1">Answer 1:</label>
                    <input type="text" id="answer_1" name="answers[]" class="form-control" required>

                    <input type="checkbox" id="correct_answer_1" name="correct_answers[]" value="1">  {{-- Correctly associate with answer 1 --}}
                    <label for="correct_answer_1">Correct Answer</label>

                    <label for="answer_2">Answer 2:</label>
                    <input type="text" id="answer_2" name="answers[]" class="form-control" required>

                    <input type="checkbox" id="correct_answer_2" name="correct_answers[]" value="2">  {{-- Correctly associate with answer 2 --}}
                    <label for="correct_answer_2">Correct Answer</label>

                    <div id="dynamic_answer_options"></div>  {{-- Container for dynamic options --}}

                    <button type="button" class="btn btn-primary" onclick="addAnswerOption()">Add Answer Option</button>
                </div>

                <div class="form-group" id="true_or_false_options" style="display: none;">  {{-- Initially hidden --}}
                    <label for="true_answer">Is the statement true?</label>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="true_answer" name="correct_answer" value="true" required>
                        <label class="form-check-label" for="true_answer">True</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" id="false_answer" name="correct_answer" value="false" required>
                        <label class="form-check-label" for="false_answer">False</label>
                    </div>
                </div>

                <script>
                    function handleQuestionTypeChange() {
                        const questionType = document.getElementById('question_type').value;
                        const multipleChoiceOptions = document.getElementById('multiple_choice_options');
                        const trueOrFalseOptions = document.getElementById('true_or_false_options');

                        if (questionType === '{{ QuestionTypeEnums::MULTIPLE_CHOICE }}') {
                            multipleChoiceOptions.style.display = 'block';
                            trueOrFalseOptions.style.display = 'none';
                        } else if (questionType === '{{ QuestionTypeEnums::TRUEorFALSE }}') {
                            multipleChoiceOptions.style.display = 'none';
                            trueOrFalseOptions.style.display = 'block';
                        } else {
                            multipleChoiceOptions.style.display = 'none';
                            trueOrFalseOptions.style.display = 'none';
                            // Clear dynamic answer options (if any)
                            document.getElementById('dynamic_answer_options').innerHTML = '';
                        }
                    }

                    function addAnswerOption() {
                        const dynamicAnswerOptions = document.getElementById('dynamic_answer_options');
                        const currentAnswerCount = dynamicAnswerOptions.children.length + 2;  {{-- Account for initial two options --}}

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
                            checkbox.value = currentAnswerCount;  {{-- Value to identify the answer --}}

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
