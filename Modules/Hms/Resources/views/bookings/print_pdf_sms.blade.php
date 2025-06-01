Booking Confirmation

*{{ $business->name }}*

Booking ID: {{ $transaction->ref_no }}
Arrival: {{ $transaction->formatted_arrival }}
Departure: {{ $transaction->formatted_departure }}
Status: {{ ucfirst($transaction->status) }}

Customer Info
Name: {{ $transaction->contact->name }}
Phone: {{ $transaction->contact->mobile }}
Address:
@php
    $addressLines = [];

    if (!empty($transaction->contact->landmark)) {
        $addressLines[] = $transaction->contact->landmark;
    }
    if (!empty($transaction->contact->city)) {
        $addressLines[] = $transaction->contact->city;
    }
    if (!empty($transaction->contact->state)) {
        $addressLines[] = $transaction->contact->state;
    }
    if (!empty($transaction->contact->country)) {
        $addressLines[] = $transaction->contact->country;
    }

    echo implode(', ', $addressLines);
@endphp


Rooms Booked:
@foreach ($booking_rooms as $room)
  Type: {{ $room->type }}
  Room No: {{ $room->room_number }}
  Adults: {{ $room->adults }}
  Children: {{ $room->childrens }}
  Price: ₨ {{ number_format($room->total_price, 2) }}
@endforeach

Extras:
@foreach ($extras as $extra)
@if (in_array($extra->id, $extras_id))
- {{ $extra->name }} (₨ {{ number_format($extra->price, 2) }}/{{ str_replace('_', ' ', $extra->price_per) }})
@endif
@endforeach

Summary:
Room Price: ₨ {{ number_format($transaction->room_price, 2) }}
Extras Price: ₨ {{ number_format($transaction->extra_price, 2) }}

Discount:
@if (empty($transaction->hms_coupon_id) && $transaction->discount_amount > 0)
{{ number_format($transaction->discount_amount, 2) }}% (₨ {{ number_format($transaction->discount_amount * ($transaction->room_price + $transaction->extra_price) / 100, 2) }})
@else
₨ {{ number_format($transaction->discount_amount, 2) }}
@endif

Total: ₨ {{ number_format($transaction->final_total, 2) }}
Paid: ₨ {{ number_format($transaction->total_paid, 2) }}
Due: ₨ {{ number_format($transaction->final_total - $transaction->total_paid, 2) }}

Note:
@if (!empty($business->hms_settings->booking_pdf->footer_text))
{{ strip_tags($business->hms_settings->booking_pdf->footer_text) }}
@endif

Thank you for choosing {{ $business->name }}!
