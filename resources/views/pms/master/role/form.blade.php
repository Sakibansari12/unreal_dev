@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container-fluid">
       <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">@if($detail) Modify Role @else Add New Role @endif</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.role.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="material-symbols-outlined me-1">list</i><span>Go to List</span>
                    </a>
                </div>
            </div>
        </div>
     	<form method="post" action="{{ route('pms.role.save', $detail->id ?? '') }}">
            @csrf
            <input type="hidden" name="id" id="id" value="@if($detail){{ $detail->id }}@endif">
            <div class="content-box p-3">
                <div class="form-box">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-field">
                                <label for="role_name">Role Name<sup>*</sup></label>
                                <input type="text" name="role_name" value="{{ old('role_name', $detail->role_name ?? '') }}" class="form-control @error('role_name') is-invalid @enderror">
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