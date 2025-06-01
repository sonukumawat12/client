<div class="row">
    <div class="form-group col-sm-4">
          {!! Form::label('agent_no', __( 'shipping::lang.shipping_agents' ) .":") !!}
          {!! Form::select('agent_no', $shipping_agents, isset($data) ? $data[0]->agent_id : null, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select'),isset($view_page) ? "disabled" : '']); !!}
    </div>
    
</div>