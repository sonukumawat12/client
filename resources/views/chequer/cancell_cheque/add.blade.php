 
   <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel">@lang('cheque.create_cancel_cheque')</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                {!! Form::open(['url' => action('Chequer\CancellChequeController@store'), 'method' => 'post', 'enctype'
                => 'multipart/form-data']) !!}
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
						{!! Form::select('account_book_number',$cheque_books ?? [],$cancelCheque->cheque_bk_id ?? null, ['class' =>
						'form-control select2', 'placeholder' => __('messages.please_select'), 'required','id'=>'account_book_number']); !!}
					</div>
				</div>
			</div>	
			<div class="row">	
				<div class="col-sm-4">
					<div class="form-group">
						{!! Form::label('cheque_numbers', __('cheque.cheque_number').':') !!}
						{!! Form::select('cheque_numbers',$cheque_number ?? [],$cancelCheque->cheque_no ?? null, ['class' =>
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
                <div class="row">
                    <button type="button" style="margin-right: 5px;" class="pull-right btn btn-secondary"
                        data-dismiss="modal">Close</button>
                    <button type="submit" style="margin-right: 5px;"
                        class="pull-right btn btn-primary">@lang('cheque.save')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
 