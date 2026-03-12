@php
$locations = getLocations();
$collections = getCollections();
@endphp

<footer class="footer-main pb-0 section">
    <div class="footer-top">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 col-xl-6 order-2 order-xl-1">
                    <div class="row">
                        <div class="col-12 col-sm order-2 order-sm-1">
                            <div class="footerImg">
                                <img src="{{ asset('assets/website/images/footerimg.svg') }}" class="w-100" alt="">
                            </div>
                        </div>
                        <div class="col-12 col-sm-4 col-xl-6 order-1 order-sm-2">
                            <div class="footerGroup">
                                Call: <a href="tel:+919999999999">+91 99999 9999</a><br>
                                Email: <a href="mailto:support@unrealestate.in">support@unrealestate.in</a>
                            </div>
                            <div class="footerGroup">
                                <ul class="social">
                                    <li><a href="https://www.facebook.com/profile.php?id=61557281364936#"><span class="icon-facebook"></span></a></li>
                                    <li><a href="#"><span class="icon-instagram"></span></a></li>
                                </ul>
                            </div>
                            <div class="footerGroup">
                                &copy; 2025 <b>Unreal Estate</b><br>
                                Designed by <a href="http://iws.in" target="_blank">IWS</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6 order-1 order-xl-2">
                    <div class="row g-4">
                        <div class="col-12 col-sm">
                            <h5>Top Locations</h5>
                            <ul class="footerLink ">
                                @foreach($locations as $location)
                                    <li>
                                        <a href="{{ route('property-list', ['location' => $location->slug_name, 'locationName' => $location->slug_name  ]) }}">
                                            {{ $location->location_name ?? '' }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-12 col-sm">
                            <h5>Top Collections</h5>
                            <ul class="footerLink ">
                                @foreach($collections as $collection)
                                    <li>
                                        <a  href="{{ route('property-list', ['collection' => $collection->slug_name]) }}">
                                            {{ $collection->collection_name ?? '' }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-12 col-sm">
                            <h5>Sitemap</h5>
                            <ul class="footerLink ">
                                <li><a href="{{route('about.us')}}">About</a></li>
                                <li><a href="{{route('list.property')}}">List Your Property</a></li>
                                <li><a href="{{route('coupen')}}">Offers</a></li>
                                <li><a href="{{route('contactus.form')}}">Contact Us</a></li>
                                <li><a href="{{route('refund.policy')}}">Cancellation & Refund Policy</a></li>
                                <li><a href="{{route('terms.condition')}}">Terms & Conditions</a></li>
                                <li><a href="{{route('privacy.policy')}}">Privacy Policy</a></li>
                                <li><a href="{{route('faq')}}">FAQS</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>            
        </div>
    </div>    
</footer>