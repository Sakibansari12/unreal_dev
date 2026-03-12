<?php
    $appVersion = "1.0.0";
    $pagename = strtolower(basename($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unreal Estate</title>
     <link rel="shortcut icon" type="image/png" href="{{ asset('assets/pms/images/favicon.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/website/css/app.css') }}?v=<?php echo $appVersion; ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<style>
.form-section .form-submit {
    background-color: #000;
    color: #fff;
    border-radius: 10px;
    width: 100%;
}
</style>
<body>
   <div class="wrapper clearfix">
        <main class="main clearfix">
            <div class="header-wrapper">
                @include('website.layouts.landing.header') 
                   @yield('content')  
                @include('website.layouts.landing.footer') 
                <footer class="landing-footer">
                    <div class="container-fluid">
                        <div class="row justify-content-between align-items-center">
                            <div class="col">
                                <span>© 2026 Unreal Estate | Designed by <a href="https://iws.in/">IWS</a></span>
                            </div>

                            <div class="col-auto">
                            <a href="javascript:void(0);" class="back-to-top"><span class="icon-arrow-thin-up"></span> <b>Back to top</b></a>
                            </div>
                        </div>
                    </div>
                </footer>
            </div>
        </main>
        
    </div>
    
<!--<div class="contact-Option">   -->
<!--         <a href="javascript:void(0);" class="call-desktop d-none d-lg-flex" data-fancybox data-src="#contact-form">-->
<!--            <img src="{{ asset('assets/pms/images/contact.png') }}" alt="">-->
            <!-- <span class="text-white ">CALL</span> -->
<!--        </a>-->
       
<!--        <a href="tel:+918080816490" class="call-desktop d-none d-lg-flex" target="_blank">-->
<!--            <svg width="800px" height="800px" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M3.833 4h4.49L9.77 7.618l-2.325 1.55A1 1 0 0 0 7 10c.003.094 0 .001 0 .001v.021a2.026 2.026 0 0 0 .006.134c.006.082.016.193.035.33.039.27.114.642.26 1.08.294.88.87 2.019 1.992 3.141 1.122 1.122 2.261 1.698 3.14 1.992.439.146.81.22 1.082.26a4.43 4.43 0 0 0 .463.04l.013.001h.008s.112-.006.001 0a1 1 0 0 0 .894-.553l.67-1.34 4.436.74v4.32c-2.111.305-7.813.606-12.293-3.874C3.227 11.813 3.527 6.11 3.833 4zm5.24 6.486l1.807-1.204a2 2 0 0 0 .747-2.407L10.18 3.257A2 2 0 0 0 8.323 2H3.781c-.909 0-1.764.631-1.913 1.617-.34 2.242-.801 8.864 4.425 14.09 5.226 5.226 11.848 4.764 14.09 4.425.986-.15 1.617-1.004 1.617-1.913v-4.372a2 2 0 0 0-1.671-1.973l-4.436-.739a2 2 0 0 0-2.118 1.078l-.346.693a4.71 4.71 0 0 1-.363-.105c-.62-.206-1.481-.63-2.359-1.508-.878-.878-1.302-1.739-1.508-2.36a4.59 4.59 0 0 1-.125-.447z" fill="#fff"/></svg>-->
            <!-- <span class="text-white ">CALL</span> -->
<!--        </a>-->
<!--        <a href="https://wa.me/918080816490?text=Send a quote" class="whatsapp-links d-none d-lg-flex" target="_blank">-->
<!--            <svg xmlns="http://www.w3.org/2000/svg" width="78" height="78" viewBox="0 0 78 78">-->
<!--                <g id="Group_23" data-name="Group 23" transform="translate(-1809 -798)">-->
<!--                    <path id="Path_79" data-name="Path 79" d="M39,0A39,39,0,1,1,0,39,39,39,0,0,1,39,0Z" transform="translate(1809 798)" fill="#26a800" />-->
<!--                    <path id="whatsapp-svgrepo-com" d="M41.515,10.4A21.653,21.653,0,0,0,26.068,4,21.9,21.9,0,0,0,7.089,36.8L4,48.135,15.586,45.1A21.854,21.854,0,0,0,41.515,10.4ZM26.068,44.08A18.149,18.149,0,0,1,16.8,41.543l-.662-.414L9.269,42.949l1.821-6.7-.441-.69a18.224,18.224,0,1,1,33.791-9.682A18.371,18.371,0,0,1,26.068,44.08Zm9.958-13.627c-.552-.276-3.227-1.6-3.724-1.765s-.883-.276-1.241.276a24.837,24.837,0,0,1-1.738,2.124c-.3.386-.634.414-1.186,0a14.7,14.7,0,0,1-7.42-6.482c-.579-.965.552-.91,1.6-2.979a1.048,1.048,0,0,0,0-.965c0-.276-1.241-2.979-1.683-4.055s-.883-.91-1.241-.938H18.316a1.958,1.958,0,0,0-1.462.69,6.041,6.041,0,0,0-1.821,4.662,10.537,10.537,0,0,0,2.234,5.655,24.522,24.522,0,0,0,9.351,8.275,10.622,10.622,0,0,0,6.565,1.379,5.517,5.517,0,0,0,3.669-2.593,4.468,4.468,0,0,0,.331-2.593A3.852,3.852,0,0,0,36.026,30.454Z" transform="translate(1823 809)" fill="#fff" />-->
<!--                </g>-->
<!--            </svg>-->
<!--        </a>-->
<!--</div>-->



<!--<div class="hide-form" id="contact-form" style="display: none; max-width:800px;">-->
<!--    <div class="container">-->
<!--        <div class="row">-->
<!--             <div class="col-12">-->
<!--                <form class="form-section px-3 py-3 rounded">-->
<!--                    <h3 class="text-dark py-3">Contact Us</h3>-->
<!--                    <div class="row g-4">-->
<!--                        <div class="col-12 col-md-6">-->
<!--                            <input type="text" name="first_name" id="first_name"-->
<!--                                class="form-control"-->
<!--                                placeholder="First Name*">-->
<!--                            <span class="invalid-feedback"></span>-->
<!--                        </div>-->
<!--                        <div class="col-12 col-md-6">-->
<!--                            <input type="text" name="last_name" id="last_name"-->
<!--                                class="form-control" placeholder="Last Name*">-->
<!--                            <span class="invalid-feedback"></span>-->
<!--                        </div>-->
<!--                        <div class="col-12 col-md-6">-->
<!--                            <input type="text" name="email" id="email"-->
<!--                                class="form-control" placeholder="Email Address*">-->
<!--                            <span class="invalid-feedback"></span>-->
<!--                        </div>-->
<!--                         <div class="col-12 col-md-6">-->
<!--                        <div class="row form-group d-flex gap-1">-->
<!--                            <div class="col-auto" style="padding-right:0;">-->
<!--                             <select name="country_code" id="country_code" class="form-select code" style="width:100px;">-->
<!--                                          <option value="">+91-->
                                              
<!--                                            </option>-->
<!--                                                 </select>-->
<!--                                       </div>-->
<!--                                       <div class="col" style="padding-left:0;">-->
<!--                                       <input type="text" name="phone_number" class="form-control" placeholder="Phone Number*">-->
<!--                                      </div>-->
<!--                                 </div>-->
<!--                         </div>-->
<!--                        <div class="col-12">-->
<!--                            <textarea name="message" id="message" placeholder="Message*"-->
<!--                                class="form-control" rows="4"></textarea>-->
<!--                            <span class="invalid-feedback"></span>-->
<!--                        </div>-->
<!--                        <div class="col-5 col-sm">-->
<!--                            <input type="text" name="captcha" id="captcha"-->
<!--                                class="form-control" placeholder="Captcha*">-->
<!--                            <span class="invalid-feedback"></span>-->
<!--                        </div>-->
<!--                        <div class="col-5 col-sm-auto captcha">-->
<!--                            <span id="show_captcha" class="text-dark">91Abc</span>-->
<!--                            <a href="#" id="refreshCaptcha">-->
<!--                                <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36"-->
<!--                                    viewBox="0 0 24 24" fill="#e41f26">-->
<!--                                    <path d=" M17.65 6.35A8 8 0 1 0 19 12h-2a6 6 0 1 1-1.76-4.24L12-->
<!--                                10h8V2l-2.35 2.35z" />-->
<!--                                </svg>-->
<!--                            </a>-->
<!--                        </div>-->
<!--                        <div class="col-2 col-sm-auto">-->
<!--                        </div>-->
<!--                        <div class="col-12">-->
<!--                            <div id="success-message" class="alert alert-success d-none" role="alert">-->
<!--                               Thank you! Your details have been submitted successfully. Our team will get in touch with you shortly.-->
<!--                            </div>-->
<!--                            <button type="submit" class="btn form-submit">Submit</button>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </form>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
<!--</div>-->
    
    
    
    <script src="{{ asset('assets/website/js/app.js') }}?v=1.0.0"></script>
    <script src="{{ asset('assets/website/js/hamburger.js') }}?v=1.0.0"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
        Fancybox.bind('[data-fancybox]', {});

        Fancybox.bind("[data-custom-fancy]", {
            hideScrollbar: true,
            closeButton: false,
        })
        })
    </script>
    
    </body>
    </html>