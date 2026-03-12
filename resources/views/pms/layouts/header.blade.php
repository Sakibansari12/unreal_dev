<div class="header-main">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col-auto d-xl-none">
                <div class="row g-1 flex-nowrap  align-items-center">
                    <div class="col-auto">
                        <div class="mob-logo">
                            <a href="#">
                                <img src="{{asset('assets/pms/images/logo-shape.svg')}}" alt="">
                            </a>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="hamburger">
                            <span class="h-top"></span>
                            <span class="h-middle"></span>
                            <span class="h-bottom"></span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-auto ms-auto">
                <div class="row g-3 align-items-center">
                    <div class="col-auto showhidenotification" style="display: none;">
                        <div class="col-auto">
                            <a href="{{route('pms.unified-inbox')}}" class="notifyLnk"><i class="bi bi-bell-fill"></i> <span></span></a>
                        </div>
                    </div>
                    <div class="col-auto dropdown">
                        <button class="btn d-flex p-0 user-dropdown dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="user-img"></div>   <span>Hi, {{ Auth::guard('admin')->user()->name ?? '' }} ({{ Auth::guard('admin')->user()->role ?? '' }})</span>
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="{{route('pms.changepassword.form')}}">Change Password</a></li>
                            <li><a class="dropdown-item" href="{{route('pms.logout')}}">Logout</a></li>                           
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>