<style>
    .list-publish{
        font-size: 16px;
    }
</style>

<div class="content-box p-3">
    <div class="row g-5">
        <div class="col-12">
            <div class="form-box">
                <div class="row">
                    <div class="col">
                       <ul class="list-unstyled list-publish">
                        <li class="d-flex align-items-center gap-3"><i class="bi bi-check-lg" style="color: green"></i> Overview </li>

                        @if($detail->isAmmenitiesUploaded <10)
                        <li class="d-flex align-items-center gap-3"><i class="bi bi-x" style="color: red"></i> Amenities</li>
                        @else
                        <li class="d-flex align-items-center gap-3"><i class="bi bi-check-lg" style="color: green"></i> Amenities</li>
                        @endif

                        @if($detail->isGalleryUploaded <10)
                        <li class="d-flex align-items-center gap-3"><i class="bi bi-x" style="color: red"></i> Images</li>
                        @else
                        <li class="d-flex align-items-center gap-3"><i class="bi bi-check-lg" style="color: green"></i> Images</li>
                        @endif

                        @if($detail->isCancellationSlabUploaded <10)
                        <li class="d-flex align-items-center gap-3"><i class="bi bi-x" style="color: red"></i> Cancellation Slab</li>
                        @else
                        <li class="d-flex align-items-center gap-3"><i class="bi bi-check-lg" style="color: green"></i> Cancellation Slab</li>
                        @endif

                        @if($detail->isRoomSpecificAmmenitiesUploaded <10)
                        <li class="d-flex align-items-center gap-3"><i class="bi bi-x" style="color: red"></i> Room Specific Amenities</li>
                        @else
                        <li class="d-flex align-items-center gap-3"><i class="bi bi-check-lg" style="color: green"></i> Room Specific Amenities</li>
                        @endif

                        <li class="d-flex align-items-center gap-3"><i class="bi bi-check-lg" style="color: green"></i> Price (Up To 6 months)</li>
                        <li class="d-flex align-items-center gap-3"><i class="bi bi-check-lg" style="color: green"></i> Availability (Up To 6 months)</li>
                        <li class="d-flex align-items-center gap-3"><i class="bi bi-check-lg" style="color: green"></i> Min stay (Up To 6 months)</li>
                       </ul>
                    </div>
                    <div class="action-buttons text-end">
                        <button type="button" class="btn d-inline-flex btn-small rounded-2 btn-primary website-publish-property"  data-value="{{ $detail->id }}">
                            <span  class="btn-text">Proceed</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
