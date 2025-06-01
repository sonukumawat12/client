<?php



namespace Modules\Airline\Entities;



use App\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



class AirlinePassengerType extends Model

{

    use HasFactory;

    protected $table = 'airline_passenger_type';

    public $timestamps = false;

    

    protected $fillable = [

        'type_name',

        'description',

      
    ];



    public function user()

    {

        return $this->belongsTo(User::class);

    }



}

