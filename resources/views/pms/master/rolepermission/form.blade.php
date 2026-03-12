@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">Add Role/Permission</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.rolepermission.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="material-symbols-outlined me-1">list</i><span>Go to List</span>
                    </a>
                </div>
            </div>
        </div>
        <style>
            .custom-checkbox {
                width: 22px;
                height: 22px;
                margin-right: 10px;
                cursor: pointer;
            }
            .custom-label {
                font-weight: bold;
                font-size: 16px;
                color: #666;
            }
            .custom-color {
                background-color: #eeedfc;
            }
        </style>
        <form method="post" action="{{ route('pms.rolepermission.save') }}">
            @csrf
            <div class="content-box p-3">
                <div class="form-box">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-field">
                                <label for="role_id">Role<sup>*</sup></label>
                                <select name="role_id" id="role_id" class="form-control @error('role_id') is-invalid @enderror">
                                    <option value="">Select role</option>
                                    @foreach($roles as $role)
                                        @if($role->role_name != 'Owner')
                                            <option value="{{ $role->id }}" {{ $role->id == $role_id ? 'selected' : '' }}>
                                                {{ $role->role_name }}
                                            </option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12 mt-4">
                            <div class="form-field">
                                <h6 class="mb-0 fw-bold fs-4">Permissions</h6>
                            </div>
                        </div>
                    </div>

                    @foreach($items as $key => $role)
                        <div class="row mb-2">
                            <div class="col-12 custom-color pt-3">
                                <div class="form-field d-flex align-items-center">
                                    @php
                                        $isSidebarChecked = false;
                                        if (!empty($formattedData)) {
                                            foreach ($formattedData as $data) {
                                                foreach ($data['sub_sidebars'] as $perm) {
                                                    if ($perm['sidebar_id'] == $role['id'] && $perm['type'] == 'sidebar') {
                                                        $isSidebarChecked = true;
                                                        break 2;
                                                    }
                                                }
                                            }
                                        }
                                    @endphp
                                    <input type="checkbox" name="cbk{{ $role['id'] }}" id="parent-{{ $role['id'] }}" class="parent-checkbox custom-checkbox" data-role="{{ $role['id'] }}" {{ $isSidebarChecked ? 'checked' : '' }}>
                                    <label for="parent-{{ $role['id'] }}" class="custom-label">{{ $role['name'] }}</label>
                                </div>
                            </div>

                            @if(!empty($role['sub_sidebars']))
                                @foreach($role['sub_sidebars'] as $subKey => $sub)
                                    <div class="col-3 mt-3">
                                        <div class="form-field d-flex align-items-center">
                                            @php
                                                $isSubChecked = false;
                                                if (!empty($formattedData)) {
                                                    foreach ($formattedData as $data) {
                                                        foreach ($data['sub_sidebars'] as $perm) {
                                                            if ($perm['sub_sidebar_id'] == $sub['id'] && $perm['type'] == 'subsidebar') {
                                                                $isSubChecked = true;
                                                                break 2;
                                                            }
                                                        }
                                                    }
                                                }
                                            @endphp
                                            <input type="checkbox" name="subcbk{{ $sub['id'] }}" id="child-{{ $sub['id'] }}" class="child-checkbox custom-checkbox" data-parent="{{ $role['id'] }}" {{ $isSubChecked ? 'checked' : '' }}>
                                            <label for="child-{{ $sub['id'] }}">{{ $sub['name'] }}</label>
                                            <input type="hidden" name="sub_parent_map[{{ $sub['id'] }}]" value="{{ $role['id'] }}">
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="btn-wrap pt-2">
                <button type="submit" class="btn btn-primary px-5">SUBMIT</button>
            </div>
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function setupCheckboxes() {
                let parents = document.querySelectorAll(".parent-checkbox");

                parents.forEach(parent => {
                    let parentId = parent.getAttribute("data-role");
                    let children = document.querySelectorAll(`.child-checkbox[data-parent='${parentId}']`);

                    parent.addEventListener("change", function () {
                        children.forEach(child => child.checked = parent.checked);
                    });

                    children.forEach(child => {
                        child.addEventListener("change", function () {
                            let allChecked = [...children].every(cb => cb.checked);
                            let anyChecked = [...children].some(cb => cb.checked);
                            parent.checked = anyChecked;
                        });
                    });
                });
            }

            setupCheckboxes();
        });
    </script>
</section>
@endsection
