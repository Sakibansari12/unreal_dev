

                <header class="header-main compensate-for-scrollbar">

                    <div class="container">

                        <div class="row align-items-center">

                            <div class="col">

                                <a href="/" class="logo">

                                   <img src="{{ asset('assets/website/images/logo.svg') }}" alt="Unreal Estate">

                                </a>

                            </div>

                            <div class="col-auto">

                                <div class="small-search-outer text-center">

                                    <a href="javascript:void(0)" class="small-search" data-small-search>

                                       
                                        <ul>

                                            <li data-type="location">
                                                <!--Search Destination -->
                                                @if (!empty(request('location') && empty(request('locationName'))))
                                                    <div>{{ request('location') }}</div>
                                                @else
                                                    Search Destination
                                                @endif

                                            </li>

                                            <li data-type="dates">
                                            @if (!empty(request('check_in')) && !empty(request('check_out')) && !empty(request('location')))
                                                <div>
                                                    {{ date('jS M', strtotime(request('check_in'))) }} to {{ date('jS M', strtotime(request('check_out'))) }}
                                                </div>
                                            @else
                                                Dates
                                            @endif
                                            </li>

                                            <li data-type="guests">
                                                @if (!empty(request('all_total_guests')))
                                                    <div>{{ request('all_total_guests') }}</div>
                                                @else
                                                   Guests
                                                @endif

                                               

                                            </li>

                                            <li data-type="search">

                                                <div class="btn-outer">

                                                    <div class="small-search-btn"><i class="icon-search"></i></div>

                                                </div>

                                            </li>

                                        </ul>

                                    </a>

                                </div>

                            </div>



                            <div class="col">

                                <nav class="main-nav">

                                    <ul class="nav list-unstyled m-0 align-items-center justify-content-end">

                                        <li data-mobile-hide class="d-none">

                                            <a href="{{ route('hostwithnook') }}"><i class="icon-corporate-fare me-2"></i>Host With Unreal Estate</a>

                                        </li>

                                        <li data-desktop-hide>

                                            <a href="javascript:void(0)" class="search-link"><i class="icon-search"></i></a>

                                        </li>

                                        <li class="dropdown">

                                            <a href="javascript:void(0)" data-bs-toggle="dropdown" class="menu-toggle">

                                                <span class="line"></span>

                                                <span class="line"></span>

                                                <span class="line"></span>

                                            </a>

                                            <div class="dropdown-mobile-wrapper">

                                                <ul class="dropdown-menu dropdown-menu-end dropdown-shadow">

                                                    <li class="li-heading">Account</li>

                                                    @if(checkAuth())
                                                        <li>
                                                            <a class="dropdown-item" href="/my-account"><i class="icon-account-circle"></i>Your Account</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="/my-bookings"><i class="icon-travel-luggage-and-bags"></i>Your Bookings</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="/change-password"><i class="icon-person-add"></i>Change Password</a>
                                                        </li>
                                                        <li>
                                                            <form method="POST" action="{{ route('logout') }}">
                                                                @csrf
                                                                <button class="dropdown-item fs-11" type="submit"><i class="icon-login"></i> Logout</button>
                                                            </form>
                                                        </li>
                                                    @else
                                                        <li>
                                                            <a class="dropdown-item" href="{{route('customer.signup')}}"><i class="icon-person-add"></i>Sign up</a>
                                                        </li>
                                                        <li>
                                                            <a class="dropdown-item" href="{{route('customer.login')}}"><i class="icon-login"></i>Login</a>
                                                        </li>
                                                    @endif

                                                    

                                                    <li class="li-heading">Support</li>

                                                    

                                                    

                                                     <li>

                                                        <a class="dropdown-item" href="{{route('about.us')}}"><i class="icon-home-pin"></i>About Us</a>

                                                    </li>

                                                   

                                                    <li>

                                                        <a class="dropdown-item" href="{{route('cancellation_refund')}}"><i class="icon-unknown-document"></i>Cancellation Policy</a>

                                                    </li>

                                                   

                                                    

                                                    <li class="li-heading d-none">

                                                        <a href="{{ route('hostwithnook') }}" class="dropdown-item"><strong>Host With Unreal Estate</strong></a>

                                                    </li>

                                                </ul>


                                                @if(!checkAuth())
                                                <div class="dropdown-toolbar d-xl-none">

                                                    <div class="row align-items-center">

                                                        <div class="col">Get 10% off on your first booking</div>

                                                        <div class="col-auto">

                                                            <a href="/signup" class="btn btn-primary icon-link icon-link-hover">Sign up <i class="bi icon-chevron-right"></i></a>

                                                        </div>

                                                    </div>

                                                </div>
                                                @endif

                                            </div>

                                        </li>

                                    </ul>

                                </nav>

                            </div>

                            <div class="col-12">

                                <div class="header-search">

                                    @include('website.search.search-header')
                                </div>

                            </div>

                        </div>

                    </div>

                </header>

          





                