<!-- app css -->

@if(!empty($for_pdf))
<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">
@endif
<style>
	.bg_color {
		background: #357ca5;
		font-size: 20px;
		color: #fff;
	}

	.bg-aqua{
	    background: #8F3A84;
	}

	.text-center {
		text-align: center;
	}

	#ledger_table th {
		background: #357ca5;
		color: #fff;
	}

	#ledger_table>tbody>tr:nth-child(2n+1)>td,
	#ledger_table>tbody>tr:nth-child(2n+1)>th {
		background-color: rgba(89, 129, 255, 0.3);
	}
</style>

@php
    $currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
    
@endphp
<div class="col-md-6 col-sm-6 col-xs-6 @if(!empty($for_pdf)) width-50 f-left @endif">
	
</div>
<div class="col-md-6 col-sm-6 col-xs-6 text-right align-right @if(!empty($for_pdf)) width-50 f-left @endif">
	<p class=" bg_color" style="margin-top: @if(!empty($for_pdf)) 20px @else 0px @endif; font-weight: 500;">
		@lang('sms::lang.sms_summary')</p>
	<table class="table table-condensed text-left align-left no-border @if(!empty($for_pdf)) table-pdf @endif">
		<tr>
			<td>@lang('sms::lang.beginning_balance')</td>
			<td>{{ @num_format($bf_bal) }}</td>
		</tr>

		<tr>
			<td>@lang('sms::lang.total_refilled')</td>
			<td>
			    <span id="total_refilled"></span>
			</td>
		</tr>
		
		<tr>
			<td>@lang('sms::lang.sms_cost')</td>
			<td>
			    <span id="sms_cost"></span>
			</td>
		</tr>
		
		<tr>
			<td>@lang('sms::lang.interest_charged')</td>
			<td>
			    <span id="interest_charged"></span>
			</td>
		</tr>
	
		
		<tr>
			<td><strong>@lang('sms::lang.balance_due')</strong></td>
			<td><span id="balance_due">0.00</span></td>
		</tr>
	</table>
</div>
<div class="col-md-12 col-sm-12 @if(!empty($for_pdf)) width-100 @endif">
    
	<table class="table table-striped @if(!empty($for_pdf)) table-pdf td-border @endif" id="ledger_table">
		<thead>
			<tr class="row-border">
				<th>@lang('sms::lang.date')</th>
				<th>@lang('sms::lang.description')</th>
				<th>@lang('sms::lang.total_cost')</th>
				<th>@lang('sms::lang.refill_amount')</th>
				<th>@lang('sms::lang.balance')</th>
				<th>@lang('sms::lang.payment_method')</th>
			</tr>
		</thead>
		<tbody>
			@php
			 $balance = $bf_bal;
			 $total_refilled = 0;
             $sms_cost = 0;
             $interest_charged = 0;
			@endphp
			<tr>
    			<td class="row-border">
    			    
        		</td>
    			<td class="row-border">
    			    @lang('sms::lang.beginning_balance')
    			</td>
    			<td></td><td></td>
    			<td class="row-border">
    				{{ @num_format($bf_bal) }}
    			</td>
    			<td ></td>
        	</tr> 
        	
			@php  @endphp
			@foreach($ledger_details as $one)
			
			    @php
			        $note = "";
			        $sms = "";
			        
			        if($one->type == 'refill'){
			            $balance += $one->amount;
			        }else{
			            $balance -= $one->amount;
			        }
			    @endphp
			    
			    
			    @if($one->type == 'refill')
	                @php
	                    $refill_business =  \Modules\Superadmin\Entities\RefillBusiness::findOrFail($one->id);
	                    $note = $refill_business->note;
	                    $total_refilled += $one->amount;
	                @endphp
		            
		        @endif
		        
		        @if($one->type == 'interest')
	                @php
	                    $interest =  \Modules\SMS\Entities\SmsListInterest::findOrFail($one->id);
	                    $note = $interest->note;
	                    $interest_charged  += $one->amount;
	                @endphp
		        @endif
		        
		        @if($one->type == 'sms_sent')
	                @php
	                    $sms_sent =  \App\SmsLog::findOrFail($one->id);
	                    $sms = $sms_sent->message;
	                    $sms_cost += $one->amount;
	                @endphp
		        @endif
        			        
			    
    			     <tr>
    			        <td>
    			            {{@format_date($one->date)}}
    			        </td>
    			        
    			        
    			        <td>
    			            <b>{{ ucwords(str_replace('_',' ',$one->type)) }}</b><br>
    			            
    			            @if($one->type == 'sms_sent' && !empty($sms_sent))
            	                
            	                <b>No of Characters: </b> {{ $sms_sent->no_of_characters }}<br>
            	                <b>SMS Type: </b> {{ ucwords(str_replace('_',' ',$sms_sent->sms_type)) }}<br>
            	                <b>Recipients: </b> {{ $sms_sent->recipient }}<br>
            	                <b>No of SMS: </b> {{ $sms_sent->no_of_sms }}<br>
            	                <b>Unit Cost: </b> {{ @num_format($sms_sent->unit_cost) }}<br>
            	                
            		        @endif
    			            
    			            @if(!empty($note))
    			                <button class='btn btn-info note_btn btn-sm' data-string='{{$note}}'>Note</button>
    			            @endif
    			            
    			            @if(!empty($sms))
    			                <button class='btn btn-primary note_btn btn-sm' data-string='{{$sms}}'>Message Sent</button>
    			            @endif
    			        </td>
    			        
    			        
    			        <td>
    			           @if($one->type != 'refill')
        			            {{ @num_format($one->amount) }}
        			       @endif
    			        </td>
    			        
    			        <td>
    			            @if($one->type == 'refill')
    			                {{ @num_format($one->amount) }}
        			        @endif
    			        </td>
    			        
    			        <td>
    			            {{ @num_format($balance) }}
    			        </td>
    			        
    			        <td>
    			            
    			             @if($one->type == 'refill' && !empty($refill_business))
        			            <b>{{$refill_business->payment_method}}</b>
        			            @if($refill_business->payment_method == 'Cheque')
                                    <b>Bank Name</b>: {{$refill_business->bank_name}}<br><b>Cheque No</b>: {{$refill_business->cheque_no }}<br><b>Cheque Date</b>: {{@format_date($refill_business->cheque_date)}}
                                
                                @endif
                            @endif
    			            
    			        </td>
    			        
    			     </tr>
			
			
			   
			@endforeach 

		</tbody>
	</table>
</div>

    @php
        $reports_footer = \App\System::where('key','admin_reports_footer')->first();
    @endphp
    
    @if(!empty($reports_footer))
        <style>
            #footer {
                display: none;
            }
        
            @media print {
                #footer {
                    display: block !important;
                    position: fixed;
                    bottom: -1mm;
                    width: 100%;
                    text-align: center;
                    font-size: 12px;
                    color: #333;
                }
            }
        </style>

        <div id="footer">
            {{ ($reports_footer->value) }}
        </div>
    @endif



<!-- This will be printed -->
<section class="invoice print_section" id="ledger_print">
</section>

 <div class="modal fade" id="noteModal" role="dialog" 
      aria-labelledby="gridSystemModalLabel">
        <div class="modal-dialog">
          <div class="modal-content">
    
            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title">@lang( 'lang_v1.note' )</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
    
            <!-- Modal Body -->
            <div class="modal-body">
              <p id="noteContent" class="text-center"></p>
            </div>
    
          </div>
        </div>
      </div>

<script>

$(document).on('click', '.note_btn', function(e){
  let note = $(this).data('string');
  // Replace newline characters with <br>
  note = note.replace(/\n/g, '<br>');
  $("#noteContent").html(note);
  $("#noteModal").modal('show');
});  

$("#total_refilled").text('{{@num_format($total_refilled)}}');
$("#sms_cost").text('{{@num_format($sms_cost)}}');
$("#interest_charged").text('{{@num_format($interest_charged)}}');
$("#balance_due").text('{{@num_format($balance)}}');
    
</script>
