<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanProductApprovalOfficer extends Model
{
    public $timestamps = false;
    
    
    public static function add($product_id, $new_approval_officers)
    {
        $records = [];
            
        //Add new record to loan_product_approval_officers
        foreach($new_approval_officers as $user_id)
        {
            array_push($records, [
                'user_id' => $user_id,
                'product_id' => $product_id,
            ]);
        }
        
        LoanProductApprovalOfficer::insert($records);
    }
    
    
    public static function remove($product_id, $removed_approval_officers)
    {
        LoanProductApprovalOfficer::whereIn('user_id', $removed_approval_officers)
                ->where('product_id', $product_id)
                ->delete();
    }
    
    public static function getOfficerIds($product_id)
    {
        return LoanProductApprovalOfficer::where('product_id', $product_id)->pluck('user_id')->toArray();
    }
}
