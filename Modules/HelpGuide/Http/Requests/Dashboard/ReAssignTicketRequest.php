<?php

namespace Modules\HelpGuide\Http\Requests\Dashboard;

use Modules\HelpGuide\Ticket;
use Illuminate\Foundation\Http\FormRequest;

class ReAssignTicketRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
      $ticket = Ticket::withoutGlobalScope('own_ticket')->findOrFail($this->ticket);
      return $this->user()->can('reAssign', $ticket);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
          'assign_to' => ['required', 'numeric', 'exists:users,id']
        ];
    }
}
