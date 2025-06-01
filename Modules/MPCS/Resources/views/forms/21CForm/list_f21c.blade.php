<style>
.full-width-input {
    width: 100% !important; 
    box-sizing: border-box; 
    display: block; 
    padding: 5px; 
    margin: 0;
    border: 1px solid #fff;
    height: 100%; 
}
</style>
<!-- Main content -->
<section class="content" style="padding: 10px;">  
 
<div class="box-tools pull-right">
    @if ($latestForm)
        <button type="button" 
            class="btn btn-pink btn-modal mr-2" 
            style="background-color: rgb(124, 30, 114); border-color: rgb(124, 30, 114); color: white;" 
            data-href="{{ action('\Modules\MPCS\Http\Controllers\F21FormController@edit21cFormSetting', ['id' => $latestForm->id]) }}" 
            data-container=".update_form_16_a_settings_modal" 
            @if(!$settings) disabled @endif>
            <i class="fa fa-pencil"></i> @lang('mpcs::lang.edit')
        </button>
    @endif

    <button type="button" 
        class="btn btn-primary btn-modal" 
        data-href="{{ action('\Modules\MPCS\Http\Controllers\F21FormController@get21CFormSettings') }}" 
        data-container=".form_16_a_settings_modal" 
        @if($settings) enabled @endif>
        <i class="fa fa-plus"></i> Add Form 21 C Settings
    </button>
</div>
   
<div class="row">
                    <div class="col-md-3 text-red">
                        <h5 style="font-weight: bold;" class="text-red">Starting Form No: {{ $latestForm->starting_number ?? '' }}</h5>                        
                    </div>
                     
                    <div class="col-md-3">
                        <div class="text-center">
                        @if ($latestForm)
                            <h5 style="font-weight: bold;">Opening Date :{{ $latestForm->date }} </h5>
                            @else
                            <h5 style="font-weight: bold;">Opening Date : </h5>
                            @endif    
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="text-center pull-left">
                        @if ($latestForm)
                            <h5 style="font-weight: bold;" class="text-red">Time: {{ $latestForm->time }}</h5>
                            @else
                            <h5 style="font-weight: bold;" class="text-red">Time:</h5>
                        @endif
                        </div>
                    </div>
                </div>

     <div class="row">
       
            @component('components.widget', ['class' => 'box-primary'])
            <div class="col-md-12">
                <div class="box-body" style="margin-top: 20px;">
                    <div class="row">
                        <div class="col-md-12">
                            
                            <div id="msg"></div>
                            @php
    $columnsArray = [
        'receipts' => 'Receipts Section',
        'previous_day' => 'Previous Day Amount',
        'opening_stock' => 'Opening Stock Amount',
        'issue' => 'Issue Section',
        'total_issues' => 'Total Issues Amount'
    ];
@endphp

<table class="table table-bordered" id="form_21c_settings">
    <thead>
        <tr>
            <th class="no-wrap align-middle" rowspan="2">@lang('mpcs::lang.product')</th>
            @foreach ($fuelCategory as $categoryName)
                <th colspan="2" class="text-center">{{ $categoryName }}</th>
            @endforeach
        </tr>
        <tr>
            @foreach ($fuelCategory as $categoryId => $categoryName)
                <th class="text-center">Qty</th>
                <th class="text-center">Val</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($columnsArray as $colKey => $column)
            <tr>
                @php
                    $highlight = in_array($colKey, ['receipts', 'issue']) ? 'color: skyblue; font-weight: bold;' : '';
                @endphp
                <td style="{{ $highlight }} white-space: nowrap; width: auto; max-width: none;">{{ $column }}</td>

                @if (in_array($colKey, ['receipts', 'issue']))
                    <td colspan="{{ count($fuelCategory) * 2 }}"></td>
                @else
                    @foreach ($fuelCategory as $categoryId => $categoryName)
                        @php
                            $qty = $categoriesData[$categoryId][$colKey]['qty'] ?? '';
                            $val = $categoriesData[$categoryId][$colKey]['val'] ?? '';
                        @endphp
                        <td>
                            <input type="number"
                                   step="0.01"
                                   name="{{ $colKey }}[{{ $categoryId }}][qty]"
                                   
                                   value="{{ $qty }}"
                                   readonly  class="full-width-input">
                        </td>
                        <td>
                            <input type="number"
                                   step="0.01"
                                   name="{{ $colKey }}[{{ $categoryId }}][val]"
                                   
                                   value="{{ $val }}"
                                   readonly class="full-width-input" >
                        </td>
                    @endforeach
                @endif
            </tr>
        @endforeach
    </tbody>
</table>

    
                </div>
            </div>
        </div>

    </div>
            @endcomponent
         
</div>

  
    <div class="modal fade form_16_a_settings_modal"   tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>
    <div class="modal fade update_form_16_a_settings_modal"   tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel"></div>


</section>
<!-- /.content -->

 
