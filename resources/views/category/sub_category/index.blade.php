@extends('admin.layouts.app')

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="text-right mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="flexSwitchCheckCheckedDisabled">
                        <label class="form-check-label" for="flexSwitchCheckCheckedDisabled" id="viewLabel">Grid View</label>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6 offset-md-3">
                        <label for="filterSelect" class="form-label">Filter by Specific Options:</label>
                        <select class="form-select" id="filterSelect">
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $loop->iteration }}. {{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Search bar -->
        <div class="row mb-3">
            <div class="col-md-6 offset-md-3">
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Search..." id="searchInput">
                    <button class="btn btn-primary" id="searchButton">{{__('btnText.sch')}}</button>
                </div>
            </div>
        </div>

        <!-- Category views -->
        <div class="row category-card-view" id="card_view">
            @foreach ($sub_categories as $i => $sub_category)
                <div class="col-md-3 category-item" data-bs-toggle="modal" data-bs-target="#categoryModal{{$i}}" data-option="{{$sub_category->category_id}}">

                <div class="card category-card position-relative">
                        <img src="{{asset('storage/'.$sub_category->img_path)}}" class="card-img-top" alt="{{ $sub_category->name . 'picture' }}">
                        <div class="hover-overlay position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center text-white">
                            <div class="text-center">
                                <h5 class="card-title mb-0">{{ $sub_category->name }}</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal -->
                <div class="modal fade" id="categoryModal{{$i}}" tabindex="-1" aria-labelledby="categoryModalLabel" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="categoryModalLabel">Category Actions for <span class="text-success bold">{{ $sub_category->name }}</span></h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                            </div>
                            <div class="modal-body container">
                                <form action="{{ route('sub_category.destroy', $sub_category) }}" method="POST" class="row text-center">
                                    @method('DELETE')
                                    @csrf
                                    <div class="col">
                                        <button type="button" class="btn btn-warning mt-3" onclick="window.location='{{ route('sub_category.edit', $sub_category) }}'"><i class="bi bi-pencil-square"></i> {{__('btnText.modify')}}</button>
                                    </div>
                                    <div class="col">
                                        <button type="submit" class="btn btn-danger mt-3"><i class="bi bi-trash3-fill"></i> {{__('btnText.delete')}}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

            @endforeach
        </div>

        <div class="row category-table-view d-none" id="list_view">
            <div class="col-md-12">
                <table class="table category-table">
                    <thead>
                    <tr>
                        <th>No.</th>
                        <th>{{__('cate.cate_name')}}</th>
                        <th>{{__('cate.cate')}}</th>
                        <th>{{__('cate.cate_img')}}</th>
                        <th>{{__('btnText.action')}}</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach ($sub_categories as $j => $sub_category)
                        <tr class="category-item" data-option="{{$sub_category->category_id}}">
                            <td>{{ $j+1 }}</td>
                            <td>{{ $sub_category->name }}</td>
                            <td>{{ $sub_category->category->name }}</td>
                            <td><img src="{{asset('storage/'.$sub_category->img_path)}}" style="width: 100px; height: 100px;" alt="{{ $sub_category->name . 'picture' }}" class="rounded-2"></td>
                            <td>
                                <form action="{{ route('sub_category.destroy', $sub_category) }}" method="POST">
                                    @method('DELETE')
                                    @csrf
                                    <button type="button" class="btn btn-outline-success edit-category" onclick="window.location='{{ route('sub_category.edit', $sub_category) }}'">
                                        <i class="bi bi-pen"></i> {{ __('btnText.edt') }}
                                    </button>
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are You Sure 🤨')">

                                        <i class="bi bi-trash3-fill"></i> {{ __('btnText.delete') }}
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>

        $(document).ready(function() {
            // Get the checkbox element
            var checkbox = $('#flexSwitchCheckCheckedDisabled');

            // Get the label element
            var viewLabel = $('#viewLabel');

            // Get the card view and list view elements
            var cardView = $('#card_view');
            var listView = $('#list_view');

            // Get the search input and button elements
            var searchInput = $('#searchInput');
            var searchButton = $('#searchButton');

            // Function to toggle between card view and list view
            function toggleView() {
                if (checkbox.prop('checked')) {
                    cardView.removeClass('d-none');
                    listView.addClass('d-none');
                    viewLabel.text('Grid View');
                } else {
                    cardView.addClass('d-none');
                    listView.removeClass('d-none');
                    viewLabel.text('List View');
                }
            }

            // Call the toggleView function when the checkbox is clicked
            checkbox.on('click', toggleView);

            // Call the toggleView function on page load
            toggleView();

            // Function to handle search
            searchButton.on('click', function() {
                var searchText = searchInput.val().toLowerCase();
                $('.category-item').each(function() {
                    var categoryText = $(this).text().toLowerCase();
                    if (categoryText.indexOf(searchText) !== -1) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

        //     select filtering
            var filterSelect = $('#filterSelect');
            filterSelect.on('change', function() {
                const selectedOption = $(this).val();
                if (selectedOption === '') {
                    $('.category-item').show();
                } else {
                    $('.category-item').hide();
                    $('.category-item[data-option="'+selectedOption+'"]').show();
                }
            });
        });
    </script>
@endsection

