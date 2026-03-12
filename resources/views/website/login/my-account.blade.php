@extends('website.layouts.app')
@section('content')
    <div class="cms-pages">
        <section class="section section-account fade section-bg" style="opacity: 1;">
            <div class="container">
                <div class="mb-5">
                    <h3 class="h2 mb-0">Hello, {{ $user->name }}</h3>
                    <p>Welcome to your account page. You can view all your personal data here.</p>
                </div>

                <div class="row g-5">
                    <div class="col-12 col-lg-auto">
                        <div class="list-group">
                            <a href="/my-account" class="list-group-item active">
                                <svg xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 640 512"><!--!Font Awesome Free 6.7.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                    <path
                                        d="M224 0a128 128 0 1 1 0 256A128 128 0 1 1 224 0zM178.3 304l91.4 0c11.8 0 23.4 1.2 34.5 3.3c-2.1 18.5 7.4 35.6 21.8 44.8c-16.6 10.6-26.7 31.6-20 53.3c4 12.9 9.4 25.5 16.4 37.6s15.2 23.1 24.4 33c15.7 16.9 39.6 18.4 57.2 8.7l0 .9c0 9.2 2.7 18.5 7.9 26.3L29.7 512C13.3 512 0 498.7 0 482.3C0 383.8 79.8 304 178.3 304zM436 218.2c0-7 4.5-13.3 11.3-14.8c10.5-2.4 21.5-3.7 32.7-3.7s22.2 1.3 32.7 3.7c6.8 1.5 11.3 7.8 11.3 14.8l0 30.6c7.9 3.4 15.4 7.7 22.3 12.8l24.9-14.3c6.1-3.5 13.7-2.7 18.5 2.4c7.6 8.1 14.3 17.2 20.1 27.2s10.3 20.4 13.5 31c2.1 6.7-1.1 13.7-7.2 17.2l-25 14.4c.4 4 .7 8.1 .7 12.3s-.2 8.2-.7 12.3l25 14.4c6.1 3.5 9.2 10.5 7.2 17.2c-3.3 10.6-7.8 21-13.5 31s-12.5 19.1-20.1 27.2c-4.8 5.1-12.5 5.9-18.5 2.4l-24.9-14.3c-6.9 5.1-14.3 9.4-22.3 12.8l0 30.6c0 7-4.5 13.3-11.3 14.8c-10.5 2.4-21.5 3.7-32.7 3.7s-22.2-1.3-32.7-3.7c-6.8-1.5-11.3-7.8-11.3-14.8l0-30.5c-8-3.4-15.6-7.7-22.5-12.9l-24.7 14.3c-6.1 3.5-13.7 2.7-18.5-2.4c-7.6-8.1-14.3-17.2-20.1-27.2s-10.3-20.4-13.5-31c-2.1-6.7 1.1-13.7 7.2-17.2l24.8-14.3c-.4-4.1-.7-8.2-.7-12.4s.2-8.3 .7-12.4L343.8 325c-6.1-3.5-9.2-10.5-7.2-17.2c3.3-10.6 7.7-21 13.5-31s12.5-19.1 20.1-27.2c4.8-5.1 12.4-5.9 18.5-2.4l24.8 14.3c6.9-5.1 14.5-9.4 22.5-12.9l0-30.5zm92.1 133.5a48.1 48.1 0 1 0 -96.1 0 48.1 48.1 0 1 0 96.1 0z" />
                                </svg>
                                <span>My Account</span>
                            </a>
                            <a href="/my-bookings" class="list-group-item list-group-item-action">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 640 512">>
                                    <path
                                        d="M36.8 192l412.8 0c20.2-19.8 47.9-32 78.4-32c30.5 0 58.1 12.2 78.3 31.9c18.9-1.6 33.7-17.4 33.7-36.7c0-7.3-2.2-14.4-6.2-20.4L558.2 21.4C549.3 8 534.4 0 518.3 0L121.7 0c-16 0-31 8-39.9 21.4L6.2 134.7c-4 6.1-6.2 13.2-6.2 20.4C0 175.5 16.5 192 36.8 192zM384 224l-64 0 0 160-192 0 0-160-64 0 0 160 0 80c0 26.5 21.5 48 48 48l224 0c26.5 0 48-21.5 48-48l0-80 0-32 0-128zm144 16c17.7 0 32 14.3 32 32l0 48-64 0 0-48c0-17.7 14.3-32 32-32zm-80 32l0 48c-17.7 0-32 14.3-32 32l0 128c0 17.7 14.3 32 32 32l160 0c17.7 0 32-14.3 32-32l0-128c0-17.7-14.3-32-32-32l0-48c0-44.2-35.8-80-80-80s-80 35.8-80 80z" />
                                </svg>
                                <span>My Bookings</span>
                            </a>
                            <a href="/change-password" class="list-group-item list-group-item-action"><svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 448 512"><!--!Font Awesome Free 6.7.0 by @fontawesome - https://fontawesome.com License - https://fontawesome.com/license/free Copyright 2024 Fonticons, Inc.-->
                                    <path
                                        d="M144 144l0 48 160 0 0-48c0-44.2-35.8-80-80-80s-80 35.8-80 80zM80 192l0-48C80 64.5 144.5 0 224 0s144 64.5 144 144l0 48 16 0c35.3 0 64 28.7 64 64l0 192c0 35.3-28.7 64-64 64L64 512c-35.3 0-64-28.7-64-64L0 256c0-35.3 28.7-64 64-64l16 0z" />
                                </svg> <span>Change Password</span></a>
                        </div>
                    </div>
                    <div class="col">
                        <div class="account-box" style="max-width: 600px;">
                            <form id="profilesubmit" method="post">
                                @csrf()
                                <div class="row gx-3 mb-3">
                                    <div class="col-12 col-md">
                                        <!-- <label for="">First Name</label> -->
                                        <input type="text" class="form-control" placeholder="First Name"
                                            value="{{ $user->name ?? '' }}" name="first_name">
                                        <div class="error text-danger" id="first_name_error"></div>
                                    </div>
                                </div>
                                <div class="row gx-3 mb-3">
                                    <div class="col-12 col-md">
                                        <!-- <label for="">First Name</label> -->
                                        <input type="text" class="form-control" placeholder="Last Name"
                                            value="{{ $user->last_name ?? '' }}" name="last_name">
                                        <div class="error text-danger" id="last_name_error"></div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <input type="email" class="form-control" placeholder="Email"
                                        value="{{ $user->email ?? '' }}" name="email" readonly>
                                    <div class="error text-danger" id="email_error"></div>
                                </div>
                                {{-- <div class="form-group mb-3">
                                    <input type="text" class="form-control" placeholder="Phone Number"
                                        value="{{ $user->mobile_no ?? '' }}" readonly>
                                </div> --}}
                                <div class="col-12">
                                    <div class="row mb-3">
                                        <div class="col-auto">
                                            <select name="country_code" id="country_code" class="form-control country_code" style="width:100%;">
                                                <option data-countryCode="IN" value="91" selected>India (+91)</option>
                                                <option data-countryCode="GB" value="44">Norway (+47)</option>
                                                <option data-countryCode="US" value="1">UK (+44)</option>
                                                <option data-countryCode="DZ" value="213">Algeria (+213)</option>
                                                <option data-countryCode="AD" value="376">Andorra (+376)</option>
                                                <option data-countryCode="AO" value="244">Angola (+244)</option>
                                                <option data-countryCode="AI" value="1264">Anguilla (+1264)</option>
                                                <option data-countryCode="AG" value="1268">Antigua &amp; Barbuda (+1268)</option>
                                                <option data-countryCode="AR" value="54">Argentina (+54)</option>
                                                <option data-countryCode="AM" value="374">Armenia (+374)</option>
                                                <option data-countryCode="AW" value="297">Aruba (+297)</option>
                                                <option data-countryCode="AU" value="61">Australia (+61)</option>
                                                <option data-countryCode="AT" value="43">Austria (+43)</option>
                                                <option data-countryCode="AZ" value="994">Azerbaijan (+994)</option>
                                                <option data-countryCode="BS" value="1242">Bahamas (+1242)</option>
                                                <option data-countryCode="BH" value="973">Bahrain (+973)</option>
                                                <option data-countryCode="BD" value="880">Bangladesh (+880)</option>
                                                <option data-countryCode="BB" value="1246">Barbados (+1246)</option>
                                                <option data-countryCode="BY" value="375">Belarus (+375)</option>
                                                <option data-countryCode="BE" value="32">Belgium (+32)</option>
                                                <option data-countryCode="BZ" value="501">Belize (+501)</option>
                                                <option data-countryCode="BJ" value="229">Benin (+229)</option>
                                                <option data-countryCode="BM" value="1441">Bermuda (+1441)</option>
                                                <option data-countryCode="BT" value="975">Bhutan (+975)</option>
                                                <option data-countryCode="BO" value="591">Bolivia (+591)</option>
                                                <option data-countryCode="BA" value="387">Bosnia Herzegovina (+387)</option>
                                                <option data-countryCode="BW" value="267">Botswana (+267)</option>
                                                <option data-countryCode="BR" value="55">Brazil (+55)</option>
                                                <option data-countryCode="BN" value="673">Brunei (+673)</option>
                                                <option data-countryCode="BG" value="359">Bulgaria (+359)</option>
                                                <option data-countryCode="BF" value="226">Burkina Faso (+226)</option>
                                                <option data-countryCode="BI" value="257">Burundi (+257)</option>
                                                <option data-countryCode="KH" value="855">Cambodia (+855)</option>
                                                <option data-countryCode="CM" value="237">Cameroon (+237)</option>
                                                <option data-countryCode="CA" value="1">Canada (+1)</option>
                                                <option data-countryCode="CV" value="238">Cape Verde Islands (+238)</option>
                                                <option data-countryCode="KY" value="1345">Cayman Islands (+1345)</option>
                                                <option data-countryCode="CF" value="236">Central African Republic (+236)</option>
                                                <option data-countryCode="CL" value="56">Chile (+56)</option>
                                                <option data-countryCode="CN" value="86">China (+86)</option>
                                                <option data-countryCode="CO" value="57">Colombia (+57)</option>
                                                <option data-countryCode="KM" value="269">Comoros (+269)</option>
                                                <option data-countryCode="CG" value="242">Congo (+242)</option>
                                                <option data-countryCode="CK" value="682">Cook Islands (+682)</option>
                                                <option data-countryCode="CR" value="506">Costa Rica (+506)</option>
                                                <option data-countryCode="HR" value="385">Croatia (+385)</option>
                                                <option data-countryCode="CU" value="53">Cuba (+53)</option>
                                                <option data-countryCode="CY" value="90392">Cyprus North (+90392)</option>
                                                <option data-countryCode="CY" value="357">Cyprus South (+357)</option>
                                                <option data-countryCode="CZ" value="42">Czech Republic (+42)</option>
                                                <option data-countryCode="DK" value="45">Denmark (+45)</option>
                                                <option data-countryCode="DJ" value="253">Djibouti (+253)</option>
                                                <option data-countryCode="DM" value="1809">Dominica (+1809)</option>
                                                <option data-countryCode="DO" value="1809">Dominican Republic (+1809)</option>
                                                <option data-countryCode="EC" value="593">Ecuador (+593)</option>
                                                <option data-countryCode="EG" value="20">Egypt (+20)</option>
                                                <option data-countryCode="SV" value="503">El Salvador (+503)</option>
                                                <option data-countryCode="GQ" value="240">Equatorial Guinea (+240)</option>
                                                <option data-countryCode="ER" value="291">Eritrea (+291)</option>
                                                <option data-countryCode="EE" value="372">Estonia (+372)</option>
                                                <option data-countryCode="ET" value="251">Ethiopia (+251)</option>
                                                <option data-countryCode="FK" value="500">Falkland Islands (+500)</option>
                                                <option data-countryCode="FO" value="298">Faroe Islands (+298)</option>
                                                <option data-countryCode="FJ" value="679">Fiji (+679)</option>
                                                <option data-countryCode="FI" value="358">Finland (+358)</option>
                                                <option data-countryCode="FR" value="33">France (+33)</option>
                                                <option data-countryCode="GF" value="594">French Guiana (+594)</option>
                                                <option data-countryCode="PF" value="689">French Polynesia (+689)</option>
                                                <option data-countryCode="GA" value="241">Gabon (+241)</option>
                                                <option data-countryCode="GM" value="220">Gambia (+220)</option>
                                                <option data-countryCode="GE" value="7880">Georgia (+7880)</option>
                                                <option data-countryCode="DE" value="49">Germany (+49)</option>
                                                <option data-countryCode="GH" value="233">Ghana (+233)</option>
                                                <option data-countryCode="GI" value="350">Gibraltar (+350)</option>
                                                <option data-countryCode="GR" value="30">Greece (+30)</option>
                                                <option data-countryCode="GL" value="299">Greenland (+299)</option>
                                                <option data-countryCode="GD" value="1473">Grenada (+1473)</option>
                                                <option data-countryCode="GP" value="590">Guadeloupe (+590)</option>
                                                <option data-countryCode="GU" value="671">Guam (+671)</option>
                                                <option data-countryCode="GT" value="502">Guatemala (+502)</option>
                                                <option data-countryCode="GN" value="224">Guinea (+224)</option>
                                                <option data-countryCode="GW" value="245">Guinea - Bissau (+245)</option>
                                                <option data-countryCode="GY" value="592">Guyana (+592)</option>
                                                <option data-countryCode="HT" value="509">Haiti (+509)</option>
                                                <option data-countryCode="HN" value="504">Honduras (+504)</option>
                                                <option data-countryCode="HK" value="852">Hong Kong (+852)</option>
                                                <option data-countryCode="HU" value="36">Hungary (+36)</option>
                                                <option data-countryCode="IS" value="354">Iceland (+354)</option>
                                                <option data-countryCode="ID" value="62">Indonesia (+62)</option>
                                                <option data-countryCode="IR" value="98">Iran (+98)</option>
                                                <option data-countryCode="IQ" value="964">Iraq (+964)</option>
                                                <option data-countryCode="IE" value="353">Ireland (+353)</option>
                                                <option data-countryCode="IL" value="972">Israel (+972)</option>
                                                <option data-countryCode="IT" value="39">Italy (+39)</option>
                                                <option data-countryCode="JM" value="1876">Jamaica (+1876)</option>
                                                <option data-countryCode="JP" value="81">Japan (+81)</option>
                                                <option data-countryCode="JO" value="962">Jordan (+962)</option>
                                                <option data-countryCode="KZ" value="7">Kazakhstan (+7)</option>
                                                <option data-countryCode="KE" value="254">Kenya (+254)</option>
                                                <option data-countryCode="KI" value="686">Kiribati (+686)</option>
                                                <option data-countryCode="KP" value="850">Korea North (+850)</option>
                                                <option data-countryCode="KR" value="82">Korea South (+82)</option>
                                                <option data-countryCode="KW" value="965">Kuwait (+965)</option>
                                                <option data-countryCode="KG" value="996">Kyrgyzstan (+996)</option>
                                                <option data-countryCode="LA" value="856">Laos (+856)</option>
                                                <option data-countryCode="LV" value="371">Latvia (+371)</option>
                                                <option data-countryCode="LB" value="961">Lebanon (+961)</option>
                                                <option data-countryCode="LS" value="266">Lesotho (+266)</option>
                                                <option data-countryCode="LR" value="231">Liberia (+231)</option>
                                                <option data-countryCode="LY" value="218">Libya (+218)</option>
                                                <option data-countryCode="LI" value="417">Liechtenstein (+417)</option>
                                                <option data-countryCode="LT" value="370">Lithuania (+370)</option>
                                                <option data-countryCode="LU" value="352">Luxembourg (+352)</option>
                                                <option data-countryCode="MO" value="853">Macao (+853)</option>
                                                <option data-countryCode="MK" value="389">Macedonia (+389)</option>
                                                <option data-countryCode="MG" value="261">Madagascar (+261)</option>
                                                <option data-countryCode="MW" value="265">Malawi (+265)</option>
                                                <option data-countryCode="MY" value="60">Malaysia (+60)</option>
                                                <option data-countryCode="MV" value="960">Maldives (+960)</option>
                                                <option data-countryCode="ML" value="223">Mali (+223)</option>
                                                <option data-countryCode="MT" value="356">Malta (+356)</option>
                                                <option data-countryCode="MH" value="692">Marshall Islands (+692)</option>
                                                <option data-countryCode="MQ" value="596">Martinique (+596)</option>
                                                <option data-countryCode="MR" value="222">Mauritania (+222)</option>
                                                <option data-countryCode="YT" value="269">Mayotte (+269)</option>
                                                <option data-countryCode="MX" value="52">Mexico (+52)</option>
                                                <option data-countryCode="FM" value="691">Micronesia (+691)</option>
                                                <option data-countryCode="MD" value="373">Moldova (+373)</option>
                                                <option data-countryCode="MC" value="377">Monaco (+377)</option>
                                                <option data-countryCode="MN" value="976">Mongolia (+976)</option>
                                                <option data-countryCode="MS" value="1664">Montserrat (+1664)</option>
                                                <option data-countryCode="MA" value="212">Morocco (+212)</option>
                                                <option data-countryCode="MZ" value="258">Mozambique (+258)</option>
                                                <option data-countryCode="MN" value="95">Myanmar (+95)</option>
                                                <option data-countryCode="NA" value="264">Namibia (+264)</option>
                                                <option data-countryCode="NR" value="674">Nauru (+674)</option>
                                                <option data-countryCode="NP" value="977">Nepal (+977)</option>
                                                <option data-countryCode="NL" value="31">Netherlands (+31)</option>
                                                <option data-countryCode="NC" value="687">New Caledonia (+687)</option>
                                                <option data-countryCode="NZ" value="64">New Zealand (+64)</option>
                                                <option data-countryCode="NI" value="505">Nicaragua (+505)</option>
                                                <option data-countryCode="NE" value="227">Niger (+227)</option>
                                                <option data-countryCode="NG" value="234">Nigeria (+234)</option>
                                                <option data-countryCode="NU" value="683">Niue (+683)</option>
                                                <option data-countryCode="NF" value="672">Norfolk Islands (+672)</option>
                                                <option data-countryCode="NP" value="670">Northern Marianas (+670)</option>
                                                <option data-countryCode="NO" value="47">Norway (+47)</option>
                                                <option data-countryCode="OM" value="968">Oman (+968)</option>
                                                <option data-countryCode="PW" value="680">Palau (+680)</option>
                                                <option data-countryCode="PA" value="507">Panama (+507)</option>
                                                <option data-countryCode="PG" value="675">Papua New Guinea (+675)</option>
                                                <option data-countryCode="PY" value="595">Paraguay (+595)</option>
                                                <option data-countryCode="PE" value="51">Peru (+51)</option>
                                                <option data-countryCode="PH" value="63">Philippines (+63)</option>
                                                <option data-countryCode="PL" value="48">Poland (+48)</option>
                                                <option data-countryCode="PT" value="351">Portugal (+351)</option>
                                                <option data-countryCode="PR" value="1787">Puerto Rico (+1787)</option>
                                                <option data-countryCode="QA" value="974">Qatar (+974)</option>
                                                <option data-countryCode="RE" value="262">Reunion (+262)</option>
                                                <option data-countryCode="RO" value="40">Romania (+40)</option>
                                                <option data-countryCode="RU" value="7">Russia (+7)</option>
                                                <option data-countryCode="RW" value="250">Rwanda (+250)</option>
                                                <option data-countryCode="SM" value="378">San Marino (+378)</option>
                                                <option data-countryCode="ST" value="239">Sao Tome &amp; Principe (+239)</option>
                                                <option data-countryCode="SA" value="966">Saudi Arabia (+966)</option>
                                                <option data-countryCode="SN" value="221">Senegal (+221)</option>
                                                <option data-countryCode="CS" value="381">Serbia (+381)</option>
                                                <option data-countryCode="SC" value="248">Seychelles (+248)</option>
                                                <option data-countryCode="SL" value="232">Sierra Leone (+232)</option>
                                                <option data-countryCode="SG" value="65">Singapore (+65)</option>
                                                <option data-countryCode="SK" value="421">Slovak Republic (+421)</option>
                                                <option data-countryCode="SI" value="386">Slovenia (+386)</option>
                                                <option data-countryCode="SB" value="677">Solomon Islands (+677)</option>
                                                <option data-countryCode="SO" value="252">Somalia (+252)</option>
                                                <option data-countryCode="ZA" value="27">South Africa (+27)</option>
                                                <option data-countryCode="ES" value="34">Spain (+34)</option>
                                                <option data-countryCode="LK" value="94">Sri Lanka (+94)</option>
                                                <option data-countryCode="SH" value="290">St. Helena (+290)</option>
                                                <option data-countryCode="KN" value="1869">St. Kitts (+1869)</option>
                                                <option data-countryCode="SC" value="1758">St. Lucia (+1758)</option>
                                                <option data-countryCode="SD" value="249">Sudan (+249)</option>
                                                <option data-countryCode="SR" value="597">Suriname (+597)</option>
                                                <option data-countryCode="SZ" value="268">Swaziland (+268)</option>
                                                <option data-countryCode="SE" value="46">Sweden (+46)</option>
                                                <option data-countryCode="CH" value="41">Switzerland (+41)</option>
                                                <option data-countryCode="SI" value="963">Syria (+963)</option>
                                                <option data-countryCode="TW" value="886">Taiwan (+886)</option>
                                                <option data-countryCode="TJ" value="7">Tajikstan (+7)</option>
                                                <option data-countryCode="TH" value="66">Thailand (+66)</option>
                                                <option data-countryCode="TG" value="228">Togo (+228)</option>
                                                <option data-countryCode="TO" value="676">Tonga (+676)</option>
                                                <option data-countryCode="TT" value="1868">Trinidad &amp; Tobago (+1868)</option>
                                                <option data-countryCode="TN" value="216">Tunisia (+216)</option>
                                                <option data-countryCode="TR" value="90">Turkey (+90)</option>
                                                <option data-countryCode="TM" value="7">Turkmenistan (+7)</option>
                                                <option data-countryCode="TM" value="993">Turkmenistan (+993)</option>
                                                <option data-countryCode="TC" value="1649">Turks &amp; Caicos Islands (+1649)</option>
                                                <option data-countryCode="TV" value="688">Tuvalu (+688)</option>
                                                <option data-countryCode="UG" value="256">Uganda (+256)</option>
                                                <option data-countryCode="UA" value="380">Ukraine (+380)</option>
                                                <option data-countryCode="AE" value="971">United Arab Emirates (+971)</option>
                                                <option data-countryCode="UY" value="598">Uruguay (+598)</option>
                                                <option data-countryCode="UZ" value="7">Uzbekistan (+7)</option>
                                                <option data-countryCode="VU" value="678">Vanuatu (+678)</option>
                                                <option data-countryCode="VA" value="379">Vatican City (+379)</option>
                                                <option data-countryCode="VE" value="58">Venezuela (+58)</option>
                                                <option data-countryCode="VN" value="84">Vietnam (+84)</option>
                                                <option data-countryCode="VG" value="84">Virgin Islands - British (+1284)</option>
                                                <option data-countryCode="VI" value="84">Virgin Islands - US (+1340)</option>
                                                <option data-countryCode="WF" value="681">Wallis &amp; Futuna (+681)</option>
                                                <option data-countryCode="YE" value="969">Yemen (North)(+969)</option>
                                                <option data-countryCode="YE" value="967">Yemen (South)(+967)</option>
                                                <option data-countryCode="ZM" value="260">Zambia (+260)</option>
                                                <option data-countryCode="ZW" value="263">Zimbabwe (+263)</option>
                                            </select>
                                        </div>
                                        <div class="col">
                                            <input type="number" class="form-control" name="mobile" oninput="validateLength(this)"
                                            maxlength="12" minlength="6" value="{{ $user->mobile_no ?? ''}}" placeholder="Phone Number" readonly>
                                        </div>
                                        <div class="error text-danger mt-0" id="mobile_error"></div>
                                    </div>
                                </div>
                                
                                <div class="row g-4 mb-3">
                                    <div class="col-12 col-md">
                                        <select name="country_name" id="country_name" class="form-control country_name" style="width:100%;">
                                            <?php
                                            $countries = [
                                                ["code" => "91", "name" => "India", "countryCode" => "IN"],
                                                ["code" => "47", "name" => "Norway", "countryCode" => "GB"],
                                                ["code" => "44", "name" => "UK", "countryCode" => "US"],
                                                ["code" => "213", "name" => "Algeria ", "countryCode" => "DZ"],
                                                ["code" => "376", "name" => "Andorra", "countryCode" => "AD"],
                                                ["code" => "244", "name" => "Angola", "countryCode" => "AO"],
                                                ["code" => "1264", "name" => "Anguilla", "countryCode" => "AI"],
                                                ["code" => "1268", "name" => "Antigua & Barbuda", "countryCode" => "AG"],
                                                ["code" => "54", "name" => "Argentina", "countryCode" => "AR"],
                                                ["code" => "374", "name" => "Armenia", "countryCode" => "AM"],
                                                ["code" => "297", "name" => "Aruba", "countryCode" => "AW"],
                                                ["code" => "61", "name" => "Australia", "countryCode" => "AU"],
                                                ["code" => "43", "name" => "Austria", "countryCode" => "AT"],
                                                ["code" => "994", "name" => "Azerbaijan", "countryCode" => "AZ"],
                                                ["code" => "1242", "name" => "Bahamas", "countryCode" => "BS"],
                                                ["code" => "973", "name" => "Bahrain", "countryCode" => "BH"],
                                                ["code" => "880", "name" => "Bangladesh", "countryCode" => "BD"],
                                                ["code" => "1246", "name" => "Barbados", "countryCode" => "BB"],
                                                ["code" => "375", "name" => "Belarus", "countryCode" => "BY"],
                                                ["code" => "32", "name" => "Belgium", "countryCode" => "BE"],
                                                ["code" => "501", "name" => "Belize", "countryCode" => "BZ"],
                                                ["code" => "229", "name" => "Benin", "countryCode" => "BJ"],
                                                ["code" => "1441", "name" => "Bermuda", "countryCode" => "BM"],
                                                ["code" => "975", "name" => "Bhutan", "countryCode" => "BT"],
                                                ["code" => "591", "name" => "Bolivia", "countryCode" => "BO"],
                                                ["code" => "387", "name" => "Bosnia Herzegovina", "countryCode" => "BA"],
                                                ["code" => "267", "name" => "Botswana", "countryCode" => "BW"],
                                                ["code" => "55", "name" => "Brazil", "countryCode" => "BR"],
                                                ["code" => "673", "name" => "Brunei", "countryCode" => "BN"],
                                                ["code" => "359", "name" => "Bulgaria", "countryCode" => "BG"],
                                                ["code" => "226", "name" => "Burkina Faso", "countryCode" => "BF"],
                                                ["code" => "257", "name" => "Burundi", "countryCode" => "BI"],
                                                ["code" => "855", "name" => "Cambodia", "countryCode" => "KH"],
                                                ["code" => "237", "name" => "Cameroon", "countryCode" => "CM"],
                                                ["code" => "1", "name" => "Canada", "countryCode" => "CA"],
                                                ["code" => "238", "name" => "Cape Verde Islands", "countryCode" => "CV"],
                                                ["code" => "1345", "name" => "Cayman Islands", "countryCode" => "KY"],
                                                ["code" => "236", "name" => "Central African Republic", "countryCode" => "CF"],
                                                ["code" => "56", "name" => "Chile", "countryCode" => "CL"],
                                                ["code" => "86", "name" => "China", "countryCode" => "CN"],
                                                ["code" => "57", "name" => "Colombia", "countryCode" => "CO"],
                                                ["code" => "269", "name" => "Comoros", "countryCode" => "KM"],
                                                ["code" => "242", "name" => "Congo", "countryCode" => "CG"],
                                                ["code" => "682", "name" => "Cook Islands", "countryCode" => "CK"],
                                                ["code" => "506", "name" => "Costa Rica", "countryCode" => "CR"],
                                                ["code" => "385", "name" => "Croatia", "countryCode" => "HR"],
                                                ["code" => "53", "name" => "Cuba", "countryCode" => "CU"],
                                                ["code" => "90392", "name" => "Cyprus North", "countryCode" => "CY"],
                                                ["code" => "357", "name" => "Cyprus South", "countryCode" => "CY"],
                                                ["code" => "42", "name" => "Czech Republic", "countryCode" => "CZ"],
                                                ["code" => "45", "name" => "Denmark", "countryCode" => "DK"],
                                                ["code" => "253", "name" => "Djibouti", "countryCode" => "DJ"],
                                                ["code" => "1809", "name" => "Dominica", "countryCode" => "DM"],
                                                ["code" => "1809", "name" => "Dominican Republic", "countryCode" => "DO"],
                                                ["code" => "593", "name" => "Ecuador", "countryCode" => "EC"],
                                                ["code" => "20", "name" => "Egypt", "countryCode" => "EG"],
                                                ["code" => "503", "name" => "El Salvador", "countryCode" => "SV"],
                                                ["code" => "240", "name" => "Equatorial Guinea", "countryCode" => "GQ"],
                                                ["code" => "291", "name" => "Eritrea", "countryCode" => "ER"],
                                                ["code" => "372", "name" => "Estonia", "countryCode" => "EE"],
                                                ["code" => "251", "name" => "Ethiopia", "countryCode" => "ET"],
                                                ["code" => "500", "name" => "Falkland Islands", "countryCode" => "FK"],
                                                ["code" => "298", "name" => "Faroe Islands", "countryCode" => "FO"],
                                                ["code" => "679", "name" => "Fiji", "countryCode" => "FJ"],
                                                ["code" => "358", "name" => "Finland", "countryCode" => "FI"],
                                                ["code" => "33", "name" => "France", "countryCode" => "FR"],
                                                ["code" => "594", "name" => "French Guiana", "countryCode" => "GF"],
                                                ["code" => "689", "name" => "French Polynesia", "countryCode" => "PF"],
                                                ["code" => "241", "name" => "Gabon", "countryCode" => "GA"],
                                                ["code" => "220", "name" => "Gambia", "countryCode" => "GM"],
                                                ["code" => "7880", "name" => "Georgia", "countryCode" => "GE"],
                                                ["code" => "49", "name" => "Germany", "countryCode" => "DE"],
                                                ["code" => "233", "name" => "Ghana", "countryCode" => "GH"],
                                                ["code" => "350", "name" => "Gibraltar", "countryCode" => "GI"],
                                                ["code" => "30", "name" => "Greece", "countryCode" => "GR"],
                                                ["code" => "299", "name" => "Greenland", "countryCode" => "GL"],
                                                ["code" => "1473", "name" => "Grenada", "countryCode" => "GD"],
                                                ["code" => "590", "name" => "Guadeloupe", "countryCode" => "GP"],
                                                ["code" => "671", "name" => "Guam", "countryCode" => "GU"],
                                                ["code" => "502", "name" => "Guatemala", "countryCode" => "GT"],
                                                ["code" => "224", "name" => "Guinea", "countryCode" => "GN"],
                                                ["code" => "245", "name" => "Guinea - Bissau", "countryCode" => "GW"],
                                                ["code" => "592", "name" => "Guyana", "countryCode" => "GY"],
                                                ["code" => "509", "name" => "Haiti", "countryCode" => "HT"],
                                                ["code" => "504", "name" => "Honduras", "countryCode" => "HN"],
                                                ["code" => "852", "name" => "Hong Kong", "countryCode" => "HK"],
                                                ["code" => "36", "name" => "Hungary", "countryCode" => "HU"],
                                                ["code" => "354", "name" => "Iceland", "countryCode" => "IS"],
                                                ["code" => "62", "name" => "Indonesia", "countryCode" => "ID"],
                                                ["code" => "98", "name" => "Iran", "countryCode" => "IR"],
                                                ["code" => "964", "name" => "Iraq", "countryCode" => "IQ"],
                                                ["code" => "353", "name" => "Ireland", "countryCode" => "IE"],
                                                ["code" => "972", "name" => "Israel", "countryCode" => "IL"],
                                                ["code" => "39", "name" => "Italy", "countryCode" => "IT"],
                                                ["code" => "1876", "name" => "Jamaica", "countryCode" => "JM"],
                                                ["code" => "81", "name" => "Japan", "countryCode" => "JP"],
                                                ["code" => "962", "name" => "Jordan", "countryCode" => "JO"],
                                                ["code" => "7", "name" => "Kazakhstan", "countryCode" => "KZ"],
                                                ["code" => "254", "name" => "Kenya", "countryCode" => "KE"],
                                                ["code" => "686", "name" => "Kiribati", "countryCode" => "KI"],
                                                ["code" => "850", "name" => "Korea North", "countryCode" => "KP"],
                                                ["code" => "82", "name" => "Korea South", "countryCode" => "KR"],
                                                ["code" => "965", "name" => "Kuwait", "countryCode" => "KW"],
                                                ["code" => "996", "name" => "Kyrgyzstan", "countryCode" => "KG"],
                                                ["code" => "856", "name" => "Laos", "countryCode" => "LA"],
                                                ["code" => "371", "name" => "Latvia", "countryCode" => "LV"],
                                                ["code" => "961", "name" => "Lebanon", "countryCode" => "LB"],
                                                ["code" => "266", "name" => "Lesotho", "countryCode" => "LS"],
                                                ["code" => "231", "name" => "Liberia", "countryCode" => "LR"],
                                                ["code" => "218", "name" => "Libya", "countryCode" => "LY"],
                                                ["code" => "417", "name" => "Liechtenstein", "countryCode" => "LI"],
                                                ["code" => "370", "name" => "Lithuania", "countryCode" => "LT"],
                                                ["code" => "352", "name" => "Luxembourg", "countryCode" => "LU"],
                                                ["code" => "853", "name" => "Macao", "countryCode" => "MO"],
                                                ["code" => "389", "name" => "Macedonia", "countryCode" => "MK"],
                                                ["code" => "261", "name" => "Madagascar", "countryCode" => "MG"],
                                                ["code" => "265", "name" => "Malawi", "countryCode" => "MW"],
                                                ["code" => "60", "name" => "Malaysia", "countryCode" => "MY"],
                                                ["code" => "960", "name" => "Maldives", "countryCode" => "MV"],
                                                ["code" => "223", "name" => "Mali", "countryCode" => "ML"],
                                                ["code" => "356", "name" => "Malta", "countryCode" => "MT"],
                                                ["code" => "692", "name" => "Marshall Islands", "countryCode" => "MH"],
                                                ["code" => "596", "name" => "Martinique", "countryCode" => "MQ"],
                                                ["code" => "222", "name" => "Mauritania", "countryCode" => "MR"],
                                                ["code" => "269", "name" => "Mayotte", "countryCode" => "YT"],
                                                ["code" => "52", "name" => "Mexico", "countryCode" => "MX"],
                                                ["code" => "691", "name" => "Micronesia", "countryCode" => "FM"],
                                                ["code" => "373", "name" => "Moldova", "countryCode" => "MD"],
                                                ["code" => "377", "name" => "Monaco", "countryCode" => "MC"],
                                                ["code" => "976", "name" => "Mongolia", "countryCode" => "MN"],
                                                ["code" => "1664", "name" => "Montserrat", "countryCode" => "MS"],
                                                ["code" => "212", "name" => "Morocco", "countryCode" => "MA"],
                                                ["code" => "258", "name" => "Mozambique", "countryCode" => "MZ"],
                                                ["code" => "95", "name" => "Myanmar", "countryCode" => "MN"],
                                                ["code" => "264", "name" => "Namibia", "countryCode" => "NA"],
                                                ["code" => "674", "name" => "Nauru", "countryCode" => "NR"],
                                                ["code" => "977", "name" => "Nepal", "countryCode" => "NP"],
                                                ["code" => "31", "name" => "Netherlands", "countryCode" => "NL"],
                                                ["code" => "687", "name" => "New Caledonia", "countryCode" => "NC"],
                                                ["code" => "64", "name" => "New Zealand", "countryCode" => "NZ"],
                                                ["code" => "505", "name" => "Nicaragua", "countryCode" => "NI"],
                                                ["code" => "227", "name" => "Niger", "countryCode" => "NE"],
                                                ["code" => "234", "name" => "Nigeria", "countryCode" => "NG"],
                                                ["code" => "683", "name" => "Niue", "countryCode" => "NU"],
                                                ["code" => "672", "name" => "Norfolk Island", "countryCode" => "NF"],
                                                ["code" => "670", "name" => "Northern Marianas", "countryCode" => "NP"],
                                                ["code" => "47", "name" => "Norway", "countryCode" => "NO"],
                                                ["code" => "968", "name" => "Oman", "countryCode" => "OM"],
                                                ["code" => "680", "name" => "Palau", "countryCode" => "PW"],
                                                ["code" => "507", "name" => "Panama", "countryCode" => "PA"],
                                                ["code" => "675", "name" => "Papua New Guinea", "countryCode" => "PG"],
                                                ["code" => "595", "name" => "Paraguay", "countryCode" => "PY"],
                                                ["code" => "51", "name" => "Peru", "countryCode" => "PE"],
                                                ["code" => "63", "name" => "Philippines", "countryCode" => "PH"],
                                                ["code" => "48", "name" => "Poland", "countryCode" => "PL"],
                                                ["code" => "351", "name" => "Portugal", "countryCode" => "PT"],
                                                ["code" => "1787", "name" => "Puerto Rico", "countryCode" => "PR"],
                                                ["code" => "974", "name" => "Qatar", "countryCode" => "QA"],
                                                ["code" => "262", "name" => "Reunion", "countryCode" => "RE"],
                                                ["code" => "40", "name" => "Romania", "countryCode" => "RO"],
                                                ["code" => "7", "name" => "Russia", "countryCode" => "RU"],
                                                ["code" => "250", "name" => "Rwanda", "countryCode" => "RW"],
                                                ["code" => "378", "name" => "San Marino", "countryCode" => "SM"],
                                                ["code" => "239", "name" => "Sao Tome & Principe", "countryCode" => "ST"],
                                                ["code" => "966", "name" => "Saudi Arabia", "countryCode" => "SA"],
                                                ["code" => "221", "name" => "Senegal", "countryCode" => "SN"],
                                                ["code" => "381", "name" => "Serbia", "countryCode" => "CS"],
                                                ["code" => "248", "name" => "Seychelles", "countryCode" => "SC"],
                                                ["code" => "232", "name" => "Sierra Leone", "countryCode" => "SL"],
                                                ["code" => "65", "name" => "Singapore", "countryCode" => "SG"],
                                                ["code" => "421", "name" => "Slovak Republic", "countryCode" => "SK"],
                                                ["code" => "386", "name" => "Slovenia", "countryCode" => "SI"],
                                                ["code" => "677", "name" => "Solomon Islands", "countryCode" => "SB"],
                                                ["code" => "252", "name" => "Somalia", "countryCode" => "SO"],
                                                ["code" => "27", "name" => "South Africa", "countryCode" => "ZA"],
                                                ["code" => "34", "name" => "Spain", "countryCode" => "ES"],
                                                ["code" => "94", "name" => "Sri Lanka", "countryCode" => "LK"],
                                                ["code" => "290", "name" => "St. Helena", "countryCode" => "SH"],
                                                ["code" => "1869", "name" => "St. Kitts", "countryCode" => "KN"],
                                                ["code" => "1758", "name" => "St. Lucia", "countryCode" => "SC"],
                                                ["code" => "249", "name" => "Sudan", "countryCode" => "SD"],
                                                ["code" => "597", "name" => "Suriname", "countryCode" => "SR"],
                                                ["code" => "268", "name" => "Swaziland", "countryCode" => "SZ"],
                                                ["code" => "46", "name" => "Sweden", "countryCode" => "SE"],
                                                ["code" => "41", "name" => "Switzerland", "countryCode" => "CH"],
                                                ["code" => "963", "name" => "Syria", "countryCode" => "SI"],
                                                ["code" => "886", "name" => "Taiwan", "countryCode" => "TW"],
                                                ["code" => "7", "name" => "Tajikstan", "countryCode" => "TJ"],
                                                ["code" => "66", "name" => "Thailand", "countryCode" => "TH"],
                                                ["code" => "228", "name" => "Togo", "countryCode" => "TG"],
                                                ["code" => "676", "name" => "Tonga", "countryCode" => "TO"],
                                                ["code" => "1868", "name" => "Trinidad & Tobago", "countryCode" => "TT"],
                                                ["code" => "216", "name" => "Tunisia", "countryCode" => "TN"],
                                                ["code" => "90", "name" => "Turkey", "countryCode" => "TR"],
                                                ["code" => "7", "name" => "Turkmenistan", "countryCode" => "TM"],
                                                ["code" => "993", "name" => "Turkmenistan", "countryCode" => "TM"],
                                                ["code" => "1649", "name" => "Turks & Caicos Islands", "countryCode" => "TC"],
                                                ["code" => "688", "name" => "Tuvalu", "countryCode" => "TV"],
                                                ["code" => "256", "name" => "Uganda", "countryCode" => "UG"],
                                                ["code" => "380", "name" => "Ukraine", "countryCode" => "UA"],
                                                ["code" => "971", "name" => "United Arab Emirates", "countryCode" => "AE"],
                                                ["code" => "598", "name" => "Uruguay", "countryCode" => "UY"],
                                                ["code" => "7", "name" => "Uzbekistan", "countryCode" => "UZ"],
                                                ["code" => "678", "name" => "Vanuatu", "countryCode" => "VU"],
                                                ["code" => "379", "name" => "Vatican City", "countryCode" => "VA"],
                                                ["code" => "58", "name" => "Venezuela", "countryCode" => "VE"],
                                                ["code" => "84", "name" => "Vietnam", "countryCode" => "VN"],
                                                ["code" => "1284", "name" => "Virgin Islands - British", "countryCode" => "VG"],
                                                ["code" => "1340", "name" => "Virgin Islands - US", "countryCode" => "VI"],
                                                ["code" => "681", "name" => "Wallis & Futuna", "countryCode" => "WF"],
                                                ["code" => "969", "name" => "Yemen (North)", "countryCode" => "YE"],
                                                ["code" => "967", "name" => "Yemen (South)", "countryCode" => "YE"],
                                                ["code" => "260", "name" => "Zambia", "countryCode" => "ZM"],
                                                ["code" => "263", "name" => "Zimbabwe", "countryCode" => "ZW"],
                                            ];
                                            foreach ($countries as $country) {
                                                $selected = ($user->country_name == $country["name"]) ? "selected" : "";
                                                echo "<option  value='{$country['name']}' $selected>{$country['name']}</option>";
                                            }
                                            ?>
                                        </select>
                                        <div class="error text-danger" id="country_name_error"></div>
                                    </div>
                                </div>


                                <div id="india-fields" class="row g-4 mb-3" style="display: none;">
                                    <div class="col-6 col-md">
                                        <select name="state" id="state" class="form-control form-select">
                                            <option value="">Select State</option>
                                                @if(isset($statesData) && $statesData)
                                                    @foreach($statesData as $row)
                                                    <option @if($user->state == $row->state_name) selected  @endif value="{{$row->state_name}}">{{ $row->state_name ?? ''}}</option>
                                                    @endforeach 
                                                @endif
                                        </select>
                                        <div class="error text-danger" id="state_error"></div>
                                    </div>
                                    <div class="col-6 col-md">
                                        <select name="city" id="city" class="form-control form-select">
                                            <option value="">Select City</option>
                                            @if(isset($citiesData) && $citiesData)
                                                @foreach($citiesData as $row)
                                                    <option @if($row->city_name == $user->city) selected  @endif value="{{$row->city_name}}">{{ $row->city_name ?? ''}}</option>
                                                @endforeach 
                                            @endif
                                        </select>
                                        <div class="error text-danger" id="city_error"></div>
                                    </div>
                                </div>

                                <div id="other-country-fields" class="row g-4 mb-3" style="display: none;">
                                    <div class="col-6 col-md">
                                        {{-- <label for="state_input">State</label> --}}
                                        <input type="text" name="state_input" id="state_input" class="form-control" placeholder="Enter State" value="{{ $user->state }}">
                                    </div>
                                    <div class="col-6 col-md">
                                        {{-- <label for="city_input">City</label> --}}
                                        <input type="text" name="city_input" id="city_input" class="form-control" placeholder="Enter City" value="{{ $user->city }}">
                                    </div>
                                </div>
                                
                                <div class="row g-3 mb-3">
                                    
                                    <div class="col-12 col-md">
                                        <input type="text" class="form-control" name="zipcode" id="zipcode"
                                            placeholder="Zip Code" value="{{$user->zipcode ?? ''}}">
                                        <div class="error text-danger" id="zipcode_error"></div>
                                    </div>
                                </div>
                                <div class="form-group mb-3">
                                    <textarea name="address" id="address" rows="3" class="form-control h-auto" placeholder="Address"> {{$user->address ?? ''}}</textarea>
                                    <div class="error text-danger" id="address_error"></div>
                                </div>
                                <div class="form-group">
                                    <button class="btn btn-primary w-100"> Submit </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>

<script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>

<script>
    $(document).on('submit', '#profilesubmit', function(ev) {
        $('.error').html('');

        ev.preventDefault(); // Prevent browers default submit.
        var formData = new FormData(this);
        var error = false;

        if (error == false) {
            $.ajax({
                url: "{{ url('profilesubmit') }} ",
                type: 'post',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function() {
                    $(".hstackloader").html('<lord-icon src="https://cdn.lordicon.com/dpinvufc.json" trigger="loop" colors="primary:#4bb543,secondary:#4bb543" style="width:50px;"> </lord-icon>');
                    $(".hstack").css('display', 'none');
                    $(".error").text('');
                },
                success: function(result) {
                    if (result.code == 200) {
                        swal({
                            title: result.message,
                            text: "",
                            type: "success",
                            showCancelButton: false,
                            confirmButtonText: "OK",
                            closeOnConfirm: false, 
                        }, function() {
                            window.location.reload();
                        });
                        // swal(result.message, ' ', 'success');
                        // setTimeout(function() {
                        //     if(result.url != ''){
                        //         window.location.href = result.url
                        //     }else{
                        //         window.location.href = '/';
                        //     }
                        // }, 2000);
                    } else if (result.code == 401) {
                        $.each(result.message, function(prefix, val) {
                            $('#' + prefix + '_error').text(val[0]);
                        });
                        // swal(result.message, ' ', 'error');
                    } else {
                        swal(result.message, ' ', 'error');
                    }
                },
                error: function(xhr) {
                    $(".hstack").css('display', 'flex');
                },
                complete: function() {
                    $(".hstack").css('display', 'flex');
                    $(".hstackloader").text('');
                },
            })
        }
    })
</script>

<script>
    function validateLength(input) {
        // Set max length
        if (input.value.length > 12) {
            input.value = input.value.slice(0, 12);
        }

        if (input.value.length < 6) {
            $('#mobile_error').text('Mobile number must be at least 6 digits.');
        }else {
            $('#mobile_error').text('');
        }
    }
    
    $(document).ready(function() {
        
$('#country_name').change(function () {
    const selectedCountry = $(this).val();
    if (selectedCountry === "India") {
        $('#state_input').val('');
        $('#city_input').val('');
        $('#state').val('');
        $('#city').empty().append('<option value="">Select City</option>');
    } else {
        // Clear dropdowns for India
        $('#state').val('');
        $('#city').empty().append('<option value="">Select City</option>');

        // Show and clear input fields for other countries
        $('#state_input').val('');
        $('#city_input').val('');
    }
});
        
        
    $('#state').change(function() {
        var stateId = $(this).val(); 
        $.ajax({
        	headers:
            {
                'X-CSRF-TOKEN':
                    $('meta[name="csrf-token"]').attr('content')
            },
            url: "{{ url('get-city') }}/"+stateId, 
            type: 'get', 
            data: {
                state_id: stateId 
            },
            success: function(response) {
                $('#city').empty();
                 $('#city').append('<option value=" ">Select City</option>');
                response.city.forEach(function(city) {
                    $('#city').append('<option value="' + city.city_name + '">' + city.city_name + '</option>');
                });
            },
            error: function(xhr, status, error) {
                console.error(xhr.responseText);
            }
        });
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const countrySelect = document.getElementById("country_name");
    const indiaFields = document.getElementById("india-fields");
    const otherCountryFields = document.getElementById("other-country-fields");

    function toggleFields() {
        const selectedCountry = countrySelect.value;

        if (selectedCountry === "India") {
            indiaFields.style.display = "flex";
            otherCountryFields.style.display = "none";
        } else {
            indiaFields.style.display = "none";
            otherCountryFields.style.display = "flex";
        }
    }

    // Initial toggle
    toggleFields();

    // Update on change
    countrySelect.addEventListener("change", toggleFields);
});


</script>
@endsection
