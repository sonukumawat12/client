<?php

namespace Modules\Fleet\Entities;

use Illuminate\Database\Eloquent\Model;
use App\OpeningBalance;

class FleetContactLedger extends Model
{
    protected $fillable = [];

    protected $guarded  = ['id'];
    
    
    public static function createContactLedger($data)
    {
        // dd($data);
        $ledger_data = [
            'contact_id' => $data['contact_id'],
            'amount' => $data['amount'],
            'type' => $data['type'],
            'sub_type' => !empty($data['sub_type']) ? $data['sub_type'] : null,
            'operation_date' => !empty($data['operation_date']) ? $data['operation_date'] : \Carbon::now(),
            'created_by' => $data['created_by'],
            'transaction_id' => !empty($data['transaction_id']) ? $data['transaction_id'] : null,
            'transaction_payment_id' => !empty($data['transaction_payment_id']) ? $data['transaction_payment_id'] : null,
            'note' => !empty($data['note']) ? $data['note'] : null,
            'transaction_sell_line_id' => !empty($data['transaction_sell_line_id']) ? $data['transaction_sell_line_id'] : null,
            'income_type' => !empty($data['income_type']) ? $data['income_type'] : null,
            'installment_id' => !empty($data['installment_id']) ? $data['installment_id'] : null,
        ];
        $contact_ledger = FleetContactLedger::create($ledger_data);

        return $contact_ledger;
    }
}
