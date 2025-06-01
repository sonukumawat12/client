@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | Superadmin Settings')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>{{ __('Overview') }}<small>{{ __('Edit Ad') }}</small>
    </h1>
</section>
<style>
    .wrapper {
        overflow: hidden;
    }
</style>


<!-- Main content -->
<section class="content">
<form action="{{ route('update.ad') }}" method="post" class="card" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="ad_id" value="{{$ad_detail->ad_id}}">
                <div class="card-header">
                    <h4 class="page-title">{{ __('Ad Details') }}</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-xl-10">
                            <div class="row">
                                <div class="col-md-4 col-xl-4">
                                    <div class="mb-3">
                                        <label class="form-label required">{{ __('Date') }}</label>
                                        <input type="date" class="form-control" name="create_date" placeholder="{{ __('Date') }}..." value="{{date('Y-m-d', strtotime($ad_detail->created_at))}}" required readonly>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xl-4">
                                    <div class="mb-3">
                                        <label class="form-label required" for="ad_page_id">{{ __('Ad Page') }}</label>
                                        <select name="ad_page_id" id="ad_page_id" class="form-control" required>
                                            <option value='' disabled selected>{{ __('Ad Page') }}</option>
                                            @foreach ($ad_pages as $ad_page)
                                            <option value="{{ $ad_page->id }}" {{($ad_detail->ad_page_id == $ad_page->id? 'selected':'')}}>
                                                {{ $ad_page->name}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-4 col-xl-4">
                                    <div class="mb-3">
                                        <label class="form-label required" for="ad_page_slot_id">{{ __('Ad Slot') }}</label>
                                        <select name="ad_page_slot_id" id="ad_page_slot_id" class="form-control" required>
                                            <option value='' disabled selected>{{ __('Ad Slot') }}</option>
                                            @foreach ($ad_page_slots as $ad_page_slot)
                                            <option value="{{ $ad_page_slot->id }}" {{($ad_detail->ad_page_slot_id == $ad_page_slot->id? 'selected':'')}}>
                                                {{ $ad_page_slot->slot}}
                                            </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>

                                <div class="col-md-12 col-xl-12">
                                    <div class="mb-3">
                                        <div class="form-label">{{ __('Active') }}</div>
                                        <label class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" name="status" {{ $ad_detail->status == 1 ? 'checked' : '' }}>
                                        </label>
                                    </div>
                                </div>

                                <div class="col-md-6 col-xl-6">

                                    <div class="d-block">
                                        <div class="item card-img-top img-responsive " data-src="{{ $ad_detail->content }}" data-sub-html="<h4>{{__('Image Code')}} : {{ $ad_detail->code }}</h4>" style="background-image: url({{ $ad_detail->content }}); background-size:auto;"></div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-label">{{ __('Image') }} <span class="text-danger" id="recommend-size"></span>
                                        </div>
                                        <input type="file" class="form-control" name="new_content" placeholder="{{ __('Image') }}..." accept=".jpeg,.jpg,.png,.gif,.svg" />
                                    </div>

                                </div>
                                <div class="col-md-4 col-xl-4">
                                </div>
                                <div class="col-md-4 col-xl-4">
                                    <div class="mb-3">
                                        <label class="form-label required">{{ __('Start Date') }}</label>
                                        <input type="date" class="form-control" name="start_date" placeholder="{{ __('Start Date') }}..." value="{{$ad_detail->start_date}}" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xl-4">
                                    <div class="mb-3">
                                        <label class="form-label required">{{ __('End Date') }}</label>
                                        <input type="date" class="form-control" name="end_date" placeholder="{{ __('End Date') }}..." value="{{$ad_detail->end_date}}" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xl-4">
                                </div>
                                <div class="col-md-4 col-xl-4">
                                    <div class="mb-3">
                                        <label class="form-label required">{{ __('Client Name') }} </label>
                                        <input type="text" class="form-control" name="client_name" placeholder="{{ __('Client Name') }}..." value="{{$ad_detail->client_name}}" required>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xl-4">
                                    <div class="mb-3">
                                        <label class="form-label required">{{ __('Amount') }}</label>
                                        <input type="number" class="form-control" name="amount" min="0" required placeholder="{{ __('Amount') }}..." value="{{$ad_detail->amount}}">
                                    </div>
                                </div>
                                <div class="col-md-4 col-xl-4">
                                </div>
                                <div class="col-md-6 col-xl-6">
                                    <div class="mb-3">
                                        <label class="form-label">{{ __('Link') }}</label>
                                        <input type="url" class="form-control" name="link" min="0" placeholder="{{ __('Link') }}..." value="{{$ad_detail->link}}">
                                    </div>
                                </div>
                                <div class="col-md-4 col-xl-4">
                                </div>
                                <div class="col-md-4 col-xl-4 my-3">
                                    <div class="mb-3">
                                        <button type="submit" class="btn btn-primary">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-edit" width="24" height="24" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none" stroke-linecap="round" stroke-linejoin="round">
                                                <path stroke="none" d="M0 0h24v24H0z" fill="none"></path>
                                                <path d="M9 7h-3a2 2 0 0 0 -2 2v9a2 2 0 0 0 2 2h9a2 2 0 0 0 2 -2v-3">
                                                </path>
                                                <path d="M9 15h3l8.5 -8.5a1.5 1.5 0 0 0 -3 -3l-8.5 8.5v3">
                                                </path>
                                                <line x1="16" y1="5" x2="19" y2="8"></line>
                                            </svg>
                                            {{ __('Update') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
</section>
@stop
@section('scripts')
<script type="text/javascript">
    function getRecommendSize() {
        var ad_page_slot_id = $("#ad_page_slot_id").val();

        $.ajax({
            type: "POST",
            url: "{{ route('ad.get-ad-slots-data') }}",
            dataType: "json",
            data: {
                ad_page_slot_id: ad_page_slot_id,
                "_token": "{{ csrf_token() }}"
            },
            success: function(result) {

                if (result.success == 1 && result.data != null) {
                    $('#recommend-size').text('({{ __('
                        Recommended ')}}: ' + result.data.width + ' x ' + result.data.height + ')');
                }
            }
        });
    }
    $(document).ready(function() {

        getRecommendSize();

        $('#captions').lightGallery({
            thumbnail: true,
            download: false,
            selector: '.item'
        });


        $("#ad_page_slot_id").change(function() {
            getRecommendSize();
        });

        $("#ad_page_id").change(function() {
            var ad_page_id = $(this).val();
            $.ajax({
                type: "POST",
                url: "{{ route('ad.get-ad-slots-data') }}",
                dataType: "json",
                data: {
                    ad_page_id: ad_page_id,
                    "_token": "{{ csrf_token() }}"
                },
                success: function(result) {

                    if (result.success == 1) {
                        $('#recommend-size').text('');
                        $('#ad_page_slot_id').empty();
                        $('#ad_page_slot_id').append(`
                            <option value=""></option>
                        `);
                        result.data.map(function(v, i) {
                            $('#ad_page_slot_id').append(`
                            <option value="${v.id}">${v.slot}</option>
                        `);
                        });
                    }
                }
            });
        });

    });
</script>
@endsection