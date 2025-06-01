<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LoanApprovalOfficer extends Model
{
    public $timestamps = false;

    /**
     * Adds a new loan officer to approve the loan
     * @param string loan_approval_officers - should be a string of user ids separated by a comma
     */
    public static function addNew($loan_approval_officers, $product_id, $loan_id)
    {
        LoanApprovalOfficer::where('loan_id', $loan_id)->delete();

        $records = [];
        
        foreach ($loan_approval_officers as $user_id) {
            array_push($records, [
                'user_id' => $user_id,
                'product_id' => $product_id,
                'loan_id' => $loan_id,
            ]);
        }

        LoanApprovalOfficer::insert($records);
    }

    //If an officer has been added to a product, then any pending loans with that product should be updated with the new officers 
    public static function addToExisting($product_id, $new_approval_officers)
    {
        $loan_approval_officers = LoanApprovalOfficer::where('product_id', $product_id)->where('status', 'pending')->groupBy('loan_id')->get();
        $records = [];

        foreach ($loan_approval_officers as $officer) {
            foreach ($new_approval_officers as $user_id) {
                array_push($records, [
                    'user_id' => $user_id,
                    'product_id' => $officer->product_id,
                    'loan_id' => $officer->loan_id,
                ]);
            }
        }

        LoanApprovalOfficer::insert($records);
    }

    public static function removeFromExisting($product_id)
    {
        LoanApprovalOfficer::where('product_id', $product_id)
            ->where('status', 'pending')
            ->delete();
    }

    public static function updateStatus($loan_id, $status)
    {
        $approval_officer = LoanApprovalOfficer::where('loan_id', $loan_id)->where('user_id', Auth::id())->first();

        if ($approval_officer) {
            $approval_officer->status = $status;
            $approval_officer->save();
        }
    }

    public static function updateStatusForAll($loan_id, $status)
    {
        LoanApprovalOfficer::where('loan_id', $loan_id)->update([
            'status' => $status
        ]);
    }

    public static function getApprovalStatus($loan_id)
    {
        $loan_statuses = LoanApprovalOfficer::where('loan_id', $loan_id)->pluck('status')->toArray();

        //If no status is found recorded, status is pedning by default 
        if (!count($loan_statuses) > 0) {
            return 'pending';
        }

        //If any loan officer has rejected the loan, it is marked rejected
        if (in_array('rejected', $loan_statuses)) {
            return 'rejected';

            //If any loan officer has withdrawn, it is marked as withdrawn
        } else if (in_array('withdrawn', $loan_statuses)) {
            return 'withdrawn';

            //If any loan officer is yet to approve, it is marked as pending
        } else if (in_array('pending', $loan_statuses)) {
            return 'pending';

            //Otherwise the loan has been approved
        } else {
            return 'approved';
        }
    }

    public static function hasLoansToApprove($user_id)
    {
        return LoanApprovalOfficer::where('user_id', $user_id)->where('status', 'pending')->exists();
    }

    public static function canApproveLoan($loan_id, $user_id)
    {
        return LoanApprovalOfficer::where('loan_id', $loan_id)->where('user_id', $user_id)->exists();
    }
}
