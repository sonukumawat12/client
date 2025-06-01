@extends('layouts.app')

@section('title', __('Room Details'))

@section('content')
<section class="content-header">
    <h1>@lang('Room Details') - {{ $room->room_number }}</h1>
</section>

<section class="content">
    <div class="box box-solid">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <h4>@lang('Basic Information')</h4>
                    <table class="table table-bordered">
                        <tr>
                            <th width="30%">@lang('Room Number')</th>
                            <td>{{ $room->room_number }}</td>
                        </tr>
                        <tr>
                            <th>@lang('Room Type')</th>
                            <td>{{ $room->type->type ?? 'N/A' }}</td>
                        </tr>
                        <tr>
                            <th>@lang('Status')</th>
                            <td>
                                <span class="label label-{{ $room->check_in_status ? 'success' : 'warning' }}">
                                    {{ $room->check_in_status ? __('Checked In') : __('Available') }}
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h4>@lang('Room Images')</h4>
                  
                </div>
            </div>

            <div class="text-center mt-4">
              
            </div>
        </div>
    </div>
</section>
@endsection