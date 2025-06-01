<input id="{{$input_phone_name}}" type="tel" name=""  class="form-control-lg w-100" >
<input id="{{$input_phone_name}}_code" type="hidden" name="{{$input_phone_name}}[code]"  class="form-control-lg w-100" @isset($input_phone)
value="{{$input_phone['code'] ?? ''}}"    
@else
value=""
@endisset/>
<input id="{{$input_phone_name}}_phone" type="hidden" name="{{$input_phone_name}}[number]"  class="form-control-lg w-100" @isset($input_phone)
value="{{$input_phone['number'] ?? ''}}"    
@else
value=""
@endisset/>
<script>
    var phoneInputField =  document.querySelector('#{{$input_phone_name}}');
    var phoneCode = document.querySelector('#{{$input_phone_name}}_code');
    var phoneNumberInput = document.querySelector('#{{$input_phone_name}}_phone');
    const phoneInput = window.intlTelInput(phoneInputField, {
        preferredCountries: ["lk","pk",'ir','iq'],
        utilsScript:
        "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.8/js/utils.js",
    });
    @isset($input_phone)
    phoneInput.setNumber("+{{$input_phone['code'] ?? ''}}{{$input_phone['number'] ?? ''}}");
    //cleanPhoneNumber();
    @endisset
    phoneInputField.addEventListener("countrychange", function() {
        cleanPhoneNumber();

});
phoneInputField.addEventListener("keyup",function(){
    cleanPhoneNumber();
});

function cleanPhoneNumber(){
    var phone_setting = phoneInput.getSelectedCountryData();
    phoneCode.value = phone_setting.dialCode;
    phoneNumberInput.value = phoneInput.getNumber().replace('+'+phoneCode.value,'');
   // phoneInput.setNumber(phoneNumberInput.value);
}
</script>