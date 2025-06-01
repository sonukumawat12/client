<?php

namespace Modules\Petro\Http\Controllers;

use App\Unit;
use App\Product;
use App\Utils\ModuleUtil;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;

use App\Utils\Util;
use Modules\Superadmin\Entities\HelpExplanation;
use Illuminate\Routing\Controller;

class UnitController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $commonUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('unit.view') && !auth()->user()->can('unit.create') && !auth()->user()->can('property.settings.unit')) {
            abort(403, 'Unauthorized action.');
        }

        $help_explanations = HelpExplanation::pluck('value', 'help_key');

        return view('petro::unit.index')->with(compact('help_explanations'));
    }


}
