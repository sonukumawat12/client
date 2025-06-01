<?php
namespace App\Services;

use Carbon\Carbon;
use App\Denominations;
use Illuminate\Http\Request;

/**
 * All Denominations Services Provide here
 * Dev: Sakhawat Kamran
 */
class DenominationsService
{
    
    /**
     * Store Service Record
     *
     * Undocumented function long description
     *
     * @param Integer $user_id 
     * @param Double $denomination 
     * @param Integer $count 
     * @param Double $total 
     * @param Enum $type ['open','close']
     * @param Enum $module ['pos']
     * @param Enum $record_id ['pos']
     * @return type
     * @throws conditon
     **/
    public function saveOrUpdate($record = [
        'user_id' => 0,
        'denomination'=> 0.0,
        'count'=> 0,
        'total'=> 0.0,
        'type'=> 'open'
    ], $record_id = 0 , $module = 'pos')
    {
        $mdl = Denominations::where(['denominations_belongs_id'=>$record_id, 'module' => $module ,'type' => $record['type'] ,'denomination'=> $record['denomination']])->whereDate('created_at', '=', Carbon::today()->toDateString())->first();
        if($mdl)
        {
            return $mdl->update($record);
        }
        $record['denominations_belongs_id'] = $record_id;
        $record['module'] = $module;
        return Denominations::create($record);
        
    }

    /**
     * Post Services 
     *
     *
     **/
    public function push(Request $request, $record_id, $module)
    {
        $grand_total = 0;
        if($request->has('denominations'))
        {
            $denomination = $request->denominations;
            foreach ($denomination as $key => $value) {
                if($value){
                    $this->saveOrUpdate(
                        [
                            'user_id' => $request->session()->get('user.id'),
                            'denomination'=> $key,
                            'count'=> $value,
                            'total'=> $key * $value,
                            'type'=> 'close',
                        ]
                        ,
                        $record_id
                        , 
                        $module

                    );
                    $grand_total +=  $key * $value;
                }
            }
            
        }
        return $grand_total;
    }

}


