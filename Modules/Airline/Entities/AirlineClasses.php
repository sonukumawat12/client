<?php



namespace Modules\Airline\Entities;



use App\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



class AirlineClasses extends Model

{

    use HasFactory;

    protected $table = 'airline_classes';

    public $timestamps = false;

    

    protected $fillable = [

        'name',


      
    ];



    public function user()

    {

        return $this->belongsTo(User::class);

    }



}

