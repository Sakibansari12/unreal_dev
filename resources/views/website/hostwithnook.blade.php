
@extends('website.layouts.app')
@section('content')
<div class="cms-pages">
    <section class="section flex-wrap section-hero overflow-hidden">
       <div class="container">
            <div class="row g-0">   
                <div class="col-12 col-lg-6 d-flex align-items-center position-relative">
                    <div class="hero-page-content">
                        <div class="card hero-page-card">
                            <div class="card-header">
                                <span class="badge">Property Management Services</span>
                            </div>
                            <div class="card-body">
                                <h1>Host with Unreal Estate</h1>
                                <p>Looking to increase your real estate returns? Host with Unreal Estate, we provide a full property management service. Relax and unwind, we’ve got everything handled for you.</p>
                            </div>
                            <div class="card-footer">
                                <a href="#"  class="btn btn-outline-dark icon-link icon-link-hover">Free Income Estimate <i class="bi icon-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="hero-page-img">
                        <img src="{{ asset('assets/website/images/host-hero.jpg')}}" alt="">
                    </div>
                </div>
            </div>
       </div>
    </section>


    <section class="section section-why">
        <div class="container">
            <div class="title mb-4 mb-lg-5">
                <h2 class="h3 mb-0">Why Unreal Estate for your property?</h2>
            </div>
            <div class="swiper section-why-slider overflow-visible">
                <div class="swiper-wrapper">
                    <div class="swiper-slide">
                        <div class="text-card">
                            <div class="ic-icon">
                                <img loading="lazy" src="{{ asset('assets/website/images/icon-key.svg')}}" alt="">
                            </div>
                            <h3>Unlock Rental Potential</h3>
                            <p>Blending AI with industry expertise, we excel at maximizing sales. Every month, you receive a detailed report of your property’s performance.</p>
                        </div>
                    </div>


                    <div class="swiper-slide">
                        <div class="text-card">
                            <div class="ic-icon">
                                <img  loading="lazy" src="{{ asset('assets/website/images/icon-happy.svg')}}" alt="">
                            </div>
                            <h3>Exceptional Service</h3>
                            <p>Our dedicated team of professionals ensures top-notch service, making sure your guests feel right at home.</p>
                        </div>                        
                    </div>


                    <div class="swiper-slide">
                        <div class="text-card">
                            <div class="ic-icon">
                                 <img  loading="lazy" src="{{ asset('assets/website/images/icon-stars.svg')}}" alt="">
                            </div>
                            <h3>Guest Satisfaction</h3>
                            <p>Hospitality is about empathy. Our dedication to excellence will help you achieve top ratings and positive reviews.</p>
                        </div>
                    </div>

                    <div class="swiper-slide">
                        <div class="text-card">
                            <div class="ic-icon">
                                <img loading="lazy" src="{{ asset('assets/website/images/icon-tech.svg')}}" alt="">
                            </div>
                            <h3>Tech Enabled</h3>
                            <p>We leverage advanced technology and AI to manage your property with precision. Ensures smooth and hassle-free experiences to your guests.</p>
                        </div>
                    </div>
                    

                </div>
            </div>
        </div>
    </section>


    <section class="section section-why pt-lg-0">
        <div class="container">
            <div class="reverse-rows">

                <div class="row">
                    <div class="col-12">
                        <div class="title mb-4">
                            <h2 class="mb-0 h3">Leave the operational details up to us</h2>
                        </div>
                    </div>
                    <div class="col-12 col-lg-auto">
                        <div class="faq-img">
                            <span>
                                <img loading="lazy" src="{{ asset('assets/website/images/faq1.jpg')}}" alt="">
                            </span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="faqs">
                            <ul class="list-unstyled mb-0">
                                <li class="active">
                                    <h3 class="faq-title" role="button">Effortless Maintenance</h3>
                                    <div class="faq-content" style="display: block;">
                                        <p>Our team swiftly addresses all minor maintenance needs to keep your guests happy. Is the AC leaking or the Wi-Fi down? Don’t worry, we’ve got it covered.</p>
                                    </div>
                                </li>
                                <li>
                                    <h3 class="faq-title" role="button">5 Star Housekeeping service</h3>
                                    <div class="faq-content">
                                        <p>Our team swiftly addresses all minor maintenance needs to keep your guests happy. Is the AC leaking or the Wi-Fi down? Don’t worry, we’ve got it covered.</p>
                                    </div>
                                </li>
                                <li>
                                    <h3 class="faq-title" role="button">Linens and Laundry</h3>
                                    <div class="faq-content">
                                        <p>Our team swiftly addresses all minor maintenance needs to keep your guests happy. Is the AC leaking or the Wi-Fi down? Don’t worry, we’ve got it covered.</p>
                                    </div>
                                </li>
                                <li>
                                    <h3 class="faq-title" role="button">Wear and Tear</h3>
                                    <div class="faq-content">
                                        <p>Our team swiftly addresses all minor maintenance needs to keep your guests happy. Is the AC leaking or the Wi-Fi down? Don’t worry, we’ve got it covered.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>                    
                </div>


                <div class="row">
                    <div class="col-12">
                        <div class="title mb-4">
                            <h2 class="mb-0 h3">Never worry about marketing your property</h2>
                        </div>
                    </div>                    
                    <div class="col-12 col-lg-auto">
                        <div class="faq-img">
                            <span>
                                <img loading="lazy" src="{{ asset('assets/website/images/faq2.jpg')}}" alt="">
                            </span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="faqs">
                            <ul class="list-unstyled mb-0">
                                <li class="active">
                                    <h3 class="faq-title" role="button">Comprehensive Design and Décor</h3>
                                    <div class="faq-content" style="display: block;">
                                        <p>When needed, we’ll redesign your interiors to reflect modern trends and enhance sales potential.</p>
                                    </div>
                                </li>
                                <li>
                                    <h3 class="faq-title" role="button">Professional Photoshoot</h3>
                                    <div class="faq-content">
                                        <p>When needed, we’ll redesign your interiors to reflect modern trends and enhance sales potential.</p>
                                    </div>
                                </li>
                                <li>
                                    <h3 class="faq-title" role="button">Listing Creation and Optimization</h3>
                                    <div class="faq-content">
                                        <p>When needed, we’ll redesign your interiors to reflect modern trends and enhance sales potential.</p>
                                    </div>
                                </li>
                                <li>
                                    <h3 class="faq-title" role="button">Online Marketing Strategies</h3>
                                    <div class="faq-content">
                                        <p>When needed, we’ll redesign your interiors to reflect modern trends and enhance sales potential.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-12">
                        <div class="title mb-4">
                            <h2 class="mb-0 h3">We specialise in building & maintaining guest relations</h2>
                        </div>
                    </div>                    
                    <div class="col-12 col-lg-auto">
                        <div class="faq-img">
                            <span>
                                <img loading="lazy" src="{{ asset('assets/website/images/faq3.jpg')}}" alt="">
                            </span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="faqs">
                            <ul class="list-unstyled mb-0">
                                <li class="active">
                                    <h3 class="faq-title" role="button">Seamless Guest Communication</h3>
                                    <div class="faq-content" style="display: block;">
                                        <p>From handling inquiries to sending warm farewell wishes, our team manages every aspect of guest communication, ensuring a smooth experience from start to finish.</p>
                                    </div>
                                </li>
                                <li>
                                    <h3 class="faq-title" role="button">Around the clock service</h3>
                                    <div class="faq-content">
                                        <p>From handling inquiries to sending warm farewell wishes, our team manages every aspect of guest communication, ensuring a smooth experience from start to finish.</p>
                                    </div>
                                </li>
                                <li>
                                    <h3 class="faq-title" role="button">Enhanced Interactions</h3>
                                    <div class="faq-content">
                                        <p>From handling inquiries to sending warm farewell wishes, our team manages every aspect of guest communication, ensuring a smooth experience from start to finish.</p>
                                    </div>
                                </li>
                                <li>
                                    <h3 class="faq-title" role="button">Review Follow-Up</h3>
                                    <div class="faq-content">
                                        <p>From handling inquiries to sending warm farewell wishes, our team manages every aspect of guest communication, ensuring a smooth experience from start to finish.</p>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-12">
                        <div class="title mb-4">
                            <h2 class="mb-0 h3">Hassle-free Revenue Management</h2>
                        </div>
                    </div>                    
                    <div class="col-12 col-lg-auto">
                        <div class="faq-img">
                            <span>
                                <img loading="lazy" src="{{ asset('assets/website/images/faq4.jpg')}}" alt="">
                            </span>
                        </div>
                    </div>
                    <div class="col">
                        <div class="faqs">
                            <ul class="list-unstyled mb-0">
                                <li class="active">
                                    <h3 class="faq-title" role="button">Increase Revenue with AI</h3>
                                    <div class="faq-content" style="display: block;">
                                        <p>Our AI-driven dynamic pricing tool continuously adjusts rates to align with market changes and optimize sales.</p>
                                    </div>
                                </li>
                                <li>
                                    <h3 class="faq-title" role="button">Tailored Pricing Strategies</h3>
                                    <div class="faq-content">
                                        <p>Our AI-driven dynamic pricing tool continuously adjusts rates to align with market changes and optimize sales.</p>
                                    </div>
                                </li>
                                <li>
                                    <h3 class="faq-title" role="button">Transparency</h3>
                                    <div class="faq-content">
                                        <p>Our AI-driven dynamic pricing tool continuously adjusts rates to align with market changes and optimize sales.</p>
                                    </div>
                                </li>                                
                            </ul>
                        </div>
                    </div>
                </div>



            </div>
        </div>
    </section>


    <section class="section section-program pt-0">
        <div class="container">
            <div class="card card-brown">
                <div class="card-header">
                    <div class="row g-3 align-items-md-center">
                        <div class="col-auto">
                            <div class="cb-icon">
                                <img src="{{ asset('assets/website/images/icon-program.svg')}}" alt="">
                            </div>
                        </div>
                        <div class="col">
                            <h3>Unreal Estate Owners’ Program</h3>
                            <p>When you host with Unreal Estate, you are automatically enrolled in the Unreal Estate Owners’ Program. </p>
                        </div>
                        <div class="col-12 col-md-auto">
                             <a href="#"  class="btn w-100 w-md-auto btn-light-orange icon-link icon-link-hover">Enroll Now <i class="bi icon-chevron-right"></i></a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row gx-5 gy-3">
                        <div class="col-12 col-lg-3">
                            <div class="cb-body-content row gx-2">
                                <div class="col-auto col-lg-12">
                                    <img loading="lazy" src="{{ asset('assets/website/images/price-tag.svg')}}" alt="">
                                </div>
                                <div class="col">
                                    <p>Exclusive discounts starting from 10% and up to 50% on all Unreal Estate stays.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3">
                            <div class="cb-body-content row gx-2">
                                <div class="col-auto col-lg-12">
                                    <img src="{{ asset('assets/website/images/account-manager.svg')}}" loading="lazy" alt="">
                                </div>
                                <div class="col">
                                    <p>A dedicated account manager at Unreal Estate to assist with your travel needs.</p>
                                </div>
                            </div>
                        </div> 
                        <div class="col-12 col-lg-3">
                            <div class="cb-body-content row gx-2">
                                <div class="col-auto col-lg-12">
                                   <img loading="lazy" src="{{ asset('assets/website/images/support.svg')}}" alt="">
                                </div>
                                <div class="col">
                                    <p>Priority support during all your stays.</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg-3">
                            <div class="cb-body-content row gx-2">
                                <div class="col-auto col-lg-12">
                                    <img loading="lazy" src="{{ asset('assets/website/images/access.svg')}}" alt="">
                                </div>
                                <div class="col">
                                    <p>Access to investment opportunities with guaranteed returns.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        new Swiper(".section-why-slider", {
            speed: 1000,
            slidesPerView: 4,
            spaceBetween: 20,  
            breakpoints: {
                300: {
                    slidesPerView: "auto"
                },               
                767: {
                    slidesPerView: 2.5
                },
                1300: {
                    slidesPerView: 4
                },
            },        
        });
    })
</script>
@endsection