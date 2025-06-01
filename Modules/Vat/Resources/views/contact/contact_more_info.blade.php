<strong><i class="fa fa-mobile margin-r-5"></i> @lang('contact.mobile')</strong>
<p class="text-muted">
    {{ $contact->mobile }}
</p>

@if($contact->tax_number)
    <strong> @lang('contact.tax_no')</strong>
    <p class="text-muted">
        {{ $contact->tax_number }}
    </p>
@endif

@if($contact->alternate_number)
    <strong><i class="fa fa-phone margin-r-5"></i> @lang('contact.alternate_contact_number')</strong>
    <p class="text-muted">
        {{ $contact->alternate_number }}
    </p>
@endif
