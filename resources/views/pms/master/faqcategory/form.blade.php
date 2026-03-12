@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">{{ $detail ? 'Modify Faq Category' : 'Add New Faq Category' }}</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.faqcategory.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="material-symbols-outlined me-1">list</i><span>Go to List</span>
                    </a>
                </div>
            </div>
        </div>

        <form method="POST" action="{{ route('pms.faqcategory.save', $detail->id ?? '') }}">
            @csrf
            <input type="hidden" name="id" value="{{ old('id', $detail->id ?? '') }}">
            <div class="content-box p-3">
                <div class="form-box">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-field">
                                <label for="title">Category<sup>*</sup></label>
                                <input 
                                    type="text" 
                                    name="title" 
                                    id="title"
                                    value="{{ old('title', $detail->title ?? '') }}" 
                                    class="form-control @error('title') is-invalid @enderror">
                                @error('title')
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="btn-wrap pt-2">
                <button class="btn btn-primary px-5">{{ $detail ? 'UPDATE' : 'SUBMIT' }}</button>
            </div>
        </form>
    </div>
</section>
@endsection
