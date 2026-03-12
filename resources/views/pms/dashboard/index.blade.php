@extends('pms.layouts.app')
@section('content')

<style>
    :root{
        --primary:#000000;
        --secondary: #3BB6B1;
    }
    .boxWrapper{
        height: 320px;
        overflow-y:auto;
        overflow-x: hidden;
    }
    .boxWrapper:-webkit-scrollbar {
        width: 8px !important;
        height: 8px !important;
    }
        
    .boxWrapper:-webkit-scrollbar-thumb {
        background-color: #333 !important;
        border-radius: 8px !important;
    }
    .boxWrapper .adBox{
        background: linear-gradient(0deg, rgba(255, 255, 255, 1) 0%, rgb(212 241 244) 100%);
        padding: 12px 15px;
        display: block;
        width: 100%;
        text-decoration: none;
        transition: 0.3s ease all;
    }
    .boxWrapper .adBox .imgBox{
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        height:80px;
        border-radius: 50%;
        color: #ffffff;
        background: var(--secondary);
    }
    .boxWrapper .adBox .imgBox span{
        font-size: 42px;
    }
    .boxWrapper .adBox .imgBox .checkout{
        background: var(--primary);
    }
    .boxWrapper .adBox .content h6{
        transition: 0.3s ease all;
        color: #000000;
    }
    .boxWrapper .adBox .content ul{
        color: #000000;
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }
    .boxWrapper .adBox .content ul li{
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
    }
    .boxWrapper .adBox .content ul li span{
        font-size: 18px;
    }
    .boxWrapper .adBox .content .channel{
        display: inline-block;
        padding: 2px 8px;
        font-size: 13px;
        margin: 0;
    }
    .boxWrapper .adBox:hover{
        background: linear-gradient(0deg, rgba(255, 255, 255, 1) 0%, rgb(212 241 244) 50%);
    }
    .boxWrapper .adBox:hover .content h6{
        color: var(--primary);
    }
    
    
    
    
    .boxWrapper .coBox{
        background: linear-gradient(0deg, rgba(255, 255, 255, 1) 0%, rgb(212 241 244) 100%);
        padding: 12px 15px;
        display: block;
        width: 100%;
        text-decoration: none;
        transition: 0.3s ease all;
    }
    .boxWrapper .coBox .imgBox{
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        height:80px;
        border-radius: 50%;
        color: #ffffff;
        background: var(--secondary);
    }
    .boxWrapper .coBox .imgBox img{
        position: absolute;
        object-fit: cover;
        width: 100%;
        height: 100%;
        top: 0;
        left: 0;
    
    }
    .boxWrapper .coBox .imgBox .checkout{
        background: var(--primary);
    }
    .boxWrapper .coBox .content h6{
        transition: 0.3s ease all;
        color: #000000;
    }
    .boxWrapper .coBox .content ul{
        color: #000000;
        list-style: none;
        margin: 0;
        padding: 0;
        display: flex;
        align-items: center;
        flex-wrap: wrap;
    }
    .boxWrapper .coBox .content ul li{
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
    }
    .boxWrapper .coBox .content ul li span{
        font-size: 18px;
    }
    .boxWrapper .coBox .content .channel{
        display: inline-block;
        padding: 2px 8px;
        font-size: 13px;
        margin: 0;
    }
    .boxWrapper .coBox:hover{
        background: linear-gradient(0deg,rgba(255, 255, 255, 1) 0%, rgba(199,159,98, 0.1) 50%);
    }
    .boxWrapper .coBox:hover .content h6{
        color: var(--primary);
    }
    
    
    @media (max-width:991px){
    }
    
    @media (max-width:575px){    
        .boxWrapper{
            height: 500px;
            overflow-y:auto;
            overflow-x: hidden;
        }
        .boxWrapper .adBox .content h6{
            font-size: 14px;
        }
        .boxWrapper .adBox .imgBox{
            width: 70px;
            height:70px;
        }
        .boxWrapper .adBox .content ul{
            font-size: 11px;
        }
        .boxWrapper .adBox .content .channel{
            margin-top: 6px;
            font-size: 11px;
        }
    }
</style>
<section class="section">
    <div class="container-fluid">
        <div>Dashboard</div>
        
        <div class="page-wrap property-add">
            <div class="page-title mb-4">
                <div class="row gy-3 align-items-center">
                    <div class="col align-self-end">
                        <h1 class="h2 mb-0">Next Arrivals / Departures</h1>
                    </div>
                    <div class="col-auto">
                        <form method="GET" action="{{ route('pms.dashboard') }}">
                            <select class="form-select" name="type" onchange="this.form.submit()">
                                <option value="today" {{ request('type') == 'today' ? 'selected' : '' }}>Today</option>
                                <option value="tomorrow" {{ request('type') == 'tomorrow' ? 'selected' : '' }}>Tomorrow</option>
                                <option value="next_7_days" {{ request('type') == 'next_7_days' ? 'selected' : '' }}>Next 7 Days</option>
                            </select>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="boxWrapper">
                <div class="row g-0">
                    @foreach($nextArrivalDepartureList as $obj)
                        <div class="col-12 col-lg-6">
                            <a href="{{ route('pms.booking.list', ['searchBookingId' => $obj->booking_id]) }}" class="adBox">
                                <div class="row gx-3 align-items-center">
                                    <div class="col-auto">
                                        <div class="imgBox checkout position-relative {{ $obj->booking_icon_detail['type'] == 'checkin' ? 'bg-primary' : 'bg-danger' }}">
                                            <img src="{{ $obj->booking_icon_detail['booking_icon'] ?? asset('assets/pms/images/no-img.jpg') }}" alt="" width="36" height="36">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="content">
                                            <h6>{{ $obj->home->unit_name ?? '-' }}</h6>
                                            <ul>
                                                <li><i class="bi bi-people-fill me-2"></i> {{ $obj->customer_detail['first_name'] ?? '' }} {{ $obj->customer_detail['last_name'] ?? '' }}</li>
                                                <li>
                                                    <i class="bi bi-calendar-range me-2"></i>
                                                    {{ $obj->booking_icon_detail['text'] ?? '' }}:
                                                    On {{ $obj->booking_icon_detail['date'] ?? '' }}
                                                    at {{ $obj->booking_icon_detail['type'] == 'checkin' ? ($obj->home->checkin_time ?? '') : ($obj->home->checkout_time ?? '') }}
                                                </li>
                                                <li>
                                                    <span class="channel rounded-pill alert {{ $obj->booking_icon_detail['alert_class'] ?? '' }}">{{ $obj->channel ?? '' }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        
        </div>
        
        <div class="page-wrap property-add mt-4">
            <div class="page-title mb-4">
                <div class="row gy-3 align-items-center">
                    <div class="col align-self-end">
                        <h1 class="h2 mb-0">Current Occupancy</h1>
                    </div>
                </div>
            </div>
        
            <div class="boxWrapper">
                <div class="row g-0">
                    @foreach($currentOccpancyList as $obj)
                        <div class="col-12 col-lg-6">
                            <a href="{{ route('pms.booking.list', ['searchBookingId' => $obj->booking_id]) }}" class="coBox">
                                <div class="row gx-3 align-items-center">
                                    <div class="col-auto">
                                        <div class="imgBox checkout position-relative">
                                            <img src="{{ $obj->home->primary_image ?? asset('assets/pms/images/no-img.jpg') }}" alt="" width="60">
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="content">
                                            <h6>{{ $obj->home->unit_name ?? '-' }}</h6>
                                            <ul>
                                                <li><i class="bi bi-people-fill me-2"></i> {{ $obj->customer_detail['first_name'] ?? '' }} {{ $obj->customer_detail['last_name'] ?? '' }}</li>
                                                <li><i class="bi bi-calendar-range me-2"></i> {{ $obj->checkin_date }} - {{ $obj->checkout_date }}</li>
                                                <li>
                                                    <span class="channel rounded-pill alert {{ $obj->booking_icon_detail['alert_class'] ?? '' }}">{{ $obj->channel ?? '' }}</span>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>



@endsection