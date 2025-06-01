<form id="passengers-form" action="javascript:void(0)" method="POST">
    @csrf 

<div class="row mt-4">
                            <div class="col-md-12">

                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="passenger_name" value="1" class="input-icheck">
                                            Name
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="passport_number" value="1" class="input-icheck">
                                            Passport Number
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="vat_number" value="1" class="input-icheck">
                                            VAT Number
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="passenger_mobile_no" value="1" class="input-icheck">
                                            Passenger Mobile No
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="select_passport_image" value="1" class="input-icheck">
                                            Select Passport Image
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="need_to_send_sms" value="1" class="input-icheck">
                                            Need to send SMS
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="frequent_flyer_no" value="1" class="input-icheck">
                                            Frequent Flyer No
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="child" value="1" class="input-icheck">
                                            Child
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="price" value="1" class="input-icheck">
                                            Price
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="additional_service" value="1" class="input-icheck">
                                            Additional Service
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="additional_service_amount" value="1" class="input-icheck">
                                            Additional Service Amount
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="passenger_type" value="1" class="input-icheck">
                                            Passenger Type
                                        </label>
                                    </div>
                                </div>
                                


                                
                            </div>
                        </div>



                        <div class="row">
                            <div class="col-sm-8">
                                
                            </div>
                            <div class="col-sm-4">
                                <button type="submit" class="btn btn-primary mt-5 ml-5">Update Passengers</button>
                            </div>
        
                        </div>
        
                    </form>
        
        
        
        
        
                <script>

                    $(document).ready(function() {
                        $('#passengers-form').on('submit', function(e) {
                    
                            e.preventDefault();
                    
                                // Get the form data
                                var formData = $(this).serialize();
                    
                                $.ajax({
                                    url: "{{ url('airline/form_settings/update_passengers') }}", 
                                    type: 'POST',
                                    data: formData,
                                    success: function(response) {
                                        alert('Form submitted successfully!');
                                        
                                        checkData();
                                    },
                                    error: function(xhr, status, error) {
                                        alert('Error: ' + error);
                                    }
                                });
                            
                        });

                        checkData();
                        function checkData(){

                            $.ajax({
                                    url: "{{ url('airline/form_settings/check_form_settings_passengers') }}", 
                                    type: 'GET',
                                    
                                    success: function(response) {

                                        var settings = response.data;
                                        $('input[name="passenger_name"]').prop('checked', settings.name == 1);
                                        $('input[name="passport_number"]').prop('checked', settings.passport_number == 1);
                                        $('input[name="vat_number"]').prop('checked', settings.vat_number == 1);
                                        $('input[name="passenger_mobile_no"]').prop('checked', settings.passenger_mobile_no == 1);
                                        $('input[name="select_passport_image"]').prop('checked', settings.select_passport_image == 1);
                                        $('input[name="need_to_send_sms"]').prop('checked', settings.need_to_send_sms == 1);
                                        $('input[name="frequent_flyer_no"]').prop('checked', settings.frequent_flyer_no == 1);
                                        $('input[name="child"]').prop('checked', settings.child == 1);
                                        $('input[name="price"]').prop('checked', settings.price == 1);
                                        $('input[name="additional_service"]').prop('checked', settings.additional_service == 1);
                                        $('input[name="additional_service_amount"]').prop('checked', settings.additional_service_amount == 1);
                                        $('input[name="passenger_type"]').prop('checked', settings.passenger_type == 1);

                   
                                    },
                                    error: function(xhr, status, error) {
                                        alert('Error: ' + error);
                                    }
                                });

                        }

                        
                    });
                    
                    
                    </script>