@extends('layouts.app')

@section('title', __('lang_v1.sms_groups'))

@section('content')
<!-- Main content -->

<section class="content">
    <div class="row">
        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.sms_groups' )])
        
        {!! Form::open(['url' => action('\Modules\SMS\Http\Controllers\SMSController@storeSmsGroup'), 'method' =>
            'post', 'id' => 'sms_list_interest_form', 'enctype' => 'multipart/form-data' ])
            !!}
        <div class="row">
            <div class="col-md-6">
                <!-- Form fields -->
            </div>
            <div class="col-md-6">
                <!-- Form fields -->
            </div>
        </div>
        @slot('tool')
        <div class="box-tools pull-right">
            <div class="box-tools">
                <button type="button" class="btn  btn-primary"   id="sms_group_add" data-href="{{action('\Modules\SMS\Http\Controllers\SMSController@createSmsGroup')}}" >
                    <i class="fa fa-plus"></i> @lang('messages.add')
                </button>
            </div>
        </div>
        <hr>
        @endslot
        
        {!! Form::close() !!}
        
        @endcomponent
    </div>

    <!-- New Table Section -->
    <div class="row">
        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'lang_v1.sms_group_list' )])
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="sms_group_table">
                <thead>
                    <tr>
                        <th>@lang('sms::lang.action')</th>
                        <th>@lang('sms::lang.date_imported')</th>
                        <th>@lang('sms::lang.sms_group_name')</th>
                        <th>@lang('sms::lang.total_numbers')</th>
                        <th>@lang('sms::lang.user_imported')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($sms_groups as $group)
                        <tr>
                           
                            <td>
                                <!-- Edit Button -->
                                <button class="btn btn-xs btn-primary edit_group" data-id="{{ $group->id }}">
                                    @lang('lang_v1.edit')
                                </button>
                    
                                <!-- Delete Form -->
                                {!! Form::open([
                                    'url' => action('\Modules\SMS\Http\Controllers\SMSController@deleteSmsGroup', ['id' => $group->id]), 
                                    'method' => 'delete', 
                                    'style' => 'display:inline'
                                ]) !!}
                                    <button type="submit" class="btn btn-xs btn-danger" onclick="return confirm('@lang('sms::lang.confirm_delete')')">
                                        @lang('lang_v1.delete')
                                    </button>
                                {!! Form::close() !!}
                            </td>
                            <td>{{ $group->date }}</td>
                            <td>{{ $group->group_name }}</td>
                            <td>{{ $group->member_count }}</td>
                            <td>{{ $group->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @endcomponent
    </div>
        <div class="modal fade" id="sms_group_modal" tabindex="-1" role="dialog"></div>
    <div class="modal fade add_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
@endsection

@section('javascript')
<script>
$(document).ready(function(){
    $(".select2").select2();

    // Existing logic for form handling
    $('#schedule_campaign').change(function() {
        // Schedule campaign handling logic
    });

    $('#frequency').change(function() {
        // Frequency change handling logic
    });

    $('#message_tags').change(function() {
        // Message tags insertion logic
    });

    // Handle Edit button click
    $('.edit_group').click(function() {
        var groupId = $(this).data('id');
        var url = "{{ action('\Modules\SMS\Http\Controllers\SMSController@editSmsGroup', ['id' => ':id']) }}";
        url = url.replace(':id', groupId);

        // Make an AJAX call to get the edit form
        $.ajax({
            method: 'GET',
            url: url,
            success: function(response) {
                // Load the response into the modal and show it
                $("#sms_group_modal").html(response).modal('show');
            },
            error: function() {
                alert('Failed to load the edit form. Please try again.');
            }
        });
    });

    // Handle Delete button click
    $('.delete_group').click(function() {
        var groupId = $(this).data('id');
        var url = '/sms-groups/' + groupId + '/delete';
        
    
        if (confirm("@lang('sms::lang.confirm_delete')")) {
                $.ajax({
                    url: "{{ url('sms-groups/delete')}}" + '/' + groupId,
                    type: "DELETE",
                     headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        _token: "{{csrf_token()}}"
                    },
                    cache: false,
                success: function(response) {
                    if (response.success) {
                        alert(response.msg);
                        location.reload(); // Refresh the page to remove the deleted group
                    } else {
                        alert(response.msg);
                    }
                }
            });
        }
    });

});

    $(document).on('click', '#sms_group_add', function () {
			var url = $(this).data('href');
           
			$.ajax({
				method: 'GET',
				dataType: 'html',
				url: url,
				success: function (response) {
                    console.log(response);
					$("#sms_group_modal").html(response).modal('show');
				}
			});
		});
</script>
@endsection