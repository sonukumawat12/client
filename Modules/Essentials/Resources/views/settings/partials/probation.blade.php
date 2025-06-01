<div class="pos-tab-content">
    <div class="row">
        <!-- Date & Time Field -->


        {!! Form::open(['action' => '\Modules\Essentials\Http\Controllers\EssentialsProbationController@store', 'method' => 'post', 'id' => 'essentials_settings_form']) !!}

        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('date_time', __('essentials::lang.date_time') . ':') !!}
                {!! Form::datetimeLocal('date_time', \Carbon\Carbon::now()->format('Y-m-d\TH:i'), ['class' => 'form-control', 'placeholder' => __('essentials::lang.date_time'), 'readonly' => true]) !!}
            </div>
        </div>


        <!-- Department Dropdown -->
        <div class="col-xs-6">
            <div class="form-group">
                @php
                    $departmentOptions = ['0' => "All Departments"] + $departments->toArray();
                @endphp
                {!! Form::label('department', __('essentials::lang.department') . ':') !!}
                {!! Form::select(
                    'department',
                    $departmentOptions,
                    null, // Default selected value
                    ['class' => 'form-control', 'id' => 'department']
                ) !!}
            </div>
        </div>

        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('designation', __('essentials::lang.designation') . ':') !!}
                {!! Form::select(
                    'designation',
                    ['0' => "All Designations"],
                    null, // Default selected value
                    ['class' => 'form-control', 'id' => 'designation']
                ) !!}
            </div>
        </div>

        <!-- Period Dropdown (Days or Months) -->
        <div class="col-xs-6">
            <div class="form-group">
                {!! Form::label('period', __('essentials::lang.period') . ':') !!}
                {!! Form::select('period', ['days' => 'Days', 'months' => 'Months'], null, ['class' => 'form-control', 'placeholder' => __('essentials::lang.select_period')]) !!}
            </div>
        </div>

        <div class="form-group text-center">
            {{Form::submit(__('messages.save'), ['class'=>"btn btn-success"])}}
        </div>


        {!! Form::close() !!}
    </div>

    <div class="row">
        <div class="col-12">
            @component('components.widget', ['class' => 'box-solid'])
        <div class="table-responsive">
            <table class="table table-bordered table-striped " id="probation-table" style="width: 100%";>
                <thead>
                    <tr>
                        <th>@lang('messages.action')</th>
                        <th>@lang('essentials::lang.date_time')</th>
                        <th>@lang('essentials::lang.department')</th>
                        <th>@lang('essentials::lang.designation')</th>
                        <th>@lang('essentials::lang.period')</th>
                        <th>@lang('essentials::lang.status')</th>
                        <th>@lang('essentials::lang.user_added')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
        </div>
    </div>
</div>
@section('javascript')


<script>
    $(document).ready(function () {
       var probationTable =  $('#probation-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route("probation.index") }}',
            columns: [
                { data: 'action', name: 'action', orderable: false, searchable: false },
                { data: 'date_time', name: 'probations.date_time' },
                { data: 'department', name: 'departments.name' },
                { data: 'designation', name: 'designations.name' },
                { data: 'period', name: 'probations.period' },
                { data: 'status', name: 'probations.status' },
                { data: 'user_added', name: 'u.username' },
            ],
        });

        // Delete Button Click
        $(document).on('click', 'button.delete-button', function () {
            var deleteUrl = $(this).data('href');  // Get the URL to delete this probation record

            swal({
                title: LANG.sure, // Make sure LANG.sure is defined in your localization file
                text: "{{ __('This action cannot be undone.') }}", // Optional confirmation text
                icon: 'warning',
                buttons: true,
                dangerMode: true,
            }).then((willDelete) => {
                if (willDelete) {
                    // Perform the delete action using AJAX
                    $.ajax({
                        method: 'DELETE',
                        url: deleteUrl,
                        dataType: 'json',
                        success: function (response) {
                            if (response.success) {
                                toastr.success(response.msg); // Display success message
                                probationTable.ajax.reload();  // Reload the DataTable
                            } else {
                                toastr.error(response.msg); // Display error message
                            }
                        },
                        error: function () {
                            toastr.error("{{ __('messages.something_went_wrong') }}"); // Error handling
                        }
                    });
                }
            });
        });
    });

</script>

   <script>
       $(document).ready(function () {
           $('#department').change(function () {
               var departmentId = $(this).val();

               if (departmentId) {
                   $.ajax({
                       url: '{{ route('hrm.get.designations') }}',
                       type: 'POST',
                       data: {
                           department_id: departmentId,
                           _token: '{{ csrf_token() }}'
                       },
                       success: function (data) {
                           $('#designation').empty().append('<option value="0">{{ "All Designations" }}</option>');
                           $.each(data, function (key, value) {
                               $('#designation').append('<option value="' + key + '">' + value + '</option>');
                           });
                       }
                   });
               } else {
                   $('#designation').empty().append('<option value="0">{{ "All Designations" }}</option>');
               }
           });
       });
   </script>

@endsection