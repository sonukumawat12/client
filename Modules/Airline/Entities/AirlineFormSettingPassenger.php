<?php



namespace Modules\Airline\Entities;



use App\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;




class AirlineFormSettingPassenger extends Model

{

    use HasFactory;

    protected $table = 'airline_form_setting_passenger';

    public $timestamps = false;

    protected $fillable = [
        'created_by',
        'business_id',
        'name',
        'passenger_mobile_no',
        'frequent_flyer_no',
        'additional_service',
        'passport_number',
        'select_passport_image',
        'child',
        'additional_service_amount',
        'vat_number',
        'need_to_send_sms',
        'price',
        'passenger_type',
        'created_at',
        'updated_at',
       
    ];


   



    public function user()

    {

        return $this->belongsTo(User::class);

    }



}

