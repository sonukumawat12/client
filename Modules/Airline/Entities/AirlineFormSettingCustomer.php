<?php



namespace Modules\Airline\Entities;



use App\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;




class AirlineFormSettingCustomer extends Model

{

    use HasFactory;

    protected $table = 'airline_form_setting_customer';

    public $timestamps = false;

    protected $fillable = [
        'business_id',
        'created_by',
        'name',
        'vat_no',
        'credit_limit',
        'mobile',
        'address',
        'state',
        'tax_number',
        'confirm_password',
        'sub_customer',
        'passport_nic_no',
        'need_to_send_sms',
        'opening_balance',
        'transaction_date',
        'landline',
        'address_line_2',
        'country',
        'pay_term',
        'email',
        'vehicle_no',
        'passport_nic_image',
        'credit_notification_type',
        'customer_group',
        'add_more_mobile_numbers',
        'assigned_to',
        'city',
        'landmark',
        'password',
        'alternate_contact_number',
        'address_line_3',
        'signature',
        'whatsapp_number',
    ];
    


   



    public function user()

    {

        return $this->belongsTo(User::class);

    }



}

