<?php

namespace Modules\Loan\Http\Requests\LoanCharge;

use Illuminate\Foundation\Http\FormRequest;

class CreateLoanChargeRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'currency_id' => ['required'],
            'loan_charge_option_id' => ['required'],
            'loan_charge_type_id' => ['required'],
            'name' => ['required'],
            'amount' => ['required'],
            'active' => ['required'],
            'is_penalty' => ['required'],
            'allow_override' => ['required'],
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}