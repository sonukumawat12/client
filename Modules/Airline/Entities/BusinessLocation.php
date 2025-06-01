<?php



namespace Modules\Airline\Entities;



use App\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



class BusinessLocation extends Model

{

    use HasFactory;

    protected $table = 'business_locations';

    public $timestamps = false;

    

    // protected $fillable = [

    //     'type_name',

    //     'description',

      
    // ];



    public function user()

    {

        return $this->belongsTo(User::class);

    }



}

