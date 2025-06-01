<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Modules\HelpGuide\Entities\User;
use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;
use Modules\HelpGuide\Http\Resources\Dashboard\UserResource;

class EmployeeController extends Controller
{
  public function list()
  {
      // $this->authorize('viewAny', User::class);
      $user = User::orderBy('id', 'desc')->paginate(50);
      return UserResource::collection($user);
  }

  public function show($id)
  {
    $user = User::findOrFail($id);
    // $this->authorize('view', $user);
    return new UserResource($user);
  }

}
