@foreach($pumps as $key => $pump)
    <div class="col-md-6">
        <h5><b>{{$pump}}</b></h5>
    </div>
    <div class="col-md-6">
        {!! Form::checkbox('pumps[]', $key, null , ['required']); !!}
    </div>
    <div class="clearfix"></div>
@endforeach