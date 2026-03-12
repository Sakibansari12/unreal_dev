<style>
    .ulTab {
        list-style-type: none;
        margin: 0;
        padding: 0;
        overflow-x: auto
    }

    @media (max-width: 991.98px) {
        .ulTab {
            display: -webkit-box;
            display: -ms-flexbox;
            display: flex
        }
    }

    .ulTab li {
        margin: 10px 5px
    }

    .ulTab li button,
    .ulTab li a {
        border: 0px;
        padding: 10px 15px;
        background-color: #fff;
        width: 100%;
        border-radius: 6px !important;
        text-align: left;
        border: 1px solid #0E0E0E;
        display: block;
        text-decoration: none
    }

    .ulTab li button.active,
    .ulTab li a.active {
        border: 0px;
        padding: 10px 15px;
        color: #fff;
        background-color: #0e0e0e
    }

    .ulTab li button[disabled],
    .ulTab li a[disabled] {
        opacity: 1;
        color: #000
    }

    @media (max-width: 991.98px) {

        .ulTab li button,
        .ulTab li a {
            white-space: nowrap
        }
    }

    .ulTab {
        display: block !important;
        /* Ensure the menu is always visible */
    }
</style>

<div class="col-12 col-lg-3">
    <ul class="ulTab">
        <li class="list-group-item">
            <a href="{{ route('pms.property.unit.or.multiunit.overview', ['id'=>$id, 'type'=>request()->type, 'pType'=>request()->pType, 'property_id'=>request()->property_id]) }}" class="{{ request()->segment(4) === 'overview' ? 'active' : '' }}">Overview</a>
        </li>
        <li class="list-group-item">
            <a href="{{ route('pms.property.unit.or.multiunit.amenities',['id'=>$id, 'type'=>request()->type, 'pType'=>request()->pType, 'property_id'=>request()->property_id]) }}" class="{{ request()->segment(4) === 'amenities' ? 'active' : '' }}">Amenities</a>
        </li>
        <li class="list-group-item">
            <a href="{{ route('pms.property.unit.or.multiunit.websiteamenities',['id'=>$id, 'type'=>request()->type, 'pType'=>request()->pType, 'property_id'=>request()->property_id]) }}" class="{{ request()->segment(4) === 'websiteamenities' ? 'active' : '' }}">Website Amenities</a>
        </li>
        <li class="list-group-item">
            <a href="{{ route('pms.property.unit.or.multiunit.additionalcharges',['id'=>$id, 'type'=>request()->type, 'pType'=>request()->pType, 'property_id'=>request()->property_id]) }}" class="{{ request()->segment(4) === 'additionalcharges' ? 'active' : '' }}">Additional Charges
            </a>
        </li>
        <li class="list-group-item">
            <a href="{{ route('pms.property.unit.or.multiunit.gallery',['id'=>$id, 'type'=>request()->type, 'pType'=>request()->pType, 'property_id'=>request()->property_id]) }}" class="{{ request()->segment(4) === 'gallery' ? 'active' : '' }}">Gallery</a>
        </li>
        <li class="list-group-item">
            <a href="{{ route('pms.property.unit.or.multiunit.video',['id'=>$id, 'type'=>request()->type, 'pType'=>request()->pType, 'property_id'=>request()->property_id]) }}" class="{{ request()->segment(4) === 'video' ? 'active' : '' }}">Video</a>
        </li>
        @php
        $user = Auth::guard('admin')->user();
        @endphp
        @if($user->role == 'Super Admin')
        <li class="list-group-item">
            <a href="{{ route('pms.property.unit.or.multiunit.review',['id'=>$id, 'type'=>request()->type, 'pType'=>request()->pType, 'property_id'=>request()->property_id]) }}" class="{{ request()->segment(4) === 'review' ? 'active' : '' }}">Reviews</a>
        </li>
        @endif
        <li class="list-group-item">
            <a href="{{ route('pms.property.unit.or.multiunit.commas',['id'=>$id, 'type'=>request()->type, 'pType'=>request()->pType, 'property_id'=>request()->property_id]) }}" class="{{ request()->segment(4) === 'commas' ? 'active' : '' }}">Comms</a>
        </li>
        <li class="list-group-item">
            <a href="{{ route('pms.property.unit.or.multiunit.cancellationslab',['id'=>$id, 'type'=>request()->type, 'pType'=>request()->pType, 'property_id'=>request()->property_id]) }}" class="{{ request()->segment(4) === 'cancellationslab' ? 'active' : '' }}">Cancellation Slab</a>
        </li>
        <li class="list-group-item">
            <a href="{{ route('pms.property.unit.or.multiunit.floor',['id'=>$id, 'type'=>request()->type, 'pType'=>request()->pType, 'property_id'=>request()->property_id]) }}" class="{{ request()->segment(4) === 'floor' ? 'active' : '' }}">Room Specific Amenity </a>
        </li>

        <li class="list-group-item">
            <a href="{{ route('pms.property.unit.or.multiunit.websitefaq',['id'=>$id, 'type'=>request()->type, 'pType'=>request()->pType, 'property_id'=>request()->property_id]) }}" class="{{ request()->segment(4) === 'websitefaq' ? 'active' : '' }}">FAQ
            </a>
        </li>

        <!-- <li class="list-group-item">
            <a href="{{ route('pms.property.unit.or.multiunit.layoutimages',['id'=>$id, 'type'=>request()->type, 'pType'=>request()->pType, 'property_id'=>request()->property_id]) }}" class="{{ request()->segment(4) === 'layoutimages' ? 'active' : '' }}">Layout Images</a>
        </li> -->

        <li class="list-group-item">
            <a href="{{ route('pms.property.unit.or.multiunit.tag',['id'=>$id, 'type'=>request()->type, 'pType'=>request()->pType, 'property_id'=>request()->property_id]) }}" class="{{ request()->segment(4) === 'tag' ? 'active' : '' }}">Tags</a>
        </li>
        <li class="list-group-item">
            <a href="{{ route('pms.property.unit.or.multiunit.icon',['id'=>$id, 'type'=>request()->type, 'pType'=>request()->pType, 'property_id'=>request()->property_id]) }}" class="{{ request()->segment(4) === 'icon' ? 'active' : '' }}">Important Information</a>
        </li>

    </ul>
</div>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const submitBtn = document.getElementById('submitBtn');
    const btnText = document.getElementById('btnText');
    const btnLoader = document.getElementById('btnLoader');

    if (form) {
        form.addEventListener('submit', function () {
            submitBtn.disabled = true;
            btnText.textContent = 'Submitting...';
            btnLoader.classList.remove('d-none');
        });
    }
});
</script>
