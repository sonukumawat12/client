<?php



namespace Modules\Airline\Entities;



use App\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;




class AirlineFormSettingSupplier extends Model

{

    use HasFactory;

    protected $table = 'airline_form_setting_supplier';

    public $timestamps = false;

    protected $fillable = [
        'created_by',
        'business_id',
        'type',
        'tax_number',
        'transaction_date',
        'mobile',
        'address',
        'country',
        'custom_field_1',
        'custom_field_2',
        'custom_field_3',
        'custom_field_4',
        'name',
        'opening_balance',
        'supplier_group',
        'alternate_contact_number',
        'city',
        'landmark',
        'contact_id',
        'pay_term',
        'email',
        'landline',
        'state',
        'created_at',
        'updated_at'
    ];


   



    public function user()

    {

        return $this->belongsTo(User::class);

    }



}

