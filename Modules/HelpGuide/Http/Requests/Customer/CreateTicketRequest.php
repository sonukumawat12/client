<?php

namespace Modules\HelpGuide\Http\Requests\Customer;

use Modules\HelpGuide\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class CreateTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return $this->user()->can('create', Ticket::class);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {

        $customFields = customFields('ticket', 'create_ticket');
        $customFieldsValidation = [];

        foreach($customFields as $field){
            if( empty( $field['rules'] ) ) continue;
            $customFieldsValidation['custom_fields.'.$field['key']] = $field['rules'];
        }

        $rules = [
            'title' =>  ['required'],
            'category' =>  ['required','exists:helpguide_categories,id'],
            'priority' =>  ['required','in:urgent,high,medium,low'],
            'content' =>  ['required'],
        ];

        $rules = array_merge($customFieldsValidation, $rules);

        return $rules;
    }

    public function messages(){

        $customFields = customFields('ticket', 'create_ticket');
        $customFieldsValidationMsg = [];

        $messages = [
            'title.required' => __("title is required"),
            'category.required' => __("Category is required"),
            'category.exists' => __("Category not found"),
            'priority.required' => __("Priority is required"),
            'priority.in' => __("Priority must be urgent, high, medium or low"),
            'content.required' => __("Ticket content is required"),
        ];

        foreach($customFields as $field){
            if( empty( $field['rules'] ) ) continue;
            $customFieldsValidationMsg = array_merge($customFieldsValidationMsg, $field['rules_messages']);
        }

        $messages = array_merge($customFieldsValidationMsg, $messages);

        return $messages;
    }
}
