@extends('translation::layout')

@section('body')
<form action="{{ route('languages.translations.index', ['language' => $language]) }}" method="get">
    <div class="card">
        <div class="card-body p-0">

            <div class="row py-3 px-4">
                <div class="col">
                    {{ __("Search") }}
                    @include('translation::forms.search', ['name' => 'filter', 'value' => Request::get('filter')])
                </div>
                <div class="col">
                    {{ __("Language") }}
                    @include('translation::forms.select', ['name' => 'language', 'items' => $languages, 'submit' =>
                    true, 'selected' => $language])
                </div> 
                <div class="col">
                    {{ __("Group") }}
                    @include('translation::forms.select', ['name' => 'group', 'items' => $groups, 'submit' => true,
                        'selected' => Request::get('group'), 'optional' => true])
                </div>
                <div class="col">
                    <p></p>
                    <a href="{{ route('languages.translations.create', $language) }}" class="btn btn-outline-secondary">
                        {{ __('translation::translation.add') }}
                    </a>
                </div>
            </div>

            @if(count($translations))
            <div class="table-responsive">
            <table class="table table-bordred">

                <thead>
                    <tr>
                        <th width="10%">{{ __('translation::translation.group_single') }}</th>
                        <th width="10%">{{ __('translation::translation.key') }}</th>
                        <th width="30%">{{ config('app.locale') }}</th>
                        <th width="60%">{{ $language }}</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach($translations as $type => $items)

                    @foreach($items as $group => $translations)

                    @foreach($translations as $key => $value)

                    @if(!is_array($value[config('app.locale')]))
                    <tr>
                        <td>{{ $group }}</td>
                        <td>{{ $key }}</td>
                        <td>{{ $value[config('app.locale')] }}</td>
                        <td>
                            <appm-translation-input initial-translation="{{ $value[$language] }}" language="{{ $language }}"
                                group="{{ $group }}" translation-key="{{ $key }}"
                                route="{{ config('translation.ui_url') }}">
                            </appm-translation-input>
                        </td>
                    </tr>
                    @endif

                    @endforeach

                    @endforeach
                    @endforeach
                </tbody>
            </table>
            </div>
            @endif
        </div>
    </div>
</form>
@endsection