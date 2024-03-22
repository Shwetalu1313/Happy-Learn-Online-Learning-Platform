@extends('layouts.app')

@section('content')
    <div class="container py-5">
        <h3 class="text-primary-emphasis mb-5">Course {{__('cate.cates')}}</h3>
        <div class="row g-4">
            @foreach($categories as $i => $category)
                <div class="col-md-4">
                    <div class="accordion" id="accordion{{$i}}">
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse{{$i}}" aria-expanded="false" aria-controls="collapse{{$i}}">
                                    {{$category->name}}
                                </button>
                            </h2>
                            <div id="collapse{{$i}}" class="accordion-collapse collapse" data-bs-parent="#accordion{{$i}}">
                                <div class="accordion-body" style="background-image: url('{{asset('storage/'.$category->img_path)}}'); background-size: cover;">
                                    @php $counter = 0; @endphp
                                    @if($category->sub_categories->count() === 0)
                                        <p style="background: linear-gradient(90deg, rgba(2,0,36,1) 0%, rgba(9,9,121,1) 35%, rgba(0,212,255,0) 100%);">Coming Soon... 🙆🏻</p>
                                    @elseif($category->sub_categories->count() > 0)
                                        <ul class="list-group" style="cursor: pointer">
                                            @foreach($category->sub_categories as $j => $subcategory)
                                                @if($subcategory->courses->count() > 0)
                                                    <li class="list-group-item list-group-item-info">{{++$counter}}. {{$subcategory->name}}
                                                        <span class="badge text-bg-primary">{{$subcategory->courses->count()}}</span>
                                                    </li>
                                                @else
                                                    <li class="list-group-item list-group-item-info">{{++$counter}}. {{$subcategory->name}}
                                                        <span class="badge text-bg-warning">In progress</span>
                                                    </li>
                                                @endif
                                            @endforeach
                                        </ul>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
