@extends('layouts.app')

@section('content')
    <div class="container">
        <h3 class="text-primary-emphasis mb-5">Course {{__('cate.cates')}}</h3>
        <div class="row g-4">
            @foreach($categories as $i => $category)
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header" style="background: linear-gradient(225deg, #6D0530, #081C4E);">
                            <p class="card-title text-center fs-4 text-white" style="position: relative; z-index: 1;">{{$category->name}}</p>
                        </div>

                        <div class="card-body" style="background-image: url('{{asset('storage/'.$category->img_path)}}'); background-size: cover;">
                            @if($category->sub_categories->count() == 0)
                                <p style="background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(9,9,121,1) 35%, rgba(0,212,255,0) 100%);">Coming Soon... 🙆🏻</p>
                            @elseif($category->sub_categories->count() > 0)
                                <ul class="list-group" style="cursor: pointer">
                                    @foreach($category->sub_categories as $j => $subcategory)
                                        <li class="list-group-item list-group-item-info">{{$j+1}}. {{$subcategory->name}}</li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>


    </div>

@endsection
