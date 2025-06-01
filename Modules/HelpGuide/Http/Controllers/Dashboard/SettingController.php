<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Modules\HelpGuide\Entities\Setting;
use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;
use Modules\HelpGuide\Http\Requests\Dashboard\UpdateSettingRequest;

class SettingController extends Controller
{
  public function save(UpdateSettingRequest $request)
  {
    $validated = $request->validated();

    if( ! $validated ) {
      return [
        'message' => __('Nothing saved')
      ];
    }

    // Save validated setting
    foreach ($validated as $key => $value) {
      Setting::add($key, $value, Setting::getDataType($key));
    }
    
    return ['message' => __('Settings has been saved.')];

  }
}
