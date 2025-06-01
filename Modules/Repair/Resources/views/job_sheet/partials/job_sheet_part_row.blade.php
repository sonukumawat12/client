<tr class="product_row">
    <td>
        {{$variation_name}}
    </td>
    <td class="d-flex align-items-center">
    <button type="button" class="btn btn-sm btn-outline-danger qty-decrease">−</button>
    <input type="text" class="form-control input_number quantity_input mx-1 text-center" 
           value="{{@format_quantity($quantity)}}" 
           name="parts[{{$variation_id}}][quantity]" style="width: 70px;">
    <button type="button" class="btn btn-sm btn-outline-secondary qty-increase">+</button>
    <span class="ms-2">{{$unit}}</span>
</td>

    <td>
        <input type="text" class="form-control input_number price_input" 
               value="{{@format_quantity($unit_price)}}" 
               name="parts[{{$variation_id}}][sales]" readonly>
    </td>
    <td>
        <input type="text" class="form-control input_number" 
               value="{{@format_quantity($purchase_price)}}" 
               name="parts[{{$variation_id}}][purchase]" readonly>
    </td>
    <td>
        <input type="text" class="form-control input_number subtotal_input" 
               value="{{@format_quantity($quantity * $unit_price)}}" 
               name="parts[{{$variation_id}}][subtotal]" readonly>
    </td>
    <td class="text-center">
        <i class="fas fa-times remove_product_row cursor-pointer" aria-hidden="true"></i>
    </td>
</tr>
<script>
    function calculateSubtotal(row) {
        let quantity = parseFloat(row.find('.quantity_input').val()) || 0;
        let price = parseFloat(row.find('.price_input').val()) || 0;
        let subtotal = quantity * price;
        row.find('.subtotal_input').val(subtotal.toFixed(2));
    }

    // Handle plus button
    $(document).on('click', '.qty-increase', function () {
        let row = $(this).closest('.product_row');
        let input = row.find('.quantity_input');
        let currentVal = parseFloat(input.val()) || 0;
        input.val((currentVal + 1).toFixed(2));
        calculateSubtotal(row);
    });

    // Handle minus button (min quantity is 1)
    $(document).on('click', '.qty-decrease', function () {
        let row = $(this).closest('.product_row');
        let input = row.find('.quantity_input');
        let currentVal = parseFloat(input.val()) || 0;
        if (currentVal > 1) {
            input.val((currentVal - 1).toFixed(2));
            calculateSubtotal(row);
        }
    });

    // Handle manual input change (prevent values below 1)
    $(document).on('input', '.quantity_input', function () {
        let row = $(this).closest('.product_row');
        let input = $(this);
        let value = parseFloat(input.val()) || 1;
        if (value < 1) {
            input.val('1.00');
        }
        calculateSubtotal(row);
    });
</script>

