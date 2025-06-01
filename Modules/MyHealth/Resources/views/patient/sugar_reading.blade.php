@php
if (Auth::guard('web')->check()) {
$layout = 'app';
}else{
$layout = 'member';
}
@endphp
@extends('layouts.'.$layout)

@section('title', __('Sugar Reading'))

@section('content')
<style>
    
.custom-btn {
    background-color: #61CBF3;
    color: white;
    border: none;
}
.btn-green {
    background-color: green;
    color: white; /* Ensure the text is visible */
    border: none; /* Remove border if needed */
}
.brown-header {
        color: brown; /* Set the font color to brown */
    }
</style>
<!-- Main content -->
<section class="content">
   

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'Sugar Reading')])
            @slot('tool')
           <div class="box-tools">
            <button type="button" class="btn btn-primary btn-modal pull-right" 
                data-href="{{ action('\Modules\MyHealth\Http\Controllers\SugerReadingController@create') }}"
                data-container=".suggestion_model">
                <i class="fa fa-plus"></i> Add a new Date
            </button>
        </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="sugar_reading_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    
                                    <th>@lang('member::lang.date')</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="brown-header">Breakfast</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="brown-header">Lunch</th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th class="brown-header">Dinner</th>
                                     
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be populated here by DataTables -->
                            </tbody>
                        </table>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
   <!-- Modal Structure -->
<div class="modal fade suggestion_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel" aria-hidden="true">
   <div class="modal-dialog" role="document" style="width: 65%">
    <div class="modal-content">
  
      <style>
        .select2 {
          width: 100% !important;
        }
      </style>
      {!! Form::open(['url' => action('\Modules\Member\Http\Controllers\SuggestionController@store'), 'method' =>
      'post', 'id' => 'suggestion_form', 'enctype' => 'multipart/form-data' ])
      !!}
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Remark</h4>
      </div>
  
     <div class="modal-body">
    <div class="col-md-12">
       
        
        @php
            $date_field_name = 'new';
            $data_field = [];
            $today = \Carbon\Carbon::now()->format('Y-m-d');

            // Create a date array for today's date
            $data_field = createDateArray($today);
        @endphp

        <fieldset>
  <div class="row">
    <div class="col-md-6 p-0">
      <label class="text-center w-100 l-date">Year</label>
      <div class="field-inline-block w-100 text-center">
      <input type="text" pattern="[0-9]*" maxlength="1" size="1" class="date-field form-control"  placeholder="Y" name="c"  >
      <input type="text" pattern="[0-9]*" maxlength="1" size="1" class="date-field form-control" placeholder="Y" name="c"  >
      <input type="text" pattern="[0-9]*" maxlength="1" size="1" class="date-field form-control" placeholder="Y" name="c"  >
      <input type="text" pattern="[0-9]*" maxlength="1" size="1" class="date-field form-control" placeholder="Y"  name="c"  >
      </div>
    </div>
       
    <div class="col-md-3 p-0">
      <label class="text-center w-100 l-date">Month</label>
      <div class="field-inline-block w-100 text-center">
        <input type="text" pattern="[0-9]*" maxlength="1" size="1" class="date-field form-control" placeholder="M"  name="c"  >
        <input type="text" pattern="[0-9]*" maxlength="1" size="1" class="date-field form-control" placeholder="M"  name="c"  >
      </div>
    </div>
    <div class="col-md-3 p-0">
        <label class="text-center w-100 l-date">Date</label>
        <div class="field-inline-block w-100 text-center">
      
       <input type="text" pattern="[0-9]*" maxlength="1" size="1" class="date-field form-control" placeholder="D"  name="c"  >
       <input type="text" pattern="[0-9]*" maxlength="1" size="1" class="date-field form-control" placeholder="D"  name="c"  >
      </div>
    </div>
  </div>
  </fieldset>
    </div>
</div>
      <div class="clearfix"></div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
  
    </div> 
  </div> 
</div>
 <div class="modal fade suger_reading_model" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <!-- Modal for displaying notes -->
<div class="modal fade" id="noteModal" tabindex="-1" role="dialog" aria-labelledby="noteModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="noteModalLabel">{{ __("Note Details") }}</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body" id="noteContent">
        <!-- Note content will be inserted here dynamically -->
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">{{ __("Close") }}</button>
      </div>
    </div>
  </div>
</div>

<!-- Your existing HTML for the table and other elements -->

<!-- Modal HTML -->
<div class="modal fade medicine_modal" tabindex="-1" role="dialog" aria-labelledby="medicineModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Patient Medicines</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if(isset($medicineData) && count($medicineData) > 0)
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Medicine Name</th>
                                <th>Dosage</th>
                                <th>Instructions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($medicineData as $medicine)
                                <tr>
                                    <td>{{ $medicine->medicine_name }}</td>
                                    <td>{{ $medicine->amount }}</td>
                                    <td>{{ $medicine->description }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <p>No medicines found for this patient.</p>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Include jQuery and Bootstrap scripts if not already included -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>




</section>
<!-- /.content -->

@endsection

@section('javascript')
<link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.2.4/css/fixedHeader.dataTables.min.css">
<script src="https://cdn.datatables.net/fixedheader/3.2.4/js/dataTables.fixedHeader.min.js"></script>

<script>
   
    
  $(document).on('click', '#add_suggestion_btn', function() {
        var url = $(this).data('href');
         
        // Load content into the modal body
        $('.suger_reading_model .modal-body').load(url, function() {
            // Show the modal after loading content
            $('.suger_reading_model').modal({
                backdrop: 'static',
                keyboard: false
            }).modal('show');
        });
    });

 
  $(document).on('click', '#add_suggestion_btn', function(){
        $('.suger_reading_model').modal({
            backdrop: 'static',
            keyboard: false
        })
    })
    $(".suger_reading_model").on('hide.bs.modal', function(){
        tinymce.remove('#details');
    });
  $(document).on('click', '.btn-show-note', function(e) {
    e.preventDefault();
    
    // Get the note content from the button's data-note attribute
    var noteContent = $(this).data('note');
    
    // Insert the note content into the modal
    $('#noteContent').html(noteContent);
    
    // Show the modal
    $('#noteModal').modal('show');
});



 $(document).ready(function(){
    suggestion_table = $('#sugar_reading_table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url : "{{action('\Modules\MyHealth\Http\Controllers\SugerReadingController@index')}}",
            data: function(d){
                console.log(d);
            }
        },
        columnDefs: [{
            "targets": 0,
            "orderable": true,
            "searchable": false
        }],
        columns: [
            { 
                data: 'sugar_reading_date', 
                name: 'sugar_reading_date',
                render: function (data, type, row) {
                    // Parse the date string to format it correctly
                    var date = new Date(data);
                    return date.toLocaleDateString(); // Format as MM/DD/YYYY or as per locale
                } 
            },
            { data: 'breakfast1', name: 'breakfast1' },
            { data: 'breakfast2', name: 'breakfast2' },
            { data: 'breakfast3', name: 'breakfast3' },
            { data: 'breakfast', name: 'breakfast' },
            { data: 'lunch1', name: 'lunch1' },
            { data: 'lunch2', name: 'lunch2' },
            { data: 'lunch3', name: 'lunch3' },
            { data: 'lunch', name: 'lunch' },
            { data: 'dinner1', name: 'dinner1' },
            { data: 'dinner2', name: 'dinner2' },
            { data: 'dinner3', name: 'dinner3' },
            { data: 'dinner', name: 'dinner' },
        ],
        order: [[0, 'desc']], // Order by the first column (sugar_reading_date) in descending order
        fixedHeader: true, // Enable fixed header
        fnDrawCallback: function (oSettings) {
            // Callback after table redraw
        }
    });
});

</script>
@endsection