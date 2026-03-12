@extends('pms.layouts.app')
@section('content')
<section class="section">
    <div class="container-fluid">
       <div class="title">
          <div class="row align-items-center">
              <div class="col">
                 <h1 class="fs-5 mb-0">Amenities List</h1>
              </div>
              <div class="col-auto">
                <!-- Add button placeholder -->
              </div>
          </div>
       </div>

        <div class="content-box p-3">
            <form method="GET" action="{{ url('pms/ru-amenities/list') }}">
                <div class="row mb-3 g-3 flex-sm-row-reverse align-items-center">
                    <div class="col-sm-6 col-md-5 col-lg-4">
                        <div class="input-group input-group-sm">
                            <input type="text" name="search_amenitie" class="form-control" placeholder="Search by amenity." value="{{ request('search_amenitie') }}">
                            <button type="submit" class="btn btn-primary">
                                <i class="icon-search"></i>  <!-- Using an icon here -->
                            </button>
                            <a href="{{ route('pms.ru_amenities.list') }}" class="btn btn-warning">
                                
                                <span class="material-symbols-outlined">refresh</span>
                            </a>
                        </div>
                    </div>
                    <div class="col">
                        <div class="data-info text-primary">
                            {{ $items->total() }} Result{{ $items->total() == 1 ? '' : 's' }} found
                        </div>
                    </div>
                </div>
            </form>

            <div class="table-responsive data-table text-nowrap">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Amenity</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($items as $key => $value)
                        <tr>
                            <td>{{ $value->amenities_name }}</td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input btn_status" type="checkbox" role="switch" data-value="{{ $value->amenities_id }}" {{ $value->active ? 'checked' : '' }}>
                                </div>
                            </td>
                        </tr> 
                        @empty
                        <tr>
                            <td class="text-center" colspan="2">No Record Found!</td>
                        </tr> 
                        @endforelse              
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $items->links('vendor.pagination.bootstrap-5') }}
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function(){
        $('.btn_status').on('click', function(){
            let id  = $(this).data("value");
            $.ajax({
                url: '{{ url("pms/ru-amenities/toggle-status") }}/' + id,
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
