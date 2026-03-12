@php

$home_footer_banners = getHomeFooterBanners();

@endphp

<section class="section section-info bg-accent-3 section-paddy-70 d-none">

    <div class="container">

        <div class="row gy-4 gx-5 justify-content-between">

            <div class="col-12 col-lg-8 col-xxl-7">

                

            </div>

            <div class="col-12 col-lg-4 col-xl-auto">

                <div class="content max-width-320">

                    <ul class="list-unstyled m-0">

                        @foreach($home_footer_banners as $footer_banner)
                        @foreach($footer_banner->list_content ?? [] as $list)
                        <li>{{ is_array($list) ? ($list['list_content'] ?? '') : $list }}</li>
                        @endforeach
                        @endforeach


                    </ul>

                </div>

            </div>

            @if(!checkAuth())
            <div class="col-12 col-lg-6">

                <div class="content text-darkgray">

                    @foreach($home_footer_banners as $footer_banner)

                    <p>{!! isset($footer_banner->sub_title) ? $footer_banner->sub_title : '' !!}</p>

                    @endforeach

                    <p></p>

                </div>

                <div class="newsletter-subscribe-box">
                    <form action="" id="subscribecreate" method="POST">
                        <div class="row g-4">
                            <div class="col">
                                <input type="text" name="email" id="email" class="form-control rounded-pill" placeholder="Enter your email" style="height:44px;padding-left: 18px;">
                                <p></p>
                                <span class="subscribe-button"></span>
                            </div>
                            <div class="col-auto">
                                <button type="submit" id="SubscribeButton" style="height:44px;" class="btn btn-primary">Subscribe <i class="bi icon-chevron-right"></i></button>

                            </div>

                        </div>
                    </form>
                </div>

            </div>
            @endif

        </div>

    </div>

</section>


<footer class="footer-main section">
    <div class="container">
        <div class="row gy-4">
            <div class="col-12 col-lg pe-lg-5 me-lg-5">
                <div class="row g-4">
                    <div class="col-12 col-lg-auto">
                        <div class="footer-logo">
                            <img src="{{ asset('assets/website/images/footer-logo.svg') }}" alt="Unreal Estate">
                        </div>
                    </div>
                    <div class="col-12 col-lg">
                        @foreach($home_footer_banners as $footer_banner)
                        <div class="fTitle">{!! isset($footer_banner->title) ? $footer_banner->title : '' !!}</div>
                        @endforeach
                        <!-- <div class="fTitle">
                            <p>Unreal Estate is a boutique hospitality brand for beautifully managed short-term stays.</p>
                            <p>Redefining hospitality across India.</p>
                        </div> -->
                        <div class="social-media">
                            <ul>
                                <li>
                                    <a href="https://www.instagram.com/_unreal.estate_?igsh=dDh1cnd5dDdranZh&utm_source=qr" target="_blank"><img src="{{ asset('assets/website/images/icon-instagram.svg') }}" alt="Instagram"></a>
                                </li>
                                <li>
                                    <a href="https://www.linkedin.com/company/unreal-estate1/" target="_blank"><img src="{{ asset('assets/website/images/icon-linkedin.svg') }}" alt="LinkedIn"></a>
                                </li>
                                <li>
                                    <a href="https://wa.me/918469528931" target="_blank"><img src="{{ asset('assets/website/images/icon-whatsapp.svg') }}" alt="WhatsApp"></a>
                                </li>
                                <li>
                                    <a href="mailto:princy@unrealestate.com"><img src="{{ asset('assets/website/images/icon-email.svg') }}" alt="Email Unreal Estate"></a>
                                </li>
                            </ul> 
                        </div>                   
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-5 ps-xl-5">
                <div class="footer-link footer-link-wrapper">
                    <div class="row gy-3">
                        <div class="col-6">
                            <ul>                                
                                <li>
                                    <a href="{{route('customer-support')}}">Customer Support</a>
                                </li>
                                <li>
                                    <a href="{{route('cancellation_refund')}}">Cancellation Policy</a>
                                </li>
                                <li>
                                    <a href="{{route('corporate-bookings')}}">Corporate Bookings</a>
                                </li>
                            </ul>
                        </div>
                        <div class="col-6">
                            <ul>
                                <li>
                                    <a href="{{route('about.us')}}">About Us</a>
                                </li>
                                <li>
                                    <a href="{{route('careers')}}">Careers</a>
                                </li>
                                <li>
                                    <a href="{{route('privacy.policy')}}">Privacy Policy</a>
                                </li>
                                <!-- <li>
                                    <a href="{{route('terms.condition')}}">Terms of Use</a>
                                </li>
                                <li>
                                    <a href="{{route('cookie-policy')}}">Cookie Policy</a>
                                </li> -->
                            </ul>
                        </div>
                        
                    </div>
                    <div class="copyright">
                        &copy; <?php echo date('Y'); ?> <b>Unreal Estate</b> | Developed by <a href="https://iws.in" target="_blank">IWS</a>
                    </div>
                </div>
            </div>
           
        </div>
    </div>
    
</footer>


</main>

</div>







<div class="section-fancybox half-fancybox mxw-600" id="coupon" style="display: none;">

    <div class="row">

        <div class="col-12">

            <div class="fancy-heading">

                <div class="row">

                    <div class="col">

                        <h3>Apply Promo Code</h3>

                    </div>

                    <div class="col-auto">

                        <a href="javascript:void(0)" class="fancy-close" onclick="Fancybox.close()">

                            <i class="icon-close"></i>

                        </a>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-12">

            <form action="">

                <div class="row g-4">

                    <div class="col-12">

                        <div class="form-group cs-input">

                            <label for="">Enter your promo code</label>

                            <input type="text" class="form-control">

                            <span class="error justify-content-center">Incorrect promo code</span>

                        </div>

                    </div>

                    <div class="col-12">

                        <div class="form-group cs-input bottom-toolbar">

                            <div class="row align-items-center">

                                <div class="col-auto d-md-none">

                                    <a href="javascript:void(0)" class="clear-btn">Clear</a>

                                </div>

                                <div class="col text-end">

                                    <button type="submit" class="btn btn-primary icon-link icon-link-hover w-md-100" disabled>Apply <span class="d-none d-md-block"><i class="bi icon-chevron-right"></i></span></button>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </form>

        </div>

    </div>

</div>








<meta name="csrf-token" content="{{ csrf_token() }}">
<script src="{{ asset('assets/website/js/app.js') }}"></script>
<script src="{{ asset('assets/website/js/hamburger.js') }} "></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.js"></script>

<script defer>
    $('#SubscribeButton').on('click', function(e) {
        e.preventDefault();
        let formdata = $('#subscribecreate').serialize();
        $.ajax({
            url: "{{ route('subscribe-store') }}",
            type: "POST",
            data: formdata,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function(response) {
                if (response['status'] == true) {
                    $('#subscribecreate')[0].reset();
                    $('.subscribe-button').html('<span class="text-success">You have successfully subscribed!</span>');
                    setTimeout(function() {
                        $('.subscribe-button').fadeOut('slow', function() {
                            $(this).html('').show();
                        });
                    }, 5000);
                } else {
                    var errors = response['errors'];
                    if (errors) {
                        $.each(errors, function(key, value) {
                            var elementId = key.replace(/\./g, '_');
                            var inputElement = $('#' + elementId);
                            inputElement.next('p').addClass('text-danger').html(value[0]);

                            setTimeout(function() {
                                inputElement.next('p').fadeOut('slow', function() {
                                    $(this).html('').show(); // Clear the error and reset visibility
                                });
                            }, 5000);
                        });
                    }
                }
            },
            error: function(xhr, status, error) {
                console.error("AJAX request failed:", error);
            }
        });
    });
    $(document).ready(function() {
        $('#email').on('input', function() {
            $('#email').removeClass('border-danger').html('');
        });
    });
</script>

<script defer>
    document.addEventListener('DOMContentLoaded', () => {

        $(document).on('click', '[data-small-search]', function(e) {
            let dataType = $(e.target).data('type')
            setTimeout(() => {
                if (dataType == 'search') {
                    $(`[data-search-id="location"]`).trigger('click')
                } else {
                    if (dataType == 'dates') {
                        $(`[data-search-id="${dataType}"]`).first().trigger('click')
                    } else {
                        $(`[data-search-id="${dataType}"]`).trigger('click')
                    }
                }
            }, 100)
        })

        Fancybox.bind("[data-custom-fancy]", {

            hideScrollbar: true,

            closeButton: false,

        })

    })
</script>

@if(session('login_success'))
<script defer>
    document.addEventListener('DOMContentLoaded', function() {
        // Parse the query parameters
        swal({
            title: 'Login Successful',
            text: "",
            type: "success",
            showCancelButton: false,
            confirmButtonText: "OK",
            closeOnConfirm: true
        });
    });
</script>
@endif
<script>
    /* $(document).ready(function() {
        let selectedAreasC = [];
        $(document).on('click', '.area-filter-alender', function() {
            const areaId = $(this).data('area-id');
            console.log(areaId, 'areaId');
            const index = selectedAreasC.indexOf(areaId);
            if (index === -1) {
                selectedAreasC.push(areaId);
                $(this).addClass('active');
            } else {
                selectedAreasC.splice(index, 1);
                $(this).removeClass('active');
            }
            
        });
    }); */
</script>
</body>

</html>