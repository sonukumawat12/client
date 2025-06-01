<?php



namespace Modules\Airline\Entities;



use App\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;




class AirlineCustomers extends Model

{

    use HasFactory;

    protected $table = 'contacts';

    public $timestamps = false;

    protected $fillable = ['business_id'];


    // protected $fillable = [

    //     'type_name',

    //     'description',

      
    // ];



    public function user()

    {

        return $this->belongsTo(User::class);

    }



}

