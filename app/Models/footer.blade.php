<section class="section section-tagline">
    <div class="container animFade">
        <div class="row gx-1 flex-nowrap">
             <div class="col">
                 <div class="tagline-text"><span>Managing Luxury <br>Homes Since </span><strong>{{ setting()->home_since }}</strong></div>
             </div>
             <div class="col">
                 <div class="tagline-text"><span>Dedicated <br>Home Staff</span> <strong>{{ setting()->home_staff }}+</strong></div>
             </div>
             <div class="col">
                 <div class="tagline-text"><span>Hosted <br>Families</span> <strong>{{ setting()->hosted_families }}+</strong></div>
             </div>
        </div>
    </div>
</section>
<footer class="footer-main section bg-secondary-light2">
    <div class="footer-top animFade">
        <div class="container">
           <div class="row gx-5">
             <div class="col-12 col-sm-auto">
                <h2>Let's Connect,<span>We’re Happy to Help You</span></h2>
             </div>
             <div class="col col-sm align-self-end">
                <hr class="my-4 my-sm-1">
             </div>
           </div>
        </div>
    </div>

    <div class="footer-bottom">
        <div class="container">
           <div class="row gx-5">
             <div class="col-12 col-md-8">
                <ul class="footer-links list-unstyled mb-0 animFade">
                    <li><a href="{{ route('about_us') }}">About us</a></li>
                    <li><a href="{{ route('team') }}">Team</a></li>
                    <li><a href="{{ route('contact_us')}}">Contact us</a></li>
                    <li><a href="{{ route('our_diffrence') }}">Our Difference</a></li>
                    <li><a href="{{ route('faqs') }}">FAQ’s</a></li>
                    <li><a href="{{ route('join-our-network')}}">Home Owners</a></li>
                    <li><a href="{{ route('policy')}}">Policy </a></li>
                    <li><a href="{{ route('booking_page')}}">Booking</a></li>
                    <li><a href="{{ route('cancellation_refund')}}">Cancellation & Refund</a></li>
                </ul>
             </div>
             <div class="col-12 col-md-4 animFade">
                <ul class="social-links nav list-unstyled">
                    <li><a href="#"><i class="icon-facebook"></i></a></li>
                    <li><a href="#"><i class="icon-instagram"></i></a></li>
                </ul>
                <div class="copyrights">
                    &copy; V are Family<br>
                    Designed by <a href="https://iws.in" target="_blank">Infomedia Web Solutions</a>.
                </div>
             </div>
           </div>
        </div>
    </div>
</footer>