@extends('admin.layouts.app')

@section('content')
    @php use App\Enums\QuestionTypeEnums; @endphp

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <div>
            <h5 class="mb-0">Create Question</h5>
            <small class="text-muted">Exercise: {{ $exercise->title }}</small>
        </div>
        <a href="{{ route('exercise.show', $exercise) }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>Back To Questions
        </a>
    </div>

    <div class="row">
        <div class="col-md-9 col-lg-8">
            <div class="card">
                <div class="card-body pt-3">
                    <form action="{{ route('question.storeQuestion', $exercise) }}" method="post" id="question_store_form">
                        @csrf

                        <div class="mb-3">
                            <label for="question_text" class="form-label">Question Text</label>
                            <textarea id="question_text" name="question_text" class="form-control" rows="3" required>{{ old('question_text') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label for="question_type" class="form-label">Question Type</label>
                            <select id="question_type" name="question_type" class="form-select" required>
                                <option value="{{ QuestionTypeEnums::BLANK->value }}" {{ old('question_type', QuestionTypeEnums::BLANK->value) === QuestionTypeEnums::BLANK->value ? 'selected' : '' }}>Blank</option>
                                <option value="{{ QuestionTypeEnums::TRUEorFALSE->value }}" {{ old('question_type') === QuestionTypeEnums::TRUEorFALSE->value ? 'selected' : '' }}>True/False</option>
                                <option value="{{ QuestionTypeEnums::MULTIPLE_CHOICE->value }}" {{ old('question_type') === QuestionTypeEnums::MULTIPLE_CHOICE->value ? 'selected' : '' }}>Multiple Choice</option>
                            </select>
                        </div>

                        <div class="mb-3" id="blank_group">
                            <label for="blank_answer" class="form-label">Correct Answer (Blank)</label>
                            <input type="text" id="blank_answer" name="blank_answer" class="form-control" value="{{ old('blank_answer') }}">
                        </div>

                        <div class="mb-3 d-none" id="tf_group">
                            <label class="form-label">Is the statement true?</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="true_answer" name="correct_answer" value="{{ QuestionTypeEnums::TRUE->value }}" {{ old('correct_answer') === QuestionTypeEnums::TRUE->value ? 'checked' : '' }}>
                                <label class="form-check-label" for="true_answer">True</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" id="false_answer" name="correct_answer" value="{{ QuestionTypeEnums::FALSE->value }}" {{ old('correct_answer') === QuestionTypeEnums::FALSE->value ? 'checked' : '' }}>
                                <label class="form-check-label" for="false_answer">False</label>
                            </div>
                        </div>

                        <div class="mb-3 d-none" id="mc_group">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <label class="form-label mb-0">Multiple Choice Options</label>
                                <button type="button" class="btn btn-sm btn-outline-primary" id="add_option_btn">Add Option</button>
                            </div>
                            <div id="mc_options_container"></div>
                            <small class="text-muted">Select at least one correct option.</small>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('exercise.show', $exercise) }}" class="btn btn-secondary">Cancel</a>
                            <button class="btn btn-primary" type="submit">{{ __('btnText.save') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const QuestionType = {
                BLANK: @json(QuestionTypeEnums::BLANK->value),
                TF: @json(QuestionTypeEnums::TRUEorFALSE->value),
                MC: @json(QuestionTypeEnums::MULTIPLE_CHOICE->value),
            };

            const questionType = document.getElementById('question_type');
            const blankGroup = document.getElementById('blank_group');
            const tfGroup = document.getElementById('tf_group');
            const mcGroup = document.getElementById('mc_group');
            const blankAnswer = document.getElementById('blank_answer');
            const trueRadio = document.getElementById('true_answer');
            const falseRadio = document.getElementById('false_answer');
            const mcOptionsContainer = document.getElementById('mc_options_container');
            const addOptionBtn = document.getElementById('add_option_btn');

            const oldAnswers = @json(array_values(old('answers', [''])));
            const oldCorrectIndexes = @json(array_map('intval', old('correct_answers', [])));

            function renderMcOptions(initialValues, correctIndexes) {
                mcOptionsContainer.innerHTML = '';
                const values = initialValues.length >= 2 ? initialValues : ['', ''];
                values.forEach((value, index) => appendOption(value, correctIndexes.includes(index)));
            }

            function appendOption(value = '', checked = false) {
                const index = mcOptionsContainer.querySelectorAll('[data-mc-index]').length;
                const wrapper = document.createElement('div');
                wrapper.className = 'border rounded p-2 mb-2';
                wrapper.dataset.mcIndex = index;

                wrapper.innerHTML = `
                    <div class="row g-2 align-items-center">
                        <div class="col-md-8">
                            <input type="text" class="form-control" name="answers[]" value="${value.replace(/"/g, '&quot;')}">
                        </div>
                        <div class="col-md-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="correct_answers[]" value="${index}" ${checked ? 'checked' : ''}>
                                <label class="form-check-label">Correct</label>
                            </div>
                        </div>
                        <div class="col-md-1 text-end">
                            <button type="button" class="btn btn-sm btn-outline-danger remove-option">x</button>
                        </div>
                    </div>
                `;

                mcOptionsContainer.appendChild(wrapper);
            }

            function resequenceOptions() {
                const wrappers = mcOptionsContainer.querySelectorAll('[data-mc-index]');
                wrappers.forEach((wrapper, index) => {
                    wrapper.dataset.mcIndex = index;
                    const checkbox = wrapper.querySelector('input[type="checkbox"]');
                    if (checkbox) {
                        checkbox.value = index;
                    }
                });
            }

            function setRequiredState() {
                const selectedType = questionType.value;
                blankGroup.classList.toggle('d-none', selectedType !== QuestionType.BLANK);
                tfGroup.classList.toggle('d-none', selectedType !== QuestionType.TF);
                mcGroup.classList.toggle('d-none', selectedType !== QuestionType.MC);

                blankAnswer.required = selectedType === QuestionType.BLANK;
                trueRadio.required = selectedType === QuestionType.TF;
                falseRadio.required = selectedType === QuestionType.TF;

                mcOptionsContainer.querySelectorAll('input[name="answers[]"]').forEach((input) => {
                    input.required = selectedType === QuestionType.MC;
                });
            }

            addOptionBtn.addEventListener('click', function () {
                const currentCount = mcOptionsContainer.querySelectorAll('[data-mc-index]').length;
                if (currentCount >= 10) {
                    Swal.fire({ icon: 'error', title: 'Limit reached', text: 'Maximum 10 options allowed.' });
                    return;
                }
                appendOption();
                setRequiredState();
            });

            mcOptionsContainer.addEventListener('click', function (event) {
                if (!event.target.classList.contains('remove-option')) {
                    return;
                }

                const wrappers = mcOptionsContainer.querySelectorAll('[data-mc-index]');
                if (wrappers.length <= 2) {
                    Swal.fire({ icon: 'warning', title: 'Minimum options', text: 'At least 2 options are required.' });
                    return;
                }

                event.target.closest('[data-mc-index]')?.remove();
                resequenceOptions();
                setRequiredState();
            });

            questionType.addEventListener('change', setRequiredState);

            renderMcOptions(oldAnswers, oldCorrectIndexes);
            setRequiredState();
        });
    </script>
@endsection
