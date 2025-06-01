$(document).on('submit','#vat_quick_add_reference', function(event) {
            // Prevent the default form submission
            event.preventDefault();
            

            // Perform AJAX request
            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(), // Serialize form data
                success: function(response) {
                    if(response.success == true){
                        toastr.success(response.msg);
                        
                        var customerId = response.data.id;
                        var customerName = response.data.reference;
                        $('#reference_id').append($('<option>', {
                            value: customerId,
                            text: customerName,
                            selected: true
                        }));
                    
                        $('#reference_id').val(response.data.id).trigger('change');
                        
                        $('.contact_modal').modal('hide');
                    }else{
                        toastr.error(response.msg);
                    }
                    
                    
                    
                },
                error: function(xhr, status, error) {
                    console.error(error); // Log any errors
                }
            });
        });
$(document).on('submit','#vat_quick_add_customer', function(event) {
            // Prevent the default form submission
            event.preventDefault();
            

            // Perform AJAX request
            $.ajax({
                method: 'POST',
                url: $(this).attr('action'),
                data: $(this).serialize(), // Serialize form data
                success: function(response) {
                    if(response.success == true){
                        toastr.success(response.msg);
                        
                        console.log(response);
                        
                        var customerId = response.data.id;
                        var customerName = response.data.name;
                        $('#customer_id').append($('<option>', {
                            value: customerId,
                            text: customerName,
                            selected: true
                        }));
                    
                        $('#customer_id').val(response.data.id).trigger('change');
                        
                        $('.contact_modal').modal('hide');
                    }else{
                        toastr.error(response.msg);
                    }
                    
                    
                    
                },
                error: function(xhr, status, error) {
                    console.error(error); // Log any errors
                }
            });
        });