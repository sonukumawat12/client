 
   <div class="modal-dialog modal-lg" role="document" style="width: 30% !important; max-width: 30% !important;">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="exampleModalLabel">Transaction Note</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                
                
			<div class="row">
				
				
				<div class="col-sm-8">
                    <div class="form-group">
                        {!! Form::label('note', __('Note') . ':') !!}
                        {!! Form::textarea('note', $note, [
                            'class' => 'form-control', 
                            'rows' => 3, 
                            'readonly' => 'readonly',
                            'placeholder' => __('cheque.note_placeholder')
                        ]) !!}
                    </div>

				</div>

			

				<div class="clearfix"></div>
				
        
				

			</div>
                <div class="row">
                    <button type="button" style="margin-right: 5px;" class="pull-right btn btn-secondary"
                        data-dismiss="modal">Close</button>
                     
                </div>
               
                {!! Form::close() !!}
            </div>
        </div>
    </div>
 