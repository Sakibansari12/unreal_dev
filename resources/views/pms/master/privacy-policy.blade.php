@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">@if($detail) Update Privacy Policy @else Add Privacy Policy @endif</h1>
                </div>
            </div>
        </div>
        <form method="post" action="{{ route('pms.privacy.policy.save') }}">
            @csrf
            <input type="hidden" name="id" id="id" value="{{ $detail->id ?? '' }}">
            <div class="col-12">
                <div class="form-field">
                    <label for="title">Title</label>
                    <input type="text" name="title" value="{{ old('title', $detail->title ?? '') }}" class="form-control">
                </div>
            </div> 
            <div class="col-12">
                <div class="form-field">
                    <label for="cancellation_policy">Content</label>
                    <textarea name="cancellation_policy" class="form-control h-auto cancellation_policy" rows="8">{{ old('cancellation_policy', $detail->cancellation_policy ?? '') }}</textarea>
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
        $('textarea .cancellation_policy').ckeditor();
        var imgEditor = CKEDITOR.replace('cancellation_policy');
        CKFinder.setupCKEditor( imgEditor, path);
    });
</script>
@endsection