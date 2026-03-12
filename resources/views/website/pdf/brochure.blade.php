<?php
$appVersion = "1.0.0";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unreal Estate</title>
    <!--<link rel="shortcut icon" type="image/png" href="favicon.png">-->
    <!--<link rel="preconnect" href="https://fonts.googleapis.com">-->
    <!--<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>-->
    <!--<link href="https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,100..1000;1,9..40,100..1000&display=swap" rel="stylesheet">-->
    <!--<link rel="stylesheet" href="{{ asset('assets/website/css/app.css') }}">-->
    <!--<link rel="stylesheet" href="{{ asset('assets/website/css/bootstrap.css') }}">-->
    <!--<link rel="stylesheet" href="{{ asset('assets/website/css/bootstrap-icons.min.css') }}">-->
    <!--<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200&icon_names=language" />-->
    <style>
        body {
            font-family: Arial, sans-serif; /* Fallback for DM Sans */
            max-width: 100%;
            margin: 20px auto;
            color: #2c3e50;
            background: #fff;
        }
        /*table {*/
        /*    width: 100%;*/
        /*    border-collapse: collapse;*/
        /*}*/
        td, th {
            padding: 10px;
            vertical-align: middle;
        }
        .header-image {
            width: 100%;
            max-height: 800px;
            object-fit: cover;
            border-radius: 12px;
            margin-bottom: 25px;
        }
        .logo {
            display: block;
            margin: 0 auto;
            max-width: 233px;
            height: auto;
        }
        h2 {
            font-size: 44px;
            color: #2c3e50;
            margin: 0;
        }
        h3 {
            color: #c79f62;
            font-weight: bold;
            font-size: 30px;
            margin-bottom: 20px;
        }
        .location {
            color: #c79f62;
            font-size: 16px;
            margin-bottom: 25px;
        }
        .spec-item {
            background: #ecf0f1;
            border-radius: 12px;
            padding: 15px;
            font-weight: bold;
            text-align: center;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            margin-bottom: 15px;
        }
        .spec-item img {
            width: 40px;
            height: 40px;
            margin-bottom: 10px;
        }
        .amenities-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .amenities-list li {
            padding: 10px 0;
        }
        .amenities-name{
            font-size:18px;
        }
        .amenities-small-icon {
            width: 40px;
            height: 40px;
            margin-right: 10px;
            vertical-align: middle;
            border:1px solid #ddd;
            border-radius:8px;
            padding:5px;
        }
        .gallery-img {
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            margin-bottom: 10px;
        }
        .review-card {
            background: #fff;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #D1D1D1;
            padding:0;
            /*background:red;*/
        }
        .review-content {
            border-radius: 12px;
            padding: 20px;
            /*background:red;*/
        }
        .review-footer {
            border:none;
            background: #F2ECE3;
            padding: 10px;
            border-radius: 0 0 12px 12px;
        }
        .profile-img {
            background: #c79f62;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            text-align: center;
            line-height: 40px;
            color: #fff;
            font-weight: bold;
        }
        .rating {
            color: #c79f62;
            font-size: 18px;
        }
        .rating i {
            font-size: 18px;
            margin-left: 12px;
        }
        .contact-card {
            /*border: 1px solid #ddd;*/
            border-radius: 12px;
            padding: 15px;
            background: #F2ECE3;
        }
        .contact-card a {
            text-decoration: none;
            color: #2c3e50;
            font-weight: bold;
        }
        .contact-card img {
            color: #007bff;
            margin-right: 10px;
            font-size: 18px;
        }
        .pdf-section {
            page-break-inside: avoid;
        }
        @media print {
            .pdf-section {
                break-before: page;
                page-break-inside: avoid;
            }
        }
        
            @page {
                margin: 0;
                padding: 0;
                /*page-break-before: always;*/
            }
            .gallery-page {
                page-break-before: always;
                width: 100vw;
                /*height: 70vh;*/
                display: flex;
                align-items: center;
                justify-content: center;
            }
            .gallery-img {
                width: 100%;
                /*height: 100%;*/
                object-fit: contain;
                display: block;
            }
    </style>
</head>
<body>
    <table class="brouchre-section">
        <tr>
            <td align="center">
                <a href="./">
                    <img src="{{ asset('assets/website/images/logo-png.png') }}" alt="Unreal Estate Logo" class="logo">
                </a>
            </td>
        </tr>
        <tr>
            <td>
                @php
                    $firstImage = $property->imagesWebsite->first();
                    $imagePath = public_path($firstImage->filename ?? 'assets/website/images/no-image.png');
                    $imageBase64 = file_exists($imagePath) ? 'data:image/' . pathinfo($imagePath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($imagePath)) : 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/website/images/no-image.png')));
                @endphp
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td><img src="{{ $imageBase64 }}" alt="Property Image" class="header-image"></td>
                    </tr>
                    <tr>
                        <td><h2>{{ $property->unit_name_website ?? '' }}</h2></td>
                    </tr>
                    <tr>
                        <td style="font-size:22px">
                            <!--<div>-->
                                <img src="{{ asset('assets/website/images/icon-mapPin.svg') }}" width="30px" style="vertical-align: middle;">
                                <span style="color:#c79f62;">{{ $property->locationData->location_name ?? '' }}, {{ $property->state ?? ''}}</span>
                            <!--</div>-->
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>
                <table cellpadding="15" class="specs-row" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td width="33%">
                            <div class="spec-item">
                                <div><img src="{{ asset('assets/website/images/icon-house.svg') }}" alt="Bedrooms"></div>
                                {{ $property->no_of_bedrooms == 1 ? 'Bedroom' : 'Bedrooms' }}<br>
                                <strong>{{ $property->no_of_bedrooms }}</strong>
                            </div>
                        </td>
                        <td width="33%">
                            <div class="spec-item">
                                <div><img src="{{ asset('assets/website/images/icon-drop.png') }}" alt="Bathrooms"></div>
                                {{ $property->no_of_bathrooms == 1 ? 'Bathroom' : 'Bathrooms' }}<br>
                                <strong>{{ $property->no_of_bathrooms }}</strong>
                            </div>
                        </td>
                        <td width="33%">
                            <div class="spec-item">
                                <div><img src="{{ asset('assets/website/images/icon-users.svg') }}" alt="Guests"></div>
                                {{ $property->maximum_number_of_guests == 1 ? 'Guest' : 'Guests' }}<br>
                                <strong>{{ $property->maximum_number_of_guests }}</strong>
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <tr>
    <td>
        <table width="100%">
            <tr>
                <td><h3>Property Location</h3></td>
            </tr>
            <tr>
                <td>
                    @php
                        $mapImagePath = public_path('assets/website/images/home-banner-map.png');
                        $mapImageBase64 = file_exists($mapImagePath) ? 'data:image/' . pathinfo($mapImagePath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($mapImagePath)) : 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/website/images/no-image.png')));
                    @endphp
                    <a href="https://www.google.com/maps?q={{ $property->map_latitude ?? '0' }},{{ $property->map_longitude ?? '0' }}&z=18" target="_blank" style="display: block;">
                        <img src="{{ asset('assets/website/images/map-image.png') }}" alt="Map" class="header-image" style="width: 100%; height: auto; display: block;">
                    </a>
                </td>
            </tr>
        </table>
    </td>
</tr>
        
        <tr>
            <td>
                <table width="100%">
                    <tr>
                        <td><h3>About the Property</h3></td>
                    </tr>
                    <tr>
                        <td>
                            <p class="px-3 mb-4">
                            {!! $property->description !!}
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <tr>
            <td>
                <table width="100%" class="pdf-section">
                    <tr>
                        <td><h3>Amenities</h3></td>
                    </tr>
                    <tr>
                        <td>
                            <ul class="amenities-list">
                                @foreach ($property->websiteamenities as $amenity)
                                    @php
                                        $iconPath = public_path('storage/amenities/' . $amenity->amenities_image);
                                        $iconBase64 = file_exists($iconPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($iconPath)) : '';
                                    @endphp
                                    <li>
                                        <table>
                                            <tr>
                                                <td><img src="{{ $iconBase64 }}" class="amenities-small-icon"></td>
                                                <td style="font-size:25px; margin-left:0px;">{{ $amenity->amenities_name }}</td>
                                            </tr>
                                        </table>
                                        <!--<img src="{{ $iconBase64 }}" class="amenities-small-icon">-->
                                        <!--<span class="amenities-name" style="font-size:25px; ">{{ $amenity->amenities_name }}</span>-->
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        
        <tr>
    <td>
        <table width="100%">
            <!--<tr>-->
            <!--    <td><h3>Gallery</h3></td>-->
            <!--</tr>-->
            @foreach ($property->imagesWebsite->skip(1) as $image)
    @php
        $imagePath = public_path($image->filename ?? 'assets/website/images/no-image.png');
        $imageBase64 = file_exists($imagePath)
            ? 'data:image/' . pathinfo($imagePath, PATHINFO_EXTENSION) . ';base64,' . base64_encode(file_get_contents($imagePath))
            : 'data:image/png;base64,' . base64_encode(file_get_contents(public_path('assets/website/images/no-image.png')));
    @endphp

    @if ($imageBase64)
        <tr>
            <td>
                <div class="gallery-page">

                    @if ($loop->first)
                        <h3>Property Images</h3> 
                    @endif

                    <img src="{{ $imageBase64 }}" alt="Gallery Image" class="gallery-img">
                </div>
            </td>
        </tr>
    @endif
@endforeach
        </table>
    </td>
</tr>
        
        @if ($property && $property->homeReviews->isNotEmpty())
            <tr class="pdf-section">
                <td>
                    <table width="100%">
                        <tr>
                            <td><h3>Reviews</h3></td>
                        </tr>
                        <tr>
                            <td>
                                @foreach ($property->homeReviews()->latest()->take(3)->get() as $reviews)
                                    <table width="100%" class="review-card">
                                        <tr>
                                            <td class="review-content">
                                                <p>{{ $reviews->comment ?? '' }}</p>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="review-footer" style="padding:0;">
                                                <table width="100%">
                                                    <tr>
                                                        <td width="50px">
                                                            <div class="profile-img">
                                                                {{ collect(explode(' ', $reviews->guest_name))->map(fn($p) => strtoupper(substr($p, 0, 1)))->join('') }}
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <span style="display: block; color: #2c3e50;">{{ strtoupper($reviews->guest_name) ?? '' }}</span>
                                                            <span class="rating">{{ number_format($reviews->rating, 1) ?? '' }} <img src="{{ asset('assets/website/images/icon-star.svg') }}" style="margin-top:10px;"/></span>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                @endforeach
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endif
        <tr class="pdf-section">
            <td>
                <table width="100%" style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td><h2>Contact Us</h2></td>
                    </tr>
                    <!--<tr>-->
                    <!--    <td class="contact-card">-->
                    <!--        <table width="100%">-->
                                <!--<tr>-->
                                    <!--<td><img src="{{ asset('assets/website/images/icon-web.png') }}"/></td>-->
                                <!--    <td>-->
                                <!--        <img src="{{ asset('assets/website/images/icon-web.png') }}" width="15px"/>-->
                                <!--        <a href="https://unreal.tempsite.in" target="_blank">unreal.tempsite.in</a>-->
                                <!--    </td>-->
                                <!--</tr>-->
                                <!--<tr>-->
                                    <!--<td><img src="{{ asset('assets/website/images/icon-call.png') }}"/></td>-->
                                <!--    <td>-->
                                        <!--<img src="{{ asset('assets/website/images/icon-call.png') }}"/>-->
                                <!--        <a href="tel:+919999999999">+91 99999 9999</a>-->
                                <!--    </td>-->
                                <!--</tr>-->
                                <!--<tr>-->
                                    <!--<td><img src="{{ asset('assets/website/images/icon-mail.png') }}"/></td>-->
                                <!--    <td>-->
                                        <!--<img src="{{ asset('assets/website/images/icon-mail.png') }}"/>-->
                                <!--        <a href="mailto:support@unrealestate.in">support@unrealestate.in</a>-->
                                <!--    </td>-->
                                <!--</tr>-->
                                <!--<tr>-->
                                    <!--<td><img src="{{ asset('assets/website/images/icon-mapPin.svg') }}" style="margin-top:10px;"/></td>-->
                                <!--    <td>-->
                                <!--        123, Example Street, New Delhi, India - 110001-->
                                <!--    </td>-->
                                <!--</tr>-->
                    <!--        </table>-->
                    <!--    </td>-->
                    <!--</tr>-->
                </table>
            </td>
        </tr>
         <tr>
                        <td class="contact-card">
                            <table width="100%">
                                <tr>
                                    <!--<td><img src="{{ asset('assets/website/images/icon-web.png') }}"/></td>-->
                                    <td>
                                        <img src="{{ asset('assets/website/images/icon-web.png') }}" width="28px"/>
                                        <a href="https://unreal.tempsite.in" target="_blank">www.unrealestate.in</a>
                                    </td>
                                </tr>
                                <tr>
                                    <!--<td><img src="{{ asset('assets/website/images/icon-call.png') }}"/></td>-->
                                    <td>
                                        <img src="{{ asset('assets/website/images/icon-call.png') }}" width="25px"/>
                                        <a href="tel:+919920664712">+91 99206 64712</a>
                                    </td>
                                </tr>
                                <tr>
                                    <!--<td><img src="{{ asset('assets/website/images/icon-mail.png') }}"/></td>-->
                                    <td>
                                        <img src="{{ asset('assets/website/images/icon-mail.png') }}" width="25px"/>
                                        <a href="mailto:support@unrealestate.in">support@unrealestate.in</a>
                                    </td>
                                </tr>
                                <tr>
                                    <!--<td><img src="{{ asset('assets/website/images/icon-mapPin.svg') }}" style="margin-top:10px;"/></td>-->
                                    <td>
                                        <img src="{{ asset('assets/website/images/icon-mapPin.svg') }}" width="25px"/>
                                        123, Example Street, New Delhi, India - 110001
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
    </table>
</body>
</html>