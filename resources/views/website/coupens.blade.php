@extends('website.layouts.app')
@section('content')

<style>
    .couponCopyBtn{
        padding:10px 20px;
        color:#fff;
        background:#c79f62;
        border:0;
        border-radius:12px;
    }
</style>

<section class="section offers-section">
    <div class="container">
        <div class="section-heading">
            <div class="row gy-3">
                <div class="col-12 col-lg">
                    <h2>Special Offers</h2>
                </div>
            </div>
        </div>

        <div class="row gy-4">
            @forelse($specialOffers as $offer)
                <div class="col-12 col-xl-4">
                    <div class="card card-offers h-100">
                        <div class="card-body">
                            <div class="row align-items-center">
                                <div class="col">
                                    <h3>{{ $offer->offer_name }}</h3>
                                </div>
                                <div class="col-auto">
                                    <div class="card-offer-img">
                                        <img loading="lazy" 
                                             width="80" 
                                             height="80"
                                             src="{{ $offer->image_path ?: asset('assets/website/images/image.jpg') }}"
                                             alt="{{ $offer->offer_name }}">
                                    </div>
                                </div>
                            </div>
                            <p>{!! $offer->description !!}</p>
                        </div>
                        <div class="card-footer">
                            <div class="row gy-2 gx-3 align-items-center">
                                <div class="col-12 col-xxl-7">
                                    <div class="offer-code">
                                        <span>{{ $offer->couponCode->coupon_code ?? 'N/A' }}</span>
                                        <button class="btn-primary couponCopyBtn" data-clipboard-text="{{ $offer->couponCode->coupon_code ?? '' }}"
                                        data-bs-toggle="tooltip" data-bs-placement="top" title="Copy"
                                        
                                        ><i class="icon-copy"></i> Copy</button>
                                        
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="exp-text">
                                        Expires on <strong>{{date('d M, Y',strtotime($offer->validity))  }}</strong>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <p class="text-center">No special offers available at this time.</p>
                </div>
            @endforelse
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        });

        $(".offer-code .btn").each(function () {
            var clipboard = new ClipboardJS(this);
            clipboard.on('success', function (e) {
                const btn = e.trigger;
                const tooltip = bootstrap.Tooltip.getInstance(btn);
                tooltip.setContent({ '.tooltip-inner': 'Copied!' });
                tooltip.show();

                setTimeout(() => {
                    tooltip.setContent({ '.tooltip-inner': 'Copy' });
                }, 1000);
                e.clearSelection();
            });
        });
    });
</script>


@endsection