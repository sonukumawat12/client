<div class="box box-widget @if (isset($member) && $member->members->count() > 0) @else collapsed-box @endif">
    <div class="box-header with-border w-100">
        <span>Family Members</span>
        <div class="box-tools pull-right">
        @if (isset($member) && $member->members->count() > 0)
        @php
        $username =  explode('-',$member->members->last()->username) ;
        $last_row_number = last($username);
      @endphp
        {!! Form::hidden('add_on_member',$last_row_number, ['id'=>'add_on_member']) !!}
        <button type="button" class="btn btn-box-tool" data-widget="collapse"><i class="fa fa-minus"></i></button>
      
        @else 
        {!! Form::hidden('add_on_member',0, ['id'=>'add_on_member']) !!}
        <button type="button" class="btn btn-box-tool v-add-on" data-widget="collapse"><i class="fa fa-plus"></i></button>
        @endif
      </div>
    </div>
    <div class="box-body" @if (isset($member) && $member->members->count() > 0) @else  style="display: none"  @endif id="family_member_box">

      @if (isset($member) && $member->members->count() > 0)
      @foreach ($member->members as $family_member)
      @php
        $username =  explode('-',$family_member->username) ;
        $row_number = last($username);
      @endphp
      @include('member::member.partials.family_box')
      @endforeach
      @endif
      
      
    </div>
    <!-- /.box-body -->
</div>
