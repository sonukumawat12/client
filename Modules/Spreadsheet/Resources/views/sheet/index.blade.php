@extends('layouts.app')
@section('title', __('spreadsheet::lang.spreadsheet'))
@section('content')
@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/themes/default/style.min.css" />
    <!-- Ladda Themeless CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.9.4/ladda-themeless.min.css">

<!-- Spin.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/spin.js/2.3.2/spin.min.js"></script>

<!-- Ladda -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.9.4/spin.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/ladda-bootstrap/0.9.4/ladda.min.js"></script>

	<style>
		.jstree-default a { 
			white-space:normal !important; height: auto; 
		}
		.jstree-anchor {
			height: auto !important;
		}
		.jstree-default li > ins { 
			vertical-align:top; 
		}
		.jstree-leaf {
			height: auto;
		}
		.jstree-leaf a{
			height: auto !important;
		}
		.sheet-info {
			margin-left: 38px;
			margin-top: -8px;
		}
		.tree-actions {
			display: inherit;
		}
		
		
		.bg-primary{
		    background-color: #8F3A84;
		}
	</style>
@endsection

<!-- Content Header (Page header) -->
<!-- Main content -->
<section class="content no-print">
	<div class="row">
		<div class="col-md-12">
			<div class="box box-solid">
				<div class="box-header with-border">
					<h3 class="box-title">@lang('spreadsheet::lang.my_spreadsheets')</h3>
					
				</div>
				<div class="box-body">
					<div class="row">
						
					<div class="box-tools pull-right">
						@can('create.folder')
							<button type="button" class="btn btn-primary add_folder_btn btn-sm">
								<i class="fas fa-folder-plus"></i>
								@lang('spreadsheet::lang.add_folder')
							</button>
						@endcan
						@can('create.spreadsheet')	
							<button type="button" class="btn btn-success add_new_btn btn-sm">
								<i class="fas fa-plus"></i>
								New Spreadsheet
							</button>
						@endcan
					</div>
					</div>
					<div class="row">
						<div class="col-md-12">
						    
						    
						    
						    
						    <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="spreadsheets_table" style="width: 100%;">
                              <thead>
                                <tr>
                                  <th>Date</th>
                                  <th>Owner</th>
                                  <th>Folder</th>
                                  <th>Spreadsheet Name</th>
                                  <th>Shared With</th>
                                  <th>Status</th>
                                  <th>Last Updated By</th>
                                  <th class="notexport">@lang('messages.action')</th>
                                  
                                </tr>
                              </thead>
                            </table>
                          </div>
							
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="modal fade" id="share_excel_modal" tabindex="-1" role="dialog"></div>
	<div class="modal fade" id="add_folder_modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
  			<div class="modal-content">
    			{!! Form::open(['action' => '\Modules\Spreadsheet\Http\Controllers\SpreadsheetController@addFolder', 'method' => 'post' ]) !!}
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">@lang('spreadsheet::lang.add_folder')</h4>
					</div>

					<div class="modal-body">
						<div class="form-group">
							<input type="hidden" id="folder_id" name="folder_id">
							{!! Form::label('folder_name', __('lang_v1.name') . ':*') !!}
							{!! Form::text('name', null, ['class' => 'form-control', 'required', 'id' => 'folder_name']); !!}
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
					</div>

				{!! Form::close() !!}

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div>
	
	<div class="modal fade" id="add_new_modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
  			<div class="modal-content">
    			    <div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">Add</h4>
					</div>

					<div class="modal-body">
					    <div class="form-group">
					        <label for="spreadsheet_name">Name</label><br>
					        <input type="text" id="spreadsheet_name" class="form-control">
					    </div>
						<div class="form-group">
							<label for="folder_name">Folder *</label><br>
							<select class="form-control select2" id="new_folder_id" style="width: 100%">
							    <option value="">Select folder</option>
							    @foreach($folders as $folder)
    							    <option value="{{$folder->id}}">{{$folder->name}}</option>
							    @endforeach
							</select>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" id="submit_add_new" class="btn btn-primary">@lang( 'messages.save' )</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
					</div>

			

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div>

	<div class="modal fade" id="move_to_folder_modal" tabindex="-1" role="dialog">
		<div class="modal-dialog" role="document">
  			<div class="modal-content">
    			{!! Form::open(['action' => '\Modules\Spreadsheet\Http\Controllers\SpreadsheetController@moveToFolder',
					 'method' => 'post' ]) !!}
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						<h4 class="modal-title">@lang('spreadsheet::lang.move_to')</h4>
					</div>

					<div class="modal-body">
						<input type="hidden" id="spreadsheet_id" name="spreadsheet_id">
						<div class="form-group">
							{!! Form::label('move_to_folder', __('spreadsheet::lang.folder') . ':*') !!}
							{!! Form::select('move_to_folder', $folders->pluck('name', 'id'), null, 
								['class' => 'form-control select2', 'required', 'id' => 'move_to_folder', 
								'style' => 'width: 100%;', 'placeholder' => __('messages.please_select')]); !!}
						</div>
					</div>
					<div class="modal-footer">
						<button type="submit" class="btn btn-primary">@lang( 'spreadsheet::lang.move' )</button>
						<button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
					</div>

				{!! Form::close() !!}

			</div><!-- /.modal-content -->
		</div><!-- /.modal-dialog -->
	</div>
		
</section>
@stop
@section('javascript')
	<script src="https://cdnjs.cloudflare.com/ajax/libs/jstree/3.2.1/jstree.min.js"></script>
	<script type="text/javascript">
	$(document).ready(function(){
    __select2($('.select2'));
  });
		$(document).ready( function(){
			$('#move_to_folder').select2({
				dropdownParent: $('#move_to_folder_modal')
			});
		});
		$(document).on('click', '.add_folder_btn', function(){
			$('#add_folder_modal').modal('show');
		})
        $(document).on('click', '.add_new_btn', function(){
			$('#add_new_modal').modal('show');
		})
		$(document).on('click', '#submit_add_new', function(){
		    if($("#spreadsheet_name").val()  != ""){
		        var url = "{{action([\Modules\Spreadsheet\Http\Controllers\SpreadsheetController::class, 'create'])}}";
			
    			if($("#new_folder_id").val() == ""){
    			   toastr.error("Please select a folder");
    			}else{
    			    url += "?folder_id="+$("#new_folder_id").val()+"&filename="+$("#spreadsheet_name").val(); 
    			    $('#add_new_modal').modal('hide');
    			
        			window.open(url, "_blank");
    			}
    			
    			
			
		    }else{
		        toastr.error("Please enter spreadsheet name");
		    }
			
		})
		$(document).on('click', '.edit_folder', function(){
			$('#folder_id').val($(this).attr('data-id'));
			$('#folder_name').val($(this).attr('data-name'));
			$('#add_folder_modal').modal('show');
		})

		$(document).on('click', '.move_to_another_folder', function(){
			$('#spreadsheet_id').val($(this).attr('data-spreadsheet_id'));
			$('#move_to_folder_modal').modal('show');
		})

		$(document).on('hidden.bs.modal', '#add_folder_modal', function (e) {
			$('#folder_id').val('');
			$('#folder_name').val('');
		});
		$(function () {
			$.jstree.defaults.core.themes.variant = "large";
			$('#spreadsheets_tree').jstree({
				"core" : {
					"themes" : {
						"responsive": true
					}
				},
				"types" : {
					"default" : {
						"icon" : "fa fa-folder"
					},
					"file" : {
						"icon" : "fa fa-file"
					},
				},
				"plugins": ["types", "search"]
			});
			$('#spreadsheets_tree').jstree("open_all");

			var to = false;
			$('#spread_sheet_tree_search').keyup(function () {
				if(to) { clearTimeout(to); }
				to = setTimeout(function () {
				var v = $('#spread_sheet_tree_search').val();
				$('#spreadsheets_tree').jstree(true).search(v);
				}, 250);
			});

			$(document).on('click', '#expand_all', function(e){
				$('#spreadsheets_tree').jstree("open_all");
			})
			$(document).on('click', '#collapse_all', function(e){
				$('#spreadsheets_tree').jstree("close_all");
			})

			$(document).on('click', '.delete-sheet', function (e) {
				e.preventDefault();
			    var url = $(this).data('href');
			    swal({
			      title: LANG.sure,
			      icon: "warning",
			      buttons: true,
			      dangerMode: true,
			    }).then((confirmed) => {
			        if (confirmed) {
			            $.ajax({
			                method:'DELETE',
			                dataType: 'json',
			                url: url,
			                success: function(result){
			                    if (result.success) {
			                        toastr.success(result.msg);
			                        spreadsheets_table.ajax.reload();
			                    } else {
			                        toastr.error(result.msg);
			                    }
			                }
			            });
			        }
			    });
			});
			
			$(document).on('click', '.enable-disable', function (e) {
				e.preventDefault();
			    var url = $(this).data('href');
			    swal({
			      title: LANG.sure,
			      icon: "warning",
			      buttons: true,
			      dangerMode: true,
			    }).then((confirmed) => {
			        if (confirmed) {
			            $.ajax({
			                method:'GET',
			                dataType: 'json',
			                url: url,
			                success: function(result){
			                    if (result.success) {
			                        toastr.success(result.msg);
			                        spreadsheets_table.ajax.reload();
			                    } else {
			                        toastr.error(result.msg);
			                    }
			                }
			            });
			        }
			    });
			});

			$(document).on('click', '.share_excel', function () {
				var url = $(this).data('href');
				$.ajax({
	                method:'GET',
	                dataType: 'html',
	                url: url,
	                success: function(result){
	                    $("#share_excel_modal").html(result).modal("show");
	                }
	            });
			});

			

			$(document).on('click', 'a.add-sheet', function(e) {
				window.location.href = $(this).attr('href');
			});

			$('#share_excel_modal').on('shown.bs.modal', function (e) {
			    
			    //form validation
			    $("form#share_spreadsheet").validate();
			});

			$(document).on('submit', 'form#share_spreadsheet', function(e){
			    e.preventDefault();
			    var url = $('form#share_spreadsheet').attr('action');
			    var method = $('form#share_spreadsheet').attr('method');
			    var data = $('form#share_spreadsheet').serialize();
			    var ladda = Ladda.create(document.querySelector('.ladda-button'));
			    ladda.start();
			    $.ajax({
			        method: method,
			        dataType: "json",
			        url: url,
			        data:data,
			        success: function(result){
			            ladda.stop();
			            if (result.success) {
			                toastr.success(result.msg);
			                $('#share_excel_modal').modal("hide");
			                spreadsheets_table.ajax.reload();
			            } else {
			                toastr.error(result.msg);
			            }
			        }
			    });
			});
		})
		
		
		spreadsheets_table = $('#spreadsheets_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: '{{action("\Modules\Spreadsheet\Http\Controllers\SpreadsheetController@index")}}',
            data: function (d) {
                
            }
        },
        dom: 'Bfrtip',
        @include('layouts.partials.datatable_export_button')
        columns: [
            { data: 'last_opened_on', name: 'last_opened_on' },
            { data: 'created_by', name: 'created_by' },
            { data: 'fname', name: 'fname' },
            { data: 'sname', name: 'sname' },
            { data: 'shared_with', name: 'shared_with' },
            { data: 'status', name: 'status' },
            { data: 'last_updated', name: 'last_updated' },
            { data: 'action', searchable: false, orderable: false },
        ],
        fnDrawCallback: function(oSettings) {
            
        },
});

	</script>
@endsection