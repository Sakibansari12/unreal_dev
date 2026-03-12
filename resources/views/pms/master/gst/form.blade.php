@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container-fluid">
        <div class="title">
            <div class="row gx-2 align-items-center">
                <div class="col">
                     <h1 class="fs-5 mb-0">GST</h1>
                </div>
            </div>
        </div>
        <form id="gstForm" style="display: none;">
            <div class="table-responsive mb-3">
                <table class="table table-list mb-0 mw-lg">
                    <thead>
                        <tr>
                            <th>Start Tariff / Night</th>
                            <th>Upto Tariff / Night</th>
                            <th>Percentage %</th>
                            <th class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="gst-slabs-wrapper"></tbody>
                </table>
            </div>

            <div class="table-footer pt-3">
                <button type="button" class="btn btn-secondary btn-sm" id="addMoreBtn">+ Add More</button>
            </div>

            <div class="table-footer pt-4">
                <button type="submit" class="btn btn-primary btn-sm">SUBMIT
                </button>
            </div>
        </form>
    </div>
</section>


<script>
    let slabIndex = 0;

    function renderSlabRow(data = {}) {
        const start = data.slabs_start || 1;
        const upto = data.slabs_upto || '';
        const gst = data.gst_percentage || '';

        const row = `
            <tr data-index="${slabIndex}">
                <td><input type="number" name="addMultiItem[${slabIndex}][slabs_start]" class="form-control" value="${start}" readonly></td>
                <td>
                    <input type="number" name="addMultiItem[${slabIndex}][slabs_upto]" class="form-control slabs-upto" value="${upto}">
                </td>
                <td>
                    <div class="input-group">
                        <input type="number" step="0.01" name="addMultiItem[${slabIndex}][gst_percentage]" class="form-control gst-percentage" value="${gst}">
                        <span class="input-group-text">%</span>
                    </div>
                </td>
                <td class="text-center">
                    ${slabIndex !== 0 ? `<button type="button" class="btn text-danger remove-row"><span class="material-symbols-outlined">delete</span></button>` : ''}
                </td>
            </tr>
        `;
        $('#gst-slabs-wrapper').append(row);
        slabIndex++;
    }

    function getLastUptoValue() {
        const lastRow = $('#gst-slabs-wrapper tr:last');
        return parseInt(lastRow.find('input.slabs-upto').val()) || 0;
    }

    $(document).ready(function () {
        // Load GST slabs
        $.get("{{ route('pms.gst.form') }}", function (res) {
            $('#gst-loader').hide();
            $('#gstForm').show();

            if (res.status && res.data.length) {
                res.data.forEach(row => renderSlabRow(row));
            } else {
                renderSlabRow();
            }
        });

        // Add row
        $('#addMoreBtn').click(function () {
            const lastUpto = getLastUptoValue();
            // console.log(lastUpto);
            if (!lastUpto) {
                alert("Please fill 'Upto Tariff' before adding new row.");
                return;
            }

            const lastRow = $('#gst-slabs-wrapper tr:last');
            const gstVal = lastRow.find('input.gst-percentage').val();
            if (!gstVal) {
                alert("Please fill GST % before adding new row.");
                return;
            }

            renderSlabRow({ slabs_start: lastUpto + 1 });
        });

        // Remove row
        $(document).on('click', '.remove-row', function () {
            $(this).closest('tr').remove();
        });

        //Add CSRF token to all AJAX requests
		$.ajaxSetup({
		    headers: {
		        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
		    }
		});

        // Submit
        $('#gstForm').submit(function (e) {
		    e.preventDefault();

		    const formData = $(this).serialize();

		    $.post("{{ route('pms.gst.save') }}", formData, function (res) {
		        if (res.status) {
	                toastr.success(res.message, 'Success', { timeOut: 1000 });
				    // Reload after 2 seconds
				    setTimeout(() => {
				        location.reload();
				    }, 1000);
		        } else {
	             	toastr.error(res.message || 'Validation failed', 'Error', { timeOut: 2000 });
		        }
		    }).fail(function (err) {
			    if (err.responseJSON && err.responseJSON.errors) {
			        const errors = err.responseJSON.errors;

			        // Remove previous red borders and error texts
			        $('#gstForm input').removeClass('is-invalid');
			        $('#gstForm .invalid-feedback').remove();

			        Object.keys(errors).forEach(function (field) {
			        	const keys = field.split('.');

						let fieldName = "addMultiItem["+keys[1]+"]["+keys[2]+"]";
						const inputSelector = `input[name="${fieldName}"]`;
				       	const inputField = $(inputSelector);
				       	console.log(inputField);
					    if (inputField.length) {
					        inputField.addClass('is-invalid');
					    } else {
					        console.log('No matching input field found for: ' + inputSelector);
					    }
				});

			    }
			});





		});
    });
</script>
@endsection