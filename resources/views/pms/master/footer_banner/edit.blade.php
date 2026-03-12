@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container">
        <div class="title">
            <div class="row align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">Footer Banner Content</h1>
                </div>
            </div>
        </div>

        <div class="content-box p-3 mt-3">
            @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            <form action="{{ route('pms.footer-banner.save') }}" method="POST">
                @csrf
                <input type="hidden" name="id" value="{{ $banner->id ?? '' }}">

                <!-- Title -->
                <div class="row mb-3">
                    <div class="col-12">
                        <label for="title">Title <sup class="text-danger">*</sup></label>
                        <textarea name="title" id="title" class="form-control @error('title') border-danger @enderror" rows="4" required>{{ old('title', $banner->title ?? '') }}</textarea>
                        @error('title')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Subtitle -->
                <!-- <div class="row mb-3">
                    <div class="col-12">
                        <label for="sub_title">Subtitle <sup class="text-danger">*</sup></label>
                        <input type="text" name="sub_title" id="sub_title" class="form-control @error('sub_title') border-danger @enderror" value="{{ old('sub_title', $banner->sub_title ?? '') }}" required>
                        @error('sub_title')
                        <small class="text-danger">{{ $message }}</small>
                        @enderror
                    </div>
                </div> -->

                <!-- Dynamic List Content -->
                <!-- <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-success">
                            <tr>
                                <th>Footer List Content</th>
                                <th width="100" class="text-center">Action</th>
                            </tr>
                        </thead>
                        <tbody id="listContentBody">
                            @php
                            $listContents = old('list_content');
                            if (!$listContents && $banner && $banner->list_content) {
                            $listContents = json_decode($banner->list_content, true);
                            if (!is_array($listContents)) {
                            $listContents = [];
                            }
                            }
                            if (!is_array($listContents) || empty($listContents)) {
                            $listContents = [''];
                            }
                            @endphp

                            @foreach($listContents as $index => $content)
                            <tr>
                                <td>
                                    <input type="text" name="list_content[]" class="form-control @error('list_content.' . $index) border-danger @enderror" value="{{ $content }}" required>
                                    @error('list_content.' . $index)
                                    <small class="text-danger">{{ $message }}</small>
                                    @enderror
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div> -->

                <!-- Add More Button -->
                <!-- <div class="mt-3 mb-4">
                    <button type="button" class="btn btn-secondary" onclick="addNewRow()">
                        <i class="bi bi-plus-lg me-2"></i>Add More
                    </button>
                </div> -->

                <!-- Submit Button -->
                <div class="btn-wrap">
                    <button type="submit" class="btn btn-primary px-5">SUBMIT</button>
                </div>
            </form>
        </div>
    </div>
</section>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ckfinderPath = "{{ asset('ckfinder/') }}";
        CKEDITOR.replace('title');
        CKEDITOR.replaceAll('ckeditor');
    });
</script>
<!-- <script>
    function addNewRow() {
        const tbody = document.getElementById('listContentBody');
        const rowCount = tbody.rows.length;

        const newRow = document.createElement('tr');
        newRow.innerHTML = `
        <td>
            <input type="text" name="list_content[]" class="form-control" required>
        </td>
        <td class="text-center">
            <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
        tbody.appendChild(newRow);
    }

    function removeRow(button) {
        const tbody = document.getElementById('listContentBody');
        if (tbody.rows.length > 1) {
            const row = button.closest('tr');
            row.remove();
        } else {
            alert('At least one item is required.');
        }
    }

    // Optional: Prevent removing last row
    document.addEventListener('DOMContentLoaded', function() {
        const tbody = document.getElementById('listContentBody');
        if (tbody.rows.length === 0) {
            addNewRow();
        }
    });
</script> -->
@endsection