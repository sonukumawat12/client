
@foreach($sale_products as $one)
@php
    $price = (100 - $contact_group->amount) * $one->price /100;
@endphp
<tr>
    <td>
        <input type="hidden" name="sale_id" value="{{$one->sale_id}}">
        <input type="hidden" name="account_payable" value="{{$contact_group->interest_account_id}}">
        {{$one->product_name}}
        <input type="hidden" name="products[]" value="{{$one->id}}">
    </td>
    
    <td>
        {{$one->unit_name}}
    </td>
    
    <td>
        <input type="number" name="quantity[]" step="0.01" required class="form-control qty">
    </td>
    
    <td>
        <input type="number" name="price[]" step="0.01" value="{{$price}}" readonly required class="form-control unit_price">
    </td>
    
    <td class="subtotal">
        {{@num_format(0)}}
    </td>
</tr>
@endforeach