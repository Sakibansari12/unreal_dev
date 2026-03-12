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
                                <span class="badge">Careers</span>
                            </div>
                            <div class="card-body">
                                <h1 class="fs-1">Creating great stays through fulfilling careers</h1>
                                <p>Our mission is for guests to feel cared for, and it starts with caring for our team. Let’s build India’s best stays together.</p>
                            </div>                            
                        </div>
                    </div>
                </div>
                <div class="col-12 col-lg-6">
                    <div class="hero-page-img">
                        <img src="{{ asset('assets/website/images/careers.webp')}}" alt="Creating great stays through fulfilling careers">
                    </div>
                </div>
            </div>
       </div>
    </section>


    <section class="section section-why">
        <div class="container">
            <div class="title mb-4 mb-lg-5">
                <h2 class="h3 mb-0">Why join us?</h2>
            </div>
            <div class="section-why-slider2 overflow-visible  mx-xl-n2">
                <div class="row g-3">
                    <div class="col-md-6 col-xl-4">
                        <div class="text-card">
                            <div class="ic-icon">
                                <img loading="lazy" src="{{ asset('assets/website/images/handshake.svg')}}" alt="">
                            </div>
                            <h3>Culture of Care</h3>
                            <p>At the heart of Unreal Estate is a culture of care that extends through every aspect of our operations. We nurture a supportive environment where every team member feels valued and empowered to deliver exceptional service.</p>
                        </div>
                    </div>


                    <div class="col-md-6 col-xl-4">
                        <div class="text-card">
                            <div class="ic-icon">
                                <img  loading="lazy" src="{{ asset('assets/website/images/globe.svg')}}" alt="">
                            </div>
                            <h3>Live and Work anywhere</h3>
                            <p>Flexibility is key in today’s dynamic world. Our hybrid working model allows you to balance personal life and work effectively, giving you the freedom to work from home or the office as needed.</p>
                        </div>                        
                    </div>


                    <!-- <div class="col-xl-4">
                        <div class="text-card">
                            <div class="ic-icon">
                                 <img  loading="lazy" src="{{ asset('assets/website/images/cult-corporate.svg')}}" alt="">
                            </div>
                            <h3>Cult Corporate</h3>
                            <p>Embrace a healthier lifestyle with our Cult Corporate membership, offering access to top-tier fitness classes and wellness programs that ensure our team stays healthy and energized.</p>
                        </div>
                    </div>

                    <div class="col-xl-4">
                        <div class="text-card">
                            <div class="ic-icon">
                                <img loading="lazy" src="{{ asset('assets/website/images/mental.svg')}}" alt="">
                            </div>
                            <h3>Mental Well-being</h3>
                            <p>Your mental health is as important as your physical health. We support our employees with resources and programs aimed at fostering mental wellbeing, including access to subsidized counselling services and stress management workshops.</p>
                        </div>
                    </div> -->

                    <div class="col-xl-4">
                        <div class="text-card">
                            <div class="ic-icon">
                                <img loading="lazy" src="{{ asset('assets/website/images/growth.svg')}}" alt="">
                            </div>
                            <h3>Growth and Development</h3>
                            <p>We invest in your future with personalised growth and development opportunities that enhance your skills and career prospects. From workshops to continuing education, we provide the tools you need to succeed and advance.</p>
                        </div>
                    </div>
                    

                </div>
            </div>
        </div>
    </section>


 
    
    
    <section class="section section-why pt-0">
        <div class="container">
            <!-- <p>Roles for which we often recruit include:<br><strong>Property Manager, Guest Relations Manager, Interior Designer, Social Media Marketing & Revenue Manager.</strong></p> -->
            <p>Above all, the skills we value the most in our team are empathy and proactivity.<br> <strong>If you feel like you’d fit right in, let us know.</strong></p>

            <a href="mailto:shashank@unealestate.in" class="btn btn-orange icon-link icon-link-hover">Contact Us <i class="bi bi-chevron-right"></i></a>
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

