@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">
                        {{ $detail ? 'Modify Faq' : 'Add New Faq' }}
                    </h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.faq.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="material-symbols-outlined me-1">list</i><span>Go to List</span>
                    </a>
                </div>
            </div>
        </div>

        <form method="post" action="{{ route('pms.faq.save', $detail->id ?? '') }}">
            @csrf
            <input type="hidden" name="id" id="id" value="{{ $detail->id ?? '' }}">

            <div class="content-box p-3">
                <div class="form-box">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-field">
                                <label for="faq_category_id">Category<sup>*</sup></label>
                                <select name="faq_category_id" class="form-control @error('faq_category_id') is-invalid @enderror">
                                    <option value="">Select category</option>
                                    @foreach($faqCategories as $value)
                                        <option value="{{ $value->id }}" {{ old('faq_category_id', $detail->faq_category_id ?? '') == $value->id ? 'selected' : '' }}>
                                            {{ $value->title }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 

                        <div class="col-6">
                            <div class="form-field">
                                <label for="question">Question<sup>*</sup></label>
                                <input type="text" name="question" class="form-control @error('question') is-invalid @enderror" value="{{ old('question', $detail->question ?? '') }}">
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-field">
                                <label for="answer">Answer<sup>*</sup></label>
                                <textarea name="answer" rows="6" class="form-control h-auto @error('answer') is-invalid @enderror">{{ old('answer', $detail->answer ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="btn-wrap pt-2">
                <button class="btn btn-primary px-5">
                    {{ $detail ? 'UPDATE' : 'SUBMIT' }}
                </button>
            </div>
        </form>
    </div>
</section>
@endsection
