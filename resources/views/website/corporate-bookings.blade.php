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
                                <span class="badge">Corporate Bookings</span>
                            </div>
                            <div class="card-body">
                                <h1 class="fs-1">Long-term solutions for your team, at affordable costs</h1>
                                <p>We understand the importance of providing comfortable and convenient accommodations for your employees, especially during long-term projects or relocations.</p>
                            </div>
                            <div class="card-footer">
                                <a href="mailto:abdul@unrealestate.in" class="btn btn-outline-dark icon-link icon-link-hover">Contact Us <i class="bi bi-chevron-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="hero-page-img">
                        <img src="{{asset('assets/website/images/corporate.webp')}}" alt="Corporate Bookings">
                    </div>
                </div>
            </div>
       </div>
    </section>


    <section class="section section-why">
        <div class="container">
            <div class="title mb-4 mb-lg-5">
                <h2 class="h3 mb-0">Why look at Unreal Estate for your team?</h2>
            </div>
            <div class="section-why-slider2 overflow-visible  mx-xl-n2">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-4">
                        <div class="text-card">
                            <div class="ic-icon">
                                <img loading="lazy" src="{{asset('assets/website/images/comfort.svg')}}" alt="">
                            </div>
                            <h3>Comfort of Home</h3>
                            <p>Tailored long-term stay solutions offer employees a home-like environment, allowing them to settle in and feel comfortable during their assignments.</p>
                        </div>
                    </div>


                    <div class="col-md-6 col-xl-4">
                        <div class="text-card">
                            <div class="ic-icon">
                                <img  loading="lazy" src="{{asset('assets/website/images/convenience.svg')}}" alt="">
                            </div>
                            <h3>Flexibility and Convenience</h3>
                            <p>Serviced apartments combine the benefits of a hotel with the comfort of home, providing flexibility for employees to live as they would in their own homes.</p>
                        </div>                        
                    </div>


                    <div class="col-md-6 col-xl-4">
                        <div class="text-card">
                            <div class="ic-icon">
                                 <img  loading="lazy" src="{{asset('assets/website/images/prime-location.svg')}}" alt="">
                            </div>
                            <h3>Prime Locations</h3>
                            <p>Properties are strategically located in prime areas, catering to both professional and personal needs, making it easy for employees to commute and enjoy their surroundings.</p>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <div class="text-card">
                            <div class="ic-icon">
                                <img loading="lazy" src="{{asset('assets/website/images/furnished.svg')}}" alt="">
                            </div>
                            <h3>Fully Furnished and WFH Compatible Spaces</h3>
                            <p>Apartments are fully furnished and equipped with modern amenities, including desks and high-speed internet, ensuring employees have everything they need to focus on their work. Desk monitors also available on request!</p>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-4">
                        <div class="text-card">
                            <div class="ic-icon">
                                <img loading="lazy" src="{{asset('assets/website/images/support-service.svg')}}" alt="">
                            </div>
                            <h3>Comprehensive Support Services</h3>
                            <p>From housekeeping and maintenance to 24/7 guest support and seamless booking management, our dedicated team ensures a smooth and stress-free experience for employees, allowing them to remain refreshed and focused on their tasks.</p>
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
                                <img loading="lazy" src="{{asset('assets/website/images/executive-program.svg')}}" alt="">
                            </div>
                        </div>
                        <div class="col">
                            <h3>Our Executive Program</h3>
                            <p>Top notch corporate solutions that don’t break the bank</p>
                        </div>
                        <div class="col-12 col-md-auto">
                             <a href="mailto:abdul@unrealestate.in" class="btn w-100 w-md-auto btn-light-orange icon-link icon-link-hover">Enquire Now <i class="bi bi-chevron-right"></i></a>
                        </div>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row gx-5 gy-3">
                        <div class="col-12 col-lg">
                            <div class="cb-body-content row gx-2">
                                <div class="col-auto col-lg-12">
                                    <img loading="lazy" src="{{asset('assets/website/images/rates.svg')}}" alt="">
                                </div>
                                <div class="col">
                                    <p>The best rates available</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg">
                            <div class="cb-body-content row gx-2">
                                <div class="col-auto col-lg-12">
                                    <img src="{{asset('assets/website/images/account-manager.svg')}}" loading="lazy" alt="">
                                </div>
                                <div class="col">
                                    <p>Premium Support</p>
                                </div>
                            </div>
                        </div> 
                        <div class="col-12 col-lg">
                            <div class="cb-body-content row gx-2">
                                <div class="col-auto col-lg-12">
                                   <img loading="lazy" src="{{asset('assets/website/images/flexibility.svg')}}" alt="">
                                </div>
                                <div class="col">
                                    <p>Convenience of flexibility</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg">
                            <div class="cb-body-content row gx-2">
                                <div class="col-auto col-lg-12">
                                    <img loading="lazy" src="{{asset('assets/website/images/reliable.svg')}}" alt="">
                                </div>
                                <div class="col">
                                    <p>Reliable quality & comfort</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 col-lg">
                            <div class="cb-body-content row gx-2">
                                <div class="col-auto col-lg-12">
                                    <img loading="lazy" src="{{asset('assets/website/images/perks.svg')}}" alt="">
                                </div>
                                <div class="col">
                                    <p>Extra perks</p>
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
        let mm = gsap.matchMedia();
        let whySlider =  new Swiper(".section-why-slider2", {
            speed: 1000,
            slidesPerView: 4,
            init: false,
            spaceBetween: 20,  
            breakpoints: {
                300: {
                    slidesPerView: "auto"
                },               
                767: {
                    slidesPerView: 2.5
                },
                1300: {
                    slidesPerView: 3
                },
            },        
        });


        mm.add("(max-width: 1299px)", () => {
            
            whySlider.init();

            return () => { 
                whySlider.destroy(false)
            };
        });  


    })
</script>
@endsection