<?php

namespace Modules\HelpGuide\Http\Requests\Dashboard;

use Modules\HelpGuide\Entities\Setting;
use Illuminate\Foundation\Http\FormRequest;

class UpdateSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
      return $this->user()->can('update', Setting::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
      $fields = (array) array_keys( $this->request->all() );
      return Setting::getValidationRulesFor( $fields );
    }
}
