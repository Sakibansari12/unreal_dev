@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container-fluid">
       <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">@if($detail) Modify Company @else Add New Company @endif</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.company.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="material-symbols-outlined me-1">list</i><span>Go to List</span>
                    </a>
                </div>
            </div>
        </div>
     	<form method="post" action="{{ route('pms.company.save', $detail->id ?? '') }}">
            @csrf
            <input type="hidden" name="id" id="id" value="@if($detail){{ $detail->id }}@endif">
            <div class="content-box p-3">
                <div class="form-box">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-field">
                                <label for="state">State<sup>*</sup></label>
                                <select name="state_id" class="form-control @error('state_id') is-invalid @enderror">
                                   <option value="">Select State</option>
                                    @foreach($states as $value)
                                        <option value="{{ $value->id }}" 
                                            {{ old('state_id', $detail->state_id ?? '') == $value->id ? 'selected' : '' }}>
                                            {{ $value->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div> 
                        <div class="col-6">
                            <div class="form-field">
                                <label for="name">Name<sup>*</sup></label>
                                <input type="text" name="company_name" value="{{ old('company_name', $detail->company_name ?? '') }}" class="form-control @error('company_name') is-invalid @enderror">
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-field">
                                <label for="gst_no">GST No<sup>*</sup></label>
                                <input type="text" name="gst_no" maxlength="15" minlength="15" class="form-control @error('gst_no') is-invalid @enderror" value="{{ old('gst_no', $detail->gst_no ?? '') }}">
                            </div>
                        </div> 
                        <div class="col-3">
                            <div class="form-field">
                                <label for="cin_no">CIN No<sup>*</sup></label>
                                <input type="text" name="cin_no" maxlength="21" minlength="21" class="form-control @error('cin_no') is-invalid @enderror" value="{{ old('cin_no', $detail->cin_no ?? '') }}">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-field">
                                <label for="company_phone">Phone</label>
                                <input type="text" name="company_phone" maxlength="13" minlength="10" class="form-control @error('mobile') is-invalid @enderror" inputmode="tel" pattern="[0-9+]*" 
                                oninput="this.value = this.value.replace(/[^0-9+]/g, '')" value="{{ old('company_phone', $detail->company_phone ?? '') }}">
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="form-field">
                                <label for="company_address">Address<sup>*</sup></label>
                                <textarea name="company_address" rows="8" class="form-control h-auto @error('company_address') is-invalid @enderror">{{ old('company_address', $detail->company_address ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-field">
                                <label for="company_email">Email<sup>*</sup></label>
                                <input type="text" name="company_email" class="form-control @error('company_email') is-invalid @enderror" value="{{ old('company_email', $detail->company_email ?? '') }}" placeholder="">
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="form-field">
                                <label for="company_website">Website</label>
                                <input type="text" name="company_website" class="form-control @error('company_website') is-invalid @enderror" value="{{ old('company_website', $detail->company_website ?? '') }}" placeholder="">
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