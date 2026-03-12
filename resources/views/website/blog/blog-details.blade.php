@extends('website.layouts.app')
@section('content')
<div class="breadcrum">
        <div class="container-fluid">
            <ul>
                <li><a href="{{route('index')}}">Home</a></li>
                <li><a href="{{route('blog')}}">Blogs</a></li>
                <li>{{$detail->title ?? '' }}</li>
            </ul>
    </div>
</div>
<style>
    .editor-content img{
        max-width: 100%;
    }

    /*.img-box{*/
    /*    box-shadow: rgba(0, 0, 0, 0.35) 0px 5px 15px;*/
    /*    border-radius: 10px;*/
    /*}*/
    
    .blog-hero img {
    border-radius: 12px;
}
</style>
<section>
    <div class="container">
        <div class="row justify-content-center text-center">
            <h1 class="mt-4">{{$detail->title ?? '' }}</h1>
            <div class="col-12 col-md-10">
                <div class="blog-hero">
                    <img src="{{ asset('storage/blog/' . $detail->image) }}"  alt="" class="img-fluid">
                </div>
            </div>

            <div class="col-12 col-md-12">
                  <div class="content editor-content pt-5">
                    {!! $detail->description !!}
                    </div>
            </div>
        </div>
    </div>
</section>

@endsection