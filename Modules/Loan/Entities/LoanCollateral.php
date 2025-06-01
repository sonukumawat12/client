<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanCollateral extends Model
{
    protected $fillable = [
        'created_by_id',
        'loan_id',

        'loan_collateral_type_id',
        'product_name',
        'registration_date',
        'value',
        'status',

        'serial_number',
        'model_name',
        'model_number',
        'color',
        'manufacture_date',
        'condition',
        'address',
        'description',

        'registration_number',
        'mileage',
        'engine_number',
    ];

    public $table = "loan_collateral";

    public function collateral_type()
    {
        return $this->hasOne(LoanCollateralType::class, 'id', 'loan_collateral_type_id')->withDefault();
    }

    public function history()
    {
        return $this->hasMany(LoanCollateralHistory::class);
    }

    public function media()
    {
        return $this->morphMany(\Modules\Accounting\Entities\Media::class, 'model');
    }

    public function file()
    {
        return $this->media()->where('description', 'file');
    }

    public function photo()
    {
        return $this->media()->where('description', 'photo');
    }

    public static function getStatuses()
    {
        return [
            'deposited_into_branch' => trans('loan::general.deposited_into_branch'),
            'collateral_with_borrower' => trans('loan::general.collateral_with_borrower'),
            'returned_to_borrower' => trans('loan::general.returned_to_borrower'),
            'repossession_initiated' => trans('loan::general.repossession_initiated'),
            'repossessed' => trans('loan::general.repossessed'),
            'under_auction' => trans('loan::general.under_auction'),
            'sold' => trans('loan::general.sold'),
            'lost' => trans('loan::general.lost'),
        ];
    }

    public static function getConditions()
    {
        return [
            'excellent' => trans('loan::general.excellent'),
            'good' => trans('loan::general.good'),
            'fair' => trans('loan::general.fair'),
            'damaged' => trans('loan::general.damaged'),
        ];
    }
}
