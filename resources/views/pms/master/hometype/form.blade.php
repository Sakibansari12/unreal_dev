@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">@if($detail) Modify Home Type @else Add New Home Type @endif</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.hometype.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="material-symbols-outlined me-1">list</i><span>Go to List</span>
                    </a>
                </div>
            </div>
        </div>

        <form method="post" action="{{ route('pms.hometype.save') }}">
            @csrf
            <input type="hidden" name="id" id="id" value="{{ $detail->id ?? '' }}">

            <div class="content-box p-3">
                <div class="form-box">
                    <div class="row">
                        {{-- Name --}}
                        <div class="col-12">
                            <div class="form-field">
                                <label for="name">Name <sup>*</sup></label>
                                <input type="text" name="name" value="{{ old('name', $detail->name ?? '') }}" class="form-control @error('name') is-invalid @enderror">
                            </div>
                        </div>

                        {{-- SEO Fields Header --}}
                        <div class="col-12 mt-3 mb-2">
                            <h1 class="fs-5 mb-0">SEO Fields</h1>
                        </div>

                        {{-- Meta Title --}}
                        <div class="col-6">
                            <div class="form-field">
                                <label for="meta_title">Title</label>
                                <input type="text" name="meta_title" value="{{ old('meta_title', $detail->meta_title ?? '') }}" class="form-control">
                            </div>
                        </div>

                         {{-- Meta Keywords --}}
                        <div class="col-6">
                            <div class="form-field">
                                <label for="meta_keyword">Keywords</label>
                                <textarea name="meta_keyword" class="form-control h-auto">{{ old('meta_keyword', $detail->meta_keyword ?? '') }}</textarea>
                            </div>
                        </div>

                        {{-- Meta Description --}}
                        <div class="col-6">
                            <div class="form-field">
                                <label for="meta_description">Description</label>
                                <textarea name="meta_description" class="form-control h-auto">{{ old('meta_description', $detail->meta_description ?? '') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="btn-wrap pt-2">
                <button class="btn btn-primary px-5">@if($detail) UPDATE @else SUBMIT @endif</button>
            </div>
        </form>
    </div>
</section>
@endsection
