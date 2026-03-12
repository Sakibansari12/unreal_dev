<!DOCTYPE html>
<html>
<head>
    <title>Email</title>
    <style>
        body,
        body table,
        body td,
        body p,
        body a,
        body span,
        body a[href^="tel"] {
            font-family: Arial, sans-serif;
            font-size: 14px;
            line-height: 1.5;
            color: #3B2F1E;
        }
        
        @media only screen and (max-width: 600px) {
            body {
                width: 100% !important;
                min-width: 100% !important;
            }
            
            .container {
                width: 100% !important;
                padding: 20px;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0;">
    <table class="container" align="center" border="0" cellpadding="0" cellspacing="0" width="600" style="border-collapse: collapse; border: 8px solid #3bb6b1;">
        {{-- <tr>
            <td align="center" bgcolor="#002341" valign="middle" style="padding: 10px 0 12px;"><img src="https://dhanpatrai.tempsite.in/assets/images/logo.png" width="130" alt="Dhanpat Rai"></td>
        </tr> --}}
        <tr>
            <td valign="middle" style="padding: 10px 25px; background:#3bb6b1;"><h2 style="color: #ffffff; font-size:18px; margin:0">Unreal Estate Registration</h2></td>
        </tr>
        <tr>
            <td style="padding:25px;">
                <h3>Hi, <b>{{ $name }}</b></h3>
                <p>We received a registration request from your email.<br>
                    Your password is:  <b>{{ $password }}</b></p>
                {{-- <p>If you did not request a password reset, please ignore this email or contact support.</p> --}}
            </td>
        </tr>
        <tr>
            <td valign="middle" style="padding: 12px 25px; font-size:14px; background:#E4E4E4;">
                Thank You,<br>
                Unreal Estate.
            </td>
        </tr>
    </table>
</body>
</html>
