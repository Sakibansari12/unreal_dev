@extends('pms.layouts.app')
@section('content')
<style>
    .upload-wrapper {
        border: 2px dashed #C7C7C7;
        border-radius: 5px;
        height: 300px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        padding: 15px;
        user-select: none;
        background-color: #fff;
        font-size: 1rem;
        position: relative;
        overflow: hidden;
    }
    .upload-wrapper input[type=file] {
        position: absolute;
        height: 100%;
        width: 100%;
        opacity: 0;
        cursor: pointer;
        z-index: 2;
    }
    .upload-wrapper.is-invalid {
        border-color: #dc3545;
    }
    .upload-info {
        z-index: 1;
        pointer-events: none;
    }
    /* Image preview CSS without cropping: */
    .imagePreview {
        position: relative;
        width: 100%;
        height: 300px;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .imagePreview img {
        position: absolute;
        left: 50%;
        top: 50%;
        width: 100%;
        height: 100%;
        object-fit: contain;
        transform: translate(-50%, -50%);
    }
    .pdfPreview {
        display: flex;
        align-items: center;
        justify-content: center;
        flex-direction: column;
        height: 100%;
        width: 100%;
        background-color: #f8f9fa;
    }
    .pdfPreview i {
        font-size: 48px;
        color: #dc3545;
    }
    .pdfPreview span {
        font-size: 16px;
        color: #333;
    }
    .remove-preview {
        position: absolute;
        top: 10px;
        right: 10px;
        background-color: rgba(0,0,0,0.5);
        color: white;
        border: none;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        font-size: 18px;
        line-height: 30px;
        text-align: center;
        cursor: pointer;
        z-index: 3;
    }
</style>

<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                    <h1 class="fs-5 mb-0">{{ isset($detail->id) ? 'Modify Add Owner Expenses' : 'Add Owner Expenses' }}</h1>
                </div>
                <div class="col-auto">
                    <a href="{{ route('pms.owner-expenses.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                        <i class="bi bi-list-task me-2"></i>
                        Manage
                    </a>
                </div>
            </div>
        </div>

        <div class="content-box p-3">
            <div class="form-box">
                <form action="{{ route('pms.owner-expenses.save', $detail->id ?? '') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="id" id="id" value="@if($detail){{ $detail->id }}@endif">
                    <input type="hidden" name="remove_image" id="removeImageInput" value="0">
                    <div class="row">
                        <!-- <div class="col-6">
                            <div class="form-field">
                                <label for="unit">Property<sup>*</sup></label>
                                <select class="form-select form-control @error('unit') is-invalid @enderror" id="property_unit" name="unit">
                                    <option value="" disabled {{ old('unit', $detail->home_id ?? '') == '' ? 'selected' : '' }}>Select Unit</option>
                                    @foreach ($propertyUnitList as $propertyUnit)
                                        <option value="{{ $propertyUnit->id }}"
                                            {{ old('unit', $detail->home_id ?? '') == $propertyUnit->id ? 'selected' : '' }}>
                                            {{ $propertyUnit->home_name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div> -->

                        <div class="col-6">
                            <div class="form-field">
                                <label for="unit">Property<sup>*</sup></label>
                                <select class="form-select form-control js-select2-unit @error('unit') is-invalid @enderror" 
                                        id="property_unit" 
                                        name="unit"
                                        style="width: 100%;">
                                    <option value="" disabled {{ old('unit', $detail->home_id ?? '') == '' ? 'selected' : '' }}>Select Property</option>
                                    @foreach ($propertyUnitList as $propertyUnit)
                                        <option 
                                            value="{{ $propertyUnit->id }}"
                                            data-ptype="{{ $propertyUnit->pType }}"
                                            {{ old('unit', $detail->home_id ?? '') == $propertyUnit->id ? 'selected' : '' }}>
                                            {{ $propertyUnit->home_name }}
                                        </option>
                                    @endforeach
                                </select>

                                {{-- Error --}}
                                @error('unit')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>






                        <div class="col-6">
                            <div class="form-field">
                                <label for="property">Unit<sup>*</sup></label>
                                <select class="form-select form-control @error('property') is-invalid @enderror" name="property" id="propertyDropdown">
                                    <option value="" disabled>Select Property</option>
                                    @if(isset($propertyList))
                                        @foreach($propertyList as $property)
                                            <option value="{{ $property['id'] }}" 
                                                {{ old('property', $detail->property_id ?? '') == $property['id'] ? 'selected' : '' }}>
                                                {{ $property['name'] }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('property')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <input type="hidden" name="type" id="propertyTypeInput" value="{{ old('type', $detail->pType ?? '') }}">

                        </div>
                    </div>
                    <div class="row">
                        <!--<div class="col-6">-->
                        <!--    <div class="form-field">-->
                        <!--        <label for="expenses_name">Expenses Name<sup>*</sup></label>-->
                        <!--        <input -->
                        <!--            type="text" -->
                        <!--            name="expenses_name" -->
                        <!--            value="{{ old('expenses_name', $detail->expenses_name ?? '') }}" -->
                        <!--            class="form-control @error('expenses_name') is-invalid @enderror"-->
                        <!--        >-->
                        <!--        @error('expenses_name')-->
                        <!--            <div class="invalid-feedback">{{ $message }}</div>-->
                        <!--        @enderror-->
                        <!--    </div>-->
                        <!--</div>-->
                        
                        <div class="col-6">
                            <div class="form-field">
                                <label for="expenses_name">Expenses Name<sup>*</sup></label>
                                <input 
                                    type="text" 
                                    id="expenses_name" 
                                    name="expenses_name" 
                                    value="{{ old('expenses_name', $detail->expenses_name ?? '') }}" 
                                    class="form-control @error('expenses_name') is-invalid @enderror"
                                />

                                @error('expenses_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        

                        <div class="col-6">
                            <div class="form-field">
                                <label for="expenses_amount">Amount<sup>*</sup></label>
                                <input 
                                    type="text" 
                                    name="expenses_amount" 
                                    value="{{ old('expenses_amount', $detail->amount ?? '') }}" 
                                    class="form-control @error('expenses_amount') is-invalid @enderror"
                                >
                                @error('expenses_amount')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-field">
                                <label for="expenses_date">Date<span class="text-danger">*</span></label>
                                <input
                                    type="date"
                                    name="expenses_date"
                                    id="expenses_date"
                                    class="form-control @error('expenses_date') is-invalid @enderror flatpickr"
                                    value="{{ old('expenses_date', isset($detail) ? \Carbon\Carbon::parse($detail->date)->format('Y-m-d') : '') }}"
                                />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-6">
                            <label for="image">Upload</label>
                            <div class="upload-wrapper @error('image') is-invalid @enderror" id="uploadWrapper">
                                <input type="file" name="image" id="imageInput" accept="image/*,application/pdf" onchange="previewImage(event)">
                                <div class="upload-info" id="uploadInfo" onclick="document.getElementById('imageInput').click()" style="@if(isset($detail->file)) display:none; @endif">
                                    <i class="bi bi-upload fs-4"></i>
                                    <div class="upload-info-text">
                                        <strong><span class="text-primary">Browse</span> Your File.</strong>
                                        <small class="d-block">
                                            Allowed formats: JPG, JPEG, PNG, WEBP, SVG, PDF | Max size: 3MB
                                        </small>
                                    </div>
                                </div>
                                <div id="imagePreview" class="imagePreview @if(!isset($detail->file)) d-none @endif">
                                    @php
                                        $file = $detail->file ?? '';
                                        $filePath = public_path('storage/expenses/' . $file);
                                        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                                    @endphp

                                    @if(!empty($file) && file_exists($filePath))
                                        @if($extension === 'pdf')
                                            <div class="pdfPreview" style="display:flex;align-items:center;gap:10px;">
                                                <i class="bi bi-file-earmark-pdf text-danger fs-3"></i>
                                                <a href="{{ asset('storage/expenses/' . $file) }}" target="_blank">{{ $file }}</a>
                                            </div>
                                        @else
                                            <img src="{{ asset('storage/expenses/' . $file) }}" alt="Preview">
                                        @endif
                                    @endif
                                </div>

                                <button type="button" class="remove-preview @if(!isset($detail->file)) d-none @endif" onclick="removeImage()">×</button>
                            </div>
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="btn-wrap pt-2">
                        <button class="btn btn-primary px-5" type="submit">SUBMIT</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
<!-- <script>
    $('.js-select2-unit').select2({
        placeholder: "Select Property",
        allowClear: true,
        width: 'resolve'
    });
</script> -->

<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
@php
    $expenses = $owner_expenses_name->values(); // make sure it's a flat array
@endphp
<script>
    $(function () {
        let expenses = @json($expenses);

        $('#expenses_name').autocomplete({
            source: expenses,
            minLength: 0 // important: show suggestions even without typing
        }).focus(function () {
            // trigger suggestion list on focus
            $(this).autocomplete("search", "");
        });
    });
</script>


<script>
document.addEventListener('DOMContentLoaded', function () {
    var $unit = $('#property_unit');

    // init select2
    if ($.fn.select2) {
        $unit.select2({
            placeholder: "Select Property",
            allowClear: true,
            width: '100%'
        });
    } else {
        console.warn('Select2 not loaded');
    }
    
    
    var selectedUnit = "{{ old('unit', $detail->home_id ?? '') }}";
    if (selectedUnit) {
        fillProperties(selectedUnit);
    }
    
    var $property = $('#propertyDropdown');
    if ($.fn.select2) {
        $property.select2({
            placeholder: "Select unit",
            allowClear: true,
            width: '100%'
        });
    } else {
        console.warn('Select2 not loaded');
    }

    function fillProperties(unitId) {
        if (!unitId) return;

        $.ajax({
            url: "{{ route('pms.properties-by-unit', ['unitId' => 'UNIT_ID']) }}".replace('UNIT_ID', unitId),
            type: "GET",
            dataType: "json",
            success: function (response) {
                var $dropdown = $('#propertyDropdown');
                $dropdown.empty().append('<option value="" disabled selected>Select unit</option>');

                $.each(response, function (i, property) {
                    $dropdown.append(
                        '<option value="' + property.id + '" data-type="' + (property.type ?? '') + '">' +
                        property.name +
                        '</option>'
                    );
                });

                // set selected in edit mode (if any)
                var selectedProperty = "{{ old('property', $detail->property_id ?? '') }}";
                if (selectedProperty) {
                    $dropdown.val(selectedProperty).trigger('change');
                }
                

                $('#propertyDropdown').on('change', function () {
                    let selectedType = $(this).find('option:selected').data('type') || '';
                    $('#propertyTypeInput').val(selectedType);
                });




            },
            error: function (xhr) {
                console.error('Fetch failed', xhr.responseText);
            }
        });
    }

    // select pe call
$unit.on('select2:select', function () {
    var unitId = $(this).val();
    fillProperties(unitId);
});

// clear pe reset
$unit.on('select2:clear', function () {
    $('#propertyDropdown').empty().append('<option value="" disabled selected>Select Property</option>');
    //$('#propertyTypeInput').val('');
});

});
</script>



<script>
    document.addEventListener('DOMContentLoaded', () => {
        flatpickr('#expenses_date', {
            dateFormat: 'Y-m-d',
            maxDate: 'today',
            allowInput: true,
            altInput: true,
            altFormat: "d/m/Y",
        });

        // $('#property_unit').on('change', function() {
        //     let unitId = $(this).val();
        //     if (unitId) {
        //         $.ajax({
        //             url: "{{ route('pms.properties-by-unit', ['unitId' => ':id']) }}".replace(':id', unitId),
        //             type: "GET",
        //             success: function(response) {
        //             let $dropdown = $('#propertyDropdown');
        //             $dropdown.empty().append('<option value="" disabled selected>Select Property</option>');

        //             $.each(response, function(index, property) {
        //                 $dropdown.append('<option value="'+ property.id +'" data-type="'+ property.type +'">'+ property.name +'</option>');
        //             });

                    
        //             let selectedProperty = "{{ old('property', $detail->property_id ?? '') }}";
        //             if (selectedProperty) {
        //                 $dropdown.val(selectedProperty);
        //                 let selectedType = $dropdown.find('option:selected').data('type');
        //                 $('#propertyTypeInput').val(selectedType);
        //             }

        //             // Update hidden input on change
        //             $dropdown.on('change', function () {
        //                 let selectedType = $(this).find('option:selected').data('type');
        //                 $('#propertyTypeInput').val(selectedType);
        //             });
        //         }

        //         });
        //     }
        // });

        


    });
</script>
<script>
    function previewImage(event) {
        const input = event.target;
        const preview = document.getElementById('imagePreview');
        const info = document.getElementById('uploadInfo');
        const removeBtn = document.querySelector('.remove-preview');

        preview.innerHTML = '';

        if (input.files && input.files[0]) {
            const file = input.files[0];
            const fileName = file.name;
            const isPDF = file.type === 'application/pdf';

            if (isPDF) {
                const pdfDiv = document.createElement('div');
                pdfDiv.className = 'pdfPreview';
                pdfDiv.innerHTML = `<i class="bi bi-file-earmark-pdf"></i><span>${fileName}</span>`;
                preview.appendChild(pdfDiv);
            } else {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement("img");
                    img.src = e.target.result;
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }

            preview.classList.remove('d-none');
            info.style.display = 'none';
            removeBtn.classList.remove('d-none');
        }
    }

    function removeImage() {
        const input = document.getElementById('imageInput');
        const preview = document.getElementById('imagePreview');
        const info = document.getElementById('uploadInfo');
        const removeBtn = document.querySelector('.remove-preview');

        input.value = '';
        preview.innerHTML = '';
        preview.classList.add('d-none');
        info.style.display = 'block';
        removeBtn.classList.add('d-none');

        document.getElementById('removeImageInput').value = "1";
    }
</script>
@endsection