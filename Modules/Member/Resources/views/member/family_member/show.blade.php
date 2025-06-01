<div class="modal-dialog" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
            aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"> {{ $member->name }}</h4>
        </div>
  
      <div class="modal-body">
        <div class="row">
        <div class="col-lg-12">
            <div class="card mb-4">
                <div class="card-body">
                <div class="row">
                    <div class="col-sm-4">
                    <p class="mb-0">@lang('business.name')</p>
                    </div>
                    <div class="col-sm-8">
                    <p class="text-muted mb-0">{{ $member->name}}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-4">
                    <p class="mb-0">@lang('member::lang.relation')</p>
                    </div>
                    <div class="col-sm-8">
                    <p class="text-muted mb-0">{{ ucfirst($member->relation_name) }}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-4">
                    <p class="mb-0">@lang('business.mobile_number_1')</p>
                    </div>
                    <div class="col-sm-8">
                    <p class="text-muted mb-0">{{ $member->mobile_number_1?displayPhoneFormat($member->mobile_number_1):''}}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-4">
                    <p class="mb-0">@lang('business.mobile_number_2')</p>
                    </div>
                    <div class="col-sm-8">
                    <p class="text-muted mb-0">{{ $member->mobile_number_2?displayPhoneFormat($member->mobile_number_2):''}}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-4">
                    <p class="mb-0">@lang('business.mobile_number_3')</p>
                    </div>
                    <div class="col-sm-8">
                    <p class="text-muted mb-0">{{ $member->mobile_number_3 ?displayPhoneFormat($member->mobile_number_3) :''}}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-4">
                    <p class="mb-0">@lang('business.date_of_birth')</p>
                    </div>
                    <div class="col-sm-8">
                    <p class="text-muted mb-0">{{ $member->date_of_birth ? date('d/m/Y',strtotime($member->date_of_birth)): ''}}</p>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-sm-4">
                    <p class="mb-0">@lang('business.gender')</p>
                    </div>
                    <div class="col-sm-8">
                    <p class="text-muted mb-0">{{ ucfirst($member->gender) }}</p>
                    </div>
                </div>
                </div>
            </div>
            </div>
        </div>
      </div>
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->