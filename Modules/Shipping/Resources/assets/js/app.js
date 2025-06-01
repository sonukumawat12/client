$(document).on('submit','#recipient_add_form', function(event) {
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
                var customerName = response.data.name;
                $('#recipient_id').append($('<option>', {
                    value: customerId,
                    text: customerName,
                    selected: true
                }));
            
                $('#recipient_id').val(response.data.id).trigger('change');
                
                $('.modal').modal('hide');
                
                if($("#recipient_table").length > 0){
                    recipient_table.ajax.reload();
                }
                
            }else{
                toastr.error(response.msg);
            }
            
            
            
        },
        error: function(xhr, status, error) {
            console.error(error); // Log any errors
        }
    });
});