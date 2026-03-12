@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container-fluid">
       <div class="title">
          <div class="row align-items-center">
              <div class="col">
                 <h1 class="fs-5 mb-0">GST Setting</h1>
              </div>
              <div class="col-auto">
                <!-- Add button placeholder -->
              </div>
          </div>
       </div>
       <div class="content-box p-3">
            <div class="form-box">
                <div class="row">
                    <div class="col-12">
                        <div class="form-field d-flex align-items-center">
                            <input type="checkbox" id="cbk-gst" data-value="{{ $items->id }}" name="toggleStatus" 
                                   class="gst-checkbox btn_status" {{ $items->is_allow_gst ? 'checked' : '' }}>
                            <label for="cbk-gst" class="gst-label">Display Price Including GST</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
   </div>
</section>
<style>
.gst-checkbox {
    width: 22px;
    height: 22px;
    margin-right: 10px;
    cursor: pointer;
}

.gst-label {
    font-weight: bold; 
    font-size: 16px;
    color: #666;
}
</style>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        $('.btn_status').on('click', function(){
            let id  = $(this).data("value");
            $.ajax({
                url: '{{ url("pms/gst/setting/toggle-status") }}/' + id,
                type: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                },
                success: function (response) {
                    toastr.success(response.message, 'Success', {
                        timeOut: 2000,
                        positionClass: 'toast-top-right'
                    });
                },
                error: function () {
                    toastr.error('Something went wrong!', 'Error', {
                        timeOut: 2000,
                        positionClass: 'toast-top-right'
                    });
                }
            });
        });
    })
</script>
@endsection