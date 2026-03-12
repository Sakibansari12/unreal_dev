<!DOCTYPE html>

<html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml"
    xmlns:o="urn:schemas-microsoft-com:office:office">

<head>
    <meta charset="utf-8"> <!-- utf-8 works for most cases -->
    <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
    <meta name="x-apple-disable-message-reformatting"> <!-- Disable auto-scale in iOS 10 Mail entirely -->
    <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
    <!-- Tell iOS not to automatically link certain text strings. -->
    <meta name="color-scheme" content="light dark">
    <meta name="supported-color-schemes" content="light dark">
    <title></title>
</head>
<style>
    body {
        font-family: "DM Sans", sans-serif;
        margin: 0;
        padding: 20px;
    }

    .invoice-container {
        max-width: 800px;
        margin: 0 auto;
        padding: 20px;
        border: 1px solid #ddd;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 14px;
    }

    td,
    th {
        padding: 8px;
        vertical-align: top;
        text-align: left;
    }
    .title {
        font-size: 24px;
        font-weight: bold;
        text-align: right;
        margin-bottom: 0;
        margin-top: 0;
    }

    .items-table th,
    .items-table td {
        border: 1px solid #ddd;
    }

    .items-table th {
        background-color: #f2f2f2;
        font-weight: bold;
    }

    .notes ul {
        list-style-type: disc;
        margin-left: 20px;
        padding: 0;
    }

    .notes p {
        margin-top: 10px;
        font-weight: bold;
    }

    @media print {
        body {
            background-color: #fff;
        }

        .invoice-container {
            border: none;
            box-shadow: none;
            padding: 0;
        }
    }
</style>

<body>
    <div class="invoice-container">
        <table>

            @php
                $customerDetails = json_decode($detail->customer_detail);
                $companyDetails = $detail->company_details?json_decode($detail->company_detail):null;

                $cgst = round($detail->payable_amount * 0.09);
                $sgst = round($detail->payable_amount * 0.09);
               
                $path = public_path('assets/pms/images/logo.png');
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            @endphp

            <tr>
                <td width="50%">
                    <div style="width:246px; height:57px;">
                        <img src="{{$base64}}" alt="Unreal Estate" width="246" height="57">
                    </div>
                </td>
                <td style="width:50%;" align="right">
                    <h2 class="title">Tax Invoice</h2>
                    <p style="text-align:right; margin-top:5px; margin-bottom:0; color:#666666;">
                        Invoice No: {{ $invoice_no }}</p>
                    <p style="text-align:right; margin-top:5px; margin-bottom:0;  color:#666666;">Date: {{ date('F j, Y') }}</p>
                </td>
            </tr>

            <tr style="border-top: 1.5px solid #111111; padding-top: 30px;">
                <td>
                    <h3 style="margin-top: 8px; color:#c79f62; margin-bottom:10px;">From</h3>
                    <b style="color:#333333;">{{ $detail->property->stateDetail->companyInfo->company_name  }}</b>
                    <p style="margin-top:5px; margin-bottom:0; color:#666666;">{{ $detail->property->stateDetail->companyInfo->company_address  }}</p>

                </td>
                @if($detail->is_company == 1 && $companyDetails)
                    <td>
                        <h3 style="margin-top: 8px; color:#c79f62; margin-bottom:10px;">To</h3>
                        <b style="color:#333333;">{{ $companyDetails->company_name  }}</b><br>
                        <p style="margin-top:5px; margin-bottom:0; color:#666666;">{{ $companyDetails->address  }}</p>
                        <p style="margin-top:5px; margin-bottom:0; color:#666666;">{{ $companyDetails->state  }}-{{ $companyDetails->city  }}, 11096<br></p>
                    </td>
                @else
                    <td>
                    <h3 style="margin-top: 8px; color:#c79f62; margin-bottom:10px;">To</h3>
                    <b style="color:#333333;">{{ $customerDetails->first_name }} {{ $customerDetails->last_name }}</b> <br>
                        <p style="margin-top:5px; margin-bottom:0; color:#666666;">Email: {{ $customerDetails->email }}<br>
                        <p style="margin-top:5px; margin-bottom:0; color:#666666;">Mobile No.: {{ $customerDetails->mobile_number }}</p>
                    </td>
                @endif
            </tr>

            <tr style="color:#666666;">
                <td>GSTIN: {{ $detail->property->stateDetail->companyInfo->gst_no  }}<br>
                    CIN: {{ $detail->property->stateDetail->companyInfo->cin_no  }}
                </td>

                <td>
                    State Code: {{ $detail->property->stateDetail->state_code }}<br>
                    Place of Supply: {{ $detail->property->stateDetail->name }}
                </td>
            </tr>



            <tr>
                <td colspan="2" style="padding-top: 30px;">
                    <table class="items-table" style="width: 100%; border-collapse: collapse; border:1px solid #ddd;">
                        <thead>
                            <tr>
                                <th style="background: #F2ECE3;" colspan="2">Particulars</th>

                                <th style="background: #F2ECE3;">Total Amount</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr style="color:#333333;">
                                <td colspan="2">{{ $detail->property->unit_name }}</td>

                                <td style="text-align: left;">INR 
                                {{
                                    preg_replace(
                                        '/(\d)(?=(\d\d)+\d(\.\d+)?$)/',
                                        '$1,',
                                        number_format((float)$detail->base_price, 2, '.', '')
                                    )
                                }}
                                
                                </td>
                            </tr>
                            @if(!empty($detail->additional_charges))
                                @foreach($detail->additional_charges as $charge)
                                    @php
                                        // Default price
                                        $basePrice = (float)($charge['price'] ?? 0);
                            
                                        // Agar Per_Night hai to multiply kare
                                        $totalCharge = ($charge['type_option'] ?? '') === 'Per_Night'
                                            ? $basePrice * ($detail->no_of_nights ?? 1)
                                            : $basePrice;
                                    @endphp
                            
                                    <tr style="color:#333333;">
                                        <td colspan="2">
                                            {{ $charge['name'] ?? '' }}
                                        </td>
                                        <td style="text-align: left;">
                                            INR {{
                                                preg_replace(
                                                    '/(\d)(?=(\d\d)+\d(\.\d+)?$)/',
                                                    '$1,',
                                                    number_format($totalCharge, 2, '.', '')
                                                )
                                            }}
                                        </td>
                                    </tr>
                                @endforeach
                            @endif



                            <tr style="color:#333333;">
                                <td colspan="3"><p style="margin-top:5px; margin-bottom:0;"><b>Arrival date :</b> {{ $detail->checkin_date }}</p>
                                 <p style="margin-top:5px; margin-bottom:0;"><b> Departure date :</b> {{ $detail->checkout_date }}</p>
                                </td>
                            </tr>


                            <tr style="color:#333333;">
                                <td colspan="2">CGST @ {{ ($detail->tax/2) }}%</td>
                                <td style="text-align: end;">INR 
                                
                                {{
                                    preg_replace(
                                        '/(\d)(?=(\d\d)+\d(\.\d+)?$)/',
                                        '$1,',
                                        number_format((float) ($detail->tax_amount / 2), 2, '.', '')
                                    )
                                }}

                                
                                </td>
                            </tr>

                            <tr style="color:#333333;">
                                <td colspan="2">SGST @ {{ ($detail->tax/2) }}%</td>
                                <td style="text-align: end;">INR {{
                                    preg_replace(
                                        '/(\d)(?=(\d\d)+\d(\.\d+)?$)/',
                                        '$1,',
                                        number_format((float) ($detail->tax_amount / 2), 2, '.', '')
                                    )
                                }}
                                </td>
                            </tr>
                        </tbody>

                        <tfoot style="background: #F2ECE3;">
                            <tr>
                                <td style="text-align: end;" colspan="2"><b>Total License Fee</b></td>
                                <td style="text-align: end;"><b>INR 
                                
                                
                                {{
                                    preg_replace(
                                        '/(\d)(?=(\d\d)+\d(\.\d+)?$)/',
                                        '$1,',
                                        number_format((float)$detail->payable_amount, 2, '.', '')
                                    )
                                }}
                                
                                
                                
                                </b></td>
                            </tr>
                        </tfoot>
                    </table>
                </td>
            </tr>


            <tr style="color:#333333;">
                <td colspan="2">
                    <strong>Total License Fee In Words: {{ $totalPriceInWords }} Only</strong>
                </td>
            </tr>

            {{-- <tr style="color:#333333;">
                <td colspan="2" class="notes">
                    <strong>Note</strong>
                    <ul>
                        <li>Make all Cheque / Demand Draft payable to Unique Vacation Homes Private Limited</li>
                        <li>Terms and conditions of booking and use of said premises apply</li>
                        <li>RCM: No</li>
                    </ul>
                    <p>This is computer generated & does not require signature</p>
                </td>
            </tr> --}}
        </table>
        
    </div>
</body>

</html>

</body>

</html>