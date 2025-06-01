<div class="btn-group">
    <button type="button" class="btn btn-info dropdown-toggle btn-xs"
            data-toggle="dropdown" aria-expanded="false">
        {{ __("messages.actions") }}
        <span class="caret"></span><span class="sr-only">Toggle Dropdown</span>
    </button>
    <ul class="dropdown-menu dropdown-menu-left" role="menu">
        
        <li><a href="{{action('\Modules\Vat\Http\Controllers\VatContactController@show', [$id])}}"><i class="fa fa-eye" aria-hidden="true"></i> @lang("messages.view")</a></li>
        <li><a href="{{action('\Modules\Vat\Http\Controllers\VatContactController@edit', [$id])}}" class="edit_contact_button"><i class="fa fa-pencil-square-o"></i> @lang("messages.edit")</a></li>
        <li><a href="{{action('\Modules\Vat\Http\Controllers\VatContactController@destroy', [$id])}}" class="delete_contact_button"><i class="fa fa-trash"></i> @lang("messages.delete")</a></li>
        <li class="divider"></li>
        <li><a href="{{action('\Modules\Vat\Http\Controllers\VatContactController@show', [$id])."?view=contact_info"}}"><i class="fa fa-user" aria-hidden="true"></i> @lang("contact.contact_info", ["contact" => __("contact.contact") ])</a></li>
            
        <li><a href="{{action('\Modules\Vat\Http\Controllers\VatContactController@toggleActivate', [$id])}}">
                @if($active)
                    <i class="fa fa-times" aria-hidden="true"></i> @lang("lang_v1.deactivate")
                @else
                    <i class="fa fa-check" aria-hidden="true"></i> @lang("lang_v1.activate")
                @endif
            </a></li>
    </ul>
</div>

