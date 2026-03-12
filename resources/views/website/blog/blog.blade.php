
@extends('website.layouts.app')
@section('content')
<section class="section">
    <div class="container">        
        <div class="section-heading">
            <h1 class="fs-2">Blogs</h1> 
        </div>
        <div class="blog-list">
            <div class="row g-4">
                @foreach ($blogs as $blog)   
                <div class="col-12 col-md-6 col-lg-4">
                       <a href="{{ route('blog.detail', $blog->slug) }}" class="card card-blog">
                       <div class="imgBox">
                                        <time>{{ \Carbon\Carbon::parse($blog->date)->format('d M, Y') }}</time>
                                        @php
                                            $imagePath = public_path('storage/blog/' . ($blog->image ?? ''));
                                        @endphp
                                        @if(!empty($blog->image) && file_exists($imagePath))
                                            <img loading="lazy" src="{{ asset('storage/blog/' . $blog->image) }}" alt="{{ $blog->title }}">
                                        @else
                                            <img loading="lazy" src="{{ asset('assets/website/images/placeholder.jpg') }}" alt="Placeholder">
                                        @endif
                                    </div>
                        <div class="card-body pb-0">
                            <div class="content">
                                <h3>{!! $blog->title ?? '' !!}</h3>
                               {!! \Illuminate\Support\Str::words($blog->description, 20, '...') !!}
                            </div>
                        </div>
                        <div class="card-footer">
                            <span class="link">Learn more</span>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>            
        </div>
      <nav class="pt-5">
    @if ($blogs->lastPage() > 1)
        <ul class="pagination">
            @foreach (range(max(1, $blogs->currentPage() - 1), min($blogs->currentPage() + 1, $blogs->lastPage())) as $page)
                <li class="page-item {{ $page == $blogs->currentPage() ? 'active' : '' }}">
                    <a class="page-link" href="{{ $blogs->url($page) }}">{{ $page }}</a>
                </li>
            @endforeach
        </ul>
    @endif
</nav>
    </div>
</section>

@endsection
