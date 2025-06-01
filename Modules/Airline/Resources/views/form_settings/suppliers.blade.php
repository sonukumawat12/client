

 
            <form id="supplier-form" action="javascript:void(0)" method="POST">
                @csrf 


<div class="row mt-4">
                            <div class="col-md-12">

                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="type" value="1" class="input-icheck not_change" >
                                            Type
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="name" value="1" class="input-icheck not_change" >
                                            Name
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="contact_id" value="1" class="input-icheck not_change" >
                                            Contact ID
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="tax_number" value="1" class="input-icheck" >
                                            Tax Number
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="opening_balance" value="1" class="input-icheck not_change" >
                                            Opening Balance
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="pay_term" value="1" class="input-icheck" >
                                            Pay Term
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="transaction_date" value="1" class="input-icheck not_change" >
                                            Transaction Date
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="supplier_group" value="1" class="input-icheck" >
                                            Supplier Group
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="email" value="1" class="input-icheck" >
                                            Email
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="mobile" value="1" class="input-icheck" >
                                            Mobile
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="alternate_contact_number" value="1" class="input-icheck" >
                                            Alternate Contact Number
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="landline" value="1" class="input-icheck" >
                                            Landline
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="address" value="1" class="input-icheck" >
                                            Address
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="city" value="1" class="input-icheck" >
                                            City
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="state" value="1" class="input-icheck" >
                                            State
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="country" value="1" class="input-icheck" >
                                            Country
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="landmark" value="1" class="input-icheck" >
                                            Landmark
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="custom_field_1" value="1" class="input-icheck" >
                                            Custom Field 1
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="custom_field_2" value="1" class="input-icheck" >
                                            Custom Field 2
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="custom_field_3" value="1" class="input-icheck" >
                                            Custom Field 3
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="custom_field_4" value="1" class="input-icheck" >
                                            Custom Field 4
                                        </label>
                                    </div>
                                </div>
                                

                            </div>
                        </div>



            <div class="row">
                    <div class="col-sm-8">
                        
                    </div>
                    <div class="col-sm-4">
                        <button type="submit" class="btn btn-primary mt-5 ml-5">Update Suppliers</button>
                    </div>

                </div>

            </form>





                        <script>

                                        $(document).ready(function() {
                                            $('#supplier-form').on('submit', function(e) {

                                                e.preventDefault();

                                                    var formData = $(this).serialize();

                                                    $.ajax({
                                                        url: "{{ url('airline/form_settings/update_suppliers') }}", 
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
                                                        url: "{{ url('airline/form_settings/check_form_settings_suppliers') }}", 
                                                        type: 'GET',
                                                        
                                                        success: function(response) {

                                                            var settings = response.data;
                                                            $('input[name="type"]').prop('checked', settings.type == 1);
                                                            $('input[name="tax_number"]').prop('checked', settings.tax_number == 1);
                                                            $('input[name="transaction_date"]').prop('checked', settings.transaction_date == 1);
                                                            $('input[name="mobile"]').prop('checked', settings.mobile == 1);
                                                            $('input[name="address"]').prop('checked', settings.address == 1);
                                                            $('input[name="country"]').prop('checked', settings.country == 1);
                                                            $('input[name="custom_field_1"]').prop('checked', settings.custom_field_1 == 1);
                                                            $('input[name="custom_field_2"]').prop('checked', settings.custom_field_2 == 1);
                                                            $('input[name="custom_field_3"]').prop('checked', settings.custom_field_3 == 1);
                                                            $('input[name="custom_field_4"]').prop('checked', settings.custom_field_4 == 1);
                                                            $('input[name="name"]').prop('checked', settings.name == 1);
                                                            $('input[name="opening_balance"]').prop('checked', settings.opening_balance == 1);
                                                            $('input[name="supplier_group"]').prop('checked', settings.supplier_group == 1);
                                                            $('input[name="alternate_contact_number"]').prop('checked', settings.alternate_contact_number == 1);
                                                            $('input[name="city"]').prop('checked', settings.city == 1);
                                                            $('input[name="landmark"]').prop('checked', settings.landmark == 1);
                                                            $('input[name="contact_id"]').prop('checked', settings.contact_id == 1);
                                                            $('input[name="pay_term"]').prop('checked', settings.pay_term == 1);
                                                            $('input[name="email"]').prop('checked', settings.email == 1);
                                                            $('input[name="landline"]').prop('checked', settings.landline == 1);
                                                            $('input[name="state"]').prop('checked', settings.state == 1);
                                                            

                                                        },
                                                        error: function(xhr, status, error) {
                                                            alert('Error: ' + error);
                                                        }
                                                    });

                                            }
                                        });
                            
                            </script>