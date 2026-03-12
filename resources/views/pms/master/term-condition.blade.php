@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">@if($detail) Update Terms & Conditions @else Add Terms & Conditions @endif</h1>
                </div>
            </div>
        </div>
        <form method="post" action="{{ route('pms.term.condition.save') }}">
            @csrf
            <input type="hidden" name="id" id="id" value="{{ $detail->id ?? '' }}">
            <div class="col-12">
                <div class="form-field">
                    <label for="title_text">Title</label>
                    <input type="text" name="title_text" value="{{ old('title_text', $detail->title_text ?? '') }}" class="form-control">
                </div>
            </div> 
            <div class="col-12">
                <div class="form-field">
                    <label for="terms_and_condition">Content</label>
                    <textarea name="terms_and_condition" class="form-control h-auto terms_and_condition" rows="8">{{ old('terms_and_condition', $detail->terms_and_condition ?? '') }}</textarea>
                </div>
            </div>
             <div class="btn-wrap pt-2">
                <button class="btn btn-primary px-5">@if($detail) UPDATE @else SUBMIT @endif</button>
            </div>
        </form>
    </div>
</section>
@php 
    $path = asset('public/ckfinder');
@endphp
<script>
    document.addEventListener('DOMContentLoaded', () => {
        let path = "<?php echo $path; ?>";
        // ---------- CKEditor Start -------------//
        $('textarea .terms_and_condition').ckeditor();
        var imgEditor = CKEDITOR.replace('terms_and_condition');
        CKFinder.setupCKEditor( imgEditor, path);
    });
</script>
@endsection