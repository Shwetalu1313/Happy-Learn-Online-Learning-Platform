@extends('admin.layouts.app')
@section('content')
    <div class="card">
        <div class="card-body p-3">
            <div class="d-flex flex-row-reverse mb-3">
                <button class="btn btn-secondary border-success border-2 rounded-5 float-end" id="toggle-form">{{ __('Toggle Form') }}</button>
            </div>
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
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-bag-x me-3"></i> {{session('error')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check2-circle text-success me-3"></i> {{session('success')}}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            {{--end alert--}}
            <div id="lesson-body"></div>

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
