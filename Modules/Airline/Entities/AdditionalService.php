<?php



namespace Modules\Airline\Entities;



use App\User;

use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



class AdditionalService extends Model

{

    use HasFactory;

    protected $table = 'additional_service';

    public $timestamps = false;

    

    protected $fillable = [

        'id',
         
        'name', 

        'description',

        'date_added',

        'user_id'

    ];



    public function user()

    {

        return $this->belongsTo(User::class);

    }



}

