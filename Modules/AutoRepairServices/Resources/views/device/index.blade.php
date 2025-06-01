 @component('components.widget', ['class' => 'box-primary', 'title' => __( 'category.manage_your_categories' )])
        
            @slot('tool')
<button type="button" class="btn btn-sm pull-right btn-primary"
    data-href="{{ action('\Modules\AutoRepairServices\Http\Controllers\DeviceModelController@create_device') }}"
    data-container=".category_modal" id="device_category">
    <i class="fa fa-plus"></i> @lang('messages.add')
</button>
  @endslot
<br><br>
<div class="table-responsive">
    <table class="table table-bordered table-striped" id="device_table" style="width: 100%">
        <thead>
            <tr>
                <th>@lang('repair::lang.device')</th>
                  <th>@lang( 'lang_v1.description' )</th>
                <th>@lang('messages.action')</th>
                 
            </tr>
        </thead>
    </table>
</div>
 @endcomponent
<!-- Modal Container -->
<!-- <div class="modal fade device_category_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div> -->
<div class="modal fade category_modal" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>
<!-- JavaScript for Modal Handling -->
<script>
$(document).on('click', '#device_category', function () {
    var url = $(this).data('href'); // Get the URL for fetching modal content

    $.ajax({
        method: 'GET',
        dataType: 'html',
        url: url,
        success: function (response) {
            console.log(response); // Debugging output

            // Append the response to the modal container
            $('.category_modal').html(response);

            // Initialize and show the modal
            $('.category_modal').modal('show');
        },
        error: function (xhr, status, error) {
            console.error('Error loading modal content:', error);
            toastr.error('Failed to load modal content.');
        }
    });
});
</script>