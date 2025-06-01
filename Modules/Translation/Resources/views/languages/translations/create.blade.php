@extends('translation::layout')

@section('body')
<div class="card">
    <div class="card-header">{{ __('translation::translation.add_translation') }}</div>
    <form action="{{ route('languages.translations.store', $language) }}" method="POST">
        <fieldset>
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <div class="card-body p-4">
                @include('translation::forms.text', ['field' => 'group', 'label' =>
                __('translation::translation.group_label'), 'placeholder' =>
                __('translation::translation.group_placeholder')])

                @include('translation::forms.text', ['field' => 'key', 'required' => true, 'label' =>
                __('translation::translation.key_label'), 'placeholder' =>
                __('translation::translation.key_placeholder')])

                @include('translation::forms.text', ['field' => 'value', 'required' => true, 'label' =>
                __('translation::translation.value_label'), 'placeholder' =>
                __('translation::translation.value_placeholder')])
                <div>
                    @include('translation::forms.text', ['field' => 'namespace', 'label' =>
                    __('translation::translation.namespace_label'), 'placeholder' =>
                    __('translation::translation.namespace_placeholder')])
                </div>
            </div>
        </fieldset>
        <div class="card-footer flex flex-row-reverse">
            <button class="btn btn-primary">
                {{ __('translation::translation.save') }}
            </button>
        </div>
    </form>
</div>
@endsection