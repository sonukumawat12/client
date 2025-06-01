@component('components.widget', ['class' => '', 'title' => 'Contact Settings'])


<div class="row">
    {{-- {!! Form::open(['url' => action('ContactController@save_settings_contacts'), 'method' => 'POST']) !!} --}}
    {!! Form::open(['id' => 'settingsFormContacts', 'method' => 'POST']) !!}

    <div class="form-group col-sm-4">
        {!! Form::label('location', 'Location', ['class' => 'mr-2']) !!}: &nbsp;
        {!! Form::select('location[]', [], null, ['class' => 'form-control select2 select2_location', 'multiple' => 'multiple', 'placeholder' => 'Select Location', 'required']) !!}
    </div>
    
    <div class="form-group col-sm-4">
        {!! Form::label('user_id', __('contact.customer_advance_payments'), ['class' => 'mr-2']) !!}: &nbsp;
        {!! Form::select('customer_advance', $contact_settings['liability_accounts'], !empty($contact_settings['data']) ? $contact_settings['data']->customer_advance : null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.all'), 'required']) !!}
    </div>
    
    <div class="form-group col-sm-4">
        {!! Form::label('user_id', __('contact.supplier_advance_payments'), ['class' => 'mr-2']) !!}: &nbsp;
        {!! Form::select('supplier_advance', $contact_settings['asset_accounts'], !empty($contact_settings['data']) ? $contact_settings['data']->supplier_advance : null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.all'), 'required']) !!}
    </div>
    
    <div class="form-group col-sm-4">
        {!! Form::label('user_id', __('contact.customer_deposit_refund_liability_account'), ['class' => 'mr-2']) !!}: &nbsp;
        {!! Form::select('customer_deposit_refund_liability_account', $contact_settings['liability_accounts'], !empty($contact_settings['data']) ? $contact_settings['data']->customer_deposit_refund_liability_account : null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.all'), 'required']) !!}
    </div>
    
    <div class="form-group col-sm-4">
        {!! Form::label('user_id', __('contact.customer_deposit_refund_asset_account'), ['class' => 'mr-2']) !!}: &nbsp;
        {!! Form::select('customer_deposit_refund_asset_account', $contact_settings['asset_accounts'], !empty($contact_settings['data']) ? $contact_settings['data']->customer_deposit_refund_asset_account : null, ['class' => 'form-control select2', 'placeholder' => __('lang_v1.all'), 'required']) !!}
    </div>
    <div class="form-group col-sm-4">
        <br>
       <button type="submit" class="btn btn-primary mt-6">@lang('messages.save')</button>
    </div>
    
   
    
    {!! Form::close() !!}
</div>



<div class="row">

    <div class="table-responsive">
        <table class="table table-bordered table-striped" style="width: 100%" id="contact_table">
            <thead>
                
                <tr>
                    <th>Date & Time</th>
                    <th>Location</th>
                    <th>@lang('contact.customer_advance_payments')</th>
                    <th>@lang('contact.supplier_advance_payments')</th>
                    <th>@lang('contact.customer_deposit_refund_liability_account')</th>
                    <th>@lang('contact.customer_deposit_refund_asset_account')</th>
                    <th>@lang('lang_v1.created_by')</th>
                </tr>
            </thead>
            <tbody>
                @if(!empty($contact_settings['data']))
                <tr>
                    <td>{{ $contact_settings['data']->created_at }}</td>
                    {{-- <td>{{ $contact_settings['data']->location }}</td> --}}
                    <td>{{ $contact_settings['location_names'] }}</td>
                    <td>{{ $contact_settings['data']->cust }}</td>
                    <td>{{ $contact_settings['data']->sup }}</td>
                    <td>{{ $contact_settings['data']->_customer_deposit_refund_liability_account }}</td>
                    <td>{{ $contact_settings['data']->_customer_deposit_refund_asset_account }}</td>
                    
                    <td>{{ $contact_settings['data']->username }}</td>
                </tr>
                @endif
               
            </tbody>
        </table> 
    </div>

</div>

@endcomponent
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



<script>
    $(document).ready(function() {
        $('.select2_location').select2({
            placeholder: 'Select Location',
            allowClear: true
        });

        $.ajax({
            url: '{{ route("location.contacts") }}', 
            type: 'GET',
            dataType: 'json',
            success: function(data) {
                  $('.select2_location').empty(); 

                    $.each(data, function(key, value) {
                        var newOption = new Option(value.text, value.id, false, false);
                        $('.select2_location').append(newOption).trigger('change');
                    });

                    $('.select2_location').select2({
                        placeholder: 'Select Location',
                        allowClear: true
                    });
            },
            error: function(xhr, status, error) {
                console.log('Error: ' + error);
            }
        });


        $('#settingsFormContacts').on('submit', function (e) {
                e.preventDefault();

                var formData = $(this).serialize(); 
                var csrfToken = $('meta[name="csrf-token"]').attr('content');

                $.ajax({
                    url: '{{ route("check.contacts.settings.location") }}', 
                    type: 'GET',
                    data: formData,
                    success: function (response) {
                        console.log(response);

                        if (response.exists === false) {
                            Swal.fire({
                                title: 'Location does exist!',
                                text: 'Settings are already set for the Location  Do you still wish to continue?',
                                icon: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes, save it!',
                                cancelButtonText: 'No, cancel!'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    $.ajax({
                                        url: '{{ route("store.contacts.settings") }}', 
                                        type: 'POST',
                                        data:  $('#settingsFormContacts').serialize(),
                                        headers: {
                                            'X-CSRF-TOKEN': csrfToken
                                        },
                                        success: function () {
                                            window.location.href = '{{ url("airline/airline_settings") }}';
                                        },
                                        error: function (xhr, status, error) {
                                            console.log('Error: ' + error);
                                        }
                                    });
                                }
                            });
                        }
                    },
                    error: function (xhr, status, error) {
                        console.log('Error: ' + error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Oops...',
                            text: 'Something went wrong: ' + error
                        });
                    }
                });
            });



        // $('#settingsFormContacts').on('submit', function (e) {
           
        //     e.preventDefault();

        //     var formData = $(this).serialize(); 


        //     $.ajax({
        //         url: '{{ route("check.contacts.settings.location") }}', 
        //         type: 'GET',
        //         data: formData,
              
        //             success: function (response) {
        //                 console.log(response);

        //                 if(response.exists === false){

        //                      $.ajax({
        //                         url: '{{ route("store.contacts.settings") }}', 
        //                         type: 'POST',
        //                         data: formData, 
        //                         success: function (response) {

        //                              window.location.href = '{{ url("airline/airline_settings") }}';

        //                         },
        //                         error: function (xhr, status, error) {
        //                             console.log('Error: ' + error);
        //                         }
        //                     });

        //                 }
        //             },
        //             error: function (xhr, status, error) {
        //                 console.log('Error: ' + error);
        //                 alert(status);

        //             }
        //     });

        // });



    });
</script>