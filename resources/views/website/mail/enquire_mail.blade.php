
 <html lang="en" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
 <head>
     <meta charset="utf-8"> <!-- utf-8 works for most cases -->
     <meta name="viewport" content="width=device-width"> <!-- Forcing initial-scale shouldn't be necessary -->
     <meta http-equiv="X-UA-Compatible" content="IE=edge"> <!-- Use the latest (edge) version of IE rendering engine -->
     <meta name="x-apple-disable-message-reformatting">  <!-- Disable auto-scale in iOS 10 Mail entirely -->
     <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no"> <!-- Tell iOS not to automatically link certain text strings. -->
     <meta name="color-scheme" content="light dark">
     <meta name="supported-color-schemes" content="light dark">
     <title></title> <!-- The title tag shows in email notifications, like Android 4.4. -->
 
     <!-- What it does: Makes background images in 72ppi Outlook render at correct size. -->
     <!--[if gte mso 9]>
     <xml>
         <o:OfficeDocumentSettings>
             <o:PixelsPerInch>96</o:PixelsPerInch>
         </o:OfficeDocumentSettings>
     </xml>
     <![endif]-->
 
     <!-- Web Font / @font-face : BEGIN -->
     <!-- NOTE: If web fonts are not required, lines 23 - 41 can be safely removed. -->
 
     <!-- Desktop Outlook chokes on web font references and defaults to Times New Roman, so we force a safe fallback font. -->
     <!--[if mso]>
         <style>
             * {
                 font-family: sans-serif !important;
             }
         </style>
     <![endif]-->
 
     <!-- All other clients get the webfont reference; some will render the font and others will silently fail to the fallbacks. More on that here: https://web.archive.org/web/20190717120616/http://stylecampaign.com/blog/2015/02/webfont-support-in-email/ -->
     <!--[if !mso]><!-->
     <!-- insert web font reference, eg: <link href='https://fonts.googleapis.com/css?family=Roboto:400,700' rel='stylesheet' type='text/css'> -->
     <!--<![endif]-->
 
     <!-- Web Font / @font-face : END -->
 
     <!-- CSS Reset : BEGIN -->
     <style>
 
         /* What it does: Tells the email client that both light and dark styles are provided. A duplicate of meta color-scheme meta tag above. */
         :root {
           color-scheme: light dark;
           supported-color-schemes: light dark;
         }
 
         /* What it does: Remove spaces around the email design added by some email clients. */
         /* Beware: It can remove the padding / margin and add a background color to the compose a reply window. */
         html,
         body {
             margin: 0 auto !important;
             padding: 0 !important;
             height: 100% !important;
             width: 100% !important;
         }
 
         /* What it does: Stops email clients resizing small text. */
         * {
             -ms-text-size-adjust: 100%;
             -webkit-text-size-adjust: 100%;
         }
 
         /* What it does: Centers email on Android 4.4 */
         div[style*="margin: 16px 0"] {
             margin: 0 !important;
         }
 
         /* What it does: forces Samsung Android mail clients to use the entire viewport */
         #MessageViewBody, #MessageWebViewDiv{
             width: 100% !important;
         }
 
         /* What it does: Stops Outlook from adding extra spacing to tables. */
         table,
         td {
             mso-table-lspace: 0pt !important;
             mso-table-rspace: 0pt !important;
         }
 
         /* What it does: Replaces default bold style. */
         th {
             font-weight: normal;
         }
 
         /* What it does: Fixes webkit padding issue. */
         table {
             border-spacing: 0 !important;
             border-collapse: collapse !important;
             table-layout: fixed !important;
             margin: 0 auto !important;
         }
 
         /* What it does: Prevents Windows 10 Mail from underlining links despite inline CSS. Styles for underlined links should be inline. */
         a {
             text-decoration: none;
         }
 
         /* What it does: Uses a better rendering method when resizing images in IE. */
         img {
             -ms-interpolation-mode:bicubic;
         }
 
         /* What it does: A work-around for email clients meddling in triggered links. */
         a[x-apple-data-detectors],  /* iOS */
         .unstyle-auto-detected-links a,
         .aBn {
             border-bottom: 0 !important;
             cursor: default !important;
             color: inherit !important;
             text-decoration: none !important;
             font-size: inherit !important;
             font-family: inherit !important;
             font-weight: inherit !important;
             line-height: inherit !important;
         }
 
         /* What it does: Prevents Gmail from changing the text color in conversation threads. */
         .im {
             color: inherit !important;
         }
 
         /* What it does: Prevents Gmail from displaying a download button on large, non-linked images. */
         .a6S {
            display: none !important;
            opacity: 0.01 !important;
         }
         /* If the above doesn't work, add a .g-img class to any image in question. */
         img.g-img + div {
            display: none !important;
         }
 
         /* What it does: Removes right gutter in Gmail iOS app: https://github.com/TedGoas/Cerberus/issues/89  */
         /* Create one of these media queries for each additional viewport size you'd like to fix */
 
         /* iPhone 4, 4S, 5, 5S, 5C, and 5SE */
         @media only screen and (min-device-width: 320px) and (max-device-width: 374px) {
             u ~ div .email-container {
                 min-width: 320px !important;
             }
         }
         /* iPhone 6, 6S, 7, 8, and X */
         @media only screen and (min-device-width: 375px) and (max-device-width: 413px) {
             u ~ div .email-container {
                 min-width: 375px !important;
             }
         }
         /* iPhone 6+, 7+, and 8+ */
         @media only screen and (min-device-width: 414px) {
             u ~ div .email-container {
                 min-width: 414px !important;
             }
         }
 
     </style>
     <!-- CSS Reset : END -->
 
     <!-- Progressive Enhancements : BEGIN -->
     <style>
 
         /* What it does: Hover styles for buttons */
         .button-td,
         .button-a {
             transition: all 100ms ease-in;
         }
         .button-td-primary:hover,
         .button-a-primary:hover {
             background: #555555 !important;
             border-color: #555555 !important;
         }
 
         /* Media Queries */
         @media screen and (max-width: 768px) {
 
             .email-container {
                 width: 100% !important;
                 margin: auto !important;
             }
 
             /* What it does: Forces table cells into full-width rows. */
             .stack-column,
             .stack-column-center {
                 display: block !important;
                 width: 100% !important;
                 max-width: 100% !important;
                 direction: ltr !important;
             }
             /* And center justify these ones. */
             .stack-column-center {
                 text-align: center !important;
             }
 
             /* What it does: Generic utility class for centering. Useful for images, buttons, and nested tables. */
             .center-on-narrow {
                 text-align: center !important;
                 display: block !important;
                 margin-left: auto !important;
                 margin-right: auto !important;
                 float: none !important;
             }
             table.center-on-narrow {
                 display: inline-block !important;
             }
 
             /* What it does: Adjust typography on small screens to improve readability */
             .email-container p {
                 font-size: 17px !important;
             }
         }
 
         /* Dark Mode Styles : BEGIN */
         @media (prefers-color-scheme: dark) {
             .email-bg {
                 background: #111111 !important;
             }
             .darkmode-bg {
                 background: #222222 !important;
             }
             h1,
             h2,
             h3,
             p,
             li,
             .darkmode-text,
             .email-container a:not([class]) {
                 color: #F7F7F9 !important;
             }
             td.button-td-primary,
             td.button-td-primary a {
                 background: #ffffff !important;
                 border-color: #ffffff !important;
                 color: #222222 !important;
             }
             td.button-td-primary:hover,
             td.button-td-primary a:hover {
                 background: #cccccc !important;
                 border-color: #cccccc !important;
             }
             .footer td {
                 color: #aaaaaa !important;
             }
             .darkmode-fullbleed-bg {
                 background-color: #0F3016 !important;
             }
         }
         /* Dark Mode Styles : END */
     </style>
     <!-- Progressive Enhancements : END -->
 
 </head>
 <!--
     The email background color (#222222) is defined in three places:
     1. body tag: for most email clients
     2. center tag: for Gmail and Inbox mobile apps and web versions of Gmail, GSuite, Inbox, Yahoo, AOL, Libero, Comcast, freenet, Mail.ru, Orange.fr
     3. mso conditional: For Windows 10 Mail
 -->
 <body width="100%" style="margin: 0; padding: 0 !important; mso-line-height-rule: exactly; background-color: #ffffff;" class="email-bg">
   <center role="article" aria-roledescription="email" lang="en" style="width: 100%; background-color: #ffffff;" class="email-bg">
     <!--[if mso | IE]>
     <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #ffffff;" class="email-bg">
     <tr>
     <td>
     <![endif]-->
         <!-- Email Body : BEGIN -->
         <table align="center" role="presentation" cellspacing="0" cellpadding="0" border="0" width="768" style="margin: auto;background-color: #ffffff;" class="email-container">
             <!-- Email Header : BEGIN -->
              <tr>
                <td align="center" style="padding: 20px 30px; text-align: center; background-color: #ffffff; border-bottom: 1px solid #cccccc;">
                    <a href="{{ route('index') }}">
                        <img 
                            src="{{ asset('assets/pms/images/logo.png') }}" 
                            alt="Logo" 
                            width="180" 
                            height="46" 
                            style="display: block; margin: 0 auto;">
                    </a>
                </td>
            </tr>
             <!-- Email Header : END -->
 
             <tr>
                 <td style="padding: 20px 0 10px; font-family: Arial, sans-serif; font-size: 15px; line-height: 20px; color: #000000;">
                     <h1 style="margin:0px 0px 5px 0px; font-family: Arial, sans-serif; font-size: 20px; line-height: 30px; color: #333333; font-weight: bold;">Dear Admin,</h1>
                     <p>
                        We have received a booking enquiry with the following details:
                    </p>
                     <p><strong>Name:</strong> {{ $data['name'] }}</p>
                     <p><strong>Email Address:</strong> {{ $data['email'] }}</p>
                     <p><strong>Mobile Number:</strong> {{ $data['phone_number'] }}</p> 
                     <p><strong>Message:</strong> {{ $data['message'] }}</p> 
                 </td>                
             </tr>
             <tr>
                <td style="background-color: #ffffff;border-bottom: 10px solid #000000;" class="darkmode-bg">
                    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%">
                        <tbody>
                        <tr>
                            <td style="padding: 30px 30px 0 30px; font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; color: #000000;">
                                <strong>Unreal Estate PRIVATE LIMITED</strong> <br>
                                {{-- House No Plot No B Chalta No 9 P.T.S.149, <br>
                                Next to Hotel Blue Bay, Miramar, Panaji,<br>
                                North Goa, Goa, India, 403001 --}}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding: 10px 30px 30px; font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; color: #000000;">
                                <table role="presentation" cellspacing="0" cellpadding="0" border="0" style="width: auto;margin: 0 !important;">
                                    <tbody><tr>
                                        <td style="font-family: Arial, sans-serif; font-size: 14px; line-height: 20px; color: #000000;vertical-align: middle;">Follow us</td>
                                        <td style="vertical-align: middle;padding-left: 5px;">
                                            <a href="#" target="_blank">
                                                <img src="{{ asset('assets/pms/images/facebook.png') }}" alt="Facebook" width="24" height="24" border="0" style="height: auto; font-family: Arial, sans-serif; font-size: 14px; line-height: 15px; display: block; color: #000000;">
                                            </a>
                                        </td>
                                        <td style="vertical-align: middle;padding-left: 5px;">
                                            <a href="#" target="_blank">
                                                <img src="{{ asset('assets/pms/images/instagram.png') }}" alt="Instagram" width="24" height="24" border="0" style="height: auto; font-family: Arial, sans-serif; font-size: 14px; line-height: 15px; display: block; color: #000000;">
                                            </a>
                                        </td>
                                    </tr>
                                </tbody></table>
                            </td>
                        </tr>
                    </tbody>
                   </table>
                </td>
            </tr>
        
         </table>
         <!-- Email Body : END -->
 
     <!--[if mso | IE]>
     </td>
     </tr>
     </table>
     <![endif]-->
     </center>
 </body>
 
 
 </html>