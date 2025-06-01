@php
$settings = DB::table('site_settings')->where('id', 1)->select('*')->first();
$login_background_color = $settings->login_background_color;
@endphp

<style>
    /*#patient_register {*/
    /*    background: {*/
    /*            {*/
    /*            $login_background_color*/
    /*        }*/
    /*    }*/
    /*     !important;*/
    /*}*/
    label {
        color: #000000 !important;
    }
    .file-preview-frame {
        margin: 0 !important;
    }
</style>
<fieldset>
    <div id="patient_register" style="border-radius: 5px;">
        @php
        $startingPatientPrefix = App\System::getProperty('patient_prefix');
        $startingPatientID = App\System::getProperty('patient_code_start_from');
        $currentID = Modules\MyHealth\Entities\PatientDetail::all()->count();
        $nummber_of_c = strlen($startingPatientID );
        if(empty($startingPatientPrefix)){
        $startingPatientPrefix = '';
        }
        if(!empty($startingPatientID)){
        $currentID = $currentID + (int)$startingPatientID;
        }
        if(empty($currentID)){
        $p_id = $startingPatientPrefix.$startingPatientID;
        }else{
        $currentID++;
        $currentID = sprintf("%0".$nummber_of_c."d", $currentID);
        $p_id = '0001';
        if(empty($startingPatientPrefix)){
        $p_id = $currentID;
        }else{
        $p_id = $startingPatientPrefix .''.$currentID;
        }
        }
        @endphp
        <style>
            label {
                color: black;
                font-weight: 800;
            }
        </style>
        <div class="modal-body body2">
            {!! Form::hidden('package_id', null, ['class' => 'package_id']); !!}
            <div class="row">
                <div class="col-md-4">
                     <div class="form-group">
                       <small>First Name:*</small>
                        <input type="text" name="join_date" id="join_date" value="{{ date('Y-m-d', time()) }}"
                            class="form-control" readonly="readonly" required placeholder="Date" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <small>First Name:*</small>
                        <input type="text" name="first_name" id="p_first_name" class="form-control" required
                            placeholder="First Name" autocomplete="on" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <small>Last Name:*</small>
                        <input type="text" name="last_name" id="p_last_name" class="form-control" required
                            placeholder="Last Name" />
                    </div>
                </div>
            </div>
            
            <!--
            <div class="row d-none">
                <div class="col-md-4">
                    <div class="form-group">
                        <small>Address:*</small>
                        <input type="text" name="address" id="p_address" class="form-control" required placeholder="Address" autocomplete="on" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <small>City:*</small>
                        <input type="text" name="city" id="p_city" class="form-control" required placeholder="City" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <small>State :*</small>
                        <input type="text" name="state" id="p_state" class="form-control" required placeholder="State" />
                    </div>
                </div>
                 <div class="col-md-4">
                    <div class="form-group">
                        <small>District :*</small>
                        <input type="text" name="district" id="p_district" class="form-control" required placeholder="District" />
                    </div>
                </div>
            </div>
            -->
            
            <div class="row">
              {{--    <div class="col-md-4">
                    <div class="form-group">
                       @php
                            $countries = DB::table('countries')->pluck('country', 'country')->toArray();
                        @endphp
                        
                        <small>Country:*</small>
                        
                        {!! Form::select('country', $countries, null, [
                            'class' => 'form-control select2',
                            'placeholder' => __('messages.please_select'),
                            'required' => 'required',
                            'id' => 'p_country',
                            'autocomplete' => 'on'
                        ]) !!}

                        
                    </div>
                </div> --}}
                
                <!--
                <input type="hidden" name="country" id="p_country" class="form-control" required placeholder="country" />
                -->
                
                <div class="col-md-4">
                    <div class="form-group">
                        <small>Mobile Phone:*</small>
                        <input type="text" name="mobile" id="p_mobile" class="form-control" required
                            placeholder="Mobile" />
                    </div>
                </div>
                <div class="col-md-8">
                    <style>
                        .feild-box {
                            border: 1px solid #8080803b;
                            margin-top: 10px;
                            padding: 10px;
                        }
                        .custom_date_p-0 {
                            padding: 0px 15px !important;
                        }
                        .field-inline-block {
                            display: inline-flex;
                        }
                        .custom_date_date-field {
                            margin-right: 2px;
                            padding: 0px 3px;
                            text-align: center !important;
                            /* height: 54px;  Doubled the height */
                            width: 80px;   /* Doubled the width */
                            border-color: #aaa !important;
                        }
                        .custom_date_date-field:focus {
                            border-color: #2596be !important;
                            outline: none;
                        }
                        .line-separator {
                            border-top: 1px solid #ddd; /* Creates a line */
                            margin: 20px 0; /* Adds space around the line */
                        }
                    </style>
                    @php
                        // $today = \Carbon\Carbon::now();
                        // // Get today's year, month, and day
                        // $year = $today->year;
                        // $month = $today->month;
                        // $day = $today->day;
                        // // Split year into individual digits
                        // $yearDigits = str_split($year);
                        // // Split month and day into two digits
                        // $monthDigits = str_split(str_pad($month, 2, '0', STR_PAD_LEFT));
                        // $dayDigits = str_split(str_pad($day, 2, '0', STR_PAD_LEFT));
                    @endphp
                    <div class="form-group">
                        <small>Date of Birth:*</small>
                        <input type="hidden" name="date_of_birth" id="p_date_of_birth" class="form-control" readonly="readonly" required placeholder="Date of birth" />
                        <fieldset>
                            <div class="row">
                                <div class="col-md-12 p-0 custom_date_p-0">
                                    <div class="row">
                                        <div class="col-md-3 p-0 custom_date_p-0" style="margin-right: 20px;">
                                            <label class="text-center" style="color: #4d4d4d !important">Date</label>
                                            <div class="field-inline-block w-100 text-center">
                                                <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline-block" placeholder="D" id="custom_date_from_date1" required value="{{ $dayDigits[0] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                                <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline-block" placeholder="D" id="custom_date_from_date2" required value="{{ $dayDigits[1] ?? '' }}" style="width: 40px; display: inline-block;">
                                            </div>
                                        </div>
                                        <div class="col-md-3 p-0 custom_date_p-0" style="margin-right: 20px;">
                                            <label class="text-center" style="color: #4d4d4d !important">Month</label>
                                            <div class="field-inline-block w-100 text-center">
                                                <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline-block" placeholder="M" id="custom_date_from_month1" required value="{{ $monthDigits[0] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                                <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline-block" placeholder="M" id="custom_date_from_month2" required value="{{ $monthDigits[1] ?? '' }}" style="width: 40px; display: inline-block;">
                                            </div>
                                        </div>
                                        <div class="col-md-4 p-0 custom_date_p-0">
                                            <label class="text-center" style="color: #4d4d4d !important">Year</label>
                                            <div class="field-inline-block w-100 text-center">
                                                <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline" placeholder="Y" id="custom_date_from_year1" required value="{{ $yearDigits[0] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                                <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline" placeholder="Y" id="custom_date_from_year2" required value="{{ $yearDigits[1] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                                <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline" placeholder="Y" id="custom_date_from_year3" required value="{{ $yearDigits[2] ?? '' }}" style="width: 40px; display: inline-block; margin-right: 2px;">
                                                <input type="text" pattern="[0-9]*" maxlength="1" class="custom_date_date-field form-control d-inline" placeholder="Y" id="custom_date_from_year4" required value="{{ $yearDigits[3] ?? '' }}" style="width: 40px; display: inline-block;">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                        <script>
                            document.querySelectorAll('.custom_date_date-field').forEach((input, index) => {
                                input.addEventListener('input', function() {
                                    if (this.value.length >= this.maxLength) {
                                        // Move focus to the next input
                                        const nextInput = document.querySelectorAll('.custom_date_date-field')[index + 1];
                                        if (nextInput) {
                                            nextInput.focus();
                                            nextInput.select();  // Highlight the next input's value
                                        }
                                    }
                                });
                            });
                        </script>
                    </div>
                </div>
                <script>
                    $('#custom_date_from_year1, #custom_date_from_year2, #custom_date_from_year3, #custom_date_from_year4, #custom_date_from_month1, #custom_date_from_month2, #custom_date_from_date1, #custom_date_from_date2').on('change', function() {
                    let startDate = $('#custom_date_from_year1').val() 
                                + $('#custom_date_from_year2').val() 
                                + $('#custom_date_from_year3').val() 
                                + $('#custom_date_from_year4').val() 
                                + "-" 
                                + $('#custom_date_from_month1').val() 
                                + $('#custom_date_from_month2').val() 
                                + "-" 
                                + $('#custom_date_from_date1').val() 
                                + $('#custom_date_from_date2').val();
                    
                    if (startDate.length === 10) {
                        $('#p_date_of_birth').val(startDate);
                    } else {
                        // alert("Please type the full date.");
                    }
                });
                </script>
            </div>
            <!-- Hidden Latitude and Longitude Inputs -->
            <input type="hidden" name="latitude" id="latitude">
            <input type="hidden" name="longitude" id="longitude">

            <!-- Google Maps JavaScript API -->
            <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCsHrbMB_bsgtLPVdv63bbvLOoszPN4bw8&libraries=places"></script>
            <script>
                // Check if the browser supports geolocation
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        var latitude = position.coords.latitude;
                        var longitude = position.coords.longitude;
                        document.getElementById('latitude').value = latitude;
                        document.getElementById('longitude').value = longitude;
                    }, function(error) {
                        console.log("Geolocation error: ", error);
                    });
                } else {
                    console.log("Geolocation is not supported by this browser.");
                }
            </script>
            <div class="row">
                <div class="col-md-4">
                     <div class="form-group">
                        <small>Email:</small>
                        <input type="email" name="p_email" id="p_email" class="form-control" placeholder="Email" autocomplete="on"/>
                    </div>
                    
                   
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <small>Password:*</small>
                        <input type="password" name="p_password" id="p_password" class="form-control" required
                            placeholder="Password" />
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <small>Reenter Password:*</small>
                        <input type="password" name="p_confirm_password" id="p_confirm_password" class="form-control"
                            placeholder="Confrim Password" required />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <small>Gender:*</small>
                        <select name="gender" id="p_gender" class="form-control select2" required>
                            <option value="">Select Gender</option>
                            <option value="1">Male</option>
                            <option value="2">Female</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <small>Marital status:*</small>
                        <select name="marital_status" id="marital_status" class="form-control select2" required>
                            <option value="">Select Marital status</option>
                            <option value="1">Married</option>
                            <option value="2">UnMarried</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <small>Blood Group</small>
                        <select name="blood_group" id="blood_group" class="form-control select2">
                            <option value="">Select Blood Group</option>
                            <option value="1">AB-</option>
                            <option value="2">A-</option>
                            <option value="3">B-</option>
                            <option value="4">O-</option>
                            <option value="5">AB+</option>
                            <option value="6">A+</option>
                            <option value="7">B+</option>
                            <option value="8">O+</option>
                            <option value="9">Not Known</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <small>Time / Zone</small>
                        <select name="time_zone" class="form-control select2">
                            <option>Time / Zone</option>
                            <option value="Etc/GMT+12">(GMT-12:00) International Date Line West</option>
                            <option value="Pacific/Midway">(GMT-11:00) Midway Island, Samoa</option>
                            <option value="Pacific/Honolulu">(GMT-10:00) Hawaii</option>
                            <option value="US/Alaska">(GMT-09:00) Alaska</option>
                            <option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US & Canada)</option>
                            <option value="America/Tijuana">(GMT-08:00) Tijuana, Baja California</option>
                            <option value="US/Arizona">(GMT-07:00) Arizona</option>
                            <option value="America/Chihuahua">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
                            <option value="US/Mountain">(GMT-07:00) Mountain Time (US & Canada)</option>
                            <option value="America/Managua">(GMT-06:00) Central America</option>
                            <option value="US/Central">(GMT-06:00) Central Time (US & Canada)</option>
                            <option value="America/Mexico_City">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
                            <option value="Canada/Saskatchewan">(GMT-06:00) Saskatchewan</option>
                            <option value="America/Bogota">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
                            <option value="US/Eastern">(GMT-05:00) Eastern Time (US & Canada)</option>
                            <option value="US/East-Indiana">(GMT-05:00) Indiana (East)</option>
                            <option value="Canada/Atlantic">(GMT-04:00) Atlantic Time (Canada)</option>
                            <option value="America/Caracas">(GMT-04:00) Caracas, La Paz</option>
                            <option value="America/Manaus">(GMT-04:00) Manaus</option>
                            <option value="America/Santiago">(GMT-04:00) Santiago</option>
                            <option value="Canada/Newfoundland">(GMT-03:30) Newfoundland</option>
                            <option value="America/Sao_Paulo">(GMT-03:00) Brasilia</option>
                            <option value="America/Argentina/Buenos_Aires">(GMT-03:00) Buenos Aires, Georgetown</option>
                            <option value="America/Godthab">(GMT-03:00) Greenland</option>
                            <option value="America/Montevideo">(GMT-03:00) Montevideo</option>
                            <option value="America/Noronha">(GMT-02:00) Mid-Atlantic</option>
                            <option value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is.</option>
                            <option value="Atlantic/Azores">(GMT-01:00) Azores</option>
                            <option value="Africa/Casablanca">(GMT+00:00) Casablanca, Monrovia, Reykjavik</option>
                            <option value="Etc/Greenwich">(GMT+00:00) Greenwich Mean Time : Dublin, Edinburgh, Lisbon,
                                London</option>
                            <option value="Europe/Amsterdam">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm,
                                Vienna</option>
                            <option value="Europe/Belgrade">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana,
                                Prague</option>
                            <option value="Europe/Brussels">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
                            <option value="Europe/Sarajevo">(GMT+01:00) Sarajevo, Skopje, Warsaw, Zagreb</option>
                            <option value="Africa/Lagos">(GMT+01:00) West Central Africa</option>
                            <option value="Asia/Amman">(GMT+02:00) Amman</option>
                            <option value="Europe/Athens">(GMT+02:00) Athens, Bucharest, Istanbul</option>
                            <option value="Asia/Beirut">(GMT+02:00) Beirut</option>
                            <option value="Africa/Cairo">(GMT+02:00) Cairo</option>
                            <option value="Africa/Harare">(GMT+02:00) Harare, Pretoria</option>
                            <option value="Europe/Helsinki">(GMT+02:00) Helsinki, Kyiv, Riga, Sofia, Tallinn, Vilnius
                            </option>
                            <option value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option>
                            <option value="Europe/Minsk">(GMT+02:00) Minsk</option>
                            <option value="Africa/Windhoek">(GMT+02:00) Windhoek</option>
                            <option value="Asia/Kuwait">(GMT+03:00) Kuwait, Riyadh, Baghdad</option>
                            <option value="Europe/Moscow">(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
                            <option value="Africa/Nairobi">(GMT+03:00) Nairobi</option>
                            <option value="Asia/Tbilisi">(GMT+03:00) Tbilisi</option>
                            <option value="Asia/Tehran">(GMT+03:30) Tehran</option>
                            <option value="Asia/Muscat">(GMT+04:00) Abu Dhabi, Muscat</option>
                            <option value="Asia/Baku">(GMT+04:00) Baku</option>
                            <option value="Asia/Yerevan">(GMT+04:00) Yerevan</option>
                            <option value="Asia/Kabul">(GMT+04:30) Kabul</option>
                            <option value="Asia/Yekaterinburg">(GMT+05:00) Yekaterinburg</option>
                            <option value="Asia/Karachi">(GMT+05:00) Islamabad, Karachi, Tashkent</option>
                            <option value="Asia/Calcutta">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
                            <option value="Asia/Calcutta">(GMT+05:30) Sri Jayawardenapura</option>
                            <option value="Asia/Katmandu">(GMT+05:45) Kathmandu</option>
                            <option value="Asia/Almaty">(GMT+06:00) Almaty, Novosibirsk</option>
                            <option value="Asia/Dhaka">(GMT+06:00) Astana, Dhaka</option>
                            <option value="Asia/Rangoon">(GMT+06:30) Yangon (Rangoon)</option>
                            <option value="Asia/Bangkok">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
                            <option value="Asia/Krasnoyarsk">(GMT+07:00) Krasnoyarsk</option>
                            <option value="Asia/Hong_Kong">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
                            <option value="Asia/Kuala_Lumpur">(GMT+08:00) Kuala Lumpur, Singapore</option>
                            <option value="Asia/Irkutsk">(GMT+08:00) Irkutsk, Ulaan Bataar</option>
                            <option value="Australia/Perth">(GMT+08:00) Perth</option>
                            <option value="Asia/Taipei">(GMT+08:00) Taipei</option>
                            <option value="Asia/Tokyo">(GMT+09:00) Osaka, Sapporo, Tokyo</option>
                            <option value="Asia/Seoul">(GMT+09:00) Seoul</option>
                            <option value="Asia/Yakutsk">(GMT+09:00) Yakutsk</option>
                            <option value="Australia/Adelaide">(GMT+09:30) Adelaide</option>
                            <option value="Australia/Darwin">(GMT+09:30) Darwin</option>
                            <option value="Australia/Brisbane">(GMT+10:00) Brisbane</option>
                            <option value="Australia/Canberra">(GMT+10:00) Canberra, Melbourne, Sydney</option>
                            <option value="Australia/Hobart">(GMT+10:00) Hobart</option>
                            <option value="Pacific/Guam">(GMT+10:00) Guam, Port Moresby</option>
                            <option value="Asia/Vladivostok">(GMT+10:00) Vladivostok</option>
                            <option value="Asia/Magadan">(GMT+11:00) Magadan, Solomon Is., New Caledonia</option>
                            <option value="Pacific/Auckland">(GMT+12:00) Auckland, Wellington</option>
                            <option value="Pacific/Fiji">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
                            <option value="Pacific/Tongatapu">(GMT+13:00) Nuku'alofa</option>
                        </select>
                    </div>
                    
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <small>Upload Your Image</small>
                    </div>
                    <div class="form-group">
                        <!--
                        <input type="file" name="fileToImage" id="fileToImage" accept="image/*" onchange="showMyImage(this)" style="border: none;padding: 0px;margin: 0px;">
                        -->
                        <input type="file" name="fileToImage" id="fileToImage" accept="image/*" style="border: none;padding: 0px;margin: 0px;">
                    </div>
                </div>
                <div class="col-md-4" style="padding-left: 0px;">
                        <div class="form-group">
                            <small>Height</small>
                            <input type="text" name="height" id="height" class="form-control" placeholder="Height" />
                        </div>
                    </div>
                    <div class="col-md-4" style="padding-right: 0px;">
                        <div class="form-group">
                            <small>Weight</small>
                            <input type="text" name="weight" id="weight" class="form-control" placeholder="Weight" />
                        </div>
                    </div>
                    <div class="form-group col-md-4">
                        <small>Guardian Name</small>
                        <input type="text" name="guardian_name" id="guardian_name" class="form-control"
                            placeholder="Guardian Name" />
                    </div>
                    <div class="form-group col-md-4">
                        <small>Any Known Allergies</small>
                        <input type="text" name="known_allergies" id="known_allergies" class="form-control"
                            placeholder="Knwon Allergies" />
                    </div>
                    @if(in_array('my_health', $show_referrals_in_register_page ))
                    <div class="form-group col-md-4">
                        {!! Form::label('referral_code', __('superadmin::lang.referral_code')) !!} <small>@lang('lang_v1.please_enter_referral_code_if_any')</small>
                        {!! Form::text('referral_code', 0, ['class' => 'form-control','placeholder' =>
                        __('superadmin::lang.referral_code'), 'style' => 'width: 100%;',
                        ]); !!}
                    </div>
                    @endif
                    <div class="col-md-8">
                        <div class="form-group">
                            <small>Notes</small>
                            <textarea name="notes" class="form-control" placeholder="Notes"></textarea>
                        </div>
                        <div class="clearfix"></div>
                        @if(in_array('my_health', $show_give_away_gift_in_register_page ))
                        <div class="col-md-6">
                            <div class="form-group">
                                {!! Form::label('r_give_away_gifts', __('superadmin::lang.give_away_gifts') . ':') !!}
                                @foreach ($give_away_gifts as $key => $give_away_gift)
                                <div class="checkbox">
                                    <small>
                                        {!! Form::checkbox('give_away_gifts[]', $key, false, ['class' => '', 'id' => "r_give_away_gifts"]);
                                        !!} {{$give_away_gift}}
                                    </small>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                </div>
            </div>
            <div class="row">
                
				@if(!empty($business_settings->captch_site_key))
                    <div class="col-md-12">
                    <div class="form-group" style="padding:auto; margin-top:10px;margin-bottom:10px;">
                    <div class="g-recaptcha" data-sitekey="{{ $business_settings->captch_site_key }}"></div>
                    </div>
				@endif
            </div>
        </div>
    </div>
    <div class="modal-footer" style="padding-top: 15px; padding-bottom: 0px;">
        <div class="row">
            <div class="col-md-6" style="text-align: left;">
                 <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
            <div class="col-md-6" style="text-align: right;">
                <button class="btn btn-primary pull-right" type="button" style="display: block;" id="allow_location_access">Allow Location Access</button>
                <button class="btn btn-primary pull-right" type="submit" style="display: none;" id="the_submit_button">Submit</button>
            </div>
        </div>
    </div>
</fieldset>

<script>
    navigator.permissions.query({ name: 'geolocation' }).then(function(permissionStatus) {
        console.log("Geolocation permission status:", permissionStatus.state);
        if (permissionStatus.state === "granted") {
            $('#allow_location_access').hide();
            $('#the_submit_button').show();
        } else {
            $('#allow_location_access').show();
            $('#the_submit_button').hide();
        }

        // Listen for permission changes
        permissionStatus.onchange = function() {
            console.log("Geolocation permission status changed to:", this.state);
            if (this.state === "granted") {
                $('#allow_location_access').hide();
                navigator.geolocation.getCurrentPosition(function(position) {
                    var latitude = position.coords.latitude;
                    var longitude = position.coords.longitude;
                    document.getElementById('latitude').value = latitude;
                    document.getElementById('longitude').value = longitude;
                }, function(error) {
                    console.log("Geolocation error: ", error);
                });
                $('#the_submit_button').show();
            } else {
                $('#allow_location_access').show();
                $('#the_submit_button').hide();
            }
        };
    });
    document.getElementById('allow_location_access').addEventListener('click', function() {
        // Check if the browser supports geolocation
        if ("geolocation" in navigator) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    var latitude = position.coords.latitude;
                    var longitude = position.coords.longitude;
                    document.getElementById('latitude').value = latitude;
                    document.getElementById('longitude').value = longitude;
                    $('#allow_location_access').hide();
                    $('#the_submit_button').show();
                },
                function(error) {
                    // Handle different types of geolocation errors
                    switch (error.code) {
                        case error.PERMISSION_DENIED:
                            console.log("Location access denied by the user. Please allow location access under site settings.");
                            alert("Location access denied by the user. Please allow location access under site settings.");
                            break;
                        case error.POSITION_UNAVAILABLE:
                            console.log("Location information is unavailable.");
                            alert("Location information is unavailable.");
                            break;
                        case error.TIMEOUT:
                            console.log("Location request timed out.");
                            alert("Location request timed out.");
                            break;
                        default:
                            console.log("An unknown error occurred.");
                            alert("An unknown error occurred.");
                            break;
                    }
                }
            );
        } else {
            console.log("Geolocation is not supported by this browser.");
            alert("Geolocation is not supported by this browser.");
        }
    });
</script>

<script>
        /*
        // Function to display the location details
        function displayLocationDetails(address) {
            const state = document.getElementById('p_state');
            const address1 = document.getElementById('p_address');
            const country = document.getElementById('p_country');
            const district = document.getElementById('p_district');
            const city = document.getElementById('p_city');
            
             state.value = `${address.state}`;
             city.value = `${address.city}`;
             country.value = `${address.country}`;
             district.value = `${address.district}`;
             address1.value = `${address.formatted_address}`;
             
        }

        // Function to get address from coordinates using Google Maps API
        function getAddressFromCoordinates(latitude, longitude) {
            const apiKey = 'AIzaSyCsHrbMB_bsgtLPVdv63bbvLOoszPN4bw8'; // Replace with your API key
            const url = `https://maps.googleapis.com/maps/api/geocode/json?latlng=${latitude},${longitude}&key=${apiKey}`;

            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'OK' && data.results.length > 0) {
                        const addressComponents = data.results[0].address_components;

                        const address = {
                            formatted_address: data.results[0].formatted_address,
                            city: '',
                            district: '',
                            state: '',
                            country: '',
                        };

                        // Extract address components
                        addressComponents.forEach(component => {
                            if (component.types.includes("locality")) {
                                address.city = component.long_name;
                            }
                            if (component.types.includes("administrative_area_level_2")) {
                                address.district = component.long_name;
                            }
                            if (component.types.includes("administrative_area_level_1")) {
                                address.state = component.long_name;
                            }
                            if (component.types.includes("country")) {
                                address.country = component.long_name;
                            }
                        });

                        displayLocationDetails(address);
                    } else {
                        document.getElementById('location-details').innerHTML = "Unable to retrieve address.";
                    }
                })
                .catch(error => {
                    console.error('Error fetching address:', error);
                });
        }
        
        
        // Get current location on page load
        document.addEventListener("DOMContentLoaded", function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        const latitude = position.coords.latitude;
                        const longitude = position.coords.longitude;
                        getAddressFromCoordinates(latitude, longitude); // Fetch the address
                    },
                    function(error) {
                        document.getElementById('location-details').innerHTML = "Unable to retrieve your location.";
                    }
                );
            } else {
                document.getElementById('location-details').innerHTML = "Geolocation is not supported by this browser.";
            }
        });
        */
        
    </script>

