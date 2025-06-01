<strong>{{ $member->name }}</strong><br>
<strong><i class="fa fa-map-marker margin-r-5"></i> @lang('business.address')</strong>
<p class="text-muted">
    @if($member->address)
        {{ $member->address }}
    @endif
    @if($member->electrorate)
    {{ ', ' . $member->electrorate->district?->name }}

    @if($member->electrorate->province)
        {{ ', ' . $member->electrorate->province?->name }}
    @endif
    @endif
    <br>
   
</p>
