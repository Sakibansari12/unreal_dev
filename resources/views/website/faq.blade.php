@extends('website.layouts.app')
@section('content')

<div class="breadcrum">
        <div class="container-fluid">
            <ul>
                <li><a href="{{route('index')}}">Home</a></li>
                <li>FAQ's</li>
            </ul>
    </div>
</div>
<section class="section section-faq pb-0">
    <div class="container-fluid">
        <div class="row">
            <h2>Frequently Asked Questions</h2>
            <div class="col-12">
                <div class="accordion mt-3 mb-5">
                    @foreach ($categories as $category)
                        @if ($category->faqs->isNotEmpty())
                            <!-- Category Title -->
                            <div class="faq-category-title">
                                <h3><b>{{ $category->title }}</b></h3>
                            </div>
                            <!-- FAQs for this category -->
                            @foreach ($category->faqs as $faq)
                                <div class="accordion-item">
                                    <div class="accordion-header" onclick="toggleAccordion(this)" role="button" aria-expanded="false">
                                        <h3>{{ $faq->question }}</h3>
                                        <span class="icon-chevron-down arrow"></span>
                                    </div>
                                    <div class="accordion-content" aria-hidden="true">
                                        {!! $faq->answer !!}
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    @endforeach
                    @if ($categories->isEmpty() || $categories->pluck('faqs')->flatten()->isEmpty())
                        <p>No FAQs available.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</section>
<script>
  function toggleAccordion(header) {
  const allContents = document.querySelectorAll('.accordion-content');
  const allArrows = document.querySelectorAll('.arrow');
  allContents.forEach((content, index) => {
    const arrow = allArrows[index];
    if (content !== header.nextElementSibling) {
      content.style.maxHeight = null;
      content.style.padding = '0 0px !important';
      arrow.classList.remove('open');
    }
  });
  const content = header.nextElementSibling;
  const arrow = header.querySelector('.arrow');
  if (content.style.maxHeight) {
    content.style.maxHeight = null;
    content.style.padding = '0 0px !important';
    arrow.classList.remove('open');
  } else {
    content.style.maxHeight = content.scrollHeight + "px";
    content.style.padding = '8px 0px !important';
    arrow.classList.add('open');
  }
}
</script>
@endsection