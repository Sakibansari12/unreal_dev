@extends('website.layouts.app')
@section('content')

<style>
    @media (max-width: 575.98px) {
        .team-card .team-img {
            width: auto;
        }
    }
</style>

<div class="cms-pages">



    {{-- @if (!empty($about_us)) --}}

    <section class="section flex-wrap section-hero overflow-hidden">

        <div class="container">

            <div class="row g-0">

                <div class="col-12 col-lg-6 d-flex align-items-center position-relative">

                    <div class="hero-page-content">

                        <div class="card hero-page-card">

                            <div class="card-header">

                                <span class="badge">About Us</span>

                            </div>

                            <div class="card-body">
                                @if($about_us)
                                <h1 class="fs-1">{{ $about_us->banner_text ?? '' }}</h1>
                                @endif
                                <!-- <h1 class="fs-1">{{$about_us->banner_text ?? '' }}</h1> -->

                            </div>

                        </div>

                    </div>

                </div>

                <div class="col-12 col-lg-6">

                    <div class="hero-page-img">
                        @if($about_us)
                        <img src="{{ asset('storage/about_us/' . $about_us->banner) }}" alt="">
                        @endif
                    </div>

                </div>

            </div>

        </div>

    </section>



    <section class="section section-statistics">

        <div class="container">

            <div class="stat-card">

                <div class="row gx-2">

                    <div class="col">

                        <div class="stat-box">

                            <div class="stat-icon">

                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none"

                                    xmlns="http://www.w3.org/2000/svg">

                                    <path

                                        d="M5 35.0026V11.6693H15V8.33594L20 3.33594L25 8.33594V18.3359H35V35.0026H5ZM8.33333 31.6693H11.6667V28.3359H8.33333V31.6693ZM8.33333 25.0026H11.6667V21.6693H8.33333V25.0026ZM8.33333 18.3359H11.6667V15.0026H8.33333V18.3359ZM18.3333 31.6693H21.6667V28.3359H18.3333V31.6693ZM18.3333 25.0026H21.6667V21.6693H18.3333V25.0026ZM18.3333 18.3359H21.6667V15.0026H18.3333V18.3359ZM18.3333 11.6693H21.6667V8.33594H18.3333V11.6693ZM28.3333 31.6693H31.6667V28.3359H28.3333V31.6693ZM28.3333 25.0026H31.6667V21.6693H28.3333V25.0026Z"

                                        fill="#3BB6B1" />

                                </svg>

                            </div>

                            <div class="stat-info">

                                140+

                            </div>

                            <div class="stat-name">Properties</div>

                        </div>

                    </div>

                    <div class="col">

                        <div class="stat-box">

                            <div class="stat-icon">

                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none"

                                    xmlns="http://www.w3.org/2000/svg">

                                    <g clip-path="url(#clip0_1986_319940)">

                                        <path

                                            d="M15.0026 22.9166C11.1026 22.9166 3.33594 24.8666 3.33594 28.7499V31.6666H26.6693V28.7499C26.6693 24.8666 18.9026 22.9166 15.0026 22.9166ZM7.23594 28.3333C8.63594 27.3666 12.0193 26.2499 15.0026 26.2499C17.9859 26.2499 21.3693 27.3666 22.7693 28.3333H7.23594ZM15.0026 19.9999C18.2193 19.9999 20.8359 17.3833 20.8359 14.1666C20.8359 10.9499 18.2193 8.33325 15.0026 8.33325C11.7859 8.33325 9.16927 10.9499 9.16927 14.1666C9.16927 17.3833 11.7859 19.9999 15.0026 19.9999ZM15.0026 11.6666C16.3859 11.6666 17.5026 12.7833 17.5026 14.1666C17.5026 15.5499 16.3859 16.6666 15.0026 16.6666C13.6193 16.6666 12.5026 15.5499 12.5026 14.1666C12.5026 12.7833 13.6193 11.6666 15.0026 11.6666ZM26.7359 23.0166C28.6693 24.4166 30.0026 26.2833 30.0026 28.7499V31.6666H36.6693V28.7499C36.6693 25.3833 30.8359 23.4666 26.7359 23.0166ZM25.0026 19.9999C28.2193 19.9999 30.8359 17.3833 30.8359 14.1666C30.8359 10.9499 28.2193 8.33325 25.0026 8.33325C24.1026 8.33325 23.2693 8.54992 22.5026 8.91659C23.5526 10.3999 24.1693 12.2166 24.1693 14.1666C24.1693 16.1166 23.5526 17.9333 22.5026 19.4166C23.2693 19.7833 24.1026 19.9999 25.0026 19.9999Z"

                                            fill="#3BB6B1" />

                                    </g>

                                    <defs>

                                        <clipPath id="clip0_1986_319940">

                                            <rect width="40" height="40" fill="white" />

                                        </clipPath>

                                    </defs>

                                </svg>

                            </div>

                            <div class="stat-info">100k+</div>

                            <div class="stat-name">Happy Guests</div>

                        </div>

                    </div>

                    <div class="col">

                        <div class="stat-box">

                            <div class="stat-icon">

                                <svg width="40" height="40" viewBox="0 0 40 40" fill="none"

                                    xmlns="http://www.w3.org/2000/svg">

                                    <path

                                        d="M20 13.3334L17.4167 19.0834L11.6667 21.6667L17.4167 24.2501L20 30.0001L22.5833 24.2501L28.3333 21.6667L22.5833 19.0834L20 13.3334ZM15 5.00008V1.66675H25V5.00008H15ZM20 36.6667C17.9444 36.6667 16.0069 36.2709 14.1875 35.4792C12.3681 34.6876 10.7778 33.6112 9.41667 32.2501C8.05556 30.889 6.97917 29.2987 6.1875 27.4792C5.39583 25.6598 5 23.7223 5 21.6667C5 19.6112 5.39583 17.6737 6.1875 15.8542C6.97917 14.0348 8.05556 12.4445 9.41667 11.0834C10.7778 9.7223 12.3681 8.64592 14.1875 7.85425C16.0069 7.06258 17.9444 6.66675 20 6.66675C21.7222 6.66675 23.375 6.94453 24.9583 7.50008C26.5417 8.05564 28.0278 8.86119 29.4167 9.91675L31.75 7.58342L34.0833 9.91675L31.75 12.2501C32.8056 13.639 33.6111 15.1251 34.1667 16.7084C34.7222 18.2917 35 19.9445 35 21.6667C35 23.7223 34.6042 25.6598 33.8125 27.4792C33.0208 29.2987 31.9444 30.889 30.5833 32.2501C29.2222 33.6112 27.6319 34.6876 25.8125 35.4792C23.9931 36.2709 22.0556 36.6667 20 36.6667ZM20 33.3334C23.2222 33.3334 25.9722 32.1945 28.25 29.9167C30.5278 27.639 31.6667 24.889 31.6667 21.6667C31.6667 18.4445 30.5278 15.6945 28.25 13.4167C25.9722 11.139 23.2222 10.0001 20 10.0001C16.7778 10.0001 14.0278 11.139 11.75 13.4167C9.47222 15.6945 8.33333 18.4445 8.33333 21.6667C8.33333 24.889 9.47222 27.639 11.75 29.9167C14.0278 32.1945 16.7778 33.3334 20 33.3334Z"

                                        fill="#3BB6B1" />

                                </svg>

                            </div>

                            <div class="stat-info">3 years</div>

                            <div class="stat-name">of Hosting Experience</div>

                        </div>

                    </div>

                    <div class="col">

                        <div class="stat-box">

                            <div class="stat-icon">

                                <svg width="24" height="40" viewBox="0 0 24 40" fill="none"

                                    xmlns="http://www.w3.org/2000/svg">

                                    <ellipse cx="12.2682" cy="33.5995" rx="6.40104" ry="6.40075"

                                        fill="#3BB6B1" />

                                    <path

                                        d="M22.4015 0H1.59807C1.00888 0 0.53125 0.47763 0.53125 1.06682V16.5254C0.53125 16.8667 0.69457 17.1874 0.970613 17.3882L11.6242 25.1359C12.0054 25.4131 12.5233 25.4074 12.8982 25.1217L23.048 17.3889C23.3129 17.1871 23.4683 16.8732 23.4683 16.5403V1.06682C23.4683 0.47763 22.9907 0 22.4015 0Z"

                                        fill="#3BB6B1" />

                                    <path

                                        d="M23.4718 17.0687L1.60156 0H22.405C22.9942 0 23.4718 0.47763 23.4718 1.06682V17.0687Z"

                                        fill="#3BB6B1" />

                                    <path

                                        d="M13.8702 9.60113L22.7433 0.728474C23.0121 0.459655 22.8217 0 22.4416 0H2.22041C2.01758 0 1.92918 0.256386 2.08892 0.381391L13.8702 9.60113Z"

                                        fill="#97DBD8" />

                                </svg>

                            </div>

                            <div class="stat-info">Superhosts</div>

                            <div class="stat-name">on Airbnb</div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>



    <section class="section section-who pt-0">

        <div class="container">

            <div class="title mb-3 mb-lg-4 pb-lg-2">

                <h2 class="h3 mb-0">Who Are We?</h2>

            </div>

            <div class="content">
                  @if($about_us)
                <p>{!!$about_us->about_content!!}</p>
                @endif
            </div>

        </div>

    </section>



    <section class="section section-team pt-0">

        <div class="container">

            <div class="title mb-3 mb-lg-4 pb-lg-2">

                <h2 class="h3 mb-0">Our Team</h2>

            </div>

            <div class="swiper-team-slider mx-lg-n2">

                <div class="swiper-wrapper flex-lg-wrap flex-nowrap row mx-0 g-3">

                    <div class="swiper-slide col-lg-auto">
                        <div class="team-card">
                            <div class="team-img">
                                <span>
                                    <img src="{{ asset('assets/website/images/raiyaan.webp')}}" alt="Raiyaan Shingati">
                                </span>
                            </div>
                            <div class="team-info">
                                <h3>Raiyaan Shingati</h3>
                                <p>Chairman/Co-Founder</p>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide col-lg-auto">
                        <div class="team-card">
                            <div class="team-img">
                                <span>
                                    <img src="{{ asset('assets/website/images/arbaaz.webp')}}" alt="Arbaaz Clipwala">
                                </span>
                            </div>
                            <div class="team-info">
                                <h3>Arbaaz Clipwala</h3>
                                <p>CEO/Co-Founder</p>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide col-lg-auto">
                        <div class="team-card">
                            <div class="team-img">
                                <span>
                                    <img src="{{ asset('assets/website/images/princy.webp')}}" alt="Princy Patel">
                                </span>
                            </div>
                            <div class="team-info">
                                <h3>Princy Patel</h3>
                                <p>Head of Guest Relations</p>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide col-lg-auto">
                        <div class="team-card">
                            <div class="team-img">
                                <span>
                                    <img src="{{ asset('assets/website/images/abdul.webp')}}" alt="Abdulahad Tinwala">
                                </span>
                            </div>
                            <div class="team-info">
                                <h3>Abdulahad Tinwala</h3>
                                <p>VP Sales</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="swiper-slide col-lg-auto">
                        <div class="team-card">
                            <div class="team-img">
                                <span>
                                    <img src="{{ asset('assets/website/images/shashank.webp')}}" alt="Shashank Udyawar">
                                </span>
                            </div>
                            <div class="team-info">
                                <h3>Shashank Udyawar</h3>
                                <p>VP Operations</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="swiper-slide col-lg-auto">
                        <div class="team-card">
                            <div class="team-img">
                                <span>
                                    <img src="{{ asset('assets/website/images/ayush.webp')}}" alt="Ayush Mehta">
                                </span>
                            </div>
                            <div class="team-info">
                                <h3>Ayush Mehta</h3>
                                <p>Head of Accounts</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="swiper-slide col-lg-auto">
                        <div class="team-card">
                            <div class="team-img">
                                <span>
                                    <img src="{{ asset('assets/website/images/aiman.webp')}}" alt="Aiman Nawaz">
                                </span>
                            </div>
                            <div class="team-info">
                                <h3>Aiman Nawaz</h3>
                                <p>Head of Expansion</p>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide col-lg-auto">
                        <div class="team-card">
                            <div class="team-img">
                                <span>
                                    <img src="{{ asset('assets/website/images/faazil.webp')}}" alt="Faazil Clipwala">
                                </span>
                            </div>
                            <div class="team-info">
                                <h3>Faazil Clipwala</h3>
                                <p>Marketing Manager</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="swiper-slide col-lg-auto">
                        <div class="team-card">
                            <div class="team-img">
                                <span>
                                    <img src="{{ asset('assets/website/images/dharmesh.webp')}}" alt="Dharmesh Waghela">
                                </span>
                            </div>
                            <div class="team-info">
                                <h3>Dharmesh Waghela</h3>
                                <p>Quality Manager</p>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide col-lg-auto">
                        <div class="team-card">
                            <div class="team-img">
                                <span>
                                    <img src="{{ asset('assets/website/images/vani.webp')}}" alt="Vani Sharma">
                                </span>
                            </div>
                            <div class="team-info">
                                <h3>Vani Sharma</h3>
                                <p>Guest Relations</p>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide col-lg-auto">
                        <div class="team-card">
                            <div class="team-img">
                                <span>
                                    <img src="{{ asset('assets/website/images/thejashwini.webp')}}" alt="Thejashwini AM">
                                </span>
                            </div>
                            <div class="team-info">
                                <h3>Thejashwini AM</h3>
                                <p>Guest Relations</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="swiper-slide col-lg-auto">
                        <div class="team-card">
                            <div class="team-img">
                                <span>
                                    <img src="{{ asset('assets/website/images/partha.webp')}}" alt="Partha Protim">
                                </span>
                            </div>
                            <div class="team-info">
                                <h3>Partha Protim</h3>
                                <p>Guest Relations</p>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide col-lg-auto">
                        <div class="team-card">
                            <div class="team-img">
                                <span>
                                    <img src="{{ asset('assets/website/images/risha.webp')}}" alt="Risha Salian">
                                </span>
                            </div>
                            <div class="team-info">
                                <h3>Risha Salian</h3>
                                <p>Guest Relations</p>
                            </div>
                        </div>
                    </div>
                    <div class="swiper-slide col-lg-auto">
                        <div class="team-card">
                            <div class="team-img">
                                <span>
                                    <img src="{{ asset('assets/website/images/akhila.webp')}}" alt="Akhila Namdhar">
                                </span>
                            </div>
                            <div class="team-info">
                                <h3>Akhila Namdhar</h3>
                                <p>Executive Assistant</p>
                            </div>
                        </div>
                    </div>


                </div>

            </div>

        </div>

    </section>



    <section class="section section-core pt-4 mt-1">

        <div class="container">

            <div class="row gy-5 gx-4">

                <div class="col-12 col-xl-6 d-flex flex-column">

                    <div class="title mb-3 mb-lg-4 pb-lg-2">

                        <h2 class="h3 mb-0">Our Core Values</h2>

                    </div>

                    <div class="row g-2 flex-grow-1">

                        <div class="col-12 col-xl-6">

                            <div class="text-card">

                                <div class="ic-icon">

                                    <img loading="lazy" src="{{ asset('assets/website/images/care.svg')}}" alt="">

                                </div>

                                <h3>Thoughtful Living</h3>

                                <p>At <b>Homes by Unreal</b>, we believe great stays begin with thoughtful living. Every space we create is designed with intention—balancing comfort, aesthetics, and functionality. From how a home feels to how a guest is welcomed, we focus on the small human details that turn a stay into a memory.</p>

                            </div>

                        </div>

                        <div class="col-12 col-xl-6">

                            <div class="text-card">

                                <div class="ic-icon">

                                    <img loading="lazy" src="{{ asset('assets/website/images/zoom.svg')}}" alt="">

                                </div>

                                <h3>Excellence in Execution</h3>

                                <p>We believe that consistency builds <b>trust</b>. From design and maintenance to guest support and daily operations, we hold ourselves to the highest standards of <b>quality</b> and <b>reliability</b>. Integrity, transparency, and a deep sense of ownership guide everything we do <b>—so every stay feels seamless</b>.</p>

                            </div>

                        </div>

                    </div>

                </div>

                <div class="col-12 col-xl-6 d-flex flex-column">

                    <div class="title mb-3 mb-lg-4 pb-lg-2">

                        <h2 class="h3 mb-0">Current Presence</h2>

                    </div>

                    <div class="img-text-card flex-grow-1">

                        <span>

                            <img loading="lazy" src="{{ asset('assets/website/images/map.jpg')}}" alt="">

                        </span>

                        <div class="pt-3">

                            <p>Our portfolio of over <b>140+ curated homes across Bangalore, Mysore, and Pune</b> is brought to life by a team of <b>50+ hospitality professionals</b>. Every stay is shaped by human care, thoughtful design, and an unwavering commitment to quality.</p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </section>

    {{-- @endif --}}

</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {

        let mm = gsap.matchMedia();

        let teamSlider = new Swiper(".swiper-team-slider", {

            speed: 1000,

            spaceBetween: 20,

            init: false,

            breakpoints: {

                300: {

                    slidesPerView: 2.5,

                    spaceBetween: 12,

                },

                767: {

                    slidesPerView: 3.5

                },

                992: {

                    slidesPerView: 4.5

                }

            },

        });

        mm.add("(max-width: 992px)", () => {



            teamSlider.init();



            return () => {

                teamSlider.destroy(false)

            };

        });

    })
</script>

@endsection