<?php
$appVersion = "1.0.0";
$pagename = strtolower(basename($_SERVER['PHP_SELF']));
$pagerouteName = Route::currentRouteName();


if ($pagerouteName === "index") {
    $bodyClass = "home-page";
} elseif ($pagerouteName === "property-list") {
    $bodyClass = "inner-page header-page";
} else {
    $bodyClass = "inner-page hideSearch";
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">

    <meta http-equiv="x-ua-compatible" content="ie=edge">

    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no, user-scalable=0, viewport-fit=cover">



    <title>Unreal Estate</title>

    <meta name="description" content="">

    <meta name="keywords" content="">

    <link rel="shortcut icon" type="image/png" href="{{ asset('assets/website/favicon.png') }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@200..800&display=swap" rel="stylesheet">

    <!-- <link rel="stylesheet" href="https://use.typekit.net/zvv8mre.css"> -->

    <link rel="stylesheet" href="{{ asset('assets/website/css/app.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
         .hideSearch .small-search-outer{display:none !important;}
        .hideSearch .search-link{display:none !important;} 
        .firstSection {
            padding-top: 40px;
        }

        .section.section-filters,
        .section.section-filters.active {
            padding: 10px 0;
        }

        .innerFilterWrap {
            margin-left: -21px;
            margin-right: -21px;
        }

        .card.card-filter {
            padding: 0 21px;
        }

        .card.card-property .card-img-top {
            padding-bottom: 70% !important;
        }

        .card.card-filter>a {
            flex-direction: row;
            gap: 10px;
            padding: 4px 12px;
        }

        .card.card-filter .card-text {}

        .card.card-filter .card-icon {
            display: flex;
            width: 32px;
            margin-bottom: 0px;
        }

        .section.section-swiper .swiper-main-outer {
            padding-bottom: 20px;
        }

        .section.section-swiper.section-swiper-small {
            padding-top: 23px;
        }

        .propertiesPage {
            top: var(--header-height);
        }

        .custom-dropdown .icon-link {
            margin-top: 8px;
        }

        @media(max-width:1600px) {
            .card.card-filter {
                padding: 0 15px;
            }

            .innerFilterWrap {
                margin-left: -15px;
                margin-right: -15px;
            }

            .card.card-filter .card-text {
                font-size: 12px;
            }
        }

        @media(max-width:1440px) {
            .card.card-filter {
                padding: 0 3px;
            }

            .card.card-filter>a {
                flex-direction: row;
                gap: 10px;
                padding: 4px 10px;
            }
        }

        @media(max-width:991px) {
            .card.card-filter {
                padding: 0 15px;
            }

            .innerFilterWrap {
                margin-left: -15px;
                margin-right: -15px;
            }
        }

        html.page-loading body>*:not(#page-loader) {
            display: none !important;
        }

        #page-loader {
            position: fixed;
            inset: 0;
            background: #ffffff;
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .loader-inner {
            text-align: center;
        }

        .spinner {
            width: 48px;
            height: 48px;
            border: 4px solid #e5e5e5;
            border-top: 4px solid #3BB6B1;
            border-radius: 50%;
            animation: spin 0.9s linear infinite;
            margin: 0 auto 12px;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        body.page-loading {
            overflow: hidden;
        }

        .tag-item.active {
            background-color: #E2F4F4 !important;
            color: #0e726f !important;
        }
    </style>
</head>
@php
$pagename = Route::currentRouteName();
@endphp

<script>
    window.addEventListener('load', function() {
        const loader = document.getElementById('page-loader');
        if (!loader) return;

        loader.style.opacity = '0';
        loader.style.transition = 'opacity 0.4s ease';

        setTimeout(() => {
            loader.remove();
            document.documentElement.classList.remove('page-loading');
            document.body.classList.remove('page-loading');
        }, 400);
    });
</script>

<!-- <body data-scroll-id="top" class="page-loading @if ($pagename === 'index') home-page @elseif ($pagename === 'see-all-property') inner-page header-page @elseif ($pagename === 'property-detail') inner-page @elseif ($pagename === 'property-book') header-hide header-page logo-primary @else inner-page header-page @endif"> -->
<body data-scroll-id="top" class="page-loading {{ $bodyClass ?? '' }}">


    <div id="page-loader">
        <div class="loader-inner">
            <div class="spinner"></div>
            <p>Loading property details...</p>
        </div>
    </div>


    <div class="wrapper clearfix">
        <main class="main clearfix">
            <div class="header-wrapper">
                @include('website.layouts.header')
            </div>
            @yield('content')

            @include('website.layouts.footer')
        </main>
    </div>