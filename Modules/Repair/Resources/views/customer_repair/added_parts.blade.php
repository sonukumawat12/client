<!-- /.box-header -->
<div class="box-body">
    <div class="table-responsive">
        <table class="table bg-gray">
            <tr>
                <th>Part</th>
                <th>Quantity</th>
            </tr>
            @forelse($parts as $one)
                
                    <tr>
                        <td>{{ $one['name'] }}</td>
                        <td>{{ $one['quantity'] }}</td>
                    </tr>
                
            @empty
                <tr>
                  <td colspan="2" class="text-center">
                    @lang('purchase.no_records_found')
                  </td>
                </tr>
            @endforelse
        </table>
    </div>
</div>
<!-- /.box-body -->