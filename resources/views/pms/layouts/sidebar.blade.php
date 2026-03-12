<aside class="sidebar">
    <div class="logo">
        <a href="{{route('pms.dashboard')}}">
            <img src="{{asset('assets/pms/images/logo-horizontal.svg')}}" alt="">
        </a>
        <span class="menu-close">
            &times;
        </span>
    </div>
    <nav class="main-nav">
        <ul  class="d-flex flex-column">
            @if(Auth::guard('admin')->user()->role_id != 8)  
            <li class="{{ request()->routeIs('pms.dashboard') ? 'active' : '' }}">
                <a href="{{route('pms.dashboard')}}">
                    <i class="icon-dashboard"></i><span>Dashboard</span>
                </a>
            </li> 
            @endif
             @if(Auth::guard('admin')->user()->role_id == 1)  
                
                <li class="{{ request()->routeIs('pms.extrnal.channel') ? 'active' : '' }}">
                    <a href="{{route('pms.extrnal.channel')}}">
                       <i class="material-symbols-outlined">server_person</i><span>Channel Manager</span>
                    </a>
                </li>
                
                <!-- <li class="{{ request()->routeIs('pms.property.channel.manager.validate.form') ? 'active' : '' }}">
                    <a href="{{route('pms.property.channel.manager.validate.form')}}">
                       <i class="material-symbols-outlined">verified</i><span>Validate Property</span>
                    </a>
                </li> -->
            @endif 
            @if(in_array(Auth::guard('admin')->user()->role_id, [1,2,5,7,6,8]))
                <li class=" {{ request()->routeIs('pms.calendar') ? 'active' : '' }}">
                    <a href="{{route('pms.calendar')}}">
                        <i class="material-symbols-outlined">
                            calendar_month
                        </i>Calendar
                    </a>
                </li>
            @endif
            @if(Auth::guard('admin')->user()->role_id == 1 || Auth::guard('admin')->user()->role_id == 7)  
                <li class="has-dropdown {{ request()->routeIs('pms.user.list') ? 'active' : '' }}">
                    <a href="javascript:void(0)"><i class="material-symbols-outlined">group</i><span>Users</span></a>
                    <div class="submenu" >
                        <ul>
                            <li><a href="{{route('pms.user.form')}}">Add</a></li>
                            <li><a href="{{route('pms.user.list')}}">Manage</a></li>
                        </ul>
                    </div>
                </li>
            @endif
            @if(in_array(Auth::guard('admin')->user()->role_id, [1,2,7]))
                <li class="has-dropdown {{ request()->routeIs('pms.property.list') ? 'active' : '' }}">
                    <a href="javascript:void(0)"><i class="material-symbols-outlined">house</i><span>Property</span></a>
                    <div class="submenu" >
                        <ul>
                            <li><a href="{{route('pms.property.list')}}">List All Properties</a></li>
                            <!-- @if(in_array(Auth::guard('admin')->user()->role_id, [1,7]))
                            <li><a href="{{route('pms.published.property.list', ['pType' => 'unit'])}}">Published Units</a></li>
                            <li><a href="{{route('pms.featured.property.list', ['pType' => 'unit'])}}">Featured Units</a></li>
                            @endif -->
                        </ul>
                    </div>
                </li>
            @endif 
            @if(in_array(Auth::guard('admin')->user()->role_id, [1,2,4,5,7,6,8,3]))
                <li class="has-dropdown {{ request()->routeIs('pms.booking.list') ? 'active' : '' }}">
                    <a href="javascript:void(0)"><i class="material-symbols-outlined">hotel</i><span>Bookings/Enquiry</span></a>
                    <div class="submenu" >
                        <ul>
                            @if(in_array(Auth::guard('admin')->user()->role_id, [1,2,5,7,6,8]))
                            <li><a href="{{route('pms.booking.bylocation')}}">New Booking</a></li>
                            @endif
                            @if(in_array(Auth::guard('admin')->user()->role_id, [1,2,4,5,7,6,8,3]))
                            <li><a href="{{route('pms.booking.list')}}">All Bookings</a></li>
                            @endif
                            <!-- @if(in_array(Auth::guard('admin')->user()->role_id, [1,2,7]))
                            <li><a href="{{route('pms.booking.bookingEnquiry')}}">Booking Enquiry</a></li>
                            @endif -->
                        </ul>
                    </div>
                </li>
            @endif
            <!-- @if(in_array(Auth::guard('admin')->user()->role_id, [1,2,6,7]))  
                <li class="{{ request()->routeIs('pms.lead.list') ? 'active' : '' }}">
                    <a href="{{route('pms.lead.list')}}">
                       <i class="material-symbols-outlined">person_search</i><span>Leads</span>
                    </a>
                </li>
            @endif -->
            @if(in_array(Auth::guard('admin')->user()->role_id, [1,2,7]))
                <li class="{{ request()->routeIs('pms.guestdatabase.list') ? 'active' : '' }}">
                    <a href="{{route('pms.guestdatabase.list')}}">
                        <i class="material-symbols-outlined">database</i><span>Guest Database</span>
                    </a>
                </li>
            @endif
            @if(in_array(Auth::guard('admin')->user()->role_id, [1,2,5,7]))
                <li class="{{ request()->routeIs('pms.quotation.list') ? 'active' : '' }}">
                    <a href="{{route('pms.quotation.list')}}">
                        <i class="material-symbols-outlined">format_quote</i><span>Quotation</span>
                    </a>
                </li>
            @endif
            @if(in_array(Auth::guard('admin')->user()->role_id, [1,2]) && Auth::guard('admin')->user()->parent_user_id  == 1)
                <li class="{{ request()->routeIs('pms.coupon.list') ? 'active' : '' }}">
                    <a href="{{ route('pms.coupon.list')}}">
                    <i class="bi bi-tag fs-5"></i> <span>Coupons</span>
                    </a>
                </li> 
             @endif
            @if(in_array(Auth::guard('admin')->user()->role_id, [1,7]))
                <li class="{{ request()->routeIs('pms.analytics.list') ? 'active' : '' }}">
                    <a href="{{ route('pms.analytics.list')}}">
                    <i class="bi bi-bar-chart-line fs-5"></i> <span>Analytics</span>
                    </a>
                </li> 
            @endif

            @if(in_array(Auth::guard('admin')->user()->role_id, [1,2]) && Auth::guard('admin')->user()->parent_user_id  == 1)
                <li class="has-dropdown {{ request()->routeIs('pms.report.sale-report') ? 'active' : '' }}">
                    <a href="javascript:void(0)"><i class="material-symbols-outlined fs-5">lab_profile</i><span>Reports</span></a>
                    <div class="submenu" >
                        <ul>
                            <li><a href="{{route('pms.report.sale-report')}}">Sale</a></li>
                        </ul>
                    </div>
                </li>
             @endif
            <!--@if(in_array(Auth::guard('admin')->user()->role_id, [1,7, 8]))-->
            <!--    <li class="has-dropdown {{ request()->routeIs('pms.report.sale-report') ? 'active' : '' }}">-->
            <!--        <a href="javascript:void(0)"><i class="material-symbols-outlined fs-5">lab_profile</i><span>Reports</span></a>-->
            <!--        <div class="submenu" >-->
            <!--            <ul>-->
            <!--                <li><a href="{{route('pms.report.sale-report')}}">Sale</a></li>-->
            <!--                @if(in_array(Auth::guard('admin')->user()->role_id, [1,2,3,6,7]))-->
            <!--                <li><a href="{{route('pms.owner-expenses.list')}}">Owner Expenses</a></li>-->
            <!--                <li><a href="{{route('pms.owner.list')}}">Owner Revenue</a></li>-->
                            
            <!--                @endif-->
            <!--            </ul>-->
            <!--        </div>-->
            <!--    </li>-->
            <!-- @endif-->
           <!--  @if(in_array(Auth::guard('admin')->user()->role_id, [1,2,3,6,7, 8]))
                <li class="has-dropdown {{ request()->routeIs('pms.report.sale-report') ? 'active' : '' }}">
                    <a href="javascript:void(0)"><i class="material-symbols-outlined fs-5">lab_profile</i><span>Reports</span></a>
                    <div class="submenu" >
                        <ul>
                            @if(in_array(Auth::guard('admin')->user()->role_id, [1,7, 8]))
                            <li><a href="{{route('pms.report.sale-report')}}">Sale</a></li>
                            @endif
                            @if(in_array(Auth::guard('admin')->user()->role_id, [1,2,3,6,7]))
                            <li><a href="{{route('pms.owner-expenses.list')}}">Owner Expenses</a></li>
                            <li><a href="{{route('pms.owner.list')}}">Owner Revenue</a></li>
                            @endif
                            @if(in_array(Auth::guard('admin')->user()->role_id, [1]))
                           <li><a href="{{route('pms.report.property-billing')}}">Property Billing Report</a></li>
                        @endif
                        </ul>
                    </div>
                </li>
             @endif -->

            @if(Auth::guard('admin')->user()->role_id == 1)  
                <li class="{{ request()->routeIs('pms.unified-inbox') ? 'active' : '' }}">
                    <a href="{{route('pms.unified-inbox')}}">
                       <i class="material-symbols-outlined">inbox</i><span>Unified Inbox</span>
                    </a>
                </li>
            @endif

           @if(in_array(Auth::guard('admin')->user()->role_id, [1,2]) && Auth::guard('admin')->user()->parent_user_id  == 1)
            <li class="has-dropdown {{ request()->routeIs('pms.ru_amenities.list') ? 'active' : '' }}">
                <a href="javascript:void(0)"><i class="material-symbols-outlined fs-5">settings</i><span>Masters</span></a>
                <div class="submenu">
                    <ul>
                        <!-- <li><a href="{{route('pms.agreement.list')}}">Agreement</a></li> -->
                        <li><a href="{{route('pms.ru_amenities.list')}}">Amenities (OTA)</a></li>
                        <li><a href="{{route('pms.company.list')}}">Company</a></li>
                        <li><a href="{{route('pms.gst.view')}}">GST</a></li>
                        <!-- <li><a href="{{route('pms.gst_setting.list')}}">GST Setting</a></li> -->
                        <li><a href="{{route('pms.hometype.list')}}">Home Type</a></li>
                        <li><a href="{{route('pms.location.list')}}">Locations</a></li>
                        <li><a href="{{route('pms.ru_location.list')}}">Locations (OTA)</a></li>
                        <li><a href="{{route('pms.area.list')}}">Area</a></li>
                        <li><a href="{{route('pms.tags.list')}}">Tags</a></li>
                        <li><a href="{{route('pms.icons.list')}}">Icons</a></li>
                        <li><a href="{{route('pms.channel.list')}}">Channel</a></li>
                        <!-- <li><a href="{{route('pms.role.list')}}">Role</a></li> -->
                        <!-- <li><a href="{{route('pms.rolepermission.list')}}">Role/Permissions</a></li> -->
                        
                    </ul>
                </div>
            </li>
            @endif
            @if(in_array(Auth::guard('admin')->user()->role_id, [1,7]))
                <li class="{{ request()->routeIs('pms.set.pricelab.token') ? 'active' : '' }}">
                    <a href="{{ route('pms.set.pricelab.token')}}">
                    <i class="material-symbols-outlined fs-5">settings</i> <span>Integrations</span>
                    </a>
                </li>
            @endif
            @if(in_array(Auth::guard('admin')->user()->role_id, [1,2]) && Auth::guard('admin')->user()->parent_user_id  == 1)
                <li class="has-dropdown">
                    <a href="javascript:void(0)"><i class="material-symbols-outlined">desktop_windows</i><span>Website</span></a>
                    <div class="submenu">
                        <ul>
                            <li><a href="{{route('pms.bannerslide.list')}}">Banner Slide</a></li>
                            <li><a href="{{route('pms.footer-banner.edit')}}">Footer Banner Content</a></li>
                            <!-- <li><a href="{{route('pms.specialoffer.list')}}">Special Offers</a></li> -->
                            <!-- <li><a href="{{route('pms.blog.list')}}">Blogs</a></li> -->
                            <!-- <li><a href="{{route('pms.collection.list')}}">Collections</a></li>
                            <li><a href="{{route('pms.faqcategory.list')}}">FAQ Category</a></li>
                            <li><a href="{{route('pms.faq.list')}}">FAQ </a></li>
                            <li><a href="{{route('pms.privacy.policy.form')}}">Privacy Policy</a></li>
                             <li><a href="{{route('pms.refund.policy.form')}}">Cancellation and Refund Policy</a></li>
                            <li><a href="{{route('pms.term.condition.form')}}">Terms & Conditions</a></li> -->
                            <li><a href="{{route('pms.amenities.list')}}">Website Amenities</a></li>
                            <li><a href="{{route('pms.website.markup.list')}}">Website Markup</a></li>
                             <li><a href="{{route('pms.aboutus.form')}}">About Us</a></li>
                             <!-- <li><a href="{{route('pms.team.list')}}">Team</a></li> -->
                            <!-- <li><a href="{{route('pms.service.form')}}">Service</a></li>
                            <li><a href="{{route('pms.landing.list')}}">Landing Page</a></li> -->
                        </ul>
                    </div>
                </li>
            @endif
            <li class="mt-auto"><a href="{{route('pms.logout')}}"><i class="icon-logout"></i><span>Logout</span></a></li>
        </ul>
    </nav>
 </aside>