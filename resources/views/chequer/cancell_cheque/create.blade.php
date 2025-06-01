@extends('layouts.app')
@section('title', __('expense.cancel_cheque_menu'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>@lang('cheque.create_cancel_cheque')</h1>
</section>

<!-- Main content -->
<section class="content">
	@isset($cancelCheque)
	{!! Form::open(['route' => ['cancell_cheque_details.update',$cancelCheque->id], 'method' => 'post', 'id' => 'add_cheque_form' ]) !!}
	@method('put')
	@else	
	{!! Form::open(['route' => 'cancell_cheque_details.store', 'method' => 'post', 'id' => 'add_cheque_form' ]) !!}
	@endisset
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('date', __('messages.date') . ':*') !!}
						<div class="input-group">
							<span class="input-group-addon">
								<i class="fa fa-calendar"></i>
							</span>
							{!! Form::text('date',
							@format_datetime('now'),
							['class' => 'form-control', 'readonly', 'required', 'id' => 'date','readonly']);
							!!}
						</div>
					</div>
				</div>
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('bank_account', __('cheque.banck_account').':*') !!}
						{!! Form::select('bank_account', $bankAccounts ?? [],$cancelCheque->account_id ?? null, ['class' =>
						'form-control select2', 'placeholder' => __('messages.please_select'), 'required','id'=>'bank_account']); !!}
					</div>
				</div>

				
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('account_book_number', __('cheque.account_book_number').':') !!}
						{!! Form::select('account_book_number',$chequeNumbers ?? [],$cancelCheque->cheque_bk_id ?? null, ['class' =>
						'form-control select2', 'placeholder' => __('messages.please_select'), 'required','id'=>'account_book_number']); !!}
					</div>
				</div>
			</div>	
			<div class="row">	
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('cheque_numbers', __('cheque.cheque_number').':') !!}
						{!! Form::select('cheque_numbers',$chequeNumbers ?? [],$cancelCheque->cheque_no ?? null, ['class' =>
						'form-control select2', 'placeholder' => __('messages.please_select'), 'required','id'=>'cheque_numbers']); !!}
					</div>
				</div>
				
				
				
				<div class="col-sm-4">
                    <div class="form-group">
                        {!! Form::label('note', __('cheque.note') . ':') !!}
                        {!! Form::textarea('note', $cancelCheque->note ?? null, [
                            'class' => 'form-control', 
                            'rows' => 3, 
                            'placeholder' => __('cheque.note_placeholder')
                        ]) !!}
                    </div>

				</div>

				<div class="col-sm-4">
					<div class="form-group">
					{!! Form::label('user_name', __('cheque.user_name') . ':') !!}
						{!! Form::text('user_name',auth()->user()->username, ['class' =>
						'form-control', 'readonly']); !!}
					</div>
				</div>

				<div class="clearfix"></div>
				
        
				

			</div>
		</div>
	</div>
	
	<!--box end-->
	<div class="col-sm-12">
		<button id="submitBtn" type="submit" class="btn btn-success pull-right m-8">@lang('messages.save')</button>
		<button id="cancelBtn" type="button" class="btn btn-primary pull-right m-8">@lang('messages.cancel')</button> <!-- @eng 15/2 -->
		
	</div>
	{!! Form::close() !!}

</section>
@endsection

@section('javascript')
<script>
	


	
	
	$(document).ready(function() {
	
	$('#bank_account').change(function() {
		$.ajax({
			method: 'get',
			url: '/get-account-book-number/' + $(this).val(),
			data: {},
			success: function(result) {
			    console.log('AJAX Response:', result); // Log the response
				$('#account_book_number').empty();
				$(document).find('#account_book_number').append(
					`<option value="" selected>{{__('messages.please_select')}}</option>`
					);
				$.each(result.data, function (index, value) { 
					$(document).find('#account_book_number').append(
					`<option value="${index}">${value}</option>`
					);
				});
				

			},
		});
	})
	
	$('#account_book_number').change(function() {
		$.ajax({
			method: 'get',
			url: '/get-account-book-cheques/' + $(this).val(),
			data: {},
			success: function(result) {
			    console.log('AJAX Response:', result); // Log the response
				$('#cheque_numbers').empty();
				$(document).find('#cheque_numbers').append(
					`<option value="" selected>{{__('messages.please_select')}}</option>`
					);
				$.each(result.data, function (index, value) { 
					$(document).find('#cheque_numbers').append(
					`<option value="${index}">${value}</option>`
					);
				});
				

			},
		});
	})


});
	
</script>
@endsection