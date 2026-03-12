<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unreal Estate</title>
</head>

<body>

    <table width="100%" cellpadding="0" cellspacing="0"
        style="padding: 40px 0; font-family: Arial, Helvetica, sans-serif;">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                    style="background: #ffffff; border-radius: 12px; overflow: hidden; border: 1px solid lightgray;">
                    <tr>
                        <td style="background-color: #fdd1ea;  color: #A8266F; padding: 30px; text-align: center;">
                            <img src="{{ asset('assets/pms/images/logo.png') }}" alt="">
                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 0px 30px;">
                            <p
                                style="margin: 10px 0px 0px; font-size: 16px; color: #A8266F; font-weight: bold; font-size: 15px;">
                                Property List Request</p>

                        </td>
                    </tr>
                    <tr>
                        <td style="padding: 10px 30px;">
                            <table width="100%" cellpadding="0" cellspacing="0"
                                style="border-collapse: collapse; font-size: 14px;">
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #eee;"><strong
                                            style="color: #333;"> First Name:</strong></td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #eee;">
                                        {{ $data['first_name'] }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #eee;"><strong
                                            style="color: #333;">Last Name:</strong></td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #eee;">{{ $data['last_name'] }}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #eee;"><strong
                                            style="color: #333;"> Email:</strong></td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #eee;">{{ $data['email'] }}
                                    </td>
                                </tr>

                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #eee;"><strong
                                            style="color: #333;">Phone:</strong></td>
                                    <td style="padding: 12px 0;  border-bottom: 1px solid #eee;">
                                        {{ $data['country_code'] }}  {{ $data['phone_number'] }}</td>
                                </tr>
                                @php
                                    $selectedLocation = $locationTypes->firstWhere('id', $data['location_type_id']);
                                @endphp

                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #eee;"><strong
                                            style="color: #333;">Location/City:</strong></td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #eee;">
                                        {{ $selectedLocation->location_name ?? 'N/A' }}</td>
                                </tr>
                                @php
                                    $selectedProperty = $propertyTypes->firstWhere('id', $data['property_type_id']);
                                @endphp
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #eee;"><strong
                                            style="color: #333;">Properties Type:</strong></td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #eee;">
                                        {{ $selectedProperty->name ?? 'N/A' }}</td>
                                </tr>


                    </tr>

                    <tr>
                        <td style="padding: 12px 0; border-bottom: 1px solid #eee;"><strong
                                style="color: #333;">Description:</strong></td>
                        <td style="padding: 12px 0; border-bottom: 1px solid #eee;">{{ $data['description'] ?? 'N/A' }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    </td>
    </tr>
    </table>

</body>

</html>
