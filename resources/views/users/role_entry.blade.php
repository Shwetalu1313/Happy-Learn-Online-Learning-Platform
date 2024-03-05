@extends('admin.layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h5 class="card-title">Bulk Insert User Roles</h5>
                        <button class="btn btn-primary addRole" type="button">Add Role</button>
                    </div>

                    <div class="card-body">
                        <form id="bulkInsertForm" method="POST" action="{{ route('user.role.bulkInsert') }}">
                            @csrf

                            <div class="form-group" id="rolesContainer">
                                <label for="roles">Roles</label>
                                <div class="input-group mb-3">
                                    <input type="text" class="form-control" name="roles[]" required>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary deleteRole" type="button" disabled>Delete</button>
                                    </div>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        $(document).ready(function () {
            // Add new input field
            $(".addRole").click(function () {
                var html = '<div class="input-group mb-3">' +
                    '<input type="text" class="form-control" name="roles[]" required>' +
                    '<div class="input-group-append">' +
                    '<button class="btn btn-outline-secondary deleteRole" type="button">Delete</button>' +
                    '</div>' +
                    '</div>';
                $("#rolesContainer").append(html);

                // Enable delete button for newly added input
                $("#rolesContainer .input-group:last-child .deleteRole").prop('disabled', false);
            });

            // Remove input field (except the last one)
            $("body").on("click", ".deleteRole", function () {
                var inputGroup = $(this).closest('.input-group');
                var inputField = inputGroup.find('input');

                if ($(this).text() === 'Delete' && $("#rolesContainer .input-group").length > 1) {
                    inputGroup.remove();

                    // Update delete button states after removal
                    $("#rolesContainer .input-group:last-child .deleteRole").prop('disabled', false);
                    $("#rolesContainer .input-group:first-child .deleteRole").prop('disabled', true);
                }
            });

            $('#bulkInsertForm').submit(function (event) {
                event.preventDefault();

                // Perform validation or other actions as needed

                $.ajax({
                    url: $(this).attr('action'),
                    method: 'POST',
                    data: $(this).serialize(),
                    dataType: 'json',
                    success: function (response) {
                        // Check if response has a status field
                        if (response.hasOwnProperty('status')) {
                            // Check response status for success or error
                            if (response.status === 200) {
                                // Success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: response.message
                                });
                            } else {
                                // Error message
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error!',
                                    text: response.message
                                });
                            }
                        } else {
                            // Assume success if no status field is provided
                            Swal.fire({
                                icon: 'success',
                                title: 'Success!',
                                text: response.message
                            });
                        }
                    },
                    error: function (jqXHR, textStatus, errorThrown) {
                        // Handle errors gracefully
                        Swal.fire({
                            icon: 'error',
                            title: 'Error!',
                            text: 'An unexpected error occurred.'
                        });
                    }
                });
            });

        });
    </script>

@endsection
