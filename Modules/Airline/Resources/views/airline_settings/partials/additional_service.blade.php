@component('components.widget', ['class' => '', 'title' => 'Addional Services'])
<div class="row" style="margin-bottom: 4px;">
    <button type="button" class="btn btn-primary btn-modal pull-right"
    id="create_service">
        <i class="fa fa-plus"></i>
        Add
    </button>
</div>
<div class="row">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="service_table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Service Name</th>
                    <th>Description</th>
                    <th>Created Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                 @foreach ($airline_additional_service as $i => $ap)
                <tr>
                    <td>{{ $i +1 }}</td>
                    <td>{{ $ap->name }}</td>
                    <td>{{ $ap->description }}</td>
                    <td>{{ $ap->date_added }}</td>
                    <td class="d-flex">
                        <button class="btn btn-sm bg-warning edit" data-id="{{ $ap->id }}" data-servicename="{{ $ap->name }}" data-description="{{ $ap->description }}">Edit</button>
                        <button class="btn btn-sm bg-danger delete" style="margin-left: 4px" data-id="{{ $ap->id }}" data-servicename="{{ $ap->name }}">Delete</button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="service_form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="service_form" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="service_form_title">Modal title</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 p-3">
                            <button type="button" class="btn btn-primary btn-modal pull-right" id="addFormBtn">
                        <i class="fa fa-plus"></i>
                    </button>
                        </div>
                    </div>
                    
                    <div class="row  form-section">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="serviceName" class="col-form-label text-md-right">Name *</label>
                                <input type="text" class="form-control" name="serviceName" autocomplete="name" autofocus>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="description" class="col-form-label text-md-right">Description*</label>
                                <textarea class="form-control large-input" name="description" autocomplete="description" autofocus></textarea>
                            </div>
                        </div>
                        <div class="col-md-12 text-right">
                            <button class="btn btn-danger btn-sm p-0 remove-form-section"><i class="fa fa-minus"></i></button>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>

    </div>
</div>

<div class="modal fade" id="service_edit_form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel1">
    <div class="modal-dialog" role="document">
        <form id="service_edit_form" method="post">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="service_form_edit_title">Modal title</h4>
                </div>
                <div class="modal-body">
                    <div class="row  form-section">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="serviceName" class="col-form-label text-md-right">Name *</label>
                                <input type="text" class="form-control" name="serviceName" autocomplete="name" autofocus>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group">
                                <label for="description" class="col-form-label text-md-right">Description*</label>
                                <textarea class="form-control large-input" name="description" autocomplete="description" autofocus></textarea>
                            </div>
                        </div>
                    </div>
                    
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>

    </div>
</div>


<div class="modal fade" id="service_delete_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">Are you sure to remove <strong id="service_delete_name"></strong></h4>
            </div>
            <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-danger" id="delete_additional_service">Delete</button>
            </div>
        </div>
    </div>
</div>

@endcomponent


<script>
   const service_module = {
        edit_id: null,
        delete_id: null,
        init: function() {
            this.listener()
        },
        submit: () => {
             
             var postData = [];

            $('#service_form_modal .form-section').each(function () {
                var formData = {
                    serviceName: $(this).find('[name^="serviceName"]').val(),
                    description: $(this).find('[name^="description"]').val()
                };

                postData.push(formData);
            });
            
            // console.log(postData)
            $.ajax({
            url: '/airline/add_service',
            method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                 data: {
                    data: postData 
                },
                success: function (data, b, c) {
                    if (data.statusText == 'Success') {
                        var currentUrl = window.location.href;
                        var urlParts = currentUrl.split('?');
                        var mainUrl = urlParts[0];
                        var updatedUrl = updatedUrl = mainUrl + '?tab=additionalservice';
                        window.location.href = updatedUrl;
                    }
                },
                error: function(data, b, c) {
                }
            });
        },
        update: () => {
            const patchData = {
                id: service_module.edit_id,
                serviceName: $('#service_edit_form input[name=serviceName]').val(),
                description: $('#service_edit_form textarea[name=description]').val()
            }

            $.ajax({
                url: '{{ route('edit_service') }}',
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: patchData,
                success: function(data, b, c) {
                    if (data.statusText == 'Success') {
                        var currentUrl = window.location.href;
                        var urlParts = currentUrl.split('?');
                        var mainUrl = urlParts[0];
                        var updatedUrl = updatedUrl = mainUrl + '?tab=additionalservice';
                        window.location.href = updatedUrl;
                    }
                },
                error: function(data, b, c) {}
            });
        },
        delete: () => {
            const patchData = {
                id: service_module.delete_id,
            }

            $.ajax({
                url: '{{ route('delete_service') }}',
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: patchData,
                success: function(data, b, c) {
                    if (data.statusText == 'Success') {
                        var currentUrl = window.location.href;
                        var urlParts = currentUrl.split('?');
                        var mainUrl = urlParts[0];
                        var updatedUrl = updatedUrl = mainUrl + '?tab=additionalservice';
                        window.location.href = updatedUrl;
                    }
                },
                error: function(data, b, c) {}
            });
        },
        
        
        resetError: () => {
            $('#agent_form .form-group').removeClass('hass-error');
            $('#agent_form .invalid-feedback').hide();
        },
        listener: function() {
            $('#create_service').click(function() {
                service_module.edit_id = null; // 
                service_module.resetError(); // Assuming there's a resetError function

                $('#service_form_title').html('Create Service');
                $('#service_form_modal').modal('show');
            });
            
            $('#service_table .edit').click(function() {
                let data = $(this).data();
                console.log(data);
                service_module.resetError();
                service_module.edit_id = data.id;
                $('#service_edit_form input[name=serviceName]').val(data.servicename);
                $('#service_edit_form textarea[name=description]').val(data.description);
                 $('#service_form_edit_title').html('Update <strong>' + data.servicename + '</strong>');
                $('#service_edit_form_modal').modal('show');
            });
            
            $('#service_table .delete').click(function() {
                let data = $(this).data();
                service_module.delete_id = data.id;
                $('#service_delete_name').html(data.servicename);
                $('#service_delete_modal').modal('show');
            });
            
            
            $('#service_form').submit((e) => {
                e.preventDefault();
               
                this.submit();
                
            });
            
            $('#service_edit_form').submit((e) => {
                e.preventDefault();
                this.update();
                
            });
            
             $('#delete_additional_service').click((e) => {
                  e.preventDefault();
                    this.delete();

            })

        }
    }
    
    $(document).ready(() => {
        service_module.init();
        
        var formSectionCounter = 1;

        // Handle the click event on the "Add Form Section" button
        $('#addFormBtn').click(function () {
            // Clone the original form section and update its ID and input names
            var clonedFormSection = $('.form-section:first').clone();
            formSectionCounter++;
            clonedFormSection.addClass('form-section-' + formSectionCounter);

            // Update input names to ensure uniqueness
            clonedFormSection.find('[name="serviceName"]').attr('name', 'serviceName_' + formSectionCounter);
            clonedFormSection.find('[name="description"]').attr('name', 'description_' + formSectionCounter);
            
            clonedFormSection.find('input, textarea').val('');
            // Append the cloned form section to the modal body
            $('#service_form_modal .modal-body').append(clonedFormSection);
        }); 
        
         // Handle form section removal
        $('#service_form_modal').on('click', '.remove-form-section', function () {
            // Remove the corresponding form section
            $(this).closest('.form-section').remove();
        });
        
    });
    
    
</script>

<style>

    .short-input {
        width: 60%; 
        margin-bottom: 5px;
        
    }
    
    .large-input {
        width: 100%;
        height: 150px; 
        margin-bottom: 5px;
    }
    
    /* Styling the labels */
    .col-form-label {
        font-weight: bold;
    }
    
    
    .form-group.row {
        margin-bottom: 15px;
    }
    
    
    .col-md-4 {
        display: flex;
        align-items: center;
    }

    
</style>   


