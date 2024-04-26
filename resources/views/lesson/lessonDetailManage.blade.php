@extends('admin.layouts.app')
@section('content')
    @php
        use App\Models\Exercise;
        use App\Models\Lesson;

        $exercises = $lesson->exercises;
$delete_exercises = Exercise::where('lesson_id', $lesson->id)->onlyTrashed()->get();
    @endphp
    <div class="card">
        <div class="card-body p-3">
            <div class="d-flex flex-row-reverse mb-3 align-items-baseline">
                <button class="btn btn-outline-secondary mx-3" id="toggle-form">{{ __('Toggle Form') }}</button>
                <button class="mb-3 btn btn-outline-primary" id="create_exe" data-bs-toggle="modal" data-bs-target="#exercise_form_modal">Create an Exercise</button>
            </div>
            <div class="mb-3">
                <small class="text-primary-emphasis">Total Exercises (<span class="text-danger">{{$exercises->count()}}</span>)</small>
            </div>

            {{--exercise create form--}}
            <div class="modal fade" id="exercise_form_modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h1 class="modal-title fs-5" id="exampleModalLabel">Exercise Title</h1>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{route('exercise.store')}}" method="post" id="create_exercise_form">
                                @csrf
                                @method('POST')
                                <input type="text" name="content" id="question_content" class="form-control" required placeholder="Answer these questions">
                                <input type="hidden" value="{{$lesson->id}}" name="lesson_id">
                            </form>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{__('btnText.close')}}</button>
                            <button type="submit" class="btn btn-primary" onclick="document.getElementById('create_exercise_form').submit(); ">{{__('btnText.create')}}</button>
                        </div>
                    </div>
                </div>
            </div>
            {{--exercise create form end--}}



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
            <div class="" id="lesson-body"></div>

            <form method="POST" action="{{ route('lesson.update', $lesson->id) }}" id="lesson-form" style="display: none;">
                @csrf
                @method('PUT')
                <h5 class="card-title" contenteditable="true" id="title">{{ $lesson->title }}</h5>
                <div class="quill-editor-full" id="req"></div>
                <input type="hidden" name="title" id="title_input">
                <input type="hidden" name="requirements" id="req_input">
                <button class="btn btn-primary float-end mt-3" id="btn-submit">{{ __('btnText.update') }}</button>
            </form>

        </div>
    </div>

    {{--exercises section--}}
    <div class="card table-responsive">
        <table class="datatable">
            <thead>
                <tr>
                    <th class="text-center text-primary-emphasis"><strong>NO.</strong></th>
                    <th class="text-center text-primary-emphasis"><strong>Title</strong></th>
                    <th class="text-center text-primary-emphasis"><strong>Total Questions</strong></th>
                    <th class="text-center text-primary-emphasis" data-type="date" data-format="YYYY/DD/MM"><strong>Created Date</strong></th>
                    <th class="text-center text-primary-emphasis"><strong>Created Time</strong></th>
                    <th class="text-center text-primary-emphasis"><strong>Action</strong></th>
                </tr>
            </thead>
            <tbody>
            @foreach($exercises as $i => $exercise)
                <tr>
                    <td class="text-primary-emphasis text-center">{{$i+1}}</td>
                    <td class="text-primary-emphasis text-center"><a href="{{route('exercise.show',[$exercise->id])}}">{{$exercise->title}}</a></td>
                    <td class="text-primary-emphasis text-center">{{$exercise->questions->count()}}</td>
                    <td class="text-primary-emphasis text-center">{{ \Carbon\Carbon::parse($exercise->created_at)->format('Y/m/d') }}</td>
                    <td class="text-primary-emphasis text-center">{{ \Carbon\Carbon::parse($exercise->created_at)->format('H:i:s') }}</td>
                    <td class="text-center">
                        <div class="dropdown custom-dropdown">
                            <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="dropdown-text"><i class="bi bi-activity"></i></span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                    <li class="dropdown-item d-flex justify-content-between align-items-center">
                                        <form action="{{route('exercise.destroy',[$exercise->id])}}" method="post" class="w-100">
                                            @csrf
                                            @method('DELETE')
                                            <button onclick="return confirm('Are You Sure 🤨')" class="btn btn-danger w-100"><i class="bi bi-journal-x me-3"></i> {{__('btnText.delete')}}</button>
                                        </form>
                                    </li>
                                    <li class="dropdown-item d-flex justify-content-between align-items-center">
                                        <div class="w-100">
                                            <button class="btn border-0 btn-secondary w-100" title="create a new question for this exercise">
                                                <i class="bi bi-share"></i> {{__('Create a New Question')}}
                                            </button>
                                        </div>
                                    </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>

        </table>
    </div>
    {{--end exercises section--}}

    {{--exercises section--}}
    <div class="card table-responsive">
        Deleted Exercises
        <table class="datatable">
            <thead>
            <tr>
                <th class="text-center text-primary-emphasis"><strong>NO.</strong></th>
                <th class="text-center text-primary-emphasis"><strong>Title</strong></th>
                <th class="text-center text-primary-emphasis"><strong>Total Questions</strong></th>
                <th class="text-center text-primary-emphasis" data-type="date" data-format="YYYY/DD/MM"><strong>Created Date</strong></th>
                <th class="text-center text-primary-emphasis"><strong>Created Time</strong></th>
                <th class="text-center text-primary-emphasis"><strong>Action</strong></th>
            </tr>
            </thead>
            <tbody>
            @foreach($delete_exercises as $i => $exercise)
                <tr>
                    <td class="text-primary-emphasis text-center">{{$i+1}}</td>
                    <td class="text-primary-emphasis text-center">{{$exercise->title}}</td>
                    <td class="text-primary-emphasis text-center">{{$exercise->questions->count()}}</td>
                    <td class="text-primary-emphasis text-center">{{ \Carbon\Carbon::parse($exercise->created_at)->format('Y/m/d') }}</td>
                    <td class="text-primary-emphasis text-center">{{ \Carbon\Carbon::parse($exercise->created_at)->format('H:i:s') }}</td>
                    <td class="text-center">
                        <div class="dropdown custom-dropdown">
                            <button class="btn dropdown-toggle" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="dropdown-text"><i class="bi bi-activity"></i></span>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                <li class="dropdown-item d-flex justify-content-between align-items-center">
                                    <form action="{{route('exercise.restore',[$exercise->id])}}" method="post" class="w-100">
                                        @csrf
                                        @method('POST')
                                        <button type="submit" onclick="return confirm('Are You Sure 🤨')" class="btn btn-success w-100"><i class="bi bi-recycle"></i> {{__('Restore')}}</button>
                                    </form>
                                </li>
                                <li class="dropdown-item d-flex justify-content-between align-items-center">
                                    <form action="{{ route('exercise.force_del', [$exercise->id]) }}" method="post" class="w-100" id="per_del_form">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Are You Sure 🤨')" class="btn btn-outline-danger"><i class="bi bi-trash2"></i> {{ __('Delete Permanently') }}</button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>

        </table>
    </div>
    {{--end exercises section--}}

@endsection
@section('scripts')
    <script>
        function decodeHtml(html) {
            let txt = document.createElement("textarea");
            txt.innerHTML = html;
            return txt.value;
        }

        $(document).ready(function () {
            $('#lesson-body').html(decodeHtml("{{ $lesson->body }}"));

            // Toggle form and lesson-body
            $('#toggle-form').click(function() {
                $('#lesson-body').toggle();
                $('#lesson-form').toggle();

                // Change button text based on the current state
                if ($('#lesson-form').is(':visible')) {
                    $(this).text("{{ __('Hide Form') }}");
                } else {
                    $(this).text("{{ __('Show Form') }}");
                }

                $('.ql-editor').html(decodeHtml("{{ $lesson->body }}"));

                $('#btn-submit').on('click', () => {
                    const body = $('.ql-editor').html();
                    $('#req_input').val(body);

                    const title = $('#title').text();
                    $('#title_input').val(title);
                });
            });
        });
    </script>
@endsection
