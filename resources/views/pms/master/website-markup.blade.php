@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container-fluid">
       <div class="title">
          <div class="row align-items-center">
              <div class="col">
                 <h1 class="fs-5 mb-0">Website Markup</h1>
              </div>
              <div class="col-auto">
                <!-- Add button placeholder -->
              </div>
          </div>
       </div>
        <form id="websiteformId" method="post" action="{{ route('pms.website.markup.save', $items->id ?? '') }}">
            @csrf
            <input type="hidden" name="id" id="id" value="@if($items){{ $items->id }}@endif">
                <div class="content-box p-3">
                    <div class="form-box">
                        <div class="row">
                            <div class="col-4">
                                <div class="form-field">
                                    <label for="website_markup">Website Markup<sup>*</sup></label>
                                    <div class="input-group">
                                        <input type="text" name="website_markup" 
                                            value="{{ old('website_markup', $items->website_markup ?? '') }}" 
                                            class="form-control @error('website_markup') is-invalid @enderror">
                                        <span class="input-group-text">%</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="btn-wrap pt-2">
                    <button class="btn btn-primary px-5">@if($items) UPDATE @else SUBMIT @endif</button>
                </div>
        </form>
   </div>
</section>
@endsection