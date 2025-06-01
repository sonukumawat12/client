namespace App;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HmsTransaction extends Model
{
    use HasFactory;
    
    protected $table = 'hms_transactions';

    protected $fillable = [
        'transaction_date',
        'description',
        'customer_id',
        'payment_account',
        'cheque_number',
        'debit',
        'credit',
        'account_type'
    ];
}
