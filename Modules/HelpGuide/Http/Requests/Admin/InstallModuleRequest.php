<?php

namespace Modules\HelpGuide\Http\Requests\Admin;

use Modules\HelpGuide\Models\Module;
use Illuminate\Foundation\Http\FormRequest;

class InstallModuleRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
      return $this->user()->can('install', Module::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules()
    {
        return [
          'module' => ['required']
        ];
    }
}
