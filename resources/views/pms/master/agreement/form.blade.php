@extends('pms.layouts.app')
@section('content')
    <style>
        .pdf-btn {
            padding: 10px 25px;
            border-radius: 15px;
            text-decoration: none;
        }
    </style>
    <section class="section">
        <div class="container-fluid">
            <div class="title">
                <div class="row gx-2 align-items-center">
                    <div class="col">
                        <h1 class="fs-5 mb-0">
                            @if ($detail)
                                Modify Agreement
                            @else
                                Add New Agreement
                            @endif
                        </h1>
                    </div>
                    <div class="col-auto">
                        <a href="{{ route('pms.agreement.list') }}" class="btn d-flex btn-small rounded-2 btn-secondary">
                            <i class="material-symbols-outlined me-1">list</i><span>Go to List</span>
                        </a>
                    </div>
                </div>
            </div>
            <form action="{{ route('pms.agreement.save') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @if (isset($detail))
                    <input type="hidden" name="id" id="id" value="{{ $detail->id }}">
                @endif

                <div class="row">
                    <div class="col-12 col-xl-6">
                        <label for="title" class="form-label">Title<sup>*</sup></label>
                        <input type="text" name="title" id="title"
                            class="form-control @error('title') is-invalid @enderror"
                            value="{{ old('title', $detail->title ?? '') }}">
                    </div>

                    <div class="col-12 col-xl-6">
                        <div class="row">
                            <div class="col-12 col-xl-10 d-flex flex-column">
                                <label for="file_pdf" class="form-label">Upload Agreement PDF<sup>*</sup></label>

                                <input type="file" class="form-control @error('file_pdf') is-invalid @enderror"
                                    id="file_pdf" name="file_pdf" accept="application/pdf" onchange="previewPDF(this)">

                                @if (isset($detail) && $detail->file_pdf)
                                    <div class="mt-3">
                                        <label>Current PDF:</label>
                                        <a href="{{ asset('storage/' . $detail->file_pdf) }}" class="pdf-btn text-white "
                                            style="font-weight: 500; background:#C79F62;" download><i
                                                class="bi bi-download"></i>
                                            Download PDF
                                        </a>
                                    </div>
                                @endif
                            </div>

                            <div class="col-12 col-xl-2 mt-4">
                                <button type="submit" class="btn btn-primary py-2">Save</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mb-3" id="pdf-preview-box" style="display: none;">
                    <label>PDF Preview:</label>
                    <iframe id="pdf-preview" src="" width="100%" height="400px" style="border: none;"></iframe>
                </div>
            </form>
        </div>
    </section>
    <script>
        function previewPDF(input) {
            const file = input.files[0];
            if (file && file.type === "application/pdf") {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewBox = document.getElementById("pdf-preview-box");
                    const iframe = document.getElementById("pdf-preview");
                    iframe.src = e.target.result;
                    previewBox.style.display = "block";
                };
                reader.readAsDataURL(file);
            } else {
                // Reset preview if no valid PDF is selected
                const previewBox = document.getElementById("pdf-preview-box");
                const iframe = document.getElementById("pdf-preview");
                iframe.src = "";
                previewBox.style.display = "none";
            }
        }
    </script>
@endsection
