<?php

namespace App;

use App\Utils\TransactionUtil;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class AccountTransaction extends Model
{
    use SoftDeletes, LogsActivity;

    protected static $logAttributes = ['*'];
    protected static $logFillable = true;
    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fillable', 'some_other_attribute']);
    }

    protected $dates = [
        'operation_date',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class, 'transaction_id')->withTrashed();
    }

    /**
     * Get account transaction type from payment transaction type.
     *
     * @param  string $transaction_type
     * @return string
     */
    public static function getAccountTransactionType($transaction_type)
    {
        $account_transaction_types = [
            'sell' => 'credit',
            'purchase' => 'credit', // changed from debit to credit
            'expense' => 'debit',
            'purchase_return' => 'credit',
            'sell_return' => 'debit',
            'property_purchase' => 'credit',
            'property_sell' => 'credit',
            'route_operation' => 'credit',
            'shipment' => 'credit',
            'airline_ticket' => 'credit',
            'hms_booking' => 'credit', // Added for HMS booking transactions
            'fpos_sale' => 'credit',
        ];

        return $account_transaction_types[$transaction_type] ?? 'debit';
    }

    /**
     * Creates a new account transaction.
     *
     * @param  array $data
     * @return AccountTransaction
     */
    public static function createAccountTransaction($data)
    {
        $business_id = request()->session()->get('user.business_id');

        $transaction_data = [
            'amount' => $data['amount'] ?? 0,
            'account_id' => $data['account_id'] ?? null,
            'business_id' => $business_id,
            'type' => $data['type'] ?? $data['sub_type'] ?? 'debit',
            'sub_type' => $data['sub_type'] ?? null,
            'operation_date' => $data['operation_date'] ?? now(),
            'created_by' => $data['created_by'] ?? Auth::id(),
            'transaction_id' => $data['transaction_id'] ?? null,
            'transaction_payment_id' => $data['transaction_payment_id'] ?? null,
            'note' => $data['note'] ?? null,
            'slip_no' => $data['slip_no'] ?? null,
            'cheque_number' => $data['cheque_number'] ?? null,
            'attachment' => $data['attachment'] ?? null,
            'transfer_transaction_id' => $data['transfer_transaction_id'] ?? null,
            'transaction_sell_line_id' => $data['transaction_sell_line_id'] ?? null,
            'sell_line_id' => $data['sell_line_id'] ?? null,
            'purchase_line_id' => $data['purchase_line_id'] ?? null,
            'income_type' => $data['income_type'] ?? null,
            'installment_id' => $data['installment_id'] ?? null,
            'interest' => $data['interest'] ?? null,
            'fixed_asset_id' => $data['fixed_asset_id'] ?? null,
            'pair_at_id' => $data['pair_at_id'] ?? null,
            'post_dated_cheque' => $data['post_dated_cheque'] ?? 0,
            'update_post_dated_cheque' => $data['update_post_dated_cheque'] ?? 0,
            'related_account_id' => $data['related_account_id'] ?? 0,
            'credit_related_account' => $data['credit_related_account'] ?? 0,
            'bank_name' => $data['bank_name'] ?? '',
            'cheque_date' => $data['cheque_date'] ?? null,
            'auto_transfer' => $data['auto_transfer'] ?? null,
        ];

        return self::create($transaction_data);
    }

    /**
     * Updates an account transaction.
     *
     * @param  object $transaction_payment
     * @param  string $transaction_type
     * @return AccountTransaction|null
     */
    public static function updateAccountTransaction($transaction_payment, $transaction_type)
    {
        $account_id = $transaction_payment->account_id;
        $transaction_id = $transaction_payment->transaction_id;
        $transaction = Transaction::find($transaction_id);

        $account_transaction = AccountTransaction::where(
            'transaction_payment_id',
            $transaction_payment->id
        )->first();

        if ($transaction_type === 'purchase') {
            ContactLedger::where('transaction_id', $transaction_id)
                ->where('transaction_payment_id', $transaction_payment->id)
                ->update(['amount' => $transaction_payment->amount]);
        }

        if (in_array($transaction_type, ['sell', 'property_sell'])) {
            ContactLedger::where('transaction_payment_id', $transaction_payment->id)
                ->update(['amount' => $transaction_payment->amount]);
        }

        if ($account_transaction) {
            $account_transaction->update([
                'amount' => $transaction_payment->amount,
                'account_id' => $account_id,
                'operation_date' => $transaction_payment->paid_on,
            ]);

            return $account_transaction;
        }

        $accnt_trans_data = [
            'amount' => $transaction_payment->amount,
            'note' => $transaction_payment->note,
            'slip_no' => $transaction_payment->slip_no,
            'account_id' => $account_id,
            'type' => self::getAccountTransactionType($transaction_type),
            'operation_date' => $transaction_payment->paid_on,
            'created_by' => $transaction_payment->created_by,
            'transaction_id' => $transaction_payment->transaction_id,
            'transaction_payment_id' => $transaction_payment->id,
            'post_dated_cheque' => $transaction_payment->post_dated_cheque ?? 0,
            'update_post_dated_cheque' => $transaction_payment->update_post_dated_cheque ?? 0,
            'related_account_id' => $transaction_payment->related_account_id ?? 0,
            'bank_name' => $transaction_payment->bank_name ?? '',
        ];

        if ($transaction->type === 'sell' && $transaction_payment->is_return == 1) {
            $accnt_trans_data['type'] = 'debit';
        }

        self::createAccountTransaction($accnt_trans_data);
        $accnt_trans_data['contact_id'] = $transaction->contact_id;

        ContactLedger::createContactLedger($accnt_trans_data);
    }

    public function transfer_transaction()
    {
        return $this->belongsTo(self::class, 'transfer_transaction_id');
    }

    public function account()
    {
        return $this->belongsTo(\App\Account::class, 'account_id');
    }
}
