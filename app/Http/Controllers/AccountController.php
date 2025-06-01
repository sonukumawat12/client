<?php

namespace App\Http\Controllers;

use App\Account;
use App\Product;
use App\AccountGroup;
use App\AccountSetting;
use App\AccountTransaction;
use App\AccountType;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\Journal;
use App\ContactLedger;
use App\System;
use App\Transaction;
use App\TransactionSellLine;
use App\User;
use App\TransactionPayment;
use App\PurchaseLine;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use App\Utils\Util;

use Modules\Fleet\Entities\Driver;
use Modules\Fleet\Entities\Helper;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\PumpOperator;
use Modules\Superadmin\Entities\Subscription;
use Yajra\DataTables\Facades\DataTables;
use Intervention\Image\Facades\Image;
use Modules\Petro\Entities\SettlementExpensePayment;
use Modules\Petro\Entities\DailyCollection;
use Modules\Petro\Entities\Settlement;
use Modules\Superadmin\Entities\ModulePermissionLocation;
use Psy\TabCompletion\Matcher\FunctionsMatcher;
use Excel;
use Illuminate\Support\Facades\Auth;
use Modules\Property\Entities\Property;
use Modules\Property\Entities\PropertySellLine;
use Modules\Fleet\Entities\Fleet;
use Illuminate\Support\Facades\Session;
use App\NotificationTemplate;
use Modules\Petro\Entities\SettlementCashDeposit;
use Modules\Petro\Entities\SettlementLoanPayment;
use Modules\Petro\Entities\SettlementDrawingPayment;
use Modules\Petro\Entities\CustomerPayment;

use Modules\Shipping\Entities\ShippingAgent;
use Modules\Shipping\Entities\ShippingPartner;


use Modules\PriceChanges\Entities\PriceChangesDetail;
use Modules\PriceChanges\Entities\PriceChangesHeader;

use Modules\Superadmin\Entities\AccountNumber;
use Modules\Shipping\Entities\ShippingAgentCommission;
use Modules\Vat\Entities\VatPayment;
use Modules\Essentials\Entities\EssentialsEmployee;
use Modules\Essentials\Entities\EssentialsEmployeeAdvance;
use Modules\Essentials\Entities\EssentialsEmployeePaymentSetting;
use App\Category;


class AccountController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;
    protected $businessUtil;
    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, BusinessUtil $businessUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {

        $this->commonUtil = $commonUtil;
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
        $this->transactionUtil =  $transactionUtil;
        $this->businessUtil = $businessUtil;
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */

    public function getAccNo($id)
    {
        $business_id = session()->get('user.business_id');
        $is_manual = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'acc_no_manually');

        $acc_type = AccountType::find($id);

        if (empty($acc_type)) {
            return response()->json(array('disable' => 0, 'account_no' => ""));
        }
        $name = $acc_type->name;


        $acc_no =  AccountNumber::leftjoin('default_account_types as type', 'account_numbers.account_type', 'type.id')
            ->where('type.name', $name)
            ->where('account_numbers.business_id', $business_id)
            ->select([
                'account_numbers.*'
            ])
            ->first();


        if (!empty($acc_no)) {
            $current = Account::where('account_number', 'like', '%' . $acc_no->prefix . "-" . '%')->latest()->first();

            $starting = 0;
            if (!empty($current)) {
                $current_no = $current->account_number;
                $current_no_arr = explode('-', $current_no);
                $starting = (int) $current_no_arr[sizeof($current_no_arr) - 1];
            }

            if ($starting >= $acc_no->account_number) {
                $starting = $starting + 1;
            } else {
                $starting = $acc_no->account_number;
            }

            $account_no = $acc_no->prefix . "-" . ($starting);

            $response = array('disable' => 1, 'account_no' => $account_no);
        } else {
            $response = array('disable' => 0, 'account_no' => "");
        }

        return response()->json($response);
    }
    public function index(Request $request)
    {
        $business_id = session()->get('user.business_id');
        $user_id = request()->session()->get('user.id');

        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action('HomeController@index'));
        }
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }

        // if 'Post Dated Cheques' account is not created, then create
        Account::crearePostdatedChequesAccount($business_id, $user_id);

        $account_payable = Account::where('business_id', $business_id)->where('name', 'Accounts Payable')->first();

        $account_payable_id = !empty($account_payable) ? $account_payable : null;
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');

        $banking_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'banking_module');
        if (auth()->user()->can('superadmin')) {
            $account_access = 1;
        }
        $parentAccounts = Account::where([/*'is_main_account'=>1,*/'business_id' => $business_id])->pluck('name', 'id');

        if (request()->ajax()) {
            $accounts = Account::leftjoin('account_transactions as AT', function ($join) {
                $join->on('AT.account_id', '=', 'accounts.id');
                $join->where('AT.deleted_at',NULL);
            })
                ->leftjoin(
                    'transactions',
                    'AT.transaction_id',
                    '=',
                    'transactions.id'
                )
                ->leftjoin(
                    'account_types as ats',
                    'accounts.account_type_id',
                    '=',
                    'ats.id'
                )
                ->leftjoin(
                    'account_types as pat',
                    'ats.parent_account_type_id',
                    '=',
                    'pat.id'
                )
                ->leftjoin(
                    'account_groups',
                    'accounts.asset_type',
                    '=',
                    'account_groups.id'
                )
                ->leftJoin('users AS u', 'accounts.created_by', '=', 'u.id')

                ->leftJoin('transaction_payments AS TP', 'AT.transaction_payment_id', '=', 'TP.id')
                // ->where(function ($query) {
                //     $query->whereNull('AT.transaction_payment_id')
                //           ->orWhere(function ($query2) {
                //                 $query2->whereNotNull('AT.transaction_payment_id')
                //                         ->whereNotNull('TP.id');
                //           });
                // })
                ->where('accounts.business_id', $business_id)
                // ->where('accounts.visible', 1)
                ->select([
                    'accounts.location_id',
                    'accounts.name',
                    'accounts.parent_account_id',
                    'accounts.account_number',
                    'accounts.visible',
                    'accounts.is_main_account',
                    'accounts.note',
                    'accounts.id',
                    'accounts.account_type_id',
                    'accounts.created_by',
                    'accounts.disabled',
                    'accounts.asset_type',
                    'ats.name as account_type_name',
                    'pat.name as parent_account_type_name',
                    'is_closed',
                    'account_groups.name as group_name',
                    DB::raw("SUM( IF(AT.type='credit', -1*AT.amount, AT.amount) ) as ass_exp_balance"),
                    DB::raw("SUM( IF(AT.type='debit', -1*AT.amount, AT.amount) ) as li_in_eq_balance"),
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                ]);
            $accounts->where('disabled', 0);
            $acc_type = request()->get('account_type_s', null);
            $acc_sub_type = request()->get('account_sub_type', null);
            if (!empty($acc_type)  && $acc_type != 'All') {
                if (!empty($acc_sub_type) && $acc_sub_type != 'All') {
                    $accounts->where('accounts.account_type_id', $acc_sub_type);
                } else {
                    $account_type_ids = AccountType::where('business_id', $business_id)->where('parent_account_type_id', $acc_type)->pluck('id');
                    if (count($account_type_ids) > 0) {
                        $accounts->whereIn('accounts.account_type_id', $account_type_ids);
                    } else {
                        $accounts->where('accounts.account_type_id', $acc_type);
                    }
                }
            } else {
                if (!empty($acc_sub_type)  && $acc_sub_type != 'All') {
                    $accounts->where('accounts.account_type_id', $acc_sub_type);
                }
            }
            $acc_group = request()->get('account_group', null);
            if (!empty($acc_group)  && $acc_group != 'All') {
                $accounts->where('account_groups.id', $acc_group);
            }


            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations !== 'all') {
                $accounts->whereIn('accounts.location_id', $permitted_locations);
            }


            $location_id = request()->get('location_id', null);
            if (!empty($location_id)  && $location_id != 'all') {
                $accounts->where('accounts.location_id', $location_id);
            }


            $ac_parent = request()->get('parent_account_id', null);
            if (!empty($ac_parent) && $ac_parent != 'All') {
                $accounts->where('accounts.parent_account_id', $ac_parent);
            }
            $acc_name = request()->get('account_name', null);
            if (!empty($acc_name) && $acc_name != 'All') {
                $accounts->where('accounts.id', $acc_name);
            }
            if ($account_access == 0) {
                $accounts->where(function ($query) {
                    $query->whereIn('accounts.name', ['Accounts Receivable', 'Accounts Payable', 'Cards (Credit Debit) Account', 'Cash', 'Cheques in Hand', 'Customer Deposits', 'Petty Cash']);
                    $query->orWhere('accounts.visible', 1);
                });
            }
            $accounts->groupBy('accounts.id');
            $chequeId = $this->transactionUtil->account_exist_return_id('Cheques in Hand');
            $asset_type_accounts = Account::AssetTypeAccountGroupActive();
            return DataTables::of($accounts)
                ->addColumn('action', function ($row) use ($account_access, $banking_module, $chequeId) {
                    $html = '';

                    // Check if the account is of type "Post Dated Cheques"
                    $isCompanyPostDatedCheques = $row->name === 'Post Dated Cheques';

                    if ($isCompanyPostDatedCheques) {
                        $disabled = null;
                        $disabledClose = '';
                        if (($row->name == "Accounts Payable" || $row->name == "Accounts Receivable") && $banking_module == 1 && $account_access == 0) {
                            $html = '<h4 class="text-danger">You have not subscribed to Accounting Module, so details in this page will not show</h4>';
                        } else {
                            // Check if the user has edit permission and the account is not of type "Post Dated Cheques"
                            $disabledEdit = 'disabled';
                            $disabledClose = 'disabled';

                            // edit button
                            $html .=  '<button ' . $disabledEdit . ' data-href="' . action('AccountController@edit', [$row->id]) . '" data-container=".account_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</button>&nbsp';

                            // check if its main account
                            if ($row->is_main_account == 0) {
                                $html .=  '<a href="' . action('AccountController@show', [$row->id]) . '" class="btn btn-warning btn-xs"><i class="fa fa-book"></i> ' . __("account.account_book") . '</a>&nbsp';
                            } else {
                                $html .=  '<a href="' . action('AccountController@show', [$row->id]) . '" class="btn btn-warning btn-xs"><i class="fa fa-book"></i> ' . __("lang_v1.main_account_book") . '</a>&nbsp';
                            }

                            // funds transfer
                            $html .=  '<button data-href="' . action('AccountController@getFundTransfer', [$row->id]) . '" class="btn btn-xs btn-info btn-modal transfer_btn" data-container=".account_model"><i class="fa fa-exchange"></i> ' . __("account.fund_transfer") . '</button>&nbsp';


                            // if (auth()->user()->can('account.edit')) {
                            //     $html .=  '<button disabled data-href="' . action('AccountController@edit', [$row->id]) . '" data-container=".account_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</button>&nbsp';
                            // }

                            // close button
                            $html .=  '<button  data-url="' . action('AccountController@close', [$row->id]) . '" class="btn btn-xs btn-danger close_account"><i class="fa fa-close"></i> ' . __("messages.close") . '</button>&nbsp';

                            // get notes
                            $html .=  '<button data-href="' . action('AccountController@getNotes', [$row->id]) . '" class="btn btn-xs btn-default btn-modal" data-container=".account_model"><i class="fa fa-sticky-note-o "></i> ' . __("account.notes") . '</button> &nbsp';

                            // enabled 
                            $html .=  '<button data-url="' . action('AccountController@disabledStatus', [$row->id]) . '" class="btn btn-xs btn-success disable_status_account"><i class="fa fa-check"></i> ' . __("account.enabled") . '</button>&nbsp';

                            // if ($row->is_closed == 0) {
                            //     if ($row->is_main_account == 0) {
                            //         if ($row->id != $chequeId && !in_array($row->group_name, ['Cash Account', 'Card']) && !in_array($row->name, ['Accounts Receivable'])) {
                            //             $html .=  '<button ' . $disabledEdit . ' data-href="' . action('AccountController@getFundTransfer', [$row->id]) . '" class="btn btn-xs btn-info btn-modal transfer_btn" data-container=".account_model"><i class="fa fa-exchange"></i> ' . __("account.fund_transfer") . '</button>&nbsp';

                            //             if (!in_array($row->group_name, ['Bank Account'])) {
                            //                 $html .=  '<button ' . $disabledEdit . ' data-href="' . action('AccountController@getDeposit', [$row->id]) . '" class="btn btn-xs btn-success btn-modal deposit_btn" data-container=".account_model"><i class="fa fa-money"></i> ' . __("account.deposit") . '</button>&nbsp<br><br>';
                            //             }
                            //         }
                            //         $html .=  '<button ' . $disabledClose . ' data-url="' . action('AccountController@close', [$row->id]) . '" class="btn btn-xs btn-danger close_account"><i class="fa fa-close"></i> ' . __("messages.close") . '</button>&nbsp';
                            //     }
                            //     $html .=  '<button ' . $disabledEdit . ' data-href="' . action('AccountController@getNotes', [$row->id]) . '" class="btn btn-xs btn-default btn-modal" data-container=".account_model"><i class="fa fa-sticky-note-o "></i> ' . __("account.notes") . '</button> &nbsp';
                            //     if ($row->disabled == 0) {
                            //         if ($row->is_main_account == 0) {
                            //             $html .=  '<button ' . $disabledEdit . ' data-url="' . action('AccountController@disabledStatus', [$row->id]) . '" class="btn btn-xs btn-success disable_status_account"><i class="fa fa-check"></i> ' . __("account.enabled") . '</button>&nbsp';
                            //         }
                            //     }
                            // }
                        }
                    } else {
                        $disabled = '';
                        $disabledClose = '';
                        $isCompanyPostDatedCheques = false;
                        if (($row->name == "Accounts Payable" || $row->name == "Accounts Receivable") && $banking_module == 1 && $account_access == 0) {
                            $html = '<h4 class="text-danger">You have not subscribed to Accounting Module, so details in this page will not show</h4>';
                        } else {
                            // Check if the user has edit permission and the account is not of type "Post Dated Cheques"
                            $disabledEdit = (auth()->user()->can('account.edit') && !$isCompanyPostDatedCheques) ? '' : 'disabled';

                            if ($account_access == 0 && !in_array($row->group_name, ['Cash Account', 'Bank Account', 'Card']) && $row->name != 'Cheques in Hand' || $row->name == 'Opening Balance Equity Account' || $row->name == 'Post Dated Cheques') {
                                $disabledEdit = 'disabled';
                                $disabledClose = 'disabled';
                            }

                            if (auth()->user()->can('account.edit')) {
                                $html .=  '<button ' . $disabledEdit . ' data-href="' . action('AccountController@edit', [$row->id]) . '" data-container=".account_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</button>&nbsp';
                            }

                            if ($row->is_main_account == 0) {
                                $html .=  '<a href="' . action('AccountController@show', [$row->id]) . '" class="btn btn-warning btn-xs"><i class="fa fa-book"></i> ' . __("account.account_book") . '</a>&nbsp';
                            } else {
                                $html .=  '<a href="' . action('AccountController@show', [$row->id]) . '" class="btn btn-warning btn-xs"><i class="fa fa-book"></i> ' . __("lang_v1.main_account_book") . '</a>&nbsp';
                            }

                            if ($row->is_closed == 0) {
                                if ($row->is_main_account == 0) {
                                    if ($row->id != $chequeId && !in_array($row->group_name, ['Cash Account', 'Card']) && !in_array($row->name, ['Accounts Receivable'])) {
                                        $html .=  '<button ' . $disabledEdit . ' data-href="' . action('AccountController@getFundTransfer', [$row->id]) . '" class="btn btn-xs btn-info btn-modal transfer_btn" data-container=".account_model"><i class="fa fa-exchange"></i> ' . __("account.fund_transfer") . '</button>&nbsp';

                                        if (!in_array($row->group_name, ['Bank Account'])) {
                                            $html .=  '<button ' . $disabledEdit . ' data-href="' . action('AccountController@getDeposit', [$row->id]) . '" class="btn btn-xs btn-success btn-modal deposit_btn" data-container=".account_model"><i class="fa fa-money"></i> ' . __("account.deposit") . '</button>&nbsp<br><br>';
                                        }
                                    }
                                    $html .=  '<button ' . $disabledClose . ' data-url="' . action('AccountController@close', [$row->id]) . '" class="btn btn-xs btn-danger close_account"><i class="fa fa-close"></i> ' . __("messages.close") . '</button>&nbsp';
                                }
                                $html .=  '<button ' . $disabledEdit . ' data-href="' . action('AccountController@getNotes', [$row->id]) . '" class="btn btn-xs btn-default btn-modal" data-container=".account_model"><i class="fa fa-sticky-note-o "></i> ' . __("account.notes") . '</button> &nbsp';
                                if ($row->disabled == 0) {
                                    if ($row->is_main_account == 0) {
                                        $html .=  '<button ' . $disabledEdit . ' data-url="' . action('AccountController@disabledStatus', [$row->id]) . '" class="btn btn-xs btn-success disable_status_account"><i class="fa fa-check"></i> ' . __("account.enabled") . '</button>&nbsp';
                                    }
                                }
                            }
                        }
                    }

                    return $html;
                })

                ->editColumn('name', function ($row) {
                    if ($row->is_closed == 1) {
                        return $row->name . ' <small class="label pull-right bg-red no-print">' . __("account.closed") . '</small><span class="print_section">(' . __("account.closed") . ')</span>';
                    } else {
                        return $row->name;
                    }
                })
                ->editColumn('parent_account_id', function ($row) use ($parentAccounts) {
                    // logger($parentAccounts);

                    if ($row->parent_account_id && isset($parentAccounts[$row->parent_account_id])) {
                        return $parentAccounts[$row->parent_account_id];
                    }
                    return "";
                })
                ->addColumn('balance', function ($row) use ($business_id) {

                    if ($row->is_main_account == 1) {
                        $balance = $this->getAccountBalanceMain($row->id);
                        return '<span class="display_currency" data-currency_symbol="true">' . $balance['balance'] . '</span>';
                    } else {
                        $balance = Account::getAccountBalance($row->id);
                        return '<span class="display_currency" data-currency_symbol="true">' . $balance . '</span>';
                    }
                })
                ->addColumn('account_location', function ($row) {
                    if ($row->location_id == 'all') {
                        return ucfirst($row->location_id);
                    } else {
                        $loc = BusinessLocation::find($row->location_id);

                        if (!empty($loc)) {
                            return $loc->name;
                        }
                    }
                })
                ->editColumn('account_type', function ($row) {
                    $account_type = '';
                    if (!empty($row->account_type->parent_account)) {
                        $account_type .= $row->account_type->parent_account->name . ' / ';
                    }
                    if (!empty($row->account_type)) {
                        $account_type .= $row->account_type->name;
                    }
                    return $account_type;
                })
                ->editColumn('parent_account_type_name', function ($row) {
                    $parent_account_type_name = empty($row->parent_account_type_name) ? $row->account_type_name : $row->parent_account_type_name;
                    return $parent_account_type_name;
                })
                ->editColumn('account_type_name', function ($row) {
                    $account_type_name = empty($row->parent_account_type_name) ? '' : $row->account_type_name;
                    return $account_type_name;
                })
                ->editColumn('added_by', function ($row) {
                    if ($row->created_by == 1) {
                        return 'Default';
                    } else {
                        return $row->added_by;
                    }
                })
                ->editColumn('account_group', function ($row) use ($business_id) {
                    // return $row->asset_type;
                    if (!empty($row->asset_type)) {
                        $account_group =  AccountGroup::where('business_id', $business_id)->where('id', $row->asset_type)->first();
                        if (!empty($account_group)) {
                            return $account_group->name;
                        }
                        return '';
                    } else {
                        return '';
                    }
                })
                ->setRowAttr([
                    'data-visible' => function ($row) {
                        return $row->visible;
                    }
                ])
                // ->removeColumn('id')
                ->removeColumn('is_closed')
                ->rawColumns(['action', 'balance', 'name', 'account_group', 'reconcile_status'])
                ->make(true);
        }

        $not_linked_payments = TransactionPayment::leftjoin(
            'transactions as T',
            'transaction_payments.transaction_id',
            '=',
            'T.id'
        )
            ->whereNull('transaction_payments.parent_id')
            ->where('transaction_payments.business_id', $business_id)
            ->whereNull('account_id')
            ->count();
        $account_type_query = AccountType::where('business_id', $business_id)
            ->whereNull('parent_account_type_id');
        $account_types_opts = $account_type_query->pluck('name', 'id');
        $account_type_query->with(['sub_types']);
        if ($account_access == 0) {
            $account_type_query->where(function ($q) {
                $q->where('name', 'Assets')->orWhere('name', 'Liabilities');
            });
        }
        $account_types = $account_type_query->get();
        // dd($account_types->toArray());
        $filterdata = [];
        $sub_acn_arr = [];
        $filterdata['subType_']['data'][] = array('id' => "", 'text' => "All", true);
        foreach ($account_types->toArray() as $acunts) {
            $filterdata['subType_' . $acunts['id']]['data'][] = array('id' => "", 'text' => "All", true);
            foreach ($acunts['sub_types'] as $sub_Acn) {
                $filterdata['subType_']['data'][] = array('id' => $sub_Acn['id'], 'text' => $sub_Acn['name']);
                $filterdata['subType_' . $acunts['id']]['data'][] = array('id' => $sub_Acn['id'], 'text' => $sub_Acn['name']);
                $sub_acn_arr[$sub_Acn['id']] = $sub_Acn['name'];
            }
        }
        // echo "<pre>";print_r($filterdata);
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $account_groups_raw = AccountGroup::where('business_id', $business_id)->get()->toArray();
        $account_groups = [];
        $filterdata['groupType_']['data'][] = array('id' => "", 'text' => "All", true);
        foreach ($account_groups_raw as $datarow) {
            $filterdata['groupType_' . $datarow['account_type_id']]['data'][] = array('id' => $datarow['id'], 'text' => $datarow['name']);
            $account_groups[$datarow['id']] = $datarow['name'];
        }
        // dd($filterdata);
        $accounts = Account::where('business_id', $business_id)->pluck('name', 'id');
        $users = User::forDropdown($business_id);
        $orderStatuses = $this->productUtil->orderStatuses();
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $customers = Contact::customersDropdown($business_id, false);
        $chequeId = $this->transactionUtil->account_exist_return_id('Cheques in Hand');

        $can_edit_ob = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'edit_ob');


        $this->updateOBs();
        $this->updateLoans();

        $permitted_locations = auth()->user()->permitted_locations();

        if ($permitted_locations == 'all') {
            $_business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
            $_business_locations->prepend(__('messages.all'), 'all');
        } else {
            $_business_locations = BusinessLocation::where('business_id', $business_id)->whereIn('id', $permitted_locations)->pluck('name', 'id');
            $_business_locations->prepend(__('messages.all'), '');
        }

        return view('account.index')
            ->with(compact('_business_locations', 'can_edit_ob', 'chequeId', 'customers', 'parentAccounts', 'filterdata', 'account_types_opts', 'sub_acn_arr', 'not_linked_payments', 'account_types', 'account_access', 'business_locations', 'account_groups', 'users', 'accounts', 'suppliers', 'orderStatuses'));
    }
    /**
     * Show the form for creating a new resource.
     * @return Response
     */


    public function updateOBs()
    {
        $business_id = session()->get('user.business_id');
        $opening_balances = Transaction::where('type', 'opening_balance')->where('business_id', $business_id)->whereNotNull('contact_id')->get();
        $opening_balance_equity_id = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');

        foreach ($opening_balances as $bal) {
            $contact = Contact::find($bal->contact_id);

            if (!empty($contact)) {
                if ($contact->type == 'customer') {
                    $type = 'credit';
                } else {
                    $type = 'debit';
                }

                $account_transaction_data = [
                    'amount' => $bal->final_total,
                    'account_id' => $opening_balance_equity_id,
                    'type' => $type,
                    'sub_type' => 'ledger_show',
                    'operation_date' => $bal->transaction_date,
                    'created_by' => $bal->created_by,
                    'transaction_id' => $bal->id
                ];

                $id = AccountTransaction::updateOrCreate(['account_id' => $opening_balance_equity_id, 'transaction_id' => $bal->id], $account_transaction_data);
            }
        }
    }

    public function updateLoans()
    {
        $business_id = session()->get('user.business_id');
        $opening_balances = Transaction::where('sub_type', 'loan_payment')->where('business_id', $business_id)->get();
        $cash = $this->transactionUtil->account_exist_return_id('Cash');


        foreach ($opening_balances as $bal) {
            $account_transaction_data = [
                'amount' => $bal->final_total,
                'account_id' => $cash,
                'type' => 'debit',
                'operation_date' => $bal->transaction_date,
                'created_by' => $bal->created_by,
                'transaction_id' => $bal->id
            ];

            $id = AccountTransaction::updateOrCreate(['account_id' => $cash, 'transaction_id' => $bal->id, 'type' => 'debit'], $account_transaction_data);

            $account_transaction_data['type'] = 'credit';
            $id = AccountTransaction::updateOrCreate(['account_id' => $cash, 'transaction_id' => $bal->id, 'type' => 'credit'], $account_transaction_data);
        }
    }


    public function create()
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = session()->get('user.business_id');
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        $account_type_query = AccountType::where('business_id', $business_id)
            ->whereNull('parent_account_type_id')
            ->with(['sub_types']);
        if ($account_access == 0) {
            $account_type_query->whereIn('name', ['Assets', 'Liabilities']);
        }
        $account_types = $account_type_query->get();
        $account_groups = AccountGroup::where('business_id', $business_id)->pluck('name', 'id');
        $accounts = Account::where('business_id', $business_id)->pluck('name', 'id');
        $asset_type_ids = json_encode(AccountType::getAccountTypeIdOfType('Assets', $business_id));
        $parentAccounts = Account::where(['is_main_account' => 1, 'business_id' => $business_id])->pluck('name', 'id', 'asset_type');

        $parentAccountsData = Account::where(['is_main_account' => 1, 'business_id' => $business_id])->get()->toArray();
        $fixed_acc_id = AccountType::getAccountTypeIdOfType('Fixed Assets', $business_id);

        $permitted_locations = auth()->user()->permitted_locations();

        if ($permitted_locations == 'all') {
            $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
            $business_locations->prepend(__('messages.all'), 'all');
        } else {
            $business_locations = BusinessLocation::where('business_id', $business_id)->whereIn('id', $permitted_locations)->pluck('name', 'id');
            $business_locations->prepend(__('lang_v1.please_select'), '');
        }

        $fixed_acc_id = !empty($fixed_acc_id) ? $fixed_acc_id[0] : 0;

        // modified by iftekhar
        return view('account.create')
            ->with(compact('fixed_acc_id', 'account_types', 'account_groups', 'asset_type_ids', 'accounts', 'parentAccounts', 'parentAccountsData', 'business_locations'));
    }
    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            try {

                $business_id = $request->session()->get('user.business_id');

                $check = Account::where('business_id', $business_id)->where('account_number', $request->account_number)->first();
                if (!empty($check)) {
                    $output = [
                        'success' => false,
                        'msg' => __('lang_v1.account_number_added_already')
                    ];

                    return $output;
                }


                $input = $request->only(['name', 'account_number', 'note', 'account_type_id', 'asset_type', 'is_main_account', 'is_need_cheque', 'show_in_balance_sheet', 'is_property', 'location_id']);
                $user_id = $request->session()->get('user.id');
                $input['is_main_account'] = !empty($input['is_main_account']) ? $input['is_main_account'] : 0;
                $input['show_in_balance_sheet'] = empty($input['is_main_account'])  ? $input['show_in_balance_sheet'] : 0;

                $input['business_id'] = $business_id;
                $input['created_by'] = $user_id;
                $input['visible'] = 1;
                $asset_type_ids = AccountType::getAccountTypeIdOfType('Assets', $business_id);
                if (empty($input['asset_type'])) {
                    $input['asset_type'] = null;
                }
                if (!empty($request->sub_type)) {
                    $input['parent_account_id'] = $request->parent_account_id;
                }
                $account = Account::create($input);
                //Opening Balance
                $opening_bal = $request->input('opening_balance');
                if (!empty($opening_bal)) {
                    $account_type_name = AccountType::where('id', $input['account_type_id'])->first();
                    $type = 'debit';
                    if (strpos($account_type_name, "Assets") !== false || strpos($account_type_name, "Expenses") !== false) {
                        if ($opening_bal >= 0) {
                            $type = 'debit';
                        } else {
                            $type = 'credit';
                        }
                    } else {
                        if ($opening_bal >= 0) {
                            $type = 'credit';
                        } else {
                            $type = 'debit';
                        }
                    }
                    $ob_transaction_data = [
                        'amount' => abs($this->commonUtil->num_uf($opening_bal)),
                        'account_id' => $account->id,
                        'type' => $type,
                        'sub_type' => 'opening_balance',
                        'operation_date' => \Carbon::now(),
                        'created_by' => $user_id
                    ];
                    AccountTransaction::createAccountTransaction($ob_transaction_data);
                }
                $output = [
                    'success' => true,
                    'msg' => __("account.account_created_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $output;
        }
    }

    public function corectStockAccounts()
    {
        $this->transactionUtil->corectStockAccounts();
    }

    public function transferPostDatedCheques()
    {
        $this->transactionUtil->transferPostDatedCheques();
    }

    public function restorePayments()
    {
        echo "Updating security Deposits<br>";
        $this->transactionUtil->__correctSecurityDeposits();
        echo "<br>------------------------------<br>";
        echo "Updating Customer Payments<br>";
        $this->transactionUtil->__correctCustomerPayments();
        echo "<br>------------------------------<br>";
        echo "Updating Purchase Payments<br>";
        $this->transactionUtil->__correctPurchasePayments();
        echo "<br>------------------------------<br>";
        echo "Updating Expenses<br>";
        $this->transactionUtil->__correctExpenses();
    }

    public function restoreSettlementPayments()
    {
        echo "Updating Sell Payments <br>";
        $this->transactionUtil->__correctSellPayments();
        echo "<br>------------------------------<br>";
        echo "Updating Settlement Payments<br>";
        $this->transactionUtil->__correctSettlement();
        echo "<br>------------------------------<br>";
    }


    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id, Request $request)
    {

        $isIframe = $request->query('is_iframe');

        $is_iframe = !empty($isIframe) ? $isIframe : 0;

        // return $acount_balance_pre = Account::getAccountBalance($id, '2022-12-01', '2022-12-31', true, true, false);
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        // Session::flush();
        $business_id = request()->session()->get('user.business_id');
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        $banking_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'banking_module');
        $card_account_id = $this->transactionUtil->account_exist_return_id('Cards (Credit Debit) Account');
        $cheque_return_account_id = $this->transactionUtil->account_exist_return_id('Cheque Return Income');
        $card_group_id = AccountGroup::getGroupByName('Card', true);
        $bank_group_id = AccountGroup::getGroupByName('Bank Account', true);
        $cheque_in_hand_group_id = AccountGroup::getGroupByName("Cheques in Hand (Customer's)", true);
        $card_type_accounts = Account::where('business_id', $business_id)->where('asset_type', $card_group_id)->where(DB::raw("REPLACE(`name`, '  ', ' ')"), '!=', 'Cards (Credit Debit) Account')->pluck('name', 'id');
        $cheque_numbers = Transaction::chequeNumberDropDown('sell');
        $slipNos = AccountTransaction::whereNotNull('slip_no')
            ->where('business_id', $business_id)
            ->distinct('slip_no')
            ->pluck('slip_no');

        $slip_no = array();
        foreach ($slipNos as $one) {
            $slip_no[$one] = $one;
        }

        $slipNos = $slip_no;
        
        
        /**
         * @ModifiedBy : Afes Oktavianus
         * @DateBy : 02-06-2021
         * @Task : 3340
         */
        $customers = Contact::customersDropdown($business_id, false);
        $suppliers = Contact::suppliersDropdown($business_id, false);
        $this_account = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->leftjoin('account_types', 'accounts.account_type_id', 'account_types.id')
            ->where('accounts.business_id', $business_id)
            ->select('accounts.*', 'account_groups.name as group_name')
            ->with(['account_type', 'account_type.parent_account'])
            ->where('accounts.id', $id)->first();
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        if (!$account_access) {
            if ($this_account->group_name == 'COGS Account Group' || $this_account->account_type->name == "Income" || $this_account->account_type->name == "Fixed Assets" || $this_account->account_type->name == "Equity" || strpos($this_account->account_type->name, "Liabilities") !== false) {
                $account_access = 0;
            } else {
                $account_access = 1;
            }
        }

        if (request()->ajax()) {
            // dd($request->all());
            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

try{
    
            $is_iframe = request()->input('is_iframe');
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');
            Session::forget('account_balance'); // forget value if previously store in it
            $acount_balance_pre = Account::getAccountBalance($id, $start_date, $end_date, true, true, false);

            Session::put('account_balance', $acount_balance_pre);

            $realize_cheque = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'realize_cheque');


            $accounts = AccountTransaction::join(
                'accounts as A',
                'account_transactions.account_id',
                '=',
                'A.id'
            )
            ->leftjoin('transaction_payments AS TP', function ($join) {
                $join->on('TP.id', '=', 'account_transactions.transaction_payment_id');
                })
                ->leftJoin('users AS u', 'account_transactions.created_by', '=', 'u.id')
                ->leftjoin(
                    'account_types as ats',
                    'A.account_type_id',
                    '=',
                    'ats.id'
                )->where('A.id', $id);

            $accounts = $accounts->with(['transaction', 'transaction.contact', 'transfer_transaction'])->where('TP.deleted_at','=',NULL);
            
            $accounts = $accounts->where(function ($query) use ($realize_cheque) {
                $query->whereNull('account_transactions.transaction_payment_id')
                ->orWhere(function ($query2) use ($realize_cheque) {
                    $query2->whereNotNull('account_transactions.transaction_payment_id');
                });
            });

            if (!empty($start_date) && !empty($end_date)) {
                if (request()->date_based_on == 'transaction_date') {
                    $accounts = $accounts->where(function ($query) use ($start_date,$end_date) {
                        $query->whereBetween(DB::raw('date(operation_date)'), [$start_date, $end_date]);
                    });
                } else {
                    $accounts = $accounts->where(function ($query) use ($start_date,$end_date) {
                        $query->whereBetween(DB::raw('date(account_transactions.cheque_date)'), [$start_date, $end_date]);
                    });
                }
            }

            if (!empty(request()->input('type'))) {
                $accounts->where('type', request()->input('type'));
            }
            if (!empty(request()->input('card_type'))) {
                $accounts->where('TP.card_type', request()->input('card_type'));
            }
            
            if (!empty(request()->input('card_number'))) {
                $accounts->where('TP.card_number', request()->input('card_number'));
            }

            if (!empty(request()->input('cheque_number'))) {
                $accounts->where('TP.cheque_number', request()->input('cheque_number'));
            }

            if (!empty(request()->input('customer_cheque_no'))) {
                $accounts->where('TP.cheque_number', request()->input('customer_cheque_no'));
            }

            $customer = request()->input('customer');

            if (!empty($customer)) {
                $accounts->whereHas('transaction', function ($query) use ($customer) {
                    $query->where('contact_id', $customer);
                });
            }

            $supplier = request()->input('supplier');

            if (!empty($supplier)) {
                $accounts->whereHas('transaction', function ($query) use ($supplier) {
                    $query->where('contact_id', $supplier);
                });
            }


            if (!empty(request()->input('slip_no'))) {
                $accounts->where('account_transactions.slip_no', request()->input('slip_no'));
            }

            if (!empty(request()->amount)) {
                $accounts->where('TP.amount', request()->amount);
            }

           $accounts = $accounts->select([
                'type',
                'slip_no',
                'account_transactions.account_id',
                'account_transactions.related_account_id',
                'account_transactions.fixed_asset_id',
                'account_transactions.amount as amount',
                'account_transactions.interest',
                'account_transactions.reconcile_status',
                'account_transactions.sub_type as at_sub_type',
                'operation_date',
                'cheque_ref_no',
                'account_transactions.note',
                'journal_deleted',
                'account_transactions.deleted_by',
                'journal_entry',
                'account_transactions.sell_line_id',
                'account_transactions.income_type',
                'account_transactions.attachment',
                'account_transactions.cheque_number as dep_trans_cheque_number',
                'account_transactions.transaction_payment_id as tp_id',
                'TP.cheque_number',
                'TP.bank_name',
                'TP.cheque_date',
                'account_transactions.bank_name as acc_bank_name',
                'account_transactions.cheque_date',
                'TP.post_dated_cheque',
                'TP.card_type',
                'TP.card_number',
                'TP.method',
                'TP.payment_for',
                'TP.paid_on',
                'TP.payment_ref_no',
                'TP.account_id as bank_account_id',
                'TP.update_post_dated_cheque',
                'updated_type',
                'updated_by',
                'account_transactions.updated_at',
                'account_transactions.deleted_at',
                'A.name as account_name',
                'sub_type',
                'transfer_transaction_id',
                'ats.name as account_type_name',
                'account_transactions.transaction_id',
                'account_transactions.id',
                'account_transactions.pair_at_id',
                'account_transactions.auto_transfer',
                'account_transactions.txnType',
                'account_transactions.employee_advance_id',
                'account_transactions.created_at',

                DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
            ])
            ->withTrashed(); // Modified By iftekhar


            $business_details = Business::find($business_id);
            $currency_precision =  !empty($business_details) && !empty($business_details->currency_precision) ? $business_details->currency_precision : config('constants.currency_precision', 2);
            if (!$account_access) {
                $accounts = collect([]);
            }

            $accounts = $accounts->orderBy('account_transactions.operation_date', 'asc')->get();




            // Create a new collection to hold the modified accounts
            $modifiedAccounts = $accounts->flatMap(function ($row, $index) use ($business_id, $currency_precision,$id,$start_date,$end_date) {
                // Ensure $row has an id property
                $id = $row->id ?? null; // Fallback to null if id is missing
                // Prepare a new collection to hold modified rows
                
                $rowData = get_object_vars($row); // Get all existing properties
                $newRows = [];
                    if ($row->transaction) {
                        //dd($row->transaction->final_total);
                        $settlement = Settlement::where('settlement_no', $row->transaction->invoice_no)->where('business_id', $business_id)->first();
                        // Proceed only if settlementDate is set
                        if ($settlement) {
                            // Fetch transactions for the current row's settlement date

                            $transactions = DailyCollection::where('pump_operator_id', $settlement->pump_operator_id)->where('business_id', $business_id)->where('settlement_id', $settlement->id)->get();



                            // Add transactions as new rows
                            foreach ($transactions as $transaction) {

                                // Get existing properties of the row
                                // dump('abc',$transaction->balance_collection);
                                // Merge existing row data with new transaction data
                                $newRow = array_merge($rowData, [
                                    'id' => $id, // Ensure id is retained
                                    'type' => $row->type,
                                    'slip_no' => $transaction->collection_form_no ?? null, // Use transaction's slip number
                                    'account_id' => $transaction->id ?? null,
                                    'amount' => $transaction->balance_collection ?? null,
                                    'operation_date' => $transaction->settlement_date ?? null,
                                    'note' => $row->note,
                                    'added_by' => $transaction->created_by ?? null,
                                    'transaction' => null, // Explicitly set to null to avoid undefined property error
                                    'journal_deleted' => $row->journal_deleted ?? null,
                                    'sub_type' => $row->sub_type ?? null,
                                    'at_sub_type' => $row->at_sub_type ?? null,
                                    'payment_for' => $row->payment_for ?? null,
                                    'account_name' => $row->account_name ?? null,
                                    'account_type_name' => $row->account_type_name ?? null,
                                    'cheque_number' => $row->cheque_number ?? null,
                                    'reconcile_status' => $row->reconcile_status ?? null,
                                    'deleted_at' => null,
                                    'tp_id' => $row->tp_id ?? null,
                                    'txnType' => $row->txnType ?? null,
                                    'transaction' => Transaction::find($row->transaction_id)
                                ]);
                                $row->daily_collection = $newRow['amount'] ?? 0;
                                // dd($row);

                                // Convert the array back to an object
                                // $newRows[] = (object) $newRow; // Add new row as an object
                            }
                        }
                    }

                // Return the original row along with the new rows for each transaction
                // dd( collect([$row])->merge($newRows));
                // dd(collect([$row]));
                return collect([$row])->merge($newRows);
                return collect([$row]); // If no settlement date, return the original row
            });

            // dd($modifiedAccounts);
            
            return DataTables::of($modifiedAccounts)
                ->addColumn('opening_balance', function ($row) use ($currency_precision) {
                    $openingBalance = number_format(Session::get('account_balance'), $currency_precision, '.', '');
                    return  '<span class="display_currency" data-currency_symbol=true data-orig-value="' . $openingBalance . '" >' . $openingBalance . '</span>';
                })
                ->addColumn('debit', function ($row) use ($business_details, $currency_precision) {
                    // return $row->type;
                    $total_daily_collection = 0;
                    if (!empty($row->amount) && $row->type=="debit") {
                        // if($row->post_dated_cheque == 1 && !$row->sub_type == 'cheque_realize') return "";
                        // else{
                            // if (!is_null($row->transaction) && $row->transaction->type == 'stock_adjustment') {
                            //     if ($row->transaction->stock_adjustment_type == 'increase') {
                            //         return  '<span class="display_currency credit_col" data-currency_symbol=false data-orig-value="' . $row->amount . '" >' . $row->amount . '</span>'; // credit increase
                            //     } else {
                            //         return ''; // don't credit decrease
                            //     }
                            // }
                            $amount_with_discount = $row->amount;  // updated by branko

                            if (!is_null($row->transaction) && $row->account_id != $this->transactionUtil->account_exist_return_id('Finished Goods Account')) {
                                if (!is_null($row->transaction->discount_type) && $row->transaction->discount_type == "percentage") {
                                    if($row->transaction->type == 'hms_booking'){
                                        $amount_with_discount = $row->amount;
                                    }else{
                                        $amount_with_discount = $row->amount - $row->amount * $row->transaction->discount_amount / 100;
                                    }
                                } else if (!is_null($row->transaction->discount_type) && $row->transaction->discount_type == "fixed") {
                                    $sum_quantitly = DB::table('transaction_sell_lines')
                                        ->where('transaction_id', $row->transaction->id)
                                        ->sum('quantity');
                                    if(!is_null($sum_quantitly) && $sum_quantitly != 0){
                                        $amount_with_discount = $row->amount - $row->transaction->discount_amount / $sum_quantitly;
                                    }
                                } else {
                                    $amount_with_discount = $row->amount;
                                }
                            }
                            if (!is_null($row->transaction) && $row->transaction->type == "sell" && $row->transaction->sub_type == "settlement" && $row->account_id != $this->transactionUtil->account_exist_return_id('Finished Goods Account')) {
                                $sell_info = DB::table('transaction_sell_lines')->where('id', $row->sell_line_id)->first();
                                
                                if($sell_info):
                                    if ($sell_info->line_discount_type == "fixed") {
                                        $amount_with_discount = $row->amount - $sell_info->line_discount_amount;
                                    }
                                    if ($sell_info->line_discount_type == "percentage") {
                                        $amount_with_discount = $row->amount * (1 -  $sell_info->line_discount_amount / 100);
                                    }
                                endif;
                            }

                            return  '<span class="display_currency debit_col" data-currency_symbol=false data-orig-value="' . $amount_with_discount . '" >' . $amount_with_discount . '</span>';
                        // }
                    }else{
                        return "";
                    }
                })
                ->addColumn('credit', function ($row) use ($business_details, $currency_precision,$id,$start_date,$end_date) {
                    // if($row->post_dated_cheque== 1 && !$row->sub_type == 'cheque_realize'){
                    //     $credit = $row->amount;
                    //     // Session::put('daily_collection', $total_daily_collection);
                    //     return  '<span class="display_currency credit_col" data-currency_symbol=false data-orig-value="' . $credit . '" >' . $credit . '</span>';
                    // }else{
                        if (!empty($row->amount) && $row->type=="credit") {
                            // if (!is_null($row->transaction) && $row->transaction->type == 'stock_adjustment') {
                            //     if ($row->transaction->stock_adjustment_type == 'decrease') {
                            //         return  '<span class="display_currency credit_col" data-currency_symbol=false data-orig-value="' . $row->amount . '" >' . $row->amount . '</span>'; // credit increase
                            //     } else {
                            //         return ''; // don't credit decrease
                            //     }
                            // }
                            $amount_with_discount = $row->amount;  // updated by branko

                            if (!is_null($row->transaction) && $row->account_id != $this->transactionUtil->account_exist_return_id('Finished Goods Account')) {
                                if (!is_null($row->transaction->discount_type) && $row->transaction->discount_type == "percentage") {
                                    if($row->transaction->type == 'hms_booking'){
                                        $amount_with_discount = $row->amount;
                                    }else{
                                        $amount_with_discount = $row->amount - $row->amount * $row->transaction->discount_amount / 100;
                                    }
                                } else if (!is_null($row->transaction->discount_type) && $row->transaction->discount_type == "fixed") {
                                    $sum_quantitly = DB::table('transaction_sell_lines')
                                        ->where('transaction_id', $row->transaction->id)
                                        ->sum('quantity');
                                    if(!is_null($sum_quantitly) && $sum_quantitly != 0){
                                        $amount_with_discount = $row->amount - $row->transaction->discount_amount / $sum_quantitly;
                                    }
                                } else {
                                    $amount_with_discount = $row->amount;
                                }
                            }
                            if (!is_null($row->transaction) && $row->transaction->type == "sell" && $row->transaction->sub_type == "settlement" && $row->account_id != $this->transactionUtil->account_exist_return_id('Finished Goods Account')) {
                                $sell_info = DB::table('transaction_sell_lines')->where('id', $row->sell_line_id)->first();
                                if($sell_info):
                                    if ($sell_info->line_discount_type == "fixed") {
                                        $amount_with_discount = $row->amount - $sell_info->line_discount_amount;
                                    }
                                    if ($sell_info->line_discount_type == "percentage") {
                                        $amount_with_discount = $row->amount * (1 -  $sell_info->line_discount_amount / 100);
                                    }
                                endif;
                            }

                            return  '<span class="display_currency credit_col" data-currency_symbol=false data-orig-value="' . $amount_with_discount . '" >' . $amount_with_discount . '</span>';
                        }else{
                            return "";
                        }
                    // }
                })

                ->editColumn('cheque_date', function ($row) {
                    if (!empty($row->cheque_date)) {
                        return $this->commonUtil->format_date($row->cheque_date);
                    }
                })
                ->editColumn('cheque_number', function ($row) use ($business_details) {
                    
                    $transaction_payment = $row->transaction 
                    ? TransactionPayment::where('transaction_id', $row->transaction->id)->first() 
                    : null;
                        if (!empty($transaction_payment)) {
                            return $transaction_payment->cheque_number;
                        }
                    
                   

                    if ($row->sub_type == 'deposit') {
                        $tp = TransactionPayment::find($row->tp_id);
                        if (!empty($tp)) {
                            return $tp->cheque_number;
                        } else {
                            return '';
                        }
                    }
                    return $row->cheque_number;
                })
                ->editColumn('balance', function ($row) use ($business_details, $currency_precision, &$i) {
                    $balance = Session::get('account_balance');
                    if (empty($row->deleted_at)) {
                        if (strpos($row->account_type_name, "Assets") !== false || strpos($row->account_type_name, "Expenses") !== false) { // @eng 13/2


                            // $daily_collection = Session::get('daily_collection');
                            if ($row->type == 'credit') {
                                if (!is_null($row->transaction) && $row->transaction->type == 'stock_adjustment') {
                                    if ($row->transaction->stock_adjustment_type == 'decrease') {
                                        $debit = /*$daily_collection + */ $row->amount;
                                        $balance = $balance +  number_format($debit, $currency_precision, '.', ''); // perform debit calculation on decrease stock adjustment
                                    } else {
                                        $balance = $balance -  number_format($row->amount, $currency_precision, '.', '');
                                    }
                                } else {
                                    $balance = $balance -  number_format($row->amount, $currency_precision, '.', '');
                                }
                            }
                            if ($row->type == 'debit') {
                                $debit = /*$daily_collection + */ $row->amount;
                                $balance = $balance +  number_format($debit, $currency_precision, '.', '');
                            }
                        } elseif (strpos($row->account_type_name, "Income") !== false  || strpos($row->account_type_name, "Equity")  !== false || strpos($row->account_type_name, "Liabilities") !== false) { // @eng 13/2

                            if ($row->type == 'credit') {
                                if (!is_null($row->transaction) && $row->transaction->type == 'stock_adjustment') {
                                    if ($row->transaction->stock_adjustment_type == 'decrease') {
                                        $balance = $balance - number_format($row->amount, $currency_precision, '.', ''); // perform debit calculation on decrease stock adjustment
                                    } else {
                                        $balance = $balance +  number_format($row->amount, $currency_precision, '.', '');
                                    }
                                } else {
                                    $amount_with_discount = $row->amount;  // updated by branko

                                    if (!is_null($row->transaction) && $row->account_id != $this->transactionUtil->account_exist_return_id('Finished Goods Account')) {
                                        if (!is_null($row->transaction->discount_type) && $row->transaction->discount_type == "percentage") {
                                            $amount_with_discount = $row->amount - $row->amount * $row->transaction->discount_amount / 100;
                                        } else if (!is_null($row->transaction->discount_type) && $row->transaction->discount_type == "fixed") {
                                            $sum_quantitly = DB::table('transaction_sell_lines')
                                                ->where('transaction_id', $row->transaction->id)
                                                ->sum('quantity');
                                            if(!is_null($sum_quantitly) && $sum_quantitly != 0){
                                                $amount_with_discount = $row->amount - $row->transaction->discount_amount / $sum_quantitly;
                                            }
                                        } else {
                                            $amount_with_discount = $row->amount;
                                        }
                                    }
                                    if (!is_null($row->transaction) && $row->transaction->type == "sell" && $row->transaction->sub_type == "settlement" && $row->account_id != $this->transactionUtil->account_exist_return_id('Finished Goods Account')) {
                                        $sell_info = DB::table('transaction_sell_lines')->where('id', $row->sell_line_id)->first();
                                        if($sell_info):
                                            if ($sell_info->line_discount_type == "fixed") {
                                                $amount_with_discount = $row->amount - $sell_info->line_discount_amount;
                                            }
                                            if ($sell_info->line_discount_type == "percentage") {
                                                $amount_with_discount = $row->amount * (1 -  $sell_info->line_discount_amount / 100);
                                            }
                                        endif;
                                    }
                                    // if (!is_null($row->transaction) && $row->transaction->type == "sell" && $row->transaction->sub_type == "settlement" && $row->account_id == $this->transactionUtil->account_exist_return_id('Finished Goods Account')) {

                                    // }
                                    $balance = $balance +  number_format($amount_with_discount, $currency_precision, '.', '');
                                }
                            }
                            if ($row->type == 'debit') {
                                $balance = $balance - number_format($row->amount, $currency_precision, '.', '');
                            }
                        }
                    }

                    Session::put('account_balance', $balance);
                    return '<span class="display_currency" data-currency_symbol="true">' . $this->productUtil->num_f($balance, false, $business_details, true) . '</span>';
                })
                ->editColumn('operation_date', function ($row) {
                    // if (($row->update_post_dated_cheque == 1 || $row->post_dated_cheque == 1) && !empty($row->cheque_date)) {
                    // return $this->commonUtil->format_date($row->cheque_date, false);
                    // } else {
                    return $this->commonUtil->format_date($row->operation_date, false); // Modified By iftekhar
                    // }
                })
                ->addColumn('description', function ($row) use ($business_id, $id, $currency_precision,$start_date,$end_date) {
                    $details = '';
                   
                    if (!empty($row->fixed_asset_id)) {
                        $fixed_asset = \App\FixedAsset::find($row->fixed_asset_id);
                        $details .= !empty($fixed_asset) ? "<b>Asset: </b>" . $fixed_asset->asset_name . "<br><b>Location: </b>" . $fixed_asset->asset_location : '';
                    }

                    if ($row->transaction) {
                        $transaction_sell_line = TransactionSellLine::leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')->where('transaction_id', $row->transaction->id)->first();
                    }
                    // return $row->employee_advance_id;
                    if($row->txnType=='advance' && $row->transaction_id == null && $row->employee_advance_id != null):
                        // return 1;
                        $chkAdvance = AccountTransaction::join('essentials_employee_advances','account_transactions.employee_advance_id','essentials_employee_advances.id')
                        ->join('essentials_employee_payment_settings','essentials_employee_advances.payment_type_id','essentials_employee_payment_settings.id')
                        ->where('account_transactions.id',$row->id)
                        ->select([
                            'essentials_employee_advances.datetime_entered',
                            'essentials_employee_advances.payment_type_id',
                            'essentials_employee_advances.salary_period_start',
                            'essentials_employee_advances.salary_period_end',
                            'essentials_employee_advances.amount_paid',
                            'essentials_employee_advances.payment_status',
                            'essentials_employee_advances.account_id',
                            'essentials_employee_advances.check_no',
                            'essentials_employee_payment_settings.name as EPS_name',
                            'essentials_employee_payment_settings.liability_account_id as EPS_LA_id',
                            'essentials_employee_payment_settings.id as EPS_id'
                        ])->first();
                        if($chkAdvance):
                                $details .= '<span style="color: #ff0000;">Payment Type:</span>' . $chkAdvance->EPS_name . '<br>';
                                $details .= '<span style="color: #ff0000;">Salary Period:</span>' . $start_date .' - '. $end_date. '<br>';
                                
                            if($row->type=='debit'):
                                $liableAccount = Account::find($chkAdvance->account_id);
                                if($liableAccount):
                                    $details .= '<span style="color: #ff0000;">Payment Methods: </span>' . $liableAccount->name . '<br>';
                                else:
                                    $details .= '<span style="color: #ff0000;">Payment Method: </span>' . $row->account_name . '<br>';
                                endif;
                            else:
                                $details .= '<span style="color: #ff0000;">Liability or Expense Account: </span>' . $row->account_name . '<br>';
                            endif;
                        endif;
                    else:
                       
                        if ($row->transaction && $row->transaction->type == 'property_purchase') {

                            $property = Property::leftjoin('contacts', 'properties.supplier_id', 'contacts.id')
                                ->where('properties.transaction_id', $row->transaction->id)
                                ->select(['contacts.name as supplier_name', 'properties.name as property_name'])
                                ->first();

                            $details .= '<b>PO Number:</b>' . $row->transaction->invoice_no . '<br>';
                            $details .= '<b>Supplier Name:</b>' . $property->supplier_name . '<br>';
                            $details .= '<b>Property Name:</b>' . $property->property_name . '<br>';
                            if ($row->transaction->accountTransection) {
                                $details .= '<br>' . 'Payment Method:' . str_replace('_', ' ', $row->transaction->accountTransection->payment_method);
                                if ($row->transaction->accountTransection->payment_method == 'cheque') {
                                    $details .= '<br> Cheque No:' . str_replace('_', ' ', $row->transaction->accountTransection->cheque_numbers);
                                }
                            }
                        } 
                        elseif ( !empty($row->transaction->type) &&  $row->transaction->type == 'hms_booking'){
                                              \Log::info("typess: {$row->transaction->type}");
                                            $details .= '<b>HMS Bill No.:</b>' . $row->transaction->ref_no . '<br>';
                                            $details .= '<b>' . __('contact.customer') . ':</b> ' . $row->transaction->contact->name . '<br><b>';
                                        }
                        elseif (empty($row->transaction->pump_operator_id)  ||  ($row->transaction->sub_type == 'credit_sale') || $row->transaction->sub_type == 'expense' || $row->transaction->sub_type == 'settlement') {

                            if ($row->journal_deleted == 0) {
                                if (in_array($row->sub_type, ['fund_transfer', 'cheque_realize', 'deposit', 'payable', 'stock', 'opening_balance']) && ((!empty($row->transaction) && $row->transaction->is_settlement != 1) || empty($row->transaction))) {
                                    if (in_array($row->sub_type, ['cheque_realize']) && !empty($row->transfer_transaction)) {

                                        //update font color by virtual it professional referance docs number 7338
                                        if ($row->type == 'credit') {
                                            $details = "Cheque to Realize from <span style='color: red; font-weight:bold'>Post Dated Cheques Account</span>";
                                        } else {
                                            $details = "Cheque to Realize from <span style='color: red;font-weight:bold'>Post Dated Cheques Account</span>";
                                        }
                                    }
                                    if (in_array($row->sub_type, ['deposit']) && !empty($row->transfer_transaction)) {

                                        $cash_account_id = Account::getAccountByAccountName('Cash')->id;

                                        if ($row->account_id == $cash_account_id && !empty($row->cheque_number)) {

                                            $details .=  __('cheque.cheque_number') . ': ' . $row->cheque_number . "<br><b>" . __('account.encashed') . "</b>";
                                        } else {
                                            $details = __('account.' . $row->sub_type);
                                            if ($row->type == 'credit') {

                                                $details .= ' ( ' . __('account.to') . ': ' . $row->transfer_transaction->account->name . ')';
                                            } else {
                                                $details .= ' ( ' . __('account.from') . ': ' . $row->transfer_transaction->account->name . ')';
                                            }
                                            if (!empty($row->payment_for)) {
                                                $contact = Contact::find($row->payment_for);
                                                if (!empty($contact)) {
                                                    $details .= '<br><b>' . __('fleet::lang.customer') . ':</b>' . $contact->name;
                                                }
                                            }
                                            if ($row->post_dated_cheque == 1) {
                                                $details .= '<br><b>PD Cheque Dates </b>' . $row->cheque_date;
                                            }
                                            $cheque_number = !empty($row->dep_trans_cheque_number) ? $row->dep_trans_cheque_number : $row->cheque_number;
                                            $details .= '<br>' . __('cheque.cheque_number') . ': ' . $cheque_number;
                                        }
                                    }
                                    if (in_array($row->sub_type, ['fund_transfer']) && !empty($row->transfer_transaction)) {
                                        if (!empty($row->auto_transfer)) {
                                            if (!empty($row->payment_for)) {
                                                $contact = Contact::find($row->payment_for);
                                                if (!empty($contact)) {
                                                    $details .= '<b>' . __('fleet::lang.customer') . ':</b>' . $contact->name;
                                                }
                                            }
                                            if ($row->type == 'credit') {
                                                $details .= '<br><span style="color: red;">Auto Transferred to Account ' . $row->transfer_transaction->account->name . '</span>';
                                            } else {
                                                $details .= '<br><span style="color: red;"><b>Post Dated Cheque of</b> ' . $this->commonUtil->format_date($row->operation_date) . '</span>';
                                            }
                                        } else {
                                            $details = __('account.' . $row->sub_type);
                                            if ($row->type == 'credit') {

                                                $details .= ' ( ' . __('account.to') . ': ' . $row->transfer_transaction->account->name . ')';
                                            } else {
                                                $details .= ' ( ' . __('account.from') . ': ' . $row->transfer_transaction->account->name . ')';
                                            }
                                            if (!empty($row->payment_for)) {
                                                $contact = Contact::find($row->payment_for);
                                                if (!empty($contact)) {
                                                    $details .= '<br><b>' . __('fleet::lang.customer') . ':</b>' . $contact->name;
                                                }
                                            }
                                            $details .= '<br>' . __('cheque.cheque_number') . ': ' . $row->dep_trans_cheque_number;
                                        }
                                    }
                                    if (in_array($row->sub_type, ['payable', 'stock'])) {
                                        $details .= '<b>' . __('purchase.supplier') . ':</b> ' . $row->transaction->contact->name . '<br><b>' .
                                            __('purchase.ref_no') . ':</b> ' . $row->transaction->ref_no;
                                    }
                                    if (in_array($row->sub_type, ['opening_balance'])) {
                                        $details .= '<b class="text-danger">' . __('account.opening_balance') . '</b><br>';
                                        if ($row->account_name == 'Opening Balance Equity Account' && !empty($row->pair_at_id)) {
                                            $pair_at = AccountTransaction::find($row->pair_at_id);
                                            if (!empty($pair_at->account_id)) {
                                                $account = Account::find($pair_at->account_id);
                                                $details .= '<b>' . __('account.loan_account') . '</b>:' . $account->name;
                                            }
                                        }

                                        if (!empty($row->payment_for)) {
                                            $contact = Contact::find($row->payment_for);
                                            if (!empty($contact)) {
                                                $details .= '<br><b>' . __('fleet::lang.customer') . ':</b>' . $contact->name;
                                            }
                                        }
                                    }
                                } else {
                                    if (!empty($row->transaction->type)) {
                                        $transaction_payment = $row->transaction 
                                        ? TransactionPayment::where('transaction_id', $row->transaction->id)->first() 
                                        : null;                                   
                                        if ($row->transaction->type == 'purchase') {
                                            if ($row->at_sub_type == 'purchase_edit') {
                                                $details = '<span style="color: red;">Payee ' . $row->transaction->contact->name . '</span>';
                                            } else {
                                                if ($row->account_name == 'Taxes Receivable') {
                                                    $label = "VAT";
                                                } else {
                                                    $label = __('lang_v1.purchase');
                                                }
                                                $details = '<b>' . $label . '</b><br> ' . '<b>' .
                                                    __('purchase.supplier') . ':</b> ' . $row->transaction->contact->name . '<br><b>' .
                                                    __('purchase.ref_no') . ':</b> ' . $row->transaction->ref_no . '<br><b>' .
                                                    __('purchase.purchase_order') . ':</b> ' . $row->transaction->invoice_no . '<br>';
                                                    if ($transaction_payment && $transaction_payment->method == 'cheque') {
                                                        $details .= '<b>' . __('cheque.cheque_number') . ':</b> ' . $transaction_payment->cheque_number . '<br>';
                                                    } elseif ($row->method == 'bank_transfer') {
                                                    //$details .=    __('lang_v1.bank_transfer') . '</b> <br>' . __('cheque.cheque_number') . ':</b> ' . $row->cheque_number . '<br>' .
                                                        __('cheque.cheque_date') . ': ' . $this->transactionUtil->format_date($row->cheque_date);
                                                } elseif ($row->method == 'bank_transfer') {
                                                    $details .=    __('lang_v1.bank_transfer') . '</b> <br>' . __('cheque.cheque_number') . ':</b> ' . $row->cheque_number . '<br>' .
                                                        __('cheque.cheque_date') . ': ' . $this->transactionUtil->format_date($row->cheque_date);
                                                }
                                                else {
                                                    
                                                    $details .=   ucfirst($row->method);
                                                }
                                                if ($transaction_payment && $transaction_payment->method == 'pre_payments') {
                                                    $details .= '<b><span style="color: red;">' . __('Prepayment') . '</span></b> <br>';
                                                    }
                                                else {
                                                    $details .=   ucfirst($row->method);
                                                }
                                                $account_group = AccountGroup::where('business_id', $business_id)->where('id', $row->asset_type)->first();
                                                if (!empty($account_group) && $account_group->name == 'Inventory') {
                                                    $purchase_line = PurchaseLine::leftjoin('products', 'purchase_lines.product_id', 'products.id')->where('transaction_id', $row->transaction->id)->first();
                                                    $details .= 'Product: ' . $purchase_line->name;
                                                }
                                            }
                                        } 
                                        elseif ($row->transaction->type == 'cheque') {
                                            if ($row->at_sub_type == 'purchase_edit') {
                                            // $details = '<span style="color: red;">Payee ' . $row->transaction->contact->name . '</span>';
                                            } else {
                                                if ($row->account_name == 'Taxes Receivable') {
                                                    $label = "VAT";
                                                } else {
                                                    $label = __('lang_v1.purchase');
                                                }
                                                $details = '<b>' . '' . '</b> ' . '<b>' .
                                                __('Payee Name') . ':</b> ' . ($row->transaction->contact->name ?? 'N/A') . '<br><b>'.
                                                    __('Bank Name') . ':</b> ' .$transaction_payment ->bank_name . '<br><b>';
                                                if ($transaction_payment && $transaction_payment->method == 'cheque') {
                                                        $details .= '<b>' . __('cheque.cheque_number') . ':</b> ' . $transaction_payment->cheque_number . '<br>';
                                                    } elseif ($row->method == 'bank_transfer') {
                                                        __('cheque.cheque_date') . ': ' . $this->transactionUtil->format_date($row->cheque_date);
                                                } elseif ($row->method == 'bank_transfer') {
                                                    $details .=    __('lang_v1.bank_transfer') . '</b> <br>' . __('cheque.cheque_number') . ':</b> ' . $row->cheque_number . '<br>' .
                                                        __('cheque.cheque_date') . ': ' . $this->transactionUtil->format_date($row->cheque_date);
                                                }
                                                
                                                if ($transaction_payment && $transaction_payment->method == 'pre_payments') {
                                                    $details .= '<b><span style="color: red;">' . __('Prepayment') . '</span></b> <br>';
                                                    }
                                                else {
                                                    $details .=   ucfirst($row->method);
                                                }
                                                
                                            }
                                        } 
                                        elseif ($row->transaction->type == 'purchase_return') {
                                            $details = '<b>' . __('lang_v1.purchase_return') . '</b><br> ' . '<b>' .
                                                __('purchase.supplier') . ':</b> ' . $row->transaction->contact->name . '<br><b>' .
                                                __('purchase.ref_no') . ':</b> ' . $row->transaction->ref_no . '<br><b>';
                                            if ($row->method == 'cheque') {
                                                //  $details .=    __('cheque.cheque_number') . ':</b> ' . $row->cheque_number . '<br><b>' .
                                                __('cheque.cheque_date') . ':</b> ' . $this->transactionUtil->format_date($row->cheque_date);
                                            } else {
                                                $details .=   ucfirst($row->method);
                                            }
                                        } elseif ($row->transaction->type == 'sell' && $row->transaction->is_settlement != 1) {
                                            if ($row->transaction->is_direct_sale) {
                                                if ($row->account_name == "Taxes Payable") {
                                                    $details = '<b>Tax</b><br> ';
                                                } else {
                                                    $details = '<b>' . __('lang_v1.invoice_sale') . '</b><br> ';
                                                }

                                                $details .= '<b>' . __('contact.customer') . ':</b> ' . $row->transaction->contact->name . '<br><b>';
                                            } else {
                                                if ($row->account_name == "Taxes Payable") {
                                                    $details = '<b>Tax</b><br> ';
                                                } else {
                                                    $details = '<b>' . __('lang_v1.pos_sale') . '</b><br> ';
                                                }


                                                $details .= '<b>' . __('contact.customer') . ':</b> ' . $row->transaction->contact->name . '<br><b>';
                                            }
                                            if ($row->transaction->is_settlement != 1) {
                                                $details .= __('sale.invoice_no') . ':</b> ' . $row->transaction->invoice_no;
                                            }
                                            $account_group = AccountGroup::where('business_id', $business_id)->where('id', $row->asset_type)->first();
                                            if (!empty($account_group) && $account_group->name == 'Inventory') {
                                                $transaction_sell_line = TransactionSellLine::leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')->where('transaction_id', $row->transaction->id)->first();
                                                $details .= '<br>Product: ' . $transaction_sell_line->name;
                                            }
                                        } elseif ($row->transaction->type == 'opening_stock' && $row->imported == 1) {
                                            $details = 'Opening Stock <br> <b>Date:</b> ' . $this->commonUtil->format_date($row->transaction->transaction_date);
                                            $purchase_line = PurchaseLine::leftjoin('products', 'purchase_lines.product_id', 'products.id')->where('transaction_id', $row->transaction->id)->first();
                                            if (!empty($purchase_line)) {
                                                $details .= '<br>Product: ' . $purchase_line->name;
                                            }
                                        } elseif ($row->transaction->type == 'expense') {
                                            if ($row->account_name == "Taxes Receivable") {
                                                $expense_cat = \App\ExpenseCategory::find($row->transaction->expense_category_id);
                                                $details .= '<b>VAT Tax <br></b> ' . (!empty($expense_cat) ? $expense_cat->name : "") . '<br>';
                                            } else {
                                                $details .= 'Expense <br> <b>Ref:</b> ' . $row->transaction->ref_no . '<br>';
                                            }

                                            if ($row->method == 'cheque') {
                                                // $details .=    __('cheque.cheque_number') . ':</b> ' . $row->cheque_number . '<br><b>' .
                                                __('cheque.cheque_date') . ':</b> ' . $this->transactionUtil->format_date($row->cheque_date);
                                            } elseif ($row->method == 'bank_transfer') {
                                                $details .=    __('lang_v1.bank_transfer') . '</b> <br>' . __('cheque.cheque_number') . ':</b> ' . $row->cheque_number . '<br>' .
                                                    __('cheque.cheque_date') . ': ' . $this->transactionUtil->format_date($row->cheque_date);
                                            } else {
                                                $details .=   ucfirst($row->method);
                                            }
                                        } elseif ($row->transaction->type == 'opening_balance') {
                                            $contact = Contact::where('id', $row->transaction->contact_id)->first();
                                            if (!is_null($contact) && $contact->type == 'supplier') {
                                                $details = '<b>' . __('purchase.supplier') . ':</b> ' . $row->transaction->contact->name . '<br><b>' . 'Supplier Opening Balance <br> </b>' . __('purchase.ref_no') . ': ' . $row->transaction->ref_no . '<br>';
                                            }
                                            if (!is_null($contact) && $contact->type == 'customer') {
                                                $details = '<b>' . __('contact.customer') . ':</b> ' . $row->transaction->contact->name . '<br><b>' . 'Customer Opening Balance <br> </b>' . __('purchase.ref_no') . ': ' . $row->transaction->ref_no . '<br>';
                                            }
                                            if ($row->method == 'cheque') {
                                                //   $details .=    __('cheque.cheque_number') . ':</b> ' . $row->cheque_number . '<br><b>' .
                                                __('cheque.cheque_date') . ':</b> ' . $this->transactionUtil->format_date($row->cheque_date);
                                            } elseif ($row->method == 'bank_transfer') {
                                                $bank_account = null;
                                                if (!empty($row->bank_account_id)) {
                                                    $bank_account = Account::where('id', $row->bank_account_id)->first();
                                                }
                                                $details .=    __('lang_v1.bank_transfer') . ':</b> <br>' . __('cheque.cheque_number') . ':</b> ' . $row->cheque_number . '<br>' .
                                                    __('cheque.cheque_date') . ': ' . $this->transactionUtil->format_date($row->cheque_date);
                                                if (empty($bank_account)) {
                                                    $details .= '<br><b>Bank:</b>' . $bank_account->name;
                                                }
                                            } else {
                                                $details .=   ucfirst($row->method);
                                            }
                                        } elseif ($row->transaction->type == 'opening_stock') {
                                            $details = 'Stock adjustment - Opening Stock <br> <b>Date:</b> ' . $this->commonUtil->format_date($row->transaction->transaction_date);
                                            $purchase_line = PurchaseLine::leftjoin('products', 'purchase_lines.product_id', 'products.id')->where('transaction_id', $row->transaction->id)->first();
                                            if (!empty($purchase_line)) {
                                                $details .= '<br>Product: ' . $purchase_line->name;
                                            }
                                        } 
                                         elseif ($row->transaction->type == 'stock_taking') {
                                            $details = '<b>Stock Taking:</b>' .' '.$row->transaction->ref_no;
                                           
                                        }
                                        elseif ($row->transaction->type == 'shipping_agent_commission') {
                                            $label = "";
                                            $label = __('shipping::lang.shipping_agent_commission');
                                            $shipment = ShippingAgentCommission::leftjoin('shipments', 'shipping_agent_commission.shipment_id', 'shipments.id')
                                                ->leftjoin('shipping_agents', 'shipping_agents.id', 'shipments.agent_id')
                                                ->where('shipping_agent_commission.id', $row->transaction->ref_no)
                                                ->select([
                                                    'shipments.tracking_no',
                                                    'shipping_agents.name as agent_name',
                                                ])->first();
                                            if (!empty($shipment)) {
                                                $label .= "<br><b>" . __('shipping::lang.shipping_agent') . ":</b>" . $shipment->agent_name . "<br><b>" . __('shipping::lang.tracking_no') . ":</b>" . $shipment->tracking_no;
                                            }
                                            $details = $label;
                                        } elseif ($row->transaction->type == 'agent_payment') {
                                            $details = __('shipping::lang.agent_payment') . "<br><b>" . __('purchase.ref_no') . "</b>: " . $row->transaction->ref_no;
                                            $agent = ShippingAgent::find($row->transaction->parent_transaction_id);

                                            if (!empty($agent)) {
                                                $details .= "<br><b>" . __('shipping::lang.shipping_agent') . "</b>: " . $agent->name;
                                            }
                                        } elseif ($row->transaction->type == 'fpos_sale') {
                                            $details = __('tpos.fpos') . "<br><b>" . __('tpos.fpos_no') . "</b>: ";
                                            $agent = \App\TposSale::find($row->transaction->parent_transaction_id);

                                            if (!empty($agent)) {
                                                $details .= $agent->fpos_no;
                                            }
                                        } elseif ($row->transaction->type == 'shipping_agent_ob') {
                                            $details = __('shipping::lang.opening_balance') . "<br>";
                                            $agent = ShippingAgent::find($row->transaction->parent_transaction_id);

                                            if (!empty($agent)) {
                                                $details .= "<br><b>" . __('shipping::lang.shipping_agent') . "</b>: " . $agent->name;
                                            }
                                        } elseif ($row->transaction->type == 'shipping_partner_ob') {
                                            $details = __('shipping::lang.opening_balance') . "<br>";
                                            $agent = ShippingPartner::find($row->transaction->parent_transaction_id);

                                            if (!empty($agent)) {
                                                $details .= "<b>" . __('shipping::lang.shipping_partner') . "</b>: " . $agent->name;
                                            }
                                        } elseif ($row->transaction->type == 'partner_payment') {
                                            $details = __('shipping::lang.partner_payment') . "<br><b>" . __('purchase.ref_no') . "</b>: " . $row->transaction->ref_no;
                                            $agent = ShippingPartner::find($row->transaction->parent_transaction_id);

                                            if (!empty($agent)) {
                                                $details .= "<b>" . __('shipping::lang.shipping_partner') . "</b>: " . $agent->name;
                                            }
                                        } elseif ($row->transaction->type == 'vat_opening_balance') {
                                            $details = __('vat::lang.vat_opening_balance');
                                        } elseif ($row->transaction->type == 'essentials_employee_ob') {
                                            $details = __('essentials::lang.ob_of_employee');
                                            $employee = EssentialsEmployee::where('transaction_id', $row->transaction->id)->first();
                                            if (!empty($employee)) {
                                                $details .= "<b> $employee->name</b>";
                                            }
                                        } elseif ($row->transaction->type == 'stock_adjustment') {
                                            $label = "";

                                            if ($row->transaction->sub_type == 'dip_resetting') {
                                                $details = 'Dip Resetting <br> <b>Date:</b> ' . $this->commonUtil->format_date($row->transaction->transaction_date) . '<br><b class="text-danger">Dip Reset No: </b>' . $row->transaction->invoice_no;
                                            } else {
                                                // logger(json_encode($row->transaction));
                                                if ($row->transaction->stock_adjustment_type == 'increase') {
                                                    $label = __('stock_adjustment.stock_adjustment_no') . $row->transaction->ref_no . __('stock_adjustment.increased');
                                                } elseif ($row->transaction->stock_adjustment_type == 'decrease') {
                                                    $label = __('stock_adjustment.stock_adjustment_no') . $row->transaction->ref_no . __('stock_adjustment.decreased');
                                                }
                                                $details = $label . '<br> <b>Date:</b> ' . $this->commonUtil->format_date($row->transaction->transaction_date) . '<br><b>Stock Adjustment No: </b>' . $row->transaction->ref_no;
                                            }
                                        } elseif ($row->transaction->type == 'settlement' && $row->transaction->sub_type == 'expense') {
                                            $details = 'Expense <br> <b>' . 'Settlement No: ' . '</b>' . $row->transaction->invoice_no;
                                            $ref = '';
                                            if ($row->transaction->is_settlement) {
                                                $settlement_expense = SettlementExpensePayment::where('transaction_id', $row->transaction->id)->first();
                                                if (!empty($settlement_expense)) {
                                                    $ref .= '<br><b>Reference No: </b>' . $settlement_expense->reference_no . '<br><b>Reason: </b>' . $settlement_expense->reason;
                                                }
                                            }
                                            $details .= $ref;
                                        } elseif ($row->transaction->is_settlement == 1) {

                                            $transaction_payment = null;
                                            $this_tp = null;
                                            $details = '<b>' . 'Settlement No: ' . '</b>' . $row->transaction->invoice_no;
                                            $transaction_payment = TransactionPayment::where('id', $row->tp_id)->first();
                                            if ($row->transaction->type == 'sell' && $row->transaction->sub_type == 'credit_sale' && $row->type == 'debit') {
                                                $details .= '<br>Credit Sale <br><b> Customer: </b> ' . $row->transaction->contact->name;
                                                // $details .=  $transaction_payment->method;
                                                if (!empty($transaction_payment)  &&  $transaction_payment->method == 'cheque') {
                                                    $details .= '</br> <b>Bank:</b> ' . $transaction_payment->bank_name . '<b> Cheque No: </b>' . $transaction_payment->cheque_number . '  <b>Cheque Date: </b>' . $transaction_payment->cheque_date;
                                                }


                                                if ($row->interest > 0) {
                                                    $details .= '<br><b> Interest: </b> ' . number_format($row->interest, $currency_precision, '.', '');
                                                }
                                            } elseif ($row->transaction->type == 'sell' && $row->transaction->sub_type == 'credit_sale' && $row->type == 'credit') {
                                                $details .= '<br> Credit Payment <br><b> Customer: </b> ' . $row->transaction->contact->name;
                                                if ($row->interest > 0) {
                                                    $details .=  '<br><b> Interest: </b> ' . number_format($row->interest, $currency_precision, '.', '');
                                                }
                                            } elseif (!empty($transaction_payment) && $row->transaction->is_credit_sale == 0 && $transaction_payment->method == 'cash') {
                                                $this_tp = Transaction::leftjoin('contacts', 'transactions.contact_id', 'contacts.id')->where('transactions.type', 'settlement')->where('transactions.sub_type', 'cash_payment')->where('final_total', $transaction_payment->amount)->where('invoice_no', $row->transaction->invoice_no)->first();
                                                $details .= '</br>Cash Payment';
                                                // $details .= !empty($row->daily_collection) ? "<b>Daily Collection: </b>".$row->daily_collection : '';
                                                if (!empty($this_tp)) {
                                                    $details .= '<br><b>Customer:</b> ' .  $this_tp->name;
                                                }
                                            } elseif ($row->sub_type == 'deposit') {
                                                $settlement_id = Settlement::where('settlement_no', $row->transaction->invoice_no)->first()->id ?? 0;
                                                $details .= '</br>Customer Payment';
                                                $cust_payment = CustomerPayment::leftjoin('contacts', 'contacts.id', 'customer_payments.customer_id')->where('settlement_no', $settlement_id)->select('contacts.name')->first();
                                                if (!empty($cust_payment)) {
                                                    $details .= '<br><b>Customer:</b> ' .  $cust_payment->name;
                                                }
                                            } elseif ($row->transaction->type == 'settlement' && $row->transaction->sub_type == 'loan_payment') {
                                                $details = '<b>Settlement No: ' . $row->transaction->invoice_no . '</b> <br>';
                                                $cash = $this->transactionUtil->account_exist_return_id('Cash');

                                                $details .= "<br>Loan Given";

                                                if ($cash == $row->account_id) {
                                                    $loan_given_to = AccountTransaction::leftjoin('accounts', 'accounts.id', 'account_transactions.account_id')->where('account_transactions.account_id', '!=', $cash)->where('account_transactions.type', 'debit')->where('transaction_id', $row->transaction_id)->select('accounts.name')->first();
                                                    if (!empty($loan_given_to)) {
                                                        $details .= " To: <b>" . $loan_given_to->name . "</b>";
                                                    }
                                                }
                                            } elseif ($row->transaction->type == 'settlement' && $row->transaction->sub_type == 'customer_loan') {
                                                $details = "<b class='text-danger'>" . __('petro::lang.customer_loans') . "</b>";
                                                $details .= "<br>" . '<b>Settlement No: </b>' . $row->transaction->invoice_no . ' <br>';

                                                if (!empty($row->transaction->contact)) {
                                                    $details .= $row->transaction->contact->name;
                                                }
                                            } elseif ($row->transaction->type == 'settlement' && $row->transaction->sub_type == 'drawing_payment') {
                                                $details = '<span class="text-danger">Owners Drawing</span><br><b>Settlement No: </b>' . $row->transaction->invoice_no . ' <br>';


                                                $settlement = Settlement::where('settlement_no', $row->transaction->invoice_no)->first();
                                                \Log::info($row->transaction->invoice_no);
                                                $account_id = "";
                                                if (!empty($settlement)) {
                                                    $loan_pmt = SettlementDrawingPayment::where('settlement_no', $settlement->id)->where('amount', $row->amount)->first();
                                                    if (!empty($loan_pmt)) {
                                                        $account = Account::find($loan_pmt->loan_account);

                                                        if (!empty($account)) {
                                                            $account_id = $account->account_number;
                                                        }
                                                    }
                                                }
                                                $details .= "<b>Owners Drawings Account: </b>$account->name";
                                            } elseif (!empty($transaction_payment) && $row->transaction->is_credit_sale == 0 && $transaction_payment->method == 'cash_deposit') {
                                                $details .= '</br><span class="text-danger">Direct Deposit</span>';
                                            } elseif ($row->transaction->is_credit_sale == 0 && $row->transaction->sub_type == "cash_deposit") {
                                                $settlement_cash_deposits = SettlementCashDeposit::leftjoin('accounts', 'settlement_cash_deposits.bank_id', 'accounts.id')
                                                    ->where('settlement_cash_deposits.id', $row->transaction->ref_no)
                                                    ->select('settlement_cash_deposits.*', 'accounts.name as bank_name')
                                                    ->first();
                                                // logger($row->transaction->ref_no);
                                                $bank = !empty($settlement_cash_deposits) ? $settlement_cash_deposits->bank_name : "";
                                                $details .= '</br><span class="text-danger">Direct Deposit to bank: <b>' . $bank . '</b></span>';
                                            } elseif (!empty($transaction_payment) && $row->transaction->is_credit_sale == 0 &&  $transaction_payment->method == 'card' && $row->type == 'debit') {
                                                $this_tp = Transaction::leftjoin('contacts', 'transactions.contact_id', 'contacts.id')->where('transactions.type', 'settlement')->where('transactions.sub_type', 'card_payment')->where('final_total', $transaction_payment->amount)->where('invoice_no', $row->transaction->invoice_no)->first();
                                                $details .= '</br>Card Sale ';
                                                if (!empty($this_tp)) {
                                                    $details .= '<br><b>Customer:</b> ' .  $this_tp->name;
                                                }
                                            } elseif (!empty($transaction_payment) && $row->transaction->is_credit_sale == 0 &&  $transaction_payment->method == 'card' && $row->type == 'credit') {
                                                $this_tp = Transaction::leftjoin('contacts', 'transactions.contact_id', 'contacts.id')->where('transactions.type', 'settlement')->where('transactions.sub_type', 'card_payment')->where('final_total', $transaction_payment->amount)->where('invoice_no', $row->transaction->invoice_no)->first();
                                                $details .= '</br>Card Payment ';
                                                if (!empty($this_tp)) {
                                                    $details .= '<br><b>Customer:</b> ' .  $this_tp->name;
                                                }
                                            } elseif (!empty($transaction_payment) && $row->transaction->is_credit_sale == 0 &&  $transaction_payment->method == 'cheque' && $row->type == 'debit') {
                                                $details .= '</br>Cheque Payment <br> <b>Bank:</b> ' . $transaction_payment->bank_name . '<b> Cheque No: </b>' . $transaction_payment->cheque_number . '  <b>Cheque Date: </b>' . $transaction_payment->cheque_date;
                                                $this_tp = Transaction::leftjoin('contacts', 'transactions.contact_id', 'contacts.id')->where('transactions.type', 'settlement')->where('transactions.sub_type', 'cheque_payment')->where('final_total', $transaction_payment->amount)->where('invoice_no', $row->transaction->invoice_no)->first();
                                                if (!empty($this_tp)) {
                                                    $details .= '<br><b>Customer:</b> ' .  $this_tp->name;
                                                }
                                            }


                                            if ($row->account_name == "Taxes Payable") {
                                                $details .=  '<b>' . 'Settlement Amount: ' . '</b>' . $this->productUtil->num_f($row->transaction->final_total);
                                            }
                                        } elseif ($row->transaction->type == 'advance_payment') {
                                            if ($row->transaction->contact->type == 'customer') {
                                                $details = '<b>' . 'Advance Payment done by ' . '</b>' . $row->transaction->contact->name;
                                            }
                                            if ($row->transaction->contact->type == 'supplier') {
                                                $details = '<b>' . 'Advance Payment done to ' . '</b>' . $row->transaction->contact->name;
                                            }
                                            $details .= '<br><b>Payment Method :  </b>' . $row->method;
                                        } elseif ($row->transaction->type == 'security_deposit') {
                                            if ($row->transaction->contact->type == 'customer') {
                                                $details = '<b>' . 'Security Deposit     Customer ' . '</b>' . $row->transaction->contact->name . '<br><b> Payment Ref No.</b> ' . $row->transaction->ref_no;
                                            }
                                            if ($row->transaction->contact->type == 'supplier') {
                                                $details = '<b>' . 'Security Deposit     Supplier ' . '</b>' . $row->transaction->contact->name . '<br><b> Payment Ref No.</b> ' . $row->transaction->ref_no;
                                            }
                                        } elseif ($row->transaction->type == 'refund_security_deposit') {
                                            if ($row->transaction->contact->type == 'customer') {
                                                $details = '<b>' . 'Refund Security Deposit     Customer ' . '</b>' . $row->transaction->contact->name . '<br><b> Payment Ref No.</b> ' . $row->transaction->ref_no;
                                            }
                                            if ($row->transaction->contact->type == 'supplier') {
                                                $details = '<b>' . 'Refund Security Deposit     Supplier ' . '</b>' . $row->transaction->contact->name . '<br><b> Payment Ref No.</b> ' . $row->transaction->ref_no;
                                            }
                                        } elseif ($row->transaction->type == 'security_deposit_refund') {
                                            if ($row->transaction->contact->type == 'customer') {
                                                $details = '<b class="text-danger">' . 'Customer Security Deposit Refund' . '</b><br>' . $row->transaction->contact->name . '<br><b> Payment Ref No.</b> ' . $row->transaction->ref_no;
                                            }
                                            if ($row->transaction->contact->type == 'supplier') {
                                                $details = '<b class="text-danger">' . 'Supplier Security Deposit Refund' . '</b><br>' . $row->transaction->contact->name . '<br><b> Payment Ref No.</b> ' . $row->transaction->ref_no;
                                            }
                                        } elseif ($row->transaction->type == 'direct_customer_loan') {
                                            $details = '<b class="text-danger">' . __('lang_v1.direct_loan_to_customer') . '</b><br>';
                                            if (!empty($row->transaction->contact)) {
                                                $details .= $row->transaction->contact->name;
                                            }
                                        } elseif ($row->transaction->type == 'vat_price_adjustment') {
                                            $details = '<b class="">' . __('account.price_adjusted') . '</b><br>' . $row->transaction->invoice_no;
                                        } elseif ($row->transaction->type == 'ledger_discount') {
                                            $details = '<b >' . __('sale.discount') . '</b><br>';
                                            if (!empty($row->transaction->contact)) {
                                                $details .= $row->transaction->contact->name . "<br>";
                                            }

                                            if (!empty($row->transaction->transaction_note)) {
                                                foreach (json_decode($row->transaction->transaction_note) as $note) {
                                                    $details .= $note . ",";
                                                }
                                            }
                                        } elseif ($row->transaction->type == 'postdated_transfer') {
                                            $details .= '<b>' . __('account.transferred_from_postdated_cheque') . ':</b>';
                                        } elseif ($row->transaction->type == 'postdated_deposit') {

                                            if ($this->moduleUtil->account_exist_return_id('Issued Post Dated Cheques') == $row->account_id) {
                                                $details .= '<b class="text-danger">' . __('account.issued_post_dated_cheques') . ':</b><br>';
                                            } else {
                                                $details .= '<b class="text-danger">' . __('account.post_dated_cheques_full') . ':</b><br>';
                                            }

                                            if (!empty($row->transaction->contact->name)) {
                                                $details .= $row->transaction->contact->name;
                                            }



                                            $related_account = Account::find($row->related_account_id);
                                            if ($related_account) {
                                                $details .= $related_account->name . "<br>";
                                            }
                                        } elseif ($row->transaction->type == 'cheque_opening_balance') {
                                            $details .= '<b class="text-danger">' . __('account.opening_balance') . '</b><br>';
                                            if ($row->account_name == 'Opening Balance Equity Account' && !empty($row->pair_at_id)) {
                                                $pair_at = AccountTransaction::find($row->pair_at_id);
                                                if (!empty($pair_at->account_id)) {
                                                    $account = Account::find($pair_at->account_id);
                                                    $details .= '<b>' . __('account.loan_account') . '</b>:' . $account->name;
                                                }
                                            }

                                            if (!empty($row->transaction->contact->name)) {
                                                $details .= '<br><b>' . __('fleet::lang.customer') . ':</b>' . $row->transaction->contact->name;
                                            }
                                        } elseif ($row->transaction->type == 'refund') {
                                            $details = __("lang_v1.refund") . ':' . $row->transaction->ref_no . ' <br> <b> ' . __("lang_v1.invoice_no") . ':' . '</b>' . $row->transaction->invoice_no;
                                        } elseif ($row->transaction->type == 'cheque_return' && $row->at_sub_type == 'cheque_return_charges') {
                                            $details = __("lang_v1.cheque_return_charges") . ':' . $row->transaction->ref_no;
                                            $details .= '<br><b>' . __("lang_v1.bank_name") . ': </b> ' . $row->acc_bank_name . ' <b> ' . __("lang_v1.cheque_no") . ': </b> ' . $row->dep_trans_cheque_number . ' <b> ' . __("lang_v1.cheque_date") . ': </b> ' . $row->cheque_date . ' <br><b> ' . __("lang_v1.cheque_return_date") . ': </b> ' . \Carbon\Carbon::parse($row->operation_date)->format('Y-m-d');
                                        } elseif ($row->transaction->type == 'cheque_return' && $row->at_sub_type != 'cheque_return_charges') {
                                            $details = __("lang_v1.cheque_return_ref_no") . ':' . $row->cheque_ref_no;
                                            $details .= '<br><b>' . __('fleet::lang.customer') . ':</b> ' . $row->transaction->contact->name . '<br><b>' . __("lang_v1.bank_name") . ': </b> ' . $row->acc_bank_name . ' <br><b> ' . __("lang_v1.cheque_no") . ': </b> ' . $row->dep_trans_cheque_number . ' <br><b> ' . __("lang_v1.cheque_date") . ': </b> ' . $row->cheque_date. ' <br><b> ' . __("lang_v1.cheque_return_date") . ': </b> ' . \Carbon\Carbon::parse($row->operation_date)->format('Y-m-d');
                                        } elseif ($row->transaction->type == 'property_sell') {
                                            $details = __("lang_v1.sell");
                                            $details .= '<br>' . __('lang_v1.invoice_no') . ': <b>' . $row->transaction->invoice_no . '</b>';
                                            $transaction_sell_line = PropertySellLine::where('transaction_id', $row->transaction->id)->first();
                                            $property = Property::leftjoin('property_blocks', 'properties.id', 'property_blocks.property_id')
                                                ->leftjoin('units', 'properties.unit_id', 'units.id')
                                                ->where('properties.id', $transaction_sell_line->property_id)
                                                ->where('property_blocks.id', $transaction_sell_line->block_id)
                                                ->first();
                                            if (!empty($property)) {
                                                $details .= '<br><b>Project Name: </b>' . $property->name;
                                                $details .= '<br><b>Block Number: </b>' . $property->block_number;
                                            }
                                            if (!empty($row->income_type)) {
                                                $details .= '<br><b>' . ucfirst($row->income_type) . '</b>';
                                            }
                                        } elseif ($row->transaction->type == 'route_operation') {
                                            $fleet = Fleet::find($row->transaction->fleet_id);
                                            $details = '<b>' . __('fleet::lang.route_operation_no') . ':</b>' . $row->transaction->invoice_no . '<br>';
                                            if (!empty($fleet)) {
                                                $details .= '<b>' . __('fleet::lang.vehicle_no') . ':</b>' . $fleet->vehicle_number;
                                            }
                                            if (!empty($row->transaction->contact)) {
                                                $details .= '<br><b>' . __('fleet::lang.customer') . ':</b>' . $row->transaction->contact->name;
                                            }
                                        } elseif ($row->transaction->type == 'ro_advance') {
                                            if ($row->transaction->sub_type == 'driver') {
                                                $staff = Driver::find($row->transaction->contact_id)->driver_name;
                                            } else {
                                                $staff = Helper::find($row->transaction->contact_id)->helper_name;
                                            }
                                            $details = '<b>' . __('fleet::lang.advance') . '<br><b>Staff: </b>' . $staff;
                                        } elseif ($row->transaction->type == 'price_change_increase' || $row->transaction->type = "price_change_decrease") {

                                            $fdetail = PriceChangesDetail::find($row->transaction->ref_no);
                                            if (!empty($fdetail)) {
                                                $f17 = PriceChangesHeader::find($fdetail->header_id);
                                                $product = Product::find($fdetail->product_id);

                                                $form_no = !empty($f17) ? $f17->form_no : "";
                                                $product = !empty($product) ? $product->name : "";

                                                $details = '<b>' . __('pricechanges::lang.form_no') . ":</b> $form_no" . "<br><b>Product: </b> $product <br> <span class='text-danger'>Price Changed</span>";
                                            }
                                        } elseif ($row->transaction->type == 'ro_salary') {
                                            if ($row->transaction->sub_type == 'driver') {
                                                $staff = Driver::find($row->transaction->contact_id)->driver_name;
                                            } else {
                                                $staff = Helper::find($row->transaction->contact_id)->helper_name;
                                            }
                                            $details = '<b>' . __('fleet::lang.salary') . '<br><b>Staff: </b>' . $staff;
                                        } elseif ($row->transaction->type == 'fleet_opening_balance') {
                                            $fleet = Fleet::find($row->transaction->fleet_id);
                                            $details = '<b>' . __('fleet::lang.ob_of_to') . ':</b><br><b>Invoice No: </b>' . $row->transaction->invoice_no . '<br>';
                                            $contact = Contact::find($row->transaction->contact_id);
                                            $details .= '<b>' . __('contact.customer') . ':</b> ' . $contact->name;
                                            if (!empty($fleet)) {

                                                $details .= '<br><b>' . __('fleet::lang.vehicle_no') . ':</b>' . $fleet->vehicle_number;
                                            }
                                        } elseif ($row->transaction->type == 'sell_return') {
                                            $trans = Transaction::find($row->transaction->return_parent_id);
                                            $details = '<b class="text-danger">' . __('lang_v1.sell_return') . ':</b><br><b>Parent Invoice No: </b>' . $trans->invoice_no . '<br><b>Parent Invoice Amount: </b>' . $trans->final_total . '<br>';;
                                            $contact = Contact::find($trans->contact_id);
                                            $details .= '<b>' . __('contact.customer') . ':</b> ' . $contact->name;
                                        } 
                                        
                                        

                                        if (!empty($row->deleted_by)) {

                                            $user = User::find($row->deleted_by);

                                            $details .= '<br><b class="text-danger">' . __('lang_v1.deleted') . '<b> ';

                                            if (!empty($user)) {
                                                $details .= __('lang_v1.by') . " " . $user->username . " ";
                                            }

                                            if (!empty($row->deleted_at)) {
                                                $details .= "at " . $this->transactionUtil->format_date($row->deleted_at, true);
                                            }
                                        }
                                    }else{
                                        $trans = Transaction::where('id', $row->transaction_id)->first();
                                        
                                        if (!empty($trans->type) && $trans->type == 'hms_booking'){
                                            $details .= '<b>HMS Bill No.:</b>' . $trans->ref_no . '<br>';
                                            $details .= '<b>' . __('contact.customer') . ':</b> ' . $trans->contact->name . '<br><b>';
                                        }
                                    }
                                    if (!empty($row->journal_entry)) {
                                        $journal_id = Journal::where('id', $row->journal_entry)->first()->journal_id;
                                        $details = 'Journal Entry No. ' . $journal_id;
                                        $journal_accounts = Journal::where('business_id', $business_id)->where('journal_id', $journal_id)->get();
                                        if ($journal_accounts->count() === 2) {
                                            $other_journal = Journal::where('business_id', $business_id)->where('journal_id', $journal_id)->where('account_id', '!=', $id)->first();
                                            $other_account = Account::where('id', optional($other_journal)->account_id)->first();
                                            if (!empty($other_account)) {
                                                $details .=  '<br>' . $other_account->name;
                                            }
                                        }
                                    }

                                    if ($row->at_sub_type == 'vat_payment') {
                                        $details = '<b>' . __('vat::lang.vat_payment') . '</b><br>';
                                        $vat_payment = VatPayment::find($row->transfer_transaction_id);
                                        if (!empty($vat_payment)) {
                                            $details .= '<b>' . __('vat::lang.form_no') . "</b> " . $vat_payment->form_no . '<br>';
                                            if (!empty($vat_payment->cheque_date)) {
                                                $details .= '<b>' . __('vat::lang.cheque_date') . "</b> " . $vat_payment->cheque_date . '<br>';
                                            }

                                            if (!empty($vat_payment->cheque_number)) {
                                                $details .= '<b>' . __('vat::lang.cheque_number') . "</b> " . $vat_payment->cheque_number . '<br>';
                                            }

                                            if (!empty($vat_payment->recipient_name)) {
                                                $details .= '<b>' . __('vat::lang.recipient_name') . "</b> " . $vat_payment->recipient_name . '<br>';
                                            }
                                        }
                                    }
                                }
                            } else {
                                $journal = Journal::where('id', $row->journal_entry)->first();
                                if (!empty($journal)) {
                                    $journal_id = $journal->journal_d;
                                    $details = 'Journal Entry No. ' . $journal_id . ' Deleted ';
                                }
                            }
                            /**
                             * @ModifiedBy Afes
                             * @Task 127002
                             */
                            if ($row->transaction) {
                                $purchase_line = PurchaseLine::leftjoin('products', 'purchase_lines.product_id', 'products.id')->where('transaction_id', $row->transaction->id)->first();
                                if (!empty($purchase_line)) {
                                    $details .= '<br><b>Product:</b> ' . $purchase_line['name'];
                                }
                            }
                        } else {
                            $pump_operator = PumpOperator::findOrFail($row->transaction->pump_operator_id);
                            if ($row->transaction->type == 'opening_balance') {
                                $details = '<b>' . __('petro::lang.pump_operator') . ': ' . $pump_operator->name . '</b> <br><b>' . 'Opening Balance <br> </b>' . __('purchase.ref_no') . ': ' . $row->transaction->ref_no;
                            } elseif ($row->transaction->type == 'settlement' && $row->transaction->sub_type == 'shortage') {
                                $details = '<b>Settlement No: ' . $row->transaction->invoice_no . '</b> <br>Pump Operator: ' . $pump_operator->name . ' <br><b>Shortage</b><br><b>Payment Ref No : </b>' . $row->payment_ref_no;
                                $ref_added = true;
                            } elseif ($row->transaction->type == 'settlement' && $row->transaction->sub_type == 'excess') {
                                $details = '<b>Settlement No: ' . $row->transaction->invoice_no . '</b> <br>Pump Operator: ' . $pump_operator->name . ' <br><b>Excess</b><br><b>Payment Ref No: </b>' . $row->payment_ref_no;
                                $ref_added = true;
                            } elseif ($row->transaction->type == 'sell' && $row->transaction->is_settlement == '1') {
                                $scontact = ContactLedger::where('transaction_id', $row->transaction->id)->first();

                                if (!empty($scontact)) {
                                    $contact = Contact::findOrFail($scontact->contact_id);
                                    $details = '<b>Settlement No: ' . $row->transaction->invoice_no . '</b> <br><b>' . __('contact.customer') . ':</b> ' . (!empty($contact) ? $contact->name : "");
                                }
                            } elseif ($row->transaction->type == 'shortage_bulk_payment') {
                                $details = __('petro::lang.pump_operator') . ': ' . $pump_operator->name . '<br><b>' . __('petro::lang.shortage_recovered') . ' <br> </b>' . __('purchase.ref_no') . ': ' . $row->transaction->invoice_no;
                            } elseif ($row->transaction->type == 'excess_bulk_payment') {
                                $details = __('petro::lang.pump_operator') . ': ' . $pump_operator->name . '<br><b>' . __('petro::lang.excess_paid') . ' <br> </b>' . __('purchase.ref_no') . ': ' . $row->transaction->invoice_no;
                            }

                            if (!empty($row->deleted_by)) {
                                $details .= '<br><b class="btn text-danger">' . __('lang_v1.deleted') . ' ' . $this->commonUtil->format_date($row->deleted_by, false) . '<b>';
                            }
                            $purchase_line = PurchaseLine::leftjoin('products', 'purchase_lines.product_id', 'products.id')->where('transaction_id', $row->transaction->id)->first();
                            if (!empty($purchase_line)) {
                                $details .= '<br><b>Product: </b>' . $purchase_line['name'];
                            }
                        }

                        if (empty($row->transaction) && $row->sub_type != 'deposit') {

                            $contact = Contact::find($row->payment_for);
                            if (!empty($contact)) {
                                // is a customer payment
                                if ($row->update_post_dated_cheque == 1) {
                                    $details .= '<b>Customer</b> ' . $contact->name . '
                                        <br>  Ref No:' . $row->payment_ref_no;
                                    $ref_added = true;
                                    $details .= "<br>" . $contact->name;
                                } else {
                                    if (!empty($row->auto_transfer)) {
                                        $details .= '<br>Ref No:' . $row->payment_ref_no;
                                        $ref_added = true;
                                    } else {
                                        $details .= '<br><b>Customer Payment<br> </b> Ref No:' . $row->payment_ref_no;
                                        $ref_added = true;
                                        $details .= "<br>" . $contact->name;
                                    }
                                }
                            }

                            if ($row->account_name == 'Accounts Receivable') {
                                $pair_txn = AccountTransaction::leftjoin('accounts', 'accounts.id', 'account_transactions.account_id')->where('type', 'debit')->where('transaction_payment_id', $row->tp_id)->select('accounts.name')->first();
                                if (!empty($pair_txn)) {
                                    $details .= '<br><b>' . __('account.payment_method_account') . ': </b>' . $pair_txn->name;
                                }
                            }

                            if (!empty($row->cheque_date)) {
                                if ($row->update_post_dated_cheque == 1) {
                                    $details .= '<br><span style="color: red;"><b>Post Dated Cheque of</b> ' . $this->commonUtil->format_date($row->operation_date) . '</span>';
                                } else {
                                    $details .= '<br><b>' . __('account.cheque_date') . ': </b>' . $this->commonUtil->format_date($row->cheque_date);
                                }
                            }
                        }

                        if (!empty($row->payment_ref_no) && empty($ref_added)) {
                            $details .= '<br><b>Payment Ref No: </b>' . $row->payment_ref_no;
                        }
                        if (!empty($row->transaction) && $row->transaction->new_deleted_at) {
                            $user_deleted = User::find($row->transaction->new_deleted_by)->username ?? "";
                            $details = "<span class='text-danger'> Deleted. PO No: <b>" . $row->transaction->invoice_no . "</b> By <b>" . $user_deleted . "</b> at <b>" . $this->transactionUtil->format_date($row->transaction->new_deleted_at, true) . "</b></span>";
                        }
                        $details .= !empty($row->daily_collection) ? "<br><b>Daily Collection: </b>" . $row->daily_collection : '';
                        if ($row->note == "daily_collection") {
                            $details .= !empty($row->amount) ? "<br><b>Daily Collection: </b>" . number_format($row->amount, $currency_precision, '.', '') : '';
                            $details .= "<br>Cash Payment";
                            $details .= "<br><b>Customer: </b> Walk-In Customer";
                        }
                        $discount_account_id = $this->transactionUtil->account_exist_return_id('Sales Discount');
                        if (!empty($discount_account_id) && $row->account_id == $discount_account_id) {
                            $details .= "<br><b>Sales Discount for the Bill No. </b>" . $row->transaction->invoice_no;
                        }
                    endif;
                    return $details;
                })
                //added "Transaction Date" column value called'realize date' value by virtual it professional referance docs number 7338
                ->addColumn('realize_date', function($row){
                    return $this->commonUtil->format_date($row->created_at, false);
                })
                ->addColumn('cheque_date', function($row){
                    return $this->commonUtil->format_date($row->cheque_date, false);
                })
                ->addColumn('action', function ($row) use ($is_iframe, $card_account_id, $id) {
                    if ($is_iframe == 1) {
                        return '';
                    }


                    $html = '';

                    $note_html = $row->note;
                    if ($id == $card_account_id) {
                        $card_type = TransactionPayment::leftjoin('account_transactions', 'transaction_payments.id', 'account_transactions.transaction_payment_id')
                            ->leftjoin('accounts', 'transaction_payments.card_type', 'accounts.id')
                            ->where('transaction_payments.id', $row->tp_id)->select('accounts.name', 'accounts.id')->first();
                        if (!empty($card_type)) {
                            $note_html = $card_type->name;
                        }
                    }

                    if (!empty($row->cheque_date)) {
                        $cheque_html = __('lang_v1.cheque_date') . ": " . $this->commonUtil->format_date($row->cheque_date);
                    }

                    $attachment_html = '';
                    if (!empty($row->attachment)) {
                        if (strpos($row->attachment, 'jpg') || strpos($row->attachment, 'jpeg') || strpos($row->attachment, 'png')) {
                            $attachment_html = '<li><a href="#"
                            data-href="' . action("AccountController@imageModal", ["title" => "View", "url" => url($row->attachment)]) . '"
                            class="btn-modal"
                            data-container=".view_modal">' . __("messages.view") . " " . __("lang_v1.image") . '</a></li>';
                        } else {
                            $attachment_html = '<li><a class="hide-in-iframe" href="' . url($row->attachment) . '">' . __('lang_v1.download') . " " . __("lang_v1.image") . '</a></li>';
                        }
                    }


                    $html = '<div class="hide-in-iframe btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-xs"
                                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    if (!empty($note_html)) {
                        $html .= '<li><a class="note_btn" data-string="' . $note_html . '">' . __('lang_v1.note') . '</a></li>';
                    }

                    if (!empty($cheque_html)) {
                        $html .= '<li><a class="note_btn" data-string="' . $cheque_html . '">' . __('lang_v1.cheque_date') . '</a></li>';
                    }

                    $html .= '<li><a class="note_btn" data-string="' . __('lang_v1.added_by') . ": " . $row->added_by . '">' . __('lang_v1.added_by') . '</a></li>';
                    $html .= $attachment_html;

                    if (request()->session()->get('superadmin-logged-in')) {
                        $html .= '<li><a data-href="' . action('AccountController@editAccountTransaction', [$row->id]) . '" data-container=".at_modal" class="btn-modal edit_at_button"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                    }
                    // $html .= '<li><a data-href="' . action('AccountController@deleteAccountTransaction', [$row->id]) . '" class="delete_account_transaction"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                    $html .= '</ul></div>';
                    return $html;
                })
                ->editColumn('attachment', function ($row) {
                    $action = '';


                    if (!empty($row->attachment)) {
                        if (strpos($row->attachment, 'jpg') || strpos($row->attachment, 'jpeg') || strpos($row->attachment, 'png')) {
                            $action = '<a href="#"
                            data-href="' . action("AccountController@imageModal", ["title" => "View", "url" => url($row->attachment)]) . '"
                            class="btn-modal btn-xs btn btn-primary"
                            data-container=".view_modal">' . __("messages.view") . '</a>';
                        } else {
                            $action = '<a class="btn btn-default hide-in-iframe btn-xs" href="' . url($row->attachment) . '"><i class="fa fa-donwload"></i> ' . __('lang_v1.download') . '</a>';
                        }
                    }
                    return $action;
                })
                ->editColumn('reconcile_status', function ($row) use ($id, $banking_module, $is_iframe) {


                    $html = '';
                    if ($is_iframe == 0) {
                        if ($banking_module == 0) {
                            if (auth()->user()->can('account.reconcile') || auth()->user()->can('account.unreconcile')) {
                                if ($row->reconcile_status == 0) {
                                    if (auth()->user()->can('account.reconcile')) {
                                        $html = '<button type="button" class="btn btn-xs hide-in-iframe reconcile_status_btn" style="background: #FEA61E; color:#fff;" data-href="' . action('AccountController@reconcile', [$row->id]) . '"><i class="fa fa-times"></i> ' . __('account.reconcile') . '</button>';
                                    }
                                } else {
                                    if (auth()->user()->can('account.unreconcile')) {
                                        $html = '<button type="button" class="btn btn-xs hide-in-iframe reconcile_status_btn" style="background: #74b573; color:#fff;" data-href="' . action('AccountController@reconcile', [$row->id]) . '"><i class="fa fa-check"></i> ' . __('account.reconciled') . '</button>';
                                    }
                                }
                            }
                        }
                    }
                    return $html;
                })
                ->editColumn('note', function ($row) use ($card_account_id, $id) {
                    $html = $row->note;
                    if ($id == $card_account_id) {
                        $card_type = TransactionPayment::leftjoin('account_transactions', 'transaction_payments.id', 'account_transactions.transaction_payment_id')
                            ->leftjoin('accounts', 'transaction_payments.card_type', 'accounts.id')
                            ->where('transaction_payments.id', $row->tp_id)->select('accounts.name', 'accounts.id')->first();
                        if (!empty($card_type)) {
                            $html = $card_type->name;
                        }
                    }

                    if (!empty($html)) {
                        return '<button type="button" class="btn btn-xs note_btn" style="background: #8F3A84; color:#fff;" data-string="' . $html . '">' . __('lang_v1.note') . '</button>';
                    } else {
                        return '';
                    }
                })
                ->setRowAttr([

                    'class' => function ($row) {
                        if (!empty($row->deleted_at)) {
                            return 'deleted-row';
                        } else {
                            return '';
                        }
                    }
                ])
                ->removeColumn('id')
                ->removeColumn('is_closed')
                ->rawColumns(['opening_balance', 'note', 'credit', 'debit', 'balance', 'sub_type','action', 'attachment', 'reconcile_status', 'description'])
                ->make(true);
}
catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        }
    
        $account = Account::where('business_id', $business_id)
            ->with(['account_type', 'account_type.parent_account'])
            ->findOrFail($id); // modified by iftekhar

        // dd($cheque_numbers);


        if (isset($account) && $account->is_main_account == 0) {
            return view('account.show')
                ->with(compact('is_iframe', 'slipNos', 'account_access', 'account', 'card_account_id', 'card_type_accounts', 'id', 'cheque_in_hand_group_id', 'bank_group_id', 'cheque_return_account_id', 'cheque_numbers', 'customers', 'suppliers'));
        } else {
            return view('account.main_account_book')
                ->with(compact('is_iframe', 'slipNos','account_access', 'account', 'card_account_id', 'card_type_accounts', 'id', 'cheque_in_hand_group_id', 'bank_group_id', 'cheque_return_account_id', 'cheque_numbers', 'customers', 'suppliers'));
        }
    }
    public function getMainAccountBook($id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $accounts = Account::where('parent_account_id', $id)
                ->select([
                    'accounts.name',
                    'accounts.id',
                    'accounts.account_number'
                ]);
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');
            $business_details = Business::find($business_id);
            return DataTables::of($accounts)
                ->addColumn('balance', function ($row) use ($business_details, $start_date, $end_date) {
                    $balance = Account::getAccountBalance($row->id, $start_date, $end_date);
                    return  '<span class="display_currency balance" data-currency_symbol=false data-orig-value="' . $balance . '" >' . $this->productUtil->num_f($balance, false, $business_details, true) . '</span>';
                })
                ->editColumn('name', function ($row) {
                    return '<a href="' . action('AccountController@show', [$row->id]) . '">' . $row->name . '</a>&nbsp';
                })
                ->editColumn('account_number', function ($row) {
                    return '<a href="' . action('AccountController@show', [$row->id]) . '">' . $row->account_number . '</a>&nbsp';
                })
                ->removeColumn('id')
                ->rawColumns(['balance', 'name', 'account_number'])
                ->make(true);
        }
    }
    public function getAccountBalanceMain($id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $accounts = Account::where('parent_account_id', $id)->where('business_id', $business_id)
                ->select([
                    'accounts.name',
                    'accounts.id',
                    'accounts.account_number'
                ])->get();
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');
            $balance = 0;
            foreach ($accounts as  $account) {
                $balance += Account::getAccountBalance($account->id, $start_date, $end_date);
            }
            return ['balance' => round($balance, 2)];
        }
    }
    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $account = Account::where('business_id', $business_id)
                ->find($id);
            $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
            $account_type_query = AccountType::where('business_id', $business_id)
                ->whereNull('parent_account_type_id')
                ->with(['sub_types']);
            if ($account_access == 0) {
                $account_type_query->whereIn('name', ['Assets', 'Liabilities']);
            }
            $account_types = $account_type_query->get();
            $account_groups = AccountGroup::where('business_id', $business_id)->where('account_type_id', $account->account_type_id)->get();
            $selected_account_group = AccountGroup::find($account->asset_type);
            $asset_type_ids = json_encode(AccountType::getAccountTypeIdOfType('Assets', $business_id));
            $balance = AccountTransaction::where('account_id', $id)
                ->select(DB::raw("SUM( IF(account_transactions.type='credit', -1*amount, amount) ) as balance"))->first();
            $start_date = $this->commonUtil->format_date(request()->session()->get('business.start_date'));
            $parent_accounts = Account::leftjoin('account_types', 'accounts.account_type_id', 'account_types.id')
                ->where('account_types.id', $account->account_type_id)->orWhere('parent_account_type_id', $account->account_type_id)
                ->where('accounts.business_id', $business_id)
                ->select('accounts.id', 'accounts.name')
                ->pluck('accounts.name', 'accounts.id');

            $parentAccountsData = Account::where(['is_main_account' => 1, 'business_id' => $business_id])->get()->toArray();

            $fixed_acc_id = AccountType::getAccountTypeIdOfType('Fixed Assets', $business_id);

            $fixed_acc_id = !empty($fixed_acc_id) ? $fixed_acc_id[0] : 0;

            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations == 'all') {
                $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
                $business_locations->prepend(__('messages.all'), 'all');
            } else {
                $business_locations = BusinessLocation::where('business_id', $business_id)->whereIn('id', $permitted_locations)->pluck('name', 'id');
                $business_locations->prepend(__('lang_v1.please_select'), '');
            }

            // modified by iftekhar
            return view('account.edit')
                ->with(compact('business_locations', 'fixed_acc_id', 'account', 'account_types', 'balance', 'account_groups', 'account_access', 'selected_account_group', 'asset_type_ids', 'start_date', 'parent_accounts', 'parentAccountsData'));
        }
    }
    public function editAccountTransaction($transaction_id)
    {
        $account_transaction = AccountTransaction::findOrFail($transaction_id);
        $business_id = request()->session()->get('user.business_id');
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        if ($account_access == 0) {
            $accounts =  Account::where('business_id', $business_id)->where(function ($query) {
                $query->whereIn('accounts.name', ['Accounts Receivable', 'Accounts Payable', 'Cards (Credit Debit) Account', 'Cash', 'Cheques in Hand', 'Customer Deposits', 'Petty Cash']);
                $query->orWhere('accounts.visible', 1);
            })->pluck('name', 'id');
        } else {
            $accounts = Account::where('business_id', $business_id)->pluck('name', 'id');
        }
        // modified by iftekhar
        return view('account.edit_account_transaction')->with(compact('account_transaction', 'accounts'));
    }
    public function updateAccountTransaction($transaction_id)
    {
        try {
            $input = request()->except('_token');
            $new_amount = $this->transactionUtil->num_uf($input['amount']);
            $input['amount'] = $this->transactionUtil->num_uf($input['amount']);
            $account_transaction = AccountTransaction::findOrFail($transaction_id);

            $transaction = Transaction::find($account_transaction->transaction_id);

            DB::beginTransaction();
            AccountTransaction::where('id', $transaction_id)->update($input);
            $contact_ledger = ContactLedger::where('transaction_id', $account_transaction->transaction_id)->where('transaction_payment_id', $account_transaction->transaction_payment_id)->update(['amount' => $input['amount']]);

            $business_id = request()->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->first();
            $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;

            if (!empty($business->sms_settings)) {
                $phones = explode(',', str_replace(' ', '', $business->sms_settings['msg_phone_nos']));

                switch ($transaction->type) {
                    case 'advance_payment':
                        $trans_type = "Advance Payment";
                        break;

                    case 'expense':
                        $trans_type = "Expense";
                        break;

                    case 'ledger':
                        $trans_type = "Ledger";
                        break;

                    case 'opening_balance':
                        $trans_type = "Opening Balance";
                        break;

                    case 'opening_stock':
                        $trans_type = "Opening Stock";
                        break;

                    case 'purchase':
                        $trans_type = "Purchase";
                        break;

                    case 'purchase_return':
                        $trans_type = "Purchase Return";
                        break;

                    case 'route_operation':
                        $trans_type = "Route Operation";
                        break;

                    case 'sell':
                        $trans_type = "Sale";
                        break;

                    case 'settlement':
                        $trans_type = "Settlement";
                        break;

                    case 'stock_adjustment':
                        $trans_type = "Stock Adjustment";
                        break;
                }

                $accountName = Account::find($account_transaction->account_id);
                $msg_template = NotificationTemplate::where('business_id', $business_id)->where('template_for', 'transaction_changed')->first();

                if (!empty($msg_template)) {
                    $msg = $msg_template->sms_body;
                    $msg = str_replace('{transaction_type}', $trans_type, $msg);
                    $msg = str_replace('{account_name}', $accountName->name, $msg);
                    $msg = str_replace('{amount}', $this->productUtil->num_f($new_amount), $msg);
                    $msg = str_replace('{transaction_date}', $this->commonUtil->format_date($transaction->transaction_date), $msg);
                    $msg = str_replace('{invoice_no}', $transaction->invoice_no, $msg);
                    $msg = str_replace('{staff}', auth()->user()->username, $msg);


                    if (!empty($phones)) {
                        $data = [
                            'sms_settings' => $sms_settings,
                            'mobile_number' => implode(',', $phones),
                            'sms_body' => $msg
                        ];

                        $response = $this->businessUtil->sendSms($data, 'transaction_changed');
                    }
                }
            }



            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return redirect()->back()->with('status', $output);
    }
    public function deleteAccountTransaction($id)
    {
        try {
            $account_transaction = AccountTransaction::leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')->where('account_transactions.id', $id)->select('account_transactions.*', 'accounts.name', 'accounts.asset_type')->first();
            $cash_group_id = AccountGroup::getGroupByName('Cash Account', true);
            $card_group_id = AccountGroup::getGroupByName('Card', true);
            $cheque_group_id = AccountGroup::getGroupByName("Cheques in Hand (Customer's)", true);
            $bank_group_id = AccountGroup::getGroupByName('Bank Account', true);
            $transaction_id = $account_transaction->transaction_id;
            $transaction = Transaction::find($transaction_id);
            if ($transaction && in_array($transaction->type, ['sell', 'purchase', 'expense'])) {
                $output = [
                    'success' => false,
                    'msg' => __('lang_v1.transaction_exist_msg_account_transaction')
                ];
                return $output;
            }
            //if related transaction account one of the above account group
            if (!empty($account_transaction) && in_array($account_transaction->asset_type, [$cash_group_id, $card_group_id, $cheque_group_id, $bank_group_id])) {
                //delete only if account more balance then the transaction account
                if ($this->getAccountBalance($account_transaction->account_id)->balance >= $account_transaction->amount) {
                    ContactLedger::where('transaction_id', $account_transaction->transaction_id)->where('transaction_payment_id', $account_transaction->transaction_payment_id)->forcedelete();
                    TransactionPayment::where('id', $account_transaction->transaction_payment_id)->delete();
                    AccountTransaction::where('id', $id)->forcedelete();
                    $this->transactionUtil->updatePaymentStatus($transaction_id);


                    $business_id = request()->session()->get('user.business_id');
                    $business = Business::where('id', $business_id)->first();
                    $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;

                    if (!empty($business->sms_settings)) {
                        $phones = explode(',', str_replace(' ', '', $business->sms_settings['msg_phone_nos']));

                        switch ($transaction->type) {
                            case 'advance_payment':
                                $trans_type = "Advance Payment";
                                break;

                            case 'expense':
                                $trans_type = "Expense";
                                break;

                            case 'ledger':
                                $trans_type = "Ledger";
                                break;

                            case 'opening_balance':
                                $trans_type = "Opening Balance";
                                break;

                            case 'opening_stock':
                                $trans_type = "Opening Stock";
                                break;

                            case 'purchase':
                                $trans_type = "Purchase";
                                break;

                            case 'purchase_return':
                                $trans_type = "Purchase Return";
                                break;

                            case 'route_operation':
                                $trans_type = "Route Operation";
                                break;

                            case 'sell':
                                $trans_type = "Sale";
                                break;

                            case 'settlement':
                                $trans_type = "Settlement";
                                break;

                            case 'stock_adjustment':
                                $trans_type = "Stock Adjustment";
                                break;
                        }

                        $accountName = Account::find($account_transaction->account_id);

                        $msg_template = NotificationTemplate::where('business_id', $business_id)->where('template_for', 'transaction_deleted')->first();
                        if (!empty($msg_template)) {
                            $msg = $msg_template->sms_body;
                            $msg = str_replace('{transaction_type}', $trans_type, $msg);
                            $msg = str_replace('{account_name}', $accountName->name, $msg);
                            $msg = str_replace('{amount}', $this->productUtil->num_f($account_transaction->amount), $msg);
                            $msg = str_replace('{transaction_date}', $this->commonUtil->format_date($transaction->transaction_date), $msg);
                            $msg = str_replace('{invoice_no}', $transaction->invoice_no, $msg);
                            $msg = str_replace('{staff}', auth()->user()->username, $msg);



                            if (!empty($phones)) {
                                $data = [
                                    'sms_settings' => $sms_settings,
                                    'mobile_number' => implode(',', $phones),
                                    'sms_body' => $msg
                                ];

                                $response = $this->businessUtil->sendSms($data, 'transaction_deleted');
                            }
                        }
                    }
                }
            } else {
                $output = [
                    'success' => false,
                    'msg' => __('lang_v1.transaction_exist_msg_account_transaction')
                ];
                return $output;
            }
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            echo 'File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage();
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return $output;
    }
    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            try {
                $user_id = $request->session()->get('user.id');
                $input = $request->only(['location_id', 'is_property', 'name', 'account_number', 'is_need_cheque', 'note', 'show_in_balance_sheet', 'account_type_id', 'parent_account_id', 'is_main_account', 'asset_type', 'increase_reduce', 'transaction_date']);
                $business_id = request()->session()->get('user.business_id');
                $account = $oldAcc = Account::where('business_id', $business_id)
                    ->findOrFail($id);

                $account->name = $input['name'];
                $account->location_id = $input['location_id'];
                $account->is_need_cheque = $input['is_need_cheque'];
                $account->is_property = $input['is_property'];
                $account->is_main_account = !empty($input['is_main_account']) ? $input['is_main_account'] : 0;
                $account->show_in_balance_sheet = empty($input['is_main_account'])  ? $input['show_in_balance_sheet'] : 0;

                $account->account_number = $input['account_number'];
                $account->note = $input['note'];
                $account->account_type_id = $input['account_type_id'];

                $asset_type_ids = AccountType::getAccountTypeIdOfType('Assets', $business_id);
                if (empty($input['asset_type'])) {
                    $account->asset_type = null;
                }
                $account->asset_type = !empty($input['asset_type']) ? $input['asset_type'] : null;
                if (!empty($request->sub_type)) {
                    $account->parent_account_id = $request->parent_account_id;
                } else {
                    $account->parent_account_id = null;
                }


                if ($account->save()) {
                    $optrans  = AccountTransaction::where(['account_id' => $id, 'sub_type' => 'opening_balance'])->get();
                    foreach ($optrans as $tras) {
                        $account_type_name = AccountType::where('id', $account->account_type_id)->first();
                        $type = 'debit';
                        if (strpos($account_type_name, "Assets") !== false || strpos($account_type_name, "Expenses") !== false) {
                            if ($tras->amount >= 0) {
                                $type = 'debit';
                            } else {
                                $type = 'credit';
                            }
                        } else {
                            if ($tras->amount >= 0) {
                                $type = 'credit';
                            } else {
                                $type = 'debit';
                            }
                        }
                        $tras->type = $type;
                        $tras->save();
                    }
                }
                $opening_bal = $request->input('opening_balance');
                if (!empty($opening_bal) && !empty($input['increase_reduce'])) {
                    $account_type_name = AccountType::where('id', $input['account_type_id'])->first();
                    if (strpos($account_type_name->name, "Assets") !== false || strpos($account_type_name->name, "Expense") !== false) {
                        if ($input['increase_reduce'] == 'increase') {
                            $type = 'debit';
                        } else {
                            $type = 'credit';
                        }
                    } else {
                        if ($input['increase_reduce'] == 'increase') {
                            $type = 'credit';
                        } else {
                            $type = 'debit';
                        }
                    }
                    $ob_transaction_data = [
                        'amount' =>  abs($this->commonUtil->num_uf($opening_bal)),
                        'account_id' => $account->id,
                        'type' => $type,
                        'sub_type' => 'opening_balance',
                        'operation_date' => \Carbon::parse($input['transaction_date'])->format('Y-m-d H:i:s'),
                        'created_by' => $user_id
                    ];
                    AccountTransaction::createAccountTransaction($ob_transaction_data);
                }
                $output = [
                    'success' => true,
                    'msg' => __("account.account_updated_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $output;
        }
    }
    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroyAccountTransaction($id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');
                $account_transaction = AccountTransaction::findOrFail($id);
                if (in_array($account_transaction->sub_type, ['fund_transfer', 'deposit'])) {
                    //Delete transfer transaction for fund transfer
                    if (!empty($account_transaction->transfer_transaction_id)) {
                        $transfer_transaction = AccountTransaction::findOrFail($account_transaction->transfer_transaction_id);
                        $transfer_transaction->delete();
                    }
                    $account_transaction->delete();
                }
                $output = [
                    'success' => true,
                    'msg' => __("lang_v1.deleted_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $output;
        }
    }
    /**
     * Closes the specified account.
     * @return Response
     */
    public function close($id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            try {
                $business_id = session()->get('user.business_id');
                $account = Account::where('business_id', $business_id)
                    ->findOrFail($id);
                $account->is_closed = 1;
                $account->save();
                $output = [
                    'success' => true,
                    'msg' => __("account.account_closed_success")
                ];
            } catch (\Exception $e) {
                Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }
            return $output;
        }
    }
    /**
     * Shows form to transfer fund.
     * @param  int $id
     * @return Response
     */
    public function getFundTransfer($id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = session()->get('user.business_id');
            $from_account = Account::where('business_id', $business_id)
                ->NotClosed()
                ->find($id);
            $to_accounts = Account::where('business_id', $business_id)
                ->where('id', '!=', $id)
                ->NotClosed()
                ->pluck('name', 'id');
            $account_balance  = $this->getAccountBalance($id);
            $from_account_group = AccountGroup::where('id', $from_account->asset_type)->first();
            $group_name = !empty($from_account_group) ? $from_account_group->name : null;
            $check_insufficient = Account::checkInsufficientBalance($id);
            $account_groups = AccountGroup::where('business_id', $business_id)->pluck('name', 'id');
            // modified by iftekhar
            return view('account.transfer')
                ->with(compact('from_account', 'to_accounts', 'account_balance', 'check_insufficient', 'group_name', 'account_groups'));
        }
    }
    /**
     * Transfers fund from one account to another. 
     * @return Response
     */
    public function postFundTransfer(Request $request)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $business_id = session()->get('user.business_id');

            $has_reviewed = $this->transactionUtil->hasReviewed($request->input('operation_date'));

            if (!empty($has_reviewed)) {
                $output              = [
                    'success' => 0,
                    'msg'     => __('lang_v1.review_first'),
                ];

                return redirect()->back()->with(['status' => $output]);
            }

            $reviewed = $this->transactionUtil->get_review($request->input('operation_date'), $request->input('operation_date'));

            if (!empty($reviewed)) {
                $output = [
                    'success' => 0,
                    'msg'     => "You can't add a transfer for an already reviewed date",
                ];

                return redirect()->back()->with('status', $output);
            }


            $amount = $this->commonUtil->num_uf($request->input('amount'));
            $from = $request->input('from_account');
            $to = $request->input('to_account');
            $cheque_number = $request->input('cheque_number');
            $note = $request->input('note');
            $uploadFile = null;

            $fromAcc = Account::find($request->input('from_account'));
            $toAcc = Account::find($request->input('to_account'));

            //upload file
            if (!file_exists('./public/img/account_transaction/' . $business_id)) {
                mkdir('./public/img/account_transaction/' . $business_id, 0777, true);
            }
            if ($request->hasfile('attachment')) {
                $image_width = (int) System::getProperty('upload_image_width');
                $image_hieght = (int) System::getProperty('upload_image_height');
                $file = $request->file('attachment');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                    Image::make($file->getRealPath())->resize($image_width, $image_hieght)->save('public/img/account_transaction/' . $business_id . '/' . $filename);
                } else {
                    $file->move('public/img/account_transaction/' . $business_id . '/', $filename);
                }
                $uploadFile = 'public/img/account_transaction/' . $business_id . '/' . $filename;
            }




            $tp_id = null;

            if (!empty($amount)) {
                $prefix_type = 'security_deposit';
                $ref_count = $this->transactionUtil->onlyGetReferenceCount($prefix_type, $business_id, false);
                //Generate reference number
                $payment_ref_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);


                $parent_array = [
                    'business_id' => $business_id,
                    'method' => 'cheque',
                    'bank_name' => !empty($fromAcc) ? $fromAcc->name : null,
                    'cheque_number' => $cheque_number,
                    'paid_on' => $this->commonUtil->uf_date($request->input('operation_date'), true),
                    'created_by' => Auth::user()->id,
                    'amount' => $amount,
                    'cheque_date' => $this->commonUtil->uf_date($request->input('operation_date'), true),
                    'is_deposited' => 1,
                    'note' => $note,
                    'payment_ref_no' => $payment_ref_no,
                    'post_dated_cheque' => $request->post_dated_cheque ?? 0,
                    'update_post_dated_cheque' => $request->update_post_dated_cheque ?? 0
                ];

                if (!empty($request->update_post_dated_cheque)) {
                    $parent_array['account_id'] = $this->transactionUtil->account_exist_return_id('Post Dated Cheques');
                    $parent_array['related_account_id'] = $to;
                    $use_to = $this->transactionUtil->account_exist_return_id('Post Dated Cheques');
                } else {
                    $parent_array['account_id'] = $to;
                    $use_to = $to;
                }

                $parent_payment = TransactionPayment::create($parent_array);

                $tp_id = $parent_payment->id;

                DB::beginTransaction();
                if (empty($request->update_post_dated_cheque)) {
                    $credit_data = [
                        'amount' => $amount,
                        'account_id' => $from,
                        'type' => 'credit',
                        'sub_type' => 'fund_transfer',
                        'created_by' => session()->get('user.id'),
                        'note' => $note,
                        'cheque_number' => $cheque_number,
                        'transfer_account_id' => $to,
                        'operation_date' => $this->commonUtil->uf_date($request->input('operation_date'), true),
                        'cheque_date' => $this->commonUtil->uf_date($request->input('operation_date'), true),
                        'attachment' => $uploadFile,
                        'transaction_payment_id' => $tp_id,
                        'post_dated_cheque' => $request->post_dated_cheque ?? 0,
                        'update_post_dated_cheque' => $request->update_post_dated_cheque ?? 0
                    ];


                    $credit = AccountTransaction::createAccountTransaction($credit_data);
                }


                $debit_data = [
                    'amount' => $amount,
                    'account_id' => $use_to,
                    'type' => 'debit',
                    'sub_type' => 'fund_transfer',
                    'created_by' => session()->get('user.id'),
                    'note' => $note,
                    'cheque_number' => $cheque_number,
                    'transfer_account_id' => $from,
                    'operation_date' => $this->commonUtil->uf_date($request->input('operation_date'), true),
                    'cheque_date' => $this->commonUtil->uf_date($request->input('operation_date'), true),
                    'attachment' => $uploadFile,
                    'post_dated_cheque' => $request->post_dated_cheque ?? 0,
                    'update_post_dated_cheque' => $request->update_post_dated_cheque ?? 0,
                    'transaction_payment_id' => $tp_id
                ];

                if (!empty($request->update_post_dated_cheque)) {
                    $debit_data['account_id'] = $this->transactionUtil->account_exist_return_id('Post Dated Cheques');
                    $debit_data['related_account_id'] = $to;
                    $debit_data['credit_related_account'] = $from;
                }


                $debit = AccountTransaction::createAccountTransaction($debit_data);

                if (!empty($credit)) {
                    $credit->transfer_transaction_id = $debit->id;
                    $credit->save();

                    $debit->transfer_transaction_id = $credit->id;
                    $debit->save();
                }
                //added by virtual it professional referance docs number 7338
                if(!empty($debit)):
                    $cheque_deposit_bank = [
                        'account_trans_id' => $debit->id,
                        'bank_id'          => $debit->account_id,
                        'cheque_number'    => $cheque_number
                    ];

                    $cheque_depo_bank = DB::table('cheque_deposit_bank')->insert($cheque_deposit_bank);
                endif;
                
                if(!empty($credit)):
                    $cheque_recived_bank = [
                        'account_trans_id' => $credit->id,
                        'bank_id'          => $credit->account_id,
                        'cheque_number'    => $cheque_number
                    ];

                    $cheque_reciv_bank = DB::table('cheque_deposit_bank')->insert($cheque_recived_bank);
                endif;



                $from_name = Account::find($from);

                DB::commit();
            }


            $business_id = request()->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->first();
            $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;


            $accountName = "from : " . $fromAcc->name . " to : " . $toAcc->name;

            $msg_template = NotificationTemplate::where('business_id', $business_id)->where('template_for', 'transfer')->first();
            if (!empty($msg_template)) {
                $msg = $msg_template->sms_body;
                $msg = str_replace('{account}', $accountName, $msg);
                $msg = str_replace('{amount}', $this->productUtil->num_f($amount), $msg);
                $msg = str_replace('{date}', $request->input('operation_date'), $msg);
                $msg = str_replace('{staff}', auth()->user()->username, $msg);

                $phones = [];
                if (!empty($business->sms_settings)) {
                    $phones = explode(',', str_replace(' ', '', $business->sms_settings['msg_phone_nos']));
                }
                if (!empty($phones)) {
                    $data = [
                        'sms_settings' => $sms_settings,
                        'mobile_number' => implode(',', $phones),
                        'sms_body' => $msg
                    ];

                    $response = $this->businessUtil->sendSms($data, 'transfer');
                }
            }

            $output = [
                'success' => true,
                'msg' => __("account.fund_transfered_success")
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        return redirect()->back()->with('status', $output);
    }
    /**
     * Shows deposit form.// id will treate as type for list page deopsit cheque buttons
     * @param  int $id
     * @return Response
     */
    public function getChequeDeposit()
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = session()->get('user.business_id');
            $account = Account::where('business_id', $business_id)
                ->NotClosed()
                ->where('name', 'Cheques in Hand')->first();
            $id = $account->id;
            $group_id = AccountGroup::where('business_id', $business_id)->where('name', 'Bank Account')->first()->id;

            $to_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
                ->where('accounts.business_id', $business_id)
                ->whereIn('account_groups.name', ['Bank Account', 'Loans Taken', 'Loans Given'])
                ->pluck('accounts.name', 'accounts.id');

            $account_balance  = $this->getAccountBalance($id);
            return view('account.cheque_deposit')
                ->with(compact('account', 'account', 'to_accounts', 'account_balance'));
        }
    }
    /**
     * Shows deposit form.// id will treate as type for list page deopsit cheque buttons
     * @param  int $id
     * @return Response
     */
    public function postChequeDeposit(Request $request)
    {
        // dd($request->all());

        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $business_id = session()->get('user.business_id');
            $account_id = $request->input('from_account');

            $cash_account_id = Account::getAccountByAccountName('Cash')->id;

            $encash = $request->input('encash');

            $note = $request->input('note');
            $uploadFile = null;

            $account = Account::where('business_id', $business_id)
                ->findOrFail($account_id);

            $select_cheques = $request->select_cheques;

            $has_reviewed = $this->transactionUtil->hasReviewed($request->input('operation_date'));

            if (!empty($has_reviewed)) {
                $output              = [
                    'success' => 0,
                    'msg'     => __('lang_v1.review_first'),
                ];

                return redirect()->back()->with(['status' => $output]);
            }

            $reviewed = $this->transactionUtil->get_review($request->input('operation_date'), $request->input('operation_date'));
            if (!empty($reviewed)) {
                $output = [
                    'success' => 0,
                    'msg'     => "You can't add a deposit for an already reviewed date",
                ];

                return redirect()->back()->with('status', $output);
            }


            //upload file
            if (!file_exists('./public/img/account_transaction/' . $business_id)) {
                mkdir('./public/img/account_transaction/' . $business_id, 0777, true);
            }
            if ($request->hasfile('attachment')) {
                $image_width = (int) System::getProperty('upload_image_width');
                $image_hieght = (int) System::getProperty('upload_image_height');
                $file = $request->file('attachment');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                    Image::make($file->getRealPath())->resize($image_width, $image_hieght)->save('public/img/account_transaction/' . $business_id . '/' . $filename);
                } else {
                    $file->move('public/img/account_transaction/' . $business_id . '/', $filename);
                }
                $uploadFile = 'public/img/account_transaction/' . $business_id . '/' . $filename;
            }
            DB::beginTransaction();
            $total_amount = 0;
            foreach ($select_cheques as $select_cheque) {
                if (!empty($select_cheque)) {
                    $account_transaction = AccountTransaction::find($select_cheque);

                    $total_amount += $account_transaction->amount;

                    $transaction_payment = TransactionPayment::find($account_transaction->transaction_payment_id);
                    if (!empty($transaction_payment)) {
                        $amount = $this->commonUtil->num_uf($account_transaction->amount);
                        
                        $from_account = !empty($encash) ? $cash_account_id : $request->input('from_account');

                        if (!empty($amount)) {
                            $checkIH = Account::where(['name' => 'Cheques in Hand', 'business_id' => $business_id])->first();
                            $credit_data = [
                                'amount' => $amount,
                                'account_id' => $checkIH->id,
                                'type' => 'credit',
                                'post_dated_cheque' => 1,
                                'sub_type' => 'deposit',
                                'operation_date' => $this->commonUtil->uf_date($request->input('operation_date'), true),
                                'created_by' => session()->get('user.id'),
                                'transaction_payment_id' => $transaction_payment->id,
                                'note' => $note,
                                'attachment' => $uploadFile
                            ];
                            $credit = AccountTransaction::createAccountTransaction($credit_data); 
                        }

                        if ($account_transaction->post_dated_cheque == 1) {
                            $checkPost = Account::where(['name' => 'Post Dated Cheques', 'business_id' => $business_id])->first();

                            $debit_data = $credit;
                            $debit_data['type'] = 'debit';
                            $debit_data['account_id'] = $checkPost->id;
                            $debit_data['post_dated_cheque'] = 1;
                            $debit_data['transfer_transaction_id'] = $credit->id;
                            $debit_data['transaction_payment_id'] = $transaction_payment->id;
                            $debit_data['attachment'] =  $uploadFile;
                            $debit = AccountTransaction::createAccountTransaction($debit_data);
                            if (empty($encash)) {
                                    DB::table('cheque_deposit_bank')->insert([
                                        'account_trans_id' => $debit->id,
                                        'bank_id' => $request->input('from_account'),
                                        'cheque_number' => $select_cheque,
                                    ]);
                                }
                        }else{
                            $from_account = $account_id;
                            if (!empty($from_account)) {
                                $debit_data = $credit;
                                $debit_data['type'] = 'debit';
                                $debit_data['account_id'] = $from_account;
                                $debit_data['transfer_transaction_id'] = $credit->id;
                                $debit_data['transaction_payment_id'] = $transaction_payment->id;
                                $debit_data['attachment'] =  $uploadFile;
                                $debit = AccountTransaction::createAccountTransaction($debit_data);
                                $credit->transfer_transaction_id = $debit->id;
                                $credit->save();

                                if (empty($encash)) {
                                    DB::table('cheque_deposit_bank')->insert([
                                        'account_trans_id' => $debit->id,
                                        'bank_id' => $request->input('from_account'),
                                        'cheque_number' => $select_cheque,
                                    ]);
                                }
                            }
                            $credit->transfer_transaction_id = $debit->id;
                            $credit->save();
                        }
                        $transaction_payment->is_deposited = 1;
                        $transaction_payment->save();                        
                    }
                }
            }


            $business_id = request()->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->first();
            $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;
            $accountName = Account::find($account_id);
            $msg_template = NotificationTemplate::where('business_id', $business_id)->where('template_for', 'deposit')->first();
            if (!empty($msg_template)) {
                $msg = $msg_template->sms_body;
                $msg = str_replace('{account}', $accountName->name, $msg);
                $msg = str_replace('{amount}', $this->productUtil->num_f($total_amount), $msg);
                $msg = str_replace('{date}', $request->input('operation_date'), $msg);
                $msg = str_replace('{staff}', auth()->user()->username, $msg);

                $phones = [];
                if (!empty($business->sms_settings)) {
                    $phones = explode(',', str_replace(' ', '', $business->sms_settings['msg_phone_nos']));
                }

                if (!empty($phones)) {
                    $data = [
                        'sms_settings' => $sms_settings,
                        'mobile_number' => implode(',', $phones),
                        'sms_body' => $msg
                    ];

                    $response = $this->businessUtil->sendSms($data, 'deposit');
                }
            }

            DB::commit();
            $output = [
                'success' => true,
                'msg' => __("account.deposited_successfully")
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        return redirect()->back()->with('status', $output); // modified by iftekhar
    }
    /**
     * Shows deposit form.// id will treate as type for list page deopsit cheque buttons
     * @param  request $id
     * @return Response
     */
    public function getChequeList(Request $request)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            if(empty($request->input('payment_type'))):
                $payment_type = 'cheque';
            endif;
            $contact=$request->input('contact_id');
             $payment_type = $request->input('payment_type');//'pre_payments';
             
            $business_id = session()->get('user.business_id');
            $start_date = !empty($request->start_date) ? \Carbon::parse($request->start_date)->format('Y-m-d') : date('Y-m-d');
            $end_date = !empty($request->end_date) ? \Carbon::parse($request->end_date)->format('Y-m-d') : date('Y-m-d');

            $start_date_create = !empty($request->start_date_created) ? \Carbon::parse($request->start_date_created)->format('Y-m-d') : date('Y-m-d');
            $end_date_create = !empty($request->end_date_created) ? \Carbon::parse($request->end_date_created)->format('Y-m-d') : date('Y-m-d');

            $amount = $request->amount;
            $cheque_no = $request->cheque_no;

            // if ($payment_type=='cheque')
            // {
                $cheque_account = Account::getAccountByAccountName('Cheques in Hand');
       
                $query = AccountTransaction::leftJoin('transaction_payments', 'account_transactions.transaction_payment_id', '=', 'transaction_payments.id')
                     ->leftJoin('accounts', 'account_transactions.account_id', '=', 'accounts.id')
                     ->leftJoin('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
                     ->leftJoin('contacts', 'transaction_payments.payment_for', '=', 'contacts.id')
                    //  ->where('account_transactions.account_id', $cheque_account->id) // Uncomment if needed
                     ->where('transaction_payments.method', 'cheque')
                     ->where('account_transactions.amount','>', 0)
                     ->where('account_transactions.type', 'credit')
                     ->where('transaction_payments.is_deposited', 0);
                       if (!empty($start_date) && !empty($end_date)) {
                $query->whereDate('transaction_payments.cheque_date', '>=', $start_date);
                $query->whereDate('transaction_payments.cheque_date', '<=', $end_date);
            // }

            if (!empty($start_date_create) && !empty($end_date_create)) {
                $query->whereDate('account_transactions.operation_date', '>=', $start_date_create);
                $query->whereDate('account_transactions.operation_date', '<=', $end_date_create);
            }
            }
          
            elseif($payment_type=='pre_payments')
            {
                //$cheque_account = Account::getAccountByAccountName('pre_payments');
          
                $query = AccountTransaction::leftJoin('transaction_payments', 'account_transactions.transaction_payment_id', '=', 'transaction_payments.id')
                     ->leftJoin('accounts', 'account_transactions.account_id', '=', 'accounts.id')
                     ->leftJoin('transactions', 'transaction_payments.transaction_id', '=', 'transactions.id')
                     ->leftJoin('contacts', 'transaction_payments.payment_for', '=', 'contacts.id')
                     // ->where('account_transactions.account_id', $cheque_account->id) // Uncomment if needed
                     ->where('transaction_payments.method', 'pre_payments')
                     ->where('account_transactions.type', 'debit')
                     ->where('account_transactions.amount','>', 0)
                     ->where('transaction_payments.is_deposited', 0)
                     ->where('contacts.id',$contact);
            }

        
            if (!empty($amount)) {
                $query->where('transaction_payments.amount', $amount);
            }

            if (!empty($cheque_no)) {
                $query->where('transaction_payments.cheque_number', $cheque_no);
            }

            $cheque_lists = $query->select(
                'contacts.name as customer_name',
                'transaction_payments.cheque_number',
                'transaction_payments.cheque_date',
                'transaction_payments.bank_name',
                'account_transactions.amount',
                'account_transactions.id',
                'transactions.id as t_id'
            )->get();
            
              \Log::info("Payment Request{$request}");
         \Log::info("Prepaymnet{$cheque_lists}");
            return view('account.partials.cheque_list') // modified by iftekhar
                ->with(compact('cheque_lists'));
        }
    }

    public function getRealizeChequeList(Request $request)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {

            $business_id = session()->get('user.business_id');
            $start_date = !empty($request->start_date) ? \Carbon::parse($request->start_date)->format('Y-m-d') : date('Y-m-d');
            $end_date = !empty($request->end_date) ? \Carbon::parse($request->end_date)->format('Y-m-d') : date('Y-m-d');

            // $start_date_create = !empty($request->start_date_created) ? \Carbon::parse($request->start_date_created)->format('Y-m-d') : date('Y-m-d');
            // $end_date_create = !empty($request->end_date_created) ? \Carbon::parse($request->end_date_created)->format('Y-m-d') : date('Y-m-d');

            $amount = $request->amount;
            $cheque_no = $request->cheque_no;

            $cheque_account = Account::getAccountByAccountName('Cheques in Hand');
          
            //update this query and addedgiven bank for get data on cheque realize popup table by virtual it professional referance docs number 7338
            $query = AccountTransaction::join('transaction_payments', 'account_transactions.transaction_payment_id', 'transaction_payments.id')
            ->leftJoin('cheque_deposit_bank', 'cheque_deposit_bank.account_trans_id', 'account_transactions.id')
            ->leftJoin('accounts', 'account_transactions.account_id', 'accounts.id')
            ->leftJoin('account_transactions as given_bank', 'cheque_deposit_bank.account_trans_id', '=', 'given_bank.id') 
            ->leftJoin('contacts', 'transaction_payments.payment_for', 'contacts.id')
            ->where('account_transactions.account_id','!=', $cheque_account->id)
            ->where('transaction_payments.method', 'cheque')
            ->where('account_transactions.post_dated_cheque', 1)
            ->where('account_transactions.type', 'debit')
            // ->where('account_transactions.post_dated_cheque',1)
            ->where('transaction_payments.cheque_date', '<>', '')
            ->whereNull('transaction_payments.deleted_at')
            ->where('transaction_payments.is_realized', 0);
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereDate('transaction_payments.cheque_date', '>=', $start_date);
                $query->whereDate('transaction_payments.cheque_date', '<=', $end_date);
            }

            // if (!empty($start_date_create) && !empty($end_date_create)) {
            //     $query->whereDate('account_transactions.operation_date', '>=', $start_date_create);
            //     $query->whereDate('account_transactions.operation_date', '<=', $end_date_create);
            // }



            if (!empty($amount)) {
                $query->where('transaction_payments.amount', $amount);
            }

            if (!empty($cheque_no)) {
                $query->where('transaction_payments.cheque_number', $cheque_no);
            }

            if (!empty($request->realize_cheque_bank)) {
                $query->where('accounts.id', $request->realize_cheque_bank);
            }

            // dd($query->get());

            //add given bank and receiving bank details from accounts table by virtual it professional referance docs number 7338
            //sending bank = which bank to issue the cheque
            //receiving bank = which bank to receive the fund
            $cheque_lists = $query->select(
                'contacts.name as customer_name',
                'transaction_payments.cheque_number',
                'transaction_payments.cheque_date',
                'transaction_payments.paid_on',
                'accounts.name as bank_name',
                'given_bank.id as sending_bank',
                'given_bank.transfer_transaction_id as receiving_bank',
                'account_transactions.amount',
                'account_transactions.id as t_id',
                'transaction_payments.id as tp_id',
            )->get();

            $business_details = Business::find($business_id);
            return view('account.partials.realized_cheque_list') // modified by iftekhar
                ->with(compact('cheque_lists', 'business_details'));
        }
    }

    public function getRealizeChequeDeposit()
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = session()->get('user.business_id');

            $to_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
                ->where('accounts.business_id', $business_id)
                ->whereIn('account_groups.name', ['Bank Account'])
                ->pluck('accounts.name', 'accounts.id');

            return view('account.realize_cheque_deposit')
                ->with(compact('to_accounts'));
        }
    }

    public function postRealizeChequeDeposit(Request $request)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $business_id = session()->get('user.business_id');

            $select_cheques = $request->select_cheques;

            // dd($select_cheques);

            DB::beginTransaction();
            foreach ($select_cheques as $select_cheque) {
                if (!empty($select_cheque)) {
                    $account_transaction = AccountTransaction::find($select_cheque);
                    $transaction_payment = TransactionPayment::find($account_transaction->transaction_payment_id);
                    //added by virtual it professional referance docs number 7338
                    $bank_id = DB::table('cheque_deposit_bank')->where('account_trans_id', $select_cheque)->first();
                    // Choose supplier or expense name (adjust these field names as per your actual model)
                    $supplier_or_expense = $transaction_payment->supplier_name ?? $transaction_payment->customer_name;

                    // Get cheque issued date (if not available, fallback to cheque_date)
                    $cheque_issued_date = $transaction_payment->cheque_issued_date ?? $transaction_payment->cheque_date;

                    // Get the name of the user performing the operation
                    $transferred_by = auth()->user()->name;

                    // Build the description string
                    $itemDescription = $supplier_or_expense
                        . ' | Cheque issued on: ' 
                        . date('d M Y', strtotime($cheque_issued_date))
                        . ' | Cheque transferred by: ' 
                        . $transferred_by;
                    if (!empty($account_transaction)) {

                        if($account_transaction->post_dated_cheque == 1){
                            $pd_cheque_bank = Account::where(['name' => 'Post Dated Cheques', 'business_id' => $business_id])->first();

                            $checkRealiseBank = DB::table('cheque_deposit_bank')->where('account_trans_id', $account_transaction->id)->first();

                            $credit_data = [
                                'amount' => $account_transaction->amount,
                                'account_id' => $pd_cheque_bank->id,
                                'type' => 'credit',
                                'sub_type' => 'cheque_realize',
                                'cheque_date'=>$request->input("paid_on_$select_cheque"),
                                'post_dated_cheque' => '1',
                                'operation_date' => $request->input("paid_on_$select_cheque"),
                                'created_by' => session()->get('user.id'),
                                'transaction_payment_id' => $transaction_payment->id,// NEW: Add description
                                'description' => $itemDescription, 
                            ];

                            $credit = AccountTransaction::createAccountTransaction($credit_data);

                            
                            $debit_data = $credit_data;
                            $debit_data['type'] = 'debit';
                            $debit_data['account_id'] = $checkRealiseBank->bank_id;
                            $debit_data['transfer_transaction_id'] = $credit->id;
                            $debit_data['description'] = $itemDescription;
                            $debit_data['transaction_payment_id'] = $transaction_payment->id;
                            $debit = AccountTransaction::createAccountTransaction($debit_data);

                            $credit->transfer_transaction_id = $debit->id;
                            $credit->save();

                            $transaction_payment->is_realized = 1;
                            $transaction_payment->paid_on = $request->input("paid_on_$select_cheque");
                            $transaction_payment->save();
                        }                        
                    }
                }
            }

            DB::commit();
            $output = [
                'success' => true,
                'msg' => __("account.realized_successfully")
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        return redirect()->back()->with('status', $output); // modified by iftekhar
    }


    /**
     * Shows deposit form.// id will treate as type for list page deopsit cash and card buttons
     * @param  int $id
     * @return Response
     */
    public function getDeposit($id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        $type = $id;
        if (request()->ajax()) {
            $business_id = session()->get('user.business_id');
            $sub_card_accounts = null;
            $account = Account::where('business_id', $business_id)
                ->NotClosed()
                ->find($id);
            if ($type == 'cash') {
                $account = Account::where('business_id', $business_id)
                    ->NotClosed()
                    ->where('name', 'Cash')->first();
                $id = $account->id;
            } elseif ($type == 'card') {
                $card_account = Account::getAccountByAccountName('Cards (Credit Debit) Account');
                $sub_card_accounts = Account::where('parent_account_id', $card_account->id)->pluck('name', 'id');
                $id = null;
            } else {
                $account = Account::where('business_id', $business_id)
                    ->NotClosed()
                    ->find($id);
            }
            $from_accounts = Account::where('business_id', $business_id)
                ->where('id', '!=', $id)
                ->NotClosed()
                ->pluck('name', 'id');
            $group_name = null;
            if ($type != 'card') {
                $from_account_group = AccountGroup::where('id', $account->asset_type)->first();
                $group_name = !empty($from_account_group) ? $from_account_group->name : null;
            }
            //below value determine is need to check for balance for deposit or not
            $check_insufficient =  Account::checkInsufficientBalance($id);
            $account_groups = AccountGroup::where('business_id', $business_id)->pluck('name', 'id');
            $account_balance  = $type != 'card' ? $this->getAccountBalance($id) : 0.00;
            // modified by iftekhar
            return view('account.deposit')
                ->with(compact('account', 'account', 'from_accounts', 'account_balance', 'check_insufficient', 'sub_card_accounts', 'group_name', 'account_groups'));
        }
    }
    /**
     * Deposits amount.
     * @param  Request $request
     * @return \App\Http\Controllers\json
     */
    public function postDeposit(Request $request)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $business_id = session()->get('user.business_id');

            $has_reviewed = $this->transactionUtil->hasReviewed($request->input('operation_date'));

            if (!empty($has_reviewed)) {
                $output              = [
                    'success' => 0,
                    'msg'     => __('lang_v1.review_first'),
                ];

                return redirect()->back()->with(['status' => $output]);
            }

            $reviewed = $this->transactionUtil->get_review($request->input('operation_date'), $request->input('operation_date'));

            if (!empty($reviewed)) {
                $output = [
                    'success' => 0,
                    'msg'     => "You can't add a deposit for an already reviewed date",
                ];

                return redirect()->back()->with('status', $output);
            }



            $amounts = $request->input('amount');
            $account_id = $request->input('account_id');
            $note = $request->input('note');
            $cheque_number = $request->input('cheque_number');
            $uploadFile = null;
            //upload file
            if (!file_exists('./public/img/account_transaction/' . $business_id)) {
                mkdir('./public/img/account_transaction/' . $business_id, 0777, true);
            }
            if ($request->hasfile('attachment')) {
                $image_width = (int) System::getProperty('upload_image_width');
                $image_hieght = (int) System::getProperty('upload_image_height');
                $file = $request->file('attachment');
                $extension = $file->getClientOriginalExtension();
                $filename = time() . '.' . $extension;
                if (in_array($extension, ['jpg', 'jpeg', 'png'])) {
                    Image::make($file->getRealPath())->resize($image_width, $image_hieght)->save('public/img/account_transaction/' . $business_id . '/' . $filename);
                } else {
                    $file->move('public/img/account_transaction/' . $business_id . '/', $filename);
                }
                $uploadFile = 'public/img/account_transaction/' . $business_id . '/' . $filename;
            }

            $account = Account::where('business_id', $business_id)
                ->findOrFail($account_id);

            $total_amount = 0;

            if (!empty($amounts)) {
                foreach ($amounts as $one) {
                    $total_amount += $one;
                    $amount = $this->commonUtil->num_uf($one);

                    $credit_data = [
                        'amount' => $amount,
                        'account_id' => $account_id,
                        'type' => 'credit',
                        'sub_type' => 'deposit',
                        'operation_date' => $this->commonUtil->uf_date($request->input('operation_date'), true),
                        'created_by' => session()->get('user.id'),
                        'note' => $note,
                        'cheque_number' => $cheque_number,
                        'attachment' => $uploadFile
                    ];
                    $credit = AccountTransaction::createAccountTransaction($credit_data);
                    $from_account = $request->input('from_account');
                    if (!empty($from_account)) {
                        $debit_data = $credit_data;
                        $debit_data['type'] = 'debit';
                        $debit_data['account_id'] = $from_account;
                        $debit_data['transfer_transaction_id'] = $credit->id;
                        $debit_data['attachment'] =  $uploadFile;
                        $debit_data['cheque_number'] =  $cheque_number;
                        $debit = AccountTransaction::createAccountTransaction($debit_data);
                        $credit->transfer_transaction_id = $debit->id;
                        $credit->save();
                    }
                }
            }

            $business_id = request()->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->first();
            $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;
            $accountName = Account::find($account_id);
            $msg_template = NotificationTemplate::where('business_id', $business_id)->where('template_for', 'deposit')->first();
            if (!empty($msg_template)) {
                $msg = $msg_template->sms_body;
                $msg = str_replace('{account}', $accountName->name, $msg);
                $msg = str_replace('{amount}', $this->productUtil->num_f($total_amount), $msg);
                $msg = str_replace('{date}', $request->input('operation_date'), $msg);
                $msg = str_replace('{staff}', auth()->user()->username, $msg);

                $phones = [];
                if (!empty($business->sms_settings)) {
                    $phones = explode(',', str_replace(' ', '', $business->sms_settings['msg_phone_nos']));
                }

                if (!empty($phones)) {
                    $data = [
                        'sms_settings' => $sms_settings,
                        'mobile_number' => implode(',', $phones),
                        'sms_body' => $msg
                    ];

                    $response = $this->businessUtil->sendSms($data, 'deposit');
                }
            }

            $output = [
                'success' => true,
                'msg' => __("account.deposited_successfully")
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        return redirect()->back()->with('status', $output);
    }
    /**
     * Calculates account current balance.
     * @param  int $id
     * @return \App\Http\Controllers\json
     */
    public function getAccountBalance($id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        $account = Account::find($id);

        $account->balance = Account::getAccountBalance($id);

        return $account;
    }
    public function getDescription($id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        $transaction_sell_line = PropertySellLine::where('transaction_id', $id)->first();
        $property = Property::leftjoin('property_blocks', 'properties.id', 'property_blocks.property_id')
            ->where('properties.id', $transaction_sell_line->property_id)->where('property_blocks.id', $transaction_sell_line->block_id)->first();
        $data['name'] = '<br><b>Project Name: </b>' . $property->name;
        $data['block_no'] = '<br><b>Block Number: </b>' . $property->block_number;
        return $data;
    }
    /**
     * Show the specified resource.
     * @return Response
     */
    public function cashFlow()
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        if (request()->ajax()) {
            $accounts = AccountTransaction::join(
                'accounts as A',
                'account_transactions.account_id',
                '=',
                'A.id'
            )->leftjoin(
                'transactions',
                'account_transactions.transaction_id',
                '=',
                'transactions.id'
            )
                ->leftjoin(
                    'transaction_payments as TP',
                    'account_transactions.transaction_payment_id',
                    '=',
                    'TP.id'
                )
                ->where('A.business_id', $business_id)
                ->with(['transaction', 'transaction.contact', 'transfer_transaction'])
                ->select([
                    'account_transactions.type',
                    'account_transactions.amount',
                    'operation_date',
                    'account_transactions.sub_type',
                    'transfer_transaction_id',
                    DB::raw("(SELECT SUM(IF(AT.type='credit', AT.amount, -1 * AT.amount)) from account_transactions as AT JOIN accounts as ac ON ac.id=AT.account_id WHERE ac.business_id= $business_id AND AT.operation_date <= account_transactions.operation_date AND AT.deleted_at IS NULL) as balance"),
                    'account_transactions.transaction_id',
                    'account_transactions.id',
                    'A.name as account_name',
                    'TP.payment_ref_no as payment_ref_no'
                ])
                ->groupBy('account_transactions.id')
                ->orderBy('account_transactions.operation_date', 'desc');
            if (!empty(request()->input('type'))) {
                $accounts->where('account_transactions.type', request()->input('type'));
            }
            if (!empty(request()->input('account_id'))) {
                $accounts->where('A.id', request()->input('account_id'));
            }
            if (!empty(request()->location_id)) {
                $accounts->where('transactions.location_id', request()->location_id);
            } else {
                $allowed_locations = ModulePermissionLocation::getModulePermissionLocations($business_id, 'accounting_module');
                if (!empty($allowed_locations)) {
                    if (!empty($allowed_locations->locations)) {
                        $location_ids = array_keys($allowed_locations->locations);
                        $accounts->whereIn('transactions.location_id', $location_ids);
                    }
                }
            }
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');
            if (!empty($start_date) && !empty($end_date)) {
                $accounts->whereBetween(DB::raw('date(operation_date)'), [$start_date, $end_date]);
            }
            if (!$account_access) {
                $accounts = collect([]);
            }
            return DataTables::of($accounts)
                ->addColumn('debit', function ($row) {
                    if ($row->type == 'debit') {
                        return '<span class="display_currency" data-currency_symbol="true">' . $row->amount . '</span>';
                    }
                    return '';
                })
                ->addColumn('credit', function ($row) {
                    if ($row->type == 'credit') {
                        return '<span class="display_currency" data-currency_symbol="true">' . $row->amount . '</span>';
                    }
                    return '';
                })
                ->editColumn('balance', function ($row) {
                    return '<span class="display_currency" data-currency_symbol="true">' . $row->balance . '</span>';
                })
                ->editColumn('operation_date', function ($row) {
                    return $this->commonUtil->format_date($row->operation_date, true);
                })
                ->addColumn('description', function ($row) {
                    $details = '';
                    if (!empty($row->sub_type)) {
                        $details = __('account.' . $row->sub_type);
                        if (in_array($row->sub_type, ['fund_transfer', 'deposit']) && !empty($row->transfer_transaction)) {
                            if ($row->type == 'credit') {
                                $details .= ' ( ' . __('account.from') . ': ' . $row->transfer_transaction->account->name . ')';
                            } else {
                                $details .= ' ( ' . __('account.to') . ': ' . $row->transfer_transaction->account->name . ')';
                            }
                        }
                    } else {
                        if (!empty($row->transaction->type)) {
                            if ($row->transaction->type == 'purchase') {
                                $details = '<b>' . __('purchase.supplier') . ':</b> ' . isset($row->transaction->contact) ? $row->transaction->contact->name : 'N/A' . '<br><b>' .
                                    __('purchase.ref_no') . ':</b> ' . $row->transaction->ref_no;
                            } elseif ($row->transaction->type == 'sell') {
                                $details = '<b>' . __('contact.customer') . ':</b> ' . isset($row->transaction->contact) && isset($row->transaction->contact->name) ? $row->transaction->contact->name : 'N/A' . '<br><b>' .
                                    __('sale.invoice_no') . ':</b> ' . $row->transaction->invoice_no;
                            }
                        }
                    }
                    if (!empty($row->payment_ref_no)) {
                        if (!empty($details)) {
                            $details .= '<br/>';
                        }
                        $details .= '<b>' . __('lang_v1.pay_reference_no') . ':</b> ' . $row->payment_ref_no;
                    }
                    return $details;
                })
                ->removeColumn('id')
                ->rawColumns(['credit', 'debit', 'balance', 'sub_type', 'description'])
                ->make(true);
        }
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $accounts = Account::forDropdown($business_id, false);
        $accounts->prepend(__('messages.all'), '');
        // modified by iftekhar
        return view('account.cash_flow')
            ->with(compact('accounts', 'business_locations', 'account_access'));
    }
    /**
     * Shows account notes.
     * @param  int $id
     * @return Response
     */
    public function getNotes($id)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $business_id = session()->get('user.business_id');
            $account_notes = Account::join('account_transactions', 'accounts.id', 'account_transactions.account_id')
                ->where('business_id', $business_id)
                ->where('accounts.id', $id)
                ->NotClosed()
                ->select('account_transactions.operation_date', 'account_transactions.note')
                ->get();
            // modified by iftekhar
            return view('account.notes')
                ->with(compact('account_notes'));
        }
    }
    public function account_access($business_id)
    {
        $subscription = Subscription::active_subscription($business_id);
        if (!empty($subscription)) {
            $package_permissions = json_decode(DB::table('packages')->where('id', $subscription->package_id)->select('package_permissions')->first()->package_permissions);
        } else {
            $package_permissions = [];
        }
        $account_access = 0;
        if (!empty($package_permissions)) {
            if ($package_permissions->account_access == 1) {
                $account_access = 1;
            }
        }
        return $account_access;
    }
    public function getParentAccountsByType($type_id)
    {
        $business_id = session()->get('user.business_id');
        $parent_accounts = Account::leftjoin('account_types', 'accounts.account_type_id', 'account_types.id')
            ->where('account_types.id', $type_id)->orWhere('parent_account_type_id', $type_id)
            ->where('accounts.business_id', $business_id)
            ->select('accounts.id', 'accounts.name')
            ->get();
        $html = '<option selected="selected" value="">Please Select</option>';
        foreach ($parent_accounts as $accounts) {
            $html .= '<option value="' . $accounts->id . '" >' . $accounts->name . '</option>';
        }
        return $html;
    }
    public function getAccountGroupByAccount($account_id)
    {
        $business_id = session()->get('user.business_id');
        $parent_accounts = Account::where('id', $account_id)->where('business_id', $business_id)->select('asset_type')->first();
        $asset_type_accounts = Account::AssetTypeAccountGroupActive();
        $html = '<option selected="selected" value="' . $parent_accounts->asset_type . '">' . $asset_type_accounts[$parent_accounts->asset_type] . '</option>';
        return $html;
    }
    /**
     * show image modal.
     *
     * @return \Illuminate\Http\Response
     */
    public function imageModal(Request $request)
    {
        $url = $request->url;
        $title = $request->title;
        // modified by iftekhar
        return view('account.image_modal')->with(compact('title', 'url'));
    }
    public function checkAccountNumber(Request $request)
    {
        $account_number = $request->account_number;
        $business_id = session()->get('user.business_id');
        $check = Account::where('business_id', $business_id)->where('account_number', $account_number)->first();
        if (!empty($check)) {
            return ['success' => false, 'msg' => __('lang_v1.account_number_added_already')];
        }
        return ['success' => true];
    }
    public function getAccountNames(Request $request)
    {
        $business_id = session()->get('user.business_id');
        // dd(request()->get());
        $accountObj = Account::leftjoin('account_settings', 'accounts.id', 'account_settings.account_id')->where('accounts.business_id', $business_id)->where('accounts.disabled', 0)->where('accounts.visible', 1);
        $acc_type = request()->get('account_type_s', null);
        $acc_sub_type = request()->get('account_sub_type', null);
        if (!empty($acc_type)  && $acc_type != 'All') {
            if (!empty($acc_sub_type) && $acc_sub_type != 'All') {
                $accountObj->where('accounts.account_type_id', $acc_sub_type);
            } else {
                $account_type_ids = AccountType::where('business_id', $business_id)->where('parent_account_type_id', $acc_type)->pluck('id');
                if (!empty($account_type_ids) && $account_type_ids->count()) {
                    $accountObj->whereIn('accounts.account_type_id', $account_type_ids);
                } else {
                    $accountObj->where('accounts.account_type_id', $acc_type);
                }
            }
        } else {
            if (!empty($acc_sub_type)  && $acc_sub_type != 'All') {
                $accountObj->where('accounts.account_type_id', $acc_sub_type);
            }
        }
        $acc_group = request()->get('account_group', null);
        if (!empty($acc_group)  && $acc_group != 'All') {
            $accountObj->where('accounts.asset_type', $acc_group);
        }
        $ac_parent = request()->get('parent_account_id', null);
        if (!empty($ac_parent) && $ac_parent != 'All') {
            $accountObj->where('accounts.parent_account_id', $ac_parent);
        }
        $account_amount = request()->get('amount', null);
        if (!empty($account_amount)) {
            $account_amount = str_replace(',', '', $account_amount);
            $accountObj->where('account_settings.amount', $account_amount);
        }
        if (!empty(request()->start_date)) {
            $accountObj->whereDate('account_settings.date', '>=', request()->start_date);
        }

        $accounts = $accountObj->pluck('accounts.name', 'accounts.id');
        $res['data'][] = array('id' => "", 'text' => "All");
        foreach ($accounts as $key => $account) {
            $res['data'][] = array('id' => $key, 'text' => $account);
        }
        return  $res;
    }
    public function reconcile($id)
    {
        $account = AccountTransaction::findOrFail($id);
        $account->reconcile_status = !$account->reconcile_status;
        $account->save();
        return ['success' => true];
    }
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function disabledAccount()
    {
        $business_id = session()->get('user.business_id');
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse(action('HomeController@index'));
        }
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        $account_payable_id = Account::where('business_id', $business_id)->where('name', 'Accounts Payable')->first()->id;
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        if (request()->ajax()) {
            $accounts = Account::leftjoin('account_transactions as AT', function ($join) {
                $join->on('AT.account_id', '=', 'accounts.id');
                $join->whereNull('AT.deleted_at');
            })
                ->leftjoin(
                    'transactions',
                    'AT.transaction_id',
                    '=',
                    'transactions.id'
                )
                ->leftjoin(
                    'account_types as ats',
                    'accounts.account_type_id',
                    '=',
                    'ats.id'
                )
                ->leftjoin(
                    'account_types as pat',
                    'ats.parent_account_type_id',
                    '=',
                    'pat.id'
                )
                ->leftjoin(
                    'account_groups',
                    'accounts.asset_type',
                    '=',
                    'account_groups.id'
                )
                ->leftJoin('users AS u', 'accounts.created_by', '=', 'u.id')
                ->leftJoin('transaction_payments AS TP', 'AT.transaction_payment_id', '=', 'TP.id')
                ->where(function ($query) {
                    $query->whereNull('AT.transaction_payment_id')
                        ->orWhere(function ($query2) {
                            $query2->whereNotNull('AT.transaction_payment_id')
                                ->whereNotNull('TP.id');
                        });
                })
                ->where('accounts.business_id', $business_id)
                ->select([
                    'accounts.name',
                    'accounts.account_number',
                    'accounts.visible',
                    'accounts.note',
                    'accounts.id',
                    'accounts.account_type_id',
                    'accounts.created_by',
                    'accounts.disabled',
                    'accounts.asset_type',
                    'ats.name as account_type_name',
                    'pat.name as parent_account_type_name',
                    'is_closed',
                    DB::raw("SUM( IF(AT.type='credit', -1*amount, amount) ) as ass_exp_balance"),
                    DB::raw("SUM( IF(AT.type='debit', -1*amount, amount) ) as li_in_eq_balance"),
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                ]);
            $accounts->where('disabled', 1);
            if ($account_access == 0) {
                $accounts->where(function ($query) {
                    $query->whereIn('accounts.name', ['Accounts Receivable', 'Accounts Payable', 'Cards (Credit Debit) Account', 'Cash', 'Cheques in Hand', 'Customer Deposits', 'Petty Cash']);
                    $query->orWhere('accounts.visible', 1);
                });
            }
            if (!empty(request()->location_id)) {
                $accounts->where('transactions.location_id', request()->location_id);
            } else {
                $allowed_locations = ModulePermissionLocation::getModulePermissionLocations($business_id, 'accounting_module');
                if (!empty($allowed_locations)) {
                    if (!empty($allowed_locations->locations)) {
                        $location_ids = array_keys($allowed_locations->locations);
                        $accounts->whereIn('transactions.location_id', $location_ids);
                    }
                }
            }
            $accounts->groupBy('accounts.id');
            if ($account_access == 0) {
                $accounts = collect([]);
            }
            $asset_type_accounts = Account::AssetTypeAccountGroupActive();
            return DataTables::of($accounts)
                ->addColumn(
                    'action',
                    '
                    @can("account.edit")
                    <button data-href="{{action(\'AccountController@edit\',[$id])}}" data-container=".account_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    @endcan
                    <a href="{{action(\'AccountController@show\',[$id])}}" class="btn btn-warning btn-xs"><i class="fa fa-book"></i> @lang("account.account_book")</a>&nbsp;
                    @if($is_closed == 0)
                    <button data-href="{{action(\'AccountController@getFundTransfer\',[$id])}}" class="btn btn-xs btn-info btn-modal transfer_btn" data-container=".account_model"><i class="fa fa-exchange"></i> @lang("account.fund_transfer")</button>
                    <button data-href="{{action(\'AccountController@getDeposit\',[$id])}}" class="btn btn-xs btn-success btn-modal deposit_btn" data-container=".account_model"><i class="fa fa-money"></i> @lang("account.deposit")</button><br><br>
                    <button data-href="{{action(\'AccountController@getNotes\',[$id])}}" class="btn btn-xs btn-default btn-modal" data-container=".account_model"><i class="fa fa-sticky-note-o "></i> @lang("account.notes")</button>
                    <button data-url="{{action(\'AccountController@close\',[$id])}}" class="btn btn-xs btn-danger close_account"><i class="fa fa-close"></i> @lang("messages.close")</button>
                    @if($disabled == 1 )
                    <button data-url="{{action(\'AccountController@disabledStatus\',[$id])}}" class="btn btn-xs btn-info disable_status_account"><i class="fa fa-times-circle-o"></i> @lang("account.disabled")</button>
                    @endif
                    @endif
                    '
                )
                ->editColumn('name', function ($row) {
                    if ($row->is_closed == 1) {
                        return $row->name . ' <small class="label pull-right bg-red no-print">' . __("account.closed") . '</small><span class="print_section">(' . __("account.closed") . ')</span>';
                    } else {
                        return $row->name;
                    }
                })
                ->addColumn('balance', function ($row) {
                    if (strpos($row->account_type_name, "Assets") !== false || strpos($row->account_type_name, "Expense") !== false) {
                        return '<span class="display_currency" data-currency_symbol="true">' . $row->ass_exp_balance . '</span>';
                    } else {
                        return '<span class="display_currency" data-currency_symbol="true">' . $row->li_in_eq_balance . '</span>';
                    }
                })
                ->editColumn('account_type', function ($row) {
                    $account_type = '';
                    if (!empty($row->account_type->parent_account)) {
                        $account_type .= $row->account_type->parent_account->name . ' - ';
                    }
                    if (!empty($row->account_type)) {
                        $account_type .= $row->account_type->name;
                    }
                    return $account_type;
                })
                ->editColumn('parent_account_type_name', function ($row) {
                    $parent_account_type_name = empty($row->parent_account_type_name) ? $row->account_type_name : $row->parent_account_type_name;
                    return $parent_account_type_name;
                })
                ->editColumn('account_type_name', function ($row) {
                    $account_type_name = empty($row->parent_account_type_name) ? '' : $row->account_type_name;
                    return $account_type_name;
                })
                ->editColumn('added_by', function ($row) {
                    if ($row->created_by == 1) {
                        return 'Default';
                    } else {
                        return $row->added_by;
                    }
                })
                ->editColumn('account_group', function ($row) use ($business_id) {
                    // return $row->asset_type;
                    if (!empty($row->asset_type)) {
                        $account_group =  AccountGroup::where('business_id', $business_id)->where('id', $row->asset_type)->first();
                        if (!empty($account_group)) {
                            return $account_group->name;
                        }
                        return '';
                    } else {
                        return '';
                    }
                })
                ->setRowAttr([
                    'data-visible' => function ($row) {
                        return $row->visible;
                    }
                ])
                ->removeColumn('id')
                ->removeColumn('is_closed')
                ->rawColumns(['action', 'balance', 'name', 'account_group', 'reconcile_status'])
                ->make(true);
        }
        $not_linked_payments = TransactionPayment::leftjoin(
            'transactions as T',
            'transaction_payments.transaction_id',
            '=',
            'T.id'
        )
            ->whereNull('transaction_payments.parent_id')
            ->where('transaction_payments.business_id', $business_id)
            ->whereNull('account_id')
            ->count();
        $account_type_query = AccountType::where('business_id', $business_id)
            ->whereNull('parent_account_type_id')
            ->with(['sub_types']);
        if ($account_access == 0) {
            $account_type_query->where('name', 'Assets')->orWhere('name', 'Liabilities');
        }
        $account_types = $account_type_query->get();
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        // modified by iftekhar
        return view('account.disabled_accounts')
            ->with(compact('not_linked_payments', 'account_types', 'account_access', 'business_locations'));
    }
    public function disabledStatus($id)
    {
        try {
            $account = Account::findOrFail($id);
            $account->disabled = !$account->disabled;
            $account->save();
            $output = [
                'success' => true,
                'msg' => __('account.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return $output;
    }
    public function getBankAccountByGroupDP()
    {
        $group_name = request()->group_name;
        $location_id = request()->location_id;
        $business_id = session()->get('user.business_id');


        if (!empty($group_name)) {
            if ($group_name == 'credit_sale') {
                $acc_id = $this->moduleUtil->account_exist_return_id('Accounts Receivable');

                $accounts = Account::where('id', $acc_id)->select('name', 'id')->pluck('name', 'id');
            } else if ($group_name == 'credit_purchase' || $group_name == 'credit_expense') {
                $acc_id = $this->moduleUtil->account_exist_return_id('Accounts Payable');
                $accounts = Account::where('id', $acc_id)->select('name', 'id')->pluck('name', 'id');
            } else {
                $group_id = $this->moduleUtil->one_payment_type($group_name, $location_id);
                // $group_id = (AccountGroup::where('business_id', $business_id)->where('name', 'Like','%'.$group_name.'%')->first())->id;

                if (!empty($group_id)) {
                    // dd($group_name, $group_id);
                    $accounts = Account::getAccountByAccountGroupId($group_id);
                } else {
                    $accounts = [];
                }
            }
        } else {
            $accounts = [];
        }

        $dropdown = '<option value="">Please Select</option>';
        foreach ($accounts as $key => $account) {
              if (strcasecmp(trim($account), 'Issued Post Dated Cheques') === 0) {
        continue;
                }
            $dropdown .=  '<option value="' . $key . '">' . $account . '</option>';
        }
        return $dropdown;
    }
    public function getAccountByGroupId($group_id)
    {
        if (!empty($group_id)) {
            $accounts = Account::getAccountByAccountGroupId($group_id);
        } else {
            $accounts = [];
        }
        $dropdown = '<option value="">Please Select</option>';
        foreach ($accounts as $key => $account) {
            $dropdown .=  '<option value="' . $key . '">' . $account . '</option>';
        }
        return $dropdown;
    }
    public function getBankAccountDropDown()
    {
        $type = request()->type;
        if ($type == 'cash') {
            $group_name = 'Cash Account';
        }
        if ($type == 'cheque') {
            $group_name = "Cheques in Hand (Customer's)";
        }
        if ($type == 'card') {
            $group_name = 'Card';
        }
        $default_account_id = BusinessLocation::getDefaultAccountIdForMethod($type, request()->location_id);
        $business_id = session()->get('user.business_id');
        $group_id = AccountGroup::where('business_id', $business_id)->where('name', $group_name)->select('id')->first();
        if (!empty($group_id)) {
            $accounts = Account::getAccountByAccountGroupId($group_id->id);
        } else {
            $accounts = Account::where('business_id', $business_id)->where('is_main_account', 0)->pluck('name', 'id');
        }
        $selected = 'selected';
        $dropdown = '<option value="">Please Select</option>';
        foreach ($accounts as $key => $account) {
            if ($key == $default_account_id) {
                $selected = 'selected';
            }
            $check_insufficient_balance = false;
            $check_insufficient_balance = Account::checkInsufficientBalance($key);
            $account_balance = 0;
            $account_balance = $this->getAccountBalance($key);
            $dropdown .=  '<option  ' . $selected . ' value="' . $key . '" data-check_insufficient_balance="' . $check_insufficient_balance . '" data-account_balance="' . $account_balance->balance . '">' . $account . '</option>';
            $selected = null;
        }
        return $dropdown;
    }
    public function getImportAccounts()
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        $account_access = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        // modified by iftekhar
        return view('account.import')->with(compact('account_access'));
    }
    public function postImportAccounts(Request $request)
    {
        if (!auth()->user()->can('account.access')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $notAllowed = $this->commonUtil->notAllowedInDemo();
            if (!empty($notAllowed)) {
                return $notAllowed;
            }
            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            if ($request->hasFile('accounts_csv')) {
                $file = $request->file('accounts_csv');
                $parsed_array = Excel::toArray([], $file);
                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);
                $business_id = $request->session()->get('user.business_id');
                $user_id = $request->session()->get('user.id');
                $formated_data = [];
                $is_valid = true;
                $error_msg = '';
                DB::beginTransaction();
                foreach ($imported_data as $key => $value) {
                    // validate data is enough
                    if (count($value) != 8) {
                        $is_valid =  false;
                        $error_msg = "Number of columns mismatch";
                        break;
                    }

                    $row_no = $key + 1;
                    $account_array = [];

                    if (!empty(trim($value[0]))) {
                        $account_array['transaction_date'] = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value[0]);
                    } else {
                        $account_array['transaction_date'] = date('Y-m-d');
                    }

                    $account_type = null;
                    if (!empty($value[1])) {
                        $account_type = AccountType::where('business_id', $business_id)->where('name', $value[1])->first();
                        if (empty($account_type)) {
                            $is_valid =  false;
                            $error_msg = "account type not exist in row on. $row_no";
                            break;
                        }
                        $account_array['account_type_id'] = $account_type->id;
                    } else {
                        $is_valid =  false;
                        $error_msg = "account type is required in row no. $row_no";
                        break;
                    }

                    if (!empty($value[2])) {
                        $account_type = AccountType::where('business_id', $business_id)->where('name', $value[2])->first();

                        if (empty($account_type)) {
                            $is_valid =  false;
                            $error_msg = "account sub type does not exist in row on. $row_no";
                            break;
                        }
                        $account_array['account_type_id'] = $account_type->id;
                    }

                    if (!empty($account_array['account_type_id'])) {

                        $account_type = AccountType::find($account_array['account_type_id']);

                        $response = $this->getAccNo($account_type->id);
                        $content = $response->getContent();

                        $acc_no_arr = json_decode($content, true);


                        if (empty($acc_no_arr['account_no'])) {
                            $is_valid =  false;
                            $error_msg = "Prefix for " . $account_type->name . " does not exist, please add it first!";
                            break;
                        }

                        $account_array['account_number'] = $acc_no_arr['account_no'];
                    }

                    //Check account group name
                    $account_group_type = null;
                    if (!empty($value[3])) {
                        $account_group_type = AccountGroup::where('business_id', $business_id)->where('name', $value[3])->first();
                        if (empty($account_group_type)) {
                            $is_valid =  false;
                            $error_msg = "account group not exist in row on. $row_no";
                            break;
                        }
                        $account_array['asset_type'] = $account_group_type->id;
                    } else {
                        $account_array['asset_type'] = null;
                    }


                    if (!empty($value[4])) {
                        $account_array['name'] = $value[4];
                    } else {
                        $is_valid =  false;
                        $error_msg = "Account Name is required in row no. $row_no";
                        break;
                    }

                    if (!empty($value[5])) {
                        $account_array['db_cr'] = strtolower($value[5]);

                        if (!in_array($account_array['db_cr'], ['debit', 'credit'])) {
                            $is_valid =  false;
                            $error_msg = "Unsurported type in row no. $row_no" . " use only debit or credit";
                            break;
                        }
                    } else {
                        $account_array['db_cr'] = null;
                    }

                    if (!empty($value[6])) {
                        $account_array['opening_balance'] = $value[6];
                    } else {
                        $account_array['opening_balance'] = 0;
                    }

                    if (!empty($value[7])) {
                        $account_array['obe_db_cr'] = strtolower($value[7]);

                        if (!in_array($account_array['obe_db_cr'], ['debit', 'credit'])) {
                            $is_valid =  false;
                            $error_msg = "Unsurported type in row no. $row_no" . " use only debit or credit";
                            break;
                        }
                    } else {
                        $account_array['obe_db_cr'] = null;
                    }

                    if ($account_array['opening_balance'] > 0 && (empty($account_array['obe_db_cr']) || empty($account_array['db_cr']))) {
                        $is_valid =  false;
                        $error_msg = "Debit or Credit for entered amount and opening balance equity account are both required in row no. $row_no";
                        break;
                    }

                    $account_array['business_id'] = $business_id;
                    $account_array['created_by'] = Auth::user()->id;
                    $account_array['visible'] = 1;
                    $formated_data[] = $account_array;
                }
                if (!$is_valid) {
                    throw new \Exception($error_msg);
                }

                if (!empty($formated_data)) {
                    foreach ($formated_data as $account_data) {
                        $opening_balance = 0;
                        $ob_type = 'db_cr';
                        $obe_type = 'obe_db_cr';

                        if (isset($account_data['opening_balance'])) {
                            $opening_balance = $account_data['opening_balance'];
                            $obe_type = $account_data['obe_db_cr'];
                            $ob_type = $account_data['db_cr'];


                            unset($account_data['opening_balance']);
                            unset($account_data['obe_db_cr']);
                            unset($account_data['db_cr']);
                        }

                        if (isset($account_data['transaction_date'])) {
                            $transaction_date = $account_data['transaction_date'];
                            unset($account_data['transaction_date']);
                        }


                        $account_data['business_id'] = $business_id;
                        $account = Account::create($account_data);

                        if (!empty($opening_balance)) {

                            $ob_transaction_data = [
                                'amount' => abs($this->commonUtil->num_uf($opening_balance)),
                                'account_id' => $account->id,
                                'type' => $ob_type,
                                'sub_type' => 'opening_balance',
                                'operation_date' => $transaction_date,
                                'created_by' => Auth::user()->id
                            ];
                            AccountTransaction::createAccountTransaction($ob_transaction_data);

                            $ob_transaction_data['type'] = $obe_type;
                            $ob_transaction_data['account_id'] = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account') ?? null;

                            if (!empty($ob_transaction_data['account_id'])) {
                                AccountTransaction::createAccountTransaction($ob_transaction_data);
                            }
                        }
                    }
                }
                $output = [
                    'success' => 1,
                    'msg' => __('product.file_imported_successfully')
                ];
                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
            return redirect()->route('accounts.import')->with('notification', $output);
        }
        return redirect()->back()->with('status', $output);
    }
    public function addAccountOpeningBalance($opening_bal, $account_id, $date = null, $note = null)
    {
        $account = Account::find($account_id);
        $account_type_name = AccountType::where('id', $account->account_type_id)->first();
        $type = 'debit';
        if (strpos($account_type_name, "Assets") !== false || strpos($account_type_name, "Expenses") !== false) {
            if ($opening_bal >= 0) {
                $type = 'debit';
            } else {
                $type = 'credit';
            }
        } else {
            if ($opening_bal >= 0) {
                $type = 'credit';
            } else {
                $type = 'debit';
            }
        }
        $ob_transaction_data = [
            'amount' => abs($this->commonUtil->num_uf($opening_bal)),
            'account_id' => $account_id,
            'type' => $type,
            'sub_type' => 'opening_balance',
            'operation_date' => !empty($date) ? $date : \Carbon::now(),
            'note' => !empty($note) ? $note : null,
            'created_by' => Auth::user()->id
        ];
        return AccountTransaction::createAccountTransaction($ob_transaction_data);
    }
    public function getProfitLossReport()
    {
        $business_id = request()->session()->get('user.business_id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        // modified by iftekhar
        return view('account.profit_loss_report')->with(compact(
            'business_locations'
        ));
    }
    public function listDepositTransfer()
    {
        $business_id = session()->get('user.business_id');
        if (request()->ajax()) {
            $accounts = AccountTransaction::leftjoin('accounts as account_to', function ($join) {
                $join->on('account_to.id', '=', 'account_transactions.account_id');
            })
                ->leftjoin('account_transactions as account_transactions_from', function ($join) {
                    $join->on('account_transactions.transfer_transaction_id', '=', 'account_transactions_from.id');
                })
                ->leftjoin('transaction_payments', 'account_transactions.transaction_payment_id', 'transaction_payments.id')
                ->leftjoin('transactions', 'transaction_payments.transaction_id', 'transactions.id')
                ->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
                ->leftjoin('accounts as account_from', function ($join) {
                    $join->on('account_from.id', '=', 'account_transactions_from.account_id');
                })->where('account_transactions.type', 'debit')
                ->whereIn('account_transactions.sub_type', ['deposit', 'fund_transfer'])
                ->leftJoin('users', 'account_transactions.created_by', '=', 'users.id')
                ->where('account_from.business_id', $business_id)
                ->select([
                    'account_transactions.*',
                    'account_from.name as from_account',
                    'account_to.name as to_account',
                    'users.username',
                    'contacts.name as customer_name'
                ]);
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $accounts->whereDate('account_transactions.operation_date', '>=', request()->start_date);
                $accounts->whereDate('account_transactions.operation_date', '<=', request()->end_date);
            }
            if (!empty(request()->user_id)) {
                $accounts->where('account_transactions.created_by', request()->user_id);
            }
            if (!empty(request()->sub_type)) {
                $accounts->where('account_transactions.sub_type', request()->sub_type);
            }
            if (!empty(request()->from_account_id)) {
                $accounts->where('account_transactions_from.account_id', request()->from_account_id);
            }
            if (!empty(request()->to_account_id)) {
                $accounts->where('account_transactions.account_id', request()->to_account_id);
            }
            $accounts->groupBy('account_transactions.id');
            return DataTables::of($accounts)
                ->addColumn(
                    'action',
                    '
                    @can("account.deposit_transfer.edit")
                    <button data-href="{{action(\'AccountController@editDepositTransfer\',[$id])}}" data-container=".account_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    @endcan
                    '
                )
                ->editColumn('operation_date', '{{@format_date($operation_date)}}')
                ->editColumn('sub_type', function ($row) {
                    if ($row->sub_type == 'fund_transfer') {
                        return __('lang_v1.transfer');
                    }
                    return ucfirst($row->sub_type);
                })
                ->addColumn('amount', function ($row) {
                    return '<span class="display_currency" data-currency_symbol="false">' . $row->amount . '</span>';
                })
                ->removeColumn('id')
                ->removeColumn('is_closed')
                ->rawColumns(['action', 'amount'])
                ->make(true);
        }
    }

    public function chequeObTransfer()
    {

        $business_id = session()->get('user.business_id');
        if (request()->ajax()) {

            $accounts = Transaction::where('transactions.type', 'cheque_opening_balance')
                ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
                ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->where('transactions.business_id', $business_id)
                ->select([
                    'contacts.name as customer',
                    'transaction_payments.cheque_number',
                    'transaction_payments.cheque_date',
                    'transaction_payments.amount',
                    'transaction_payments.bank_name',
                    'transactions.transaction_date',
                    'transactions.id'
                ]);

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $accounts->whereDate('transactions.transaction_date', '>=', request()->start_date);
                $accounts->whereDate('transactions.transaction_date', '<=', request()->end_date);
            }
            if (!empty(request()->user_id)) {
                $accounts->where('transactions.contact_id', request()->user_id);
            }
            if (!empty(request()->cheque_number)) {
                $accounts->where('transaction_payments.cheque_number', request()->cheque_number);
            }

            if (!empty(request()->bank_name)) {
                $accounts->where('transaction_payments.bank_name', request()->bank_name);
            }

            if (!empty(request()->amount)) {
                $accounts->where('transaction_payments.amount', request()->amount);
            }

            return DataTables::of($accounts)

                ->addColumn('action', function ($row) {

                    $html = '';
                    $html = '<div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-xs"
                                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                    $html .= '<li><a data-href="' . action('AccountController@editChequeOb', [$row->id]) . '" data-container="#account_type_modal" class="btn-modal edit_at_button"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';

                    $html .= '<li><a data-href="' . action('AccountController@deleteChequeOb', [$row->id]) . '" class="cheque_ob_delete"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                    $html .= '</ul></div>';
                    return $html;
                })

                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')

                ->editColumn('cheque_date', '{{@format_date($cheque_date)}}')

                ->addColumn('amount', function ($row) {
                    return '<span class="display_currency" data-currency_symbol="false">' . $this->productUtil->num_f($row->amount) . '</span>';
                })
                ->removeColumn('id')
                ->rawColumns(['amount', 'action'])
                ->make(true);
        }
    }

    public function editChequeOb($id)
    {
        $business_id = session()->get('user.business_id');
        $account = Transaction::where('transactions.type', 'cheque_opening_balance')
            ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
            ->leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.id', $id)
            ->select([
                'contacts.name as customer',
                'transaction_payments.cheque_number',
                'transaction_payments.cheque_date',
                'transaction_payments.amount',
                'transaction_payments.bank_name',
                'transactions.transaction_date',
                'transactions.id',
                'transactions.contact_id'
            ])->first();

        $customers = Contact::customersDropdown($business_id, false);
        return view('account.partials.editchequeob', compact('account', 'customers'));
    }

    public function updateChequeOb(Request $request, $id)
    {
        $data = $request->all();
        $data['amount'] = $this->transactionUtil->num_uf($data['amount']);

        try {
            $transaction_data = ['final_total' => $data['amount'], 'contact_id' => $data['customer']];
            $tp_data = ['amount' => $data['amount'], 'bank_name' => $data['bank_name'], 'cheque_number' => $data['cheque_number'], 'cheque_date' => $data['cheque_date']];

            DB::beginTransaction();
            Transaction::where('id', $id)->update($transaction_data);
            TransactionPayment::where('transaction_id', $id)->update($tp_data);

            AccountTransaction::whereIn('id', $ats)->update(['amount' => $data['amount']]);
            AccountSetting::whereIn('at_asset_id', $ats)->orWhereIn('at_obe_id', $ats)->update(['amount' => $data['amount']]);

            $ats = AccountTransaction::where('transaction_id', $id)->pluck('id')->toArray();
            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return response()->json($output);
    }

    function duplicateTransactions($ats)
    {
        $originalRecords = AccountTransaction::whereIn('id', $ats)->get();

        $newRecords = [];

        foreach ($originalRecords as $record) {
            $newRecord = $record->replicate();

            if ($newRecord->type == 'credit') {
                $newRecord->type = 'debit';
            } elseif ($newRecord->type == 'debit') {
                $newRecord->type = 'credit';
            }

            $newRecords[] = $newRecord;
        }

        DB::transaction(function () use ($newRecords) {
            foreach ($newRecords as $newRecord) {
                $newRecord->save();
            }
        });

        return $newRecords;
    }

    public function deleteChequeOb($id)
    {
        try {

            DB::beginTransaction();
            Transaction::where('id', $id)->delete();
            TransactionPayment::where('transaction_id', $id)->delete();

            $ats = AccountTransaction::where('transaction_id', $id)->pluck('id')->toArray();
            $this->duplicateTransactions($ats);

            AccountTransaction::whereIn('id', $ats)->update(['deleted_by' => auth()->user()->id, 'deleted_at' => date('Y-m-d H:i')]);
            AccountSetting::whereIn('at_asset_id', $ats)->orWhereIn('at_obe_id', $ats)->delete();
            AccountTransaction::whereIn('id', $ats)->delete();

            DB::commit();

            $output = [
                'success' => true,
                'msg' => __("lang_v1.deleted_success")
            ];
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }
        return $output;
    }

    public function editDepositTransfer($id)
    {
        $account_transaction = AccountTransaction::leftjoin('account_transactions as account_transactions_from', function ($join) {
            $join->on('account_transactions.transfer_transaction_id', '=', 'account_transactions_from.id');
        })
            ->where('account_transactions.type', 'debit')
            ->where('account_transactions.id', $id)
            ->select([
                'account_transactions.*',
                'account_transactions.account_id as to_account',
                'account_transactions_from.account_id as from_account'
            ])->first();
        $business_id = session()->get('user.business_id');
        $accounts = Account::where('business_id', $business_id)->pluck('name', 'id');
        // modified by iftekhar
        return view('account.edit_deposit_transfer')->with(compact(
            'account_transaction',
            'accounts'
        ));
    }
    public function updateDepositTransfer($id)
    {
        try {

            $has_reviewed = $this->transactionUtil->hasReviewed(request()->operation_date);

            if (!empty($has_reviewed)) {
                $output              = [
                    'success' => 0,
                    'msg'     => __('lang_v1.review_first'),
                ];

                return redirect()->back()->with(['status' => $output]);
            }

            $reviewed = $this->transactionUtil->get_review(request()->operation_date, request()->operation_date);

            if (!empty($reviewed)) {
                $output = [
                    'success' => 0,
                    'msg'     => "You can't add a transfer for an already reviewed date",
                ];

                return redirect()->back()->with('status', $output);
            }
            $account_transaction_to = AccountTransaction::find($id);
            $account_transaction_to->operation_date = $this->transactionUtil->uf_date(request()->operation_date);
            $account_transaction_to->amount = $this->transactionUtil->num_uf(request()->amount);
            $account_transaction_to->account_id = $this->transactionUtil->num_uf(request()->to_account);
            $account_transaction_to->cheque_number = request()->cheque_number;
            DB::beginTransaction();
            $account_transaction_to->save();
            $account_transaction_from = AccountTransaction::find($account_transaction_to->transfer_transaction_id);
            $account_transaction_from->operation_date = $this->transactionUtil->uf_date(request()->operation_date);
            $account_transaction_from->amount = $this->transactionUtil->num_uf(request()->amount);
            $account_transaction_from->account_id = $this->transactionUtil->num_uf(request()->from_account);
            $account_transaction_from->cheque_number = request()->cheque_number;
            $account_transaction_from->save();
            DB::commit();
            $output = [
                'success' => true,
                'tab' => 'list_deposit_transfer',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'list_deposit_transfer',
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return redirect()->back()->with('status', $output);
    }
    public function account_details(Request $request)
    {
        if (request()->ajax()) {
            $query = DB::table("accounts")
                ->leftJoin('users AS u', 'accounts.created_by', '=', 'u.id')
                ->where("accounts.id", request()->id)
                ->select(['accounts.*', 'u.first_name'])
                ->first();

            return response()->json($query);
        }
    }
    // @eng START 15/2
    public function getAccsForWhichToCheckInsufficientBalances()
    {
        // $names = ['Cash', 'Petty Cash', 'Cash Locker'];
        return Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')->where('account_groups.name', 'Cash Account')->where('accounts.business_id', session()->get('user.business_id'))->pluck('accounts.id');
    }
    // @eng END 15/2

    public function fixDecemberSalesAccounts()
    {
        $accounts = Account::whereNull('deleted_at')->get();
        foreach ($accounts as $account) {
            // $start_date = date('Y') . '-12-01';
            $account->dec_accounts_to_fix = AccountTransaction::whereNull('account_transactions.deleted_at')
                ->where('account_transactions.type', '')
                ->where('account_transactions.account_id', $account->id)
                // ->whereDate('account_transactions.updated_at', '>=', $start_date)
                ->join('transactions', 'account_transactions.transaction_id', '=', 'transactions.id')
                ->whereNotNull('transactions.type')
                ->where('transactions.type', '!=', '')
                ->select('account_transactions.*', 'transactions.type as transaction_type')
                ->count();
        }

        return view('account.fix_december_sales_accounts')->with(compact(
            'accounts'
        ));
    }

    public function updateDecemberSalesAccounts($id)
    {
        if ($id == "All") {
            $accounts = Account::whereNull('deleted_at')->get();
        } else {
            $accounts = Account::whereNull('deleted_at')->where('id', $id)->get();
        }
        foreach ($accounts as $account) {
            // $start_date = date('Y') . '-12-01';
            $account->dec_accounts_to_fix = AccountTransaction::whereNull('account_transactions.deleted_at')
                ->where('account_transactions.type', '')
                ->where('account_transactions.account_id', $account->id)
                // ->whereDate('account_transactions.updated_at', '>=', $start_date)
                ->join('transactions', 'account_transactions.transaction_id', '=', 'transactions.id')
                ->whereNotNull('transactions.type')
                ->where('transactions.type', '!=', '')
                ->select('account_transactions.*', 'transactions.type as transaction_type')
                ->get();
            foreach ($account->dec_accounts_to_fix as $dec_account_to_fix) {
                $dec_account_to_fix->type = AccountTransaction::getAccountTransactionType($dec_account_to_fix->transaction_type) ?? '';
                \Log::info("Updating AccountTransaction: {$dec_account_to_fix->id} type to {$dec_account_to_fix->type}");
                $dec_account_to_fix->update();
            }
        }

        return redirect()->route('accounts.fixDecemberSalesAccounts')->with('msg', "Accounts updated");
    }

    public function correctSaleIncomeAccountsTax()
    {
        $products = Product::get();
        $category_ids = [];
        foreach ($products as $product) {
            if (!empty($product->sub_category_id)) {
                $category_ids[] = $product->sub_category_id;
            } elseif (!empty($product->category_id)) {
                $category_ids[] = $product->category_id;
            }
        }
        // Remove duplicates
        $category_ids = array_unique($category_ids);

        $sales_income_account_ids = [];
        foreach ($category_ids as $category_id) {
            $category = Category::where('id', $category_id)->select('sales_income_account_id')->first();
            if (!empty($category) && !empty($category->sales_income_account_id)) {
                $sales_income_account_ids[] = $category->sales_income_account_id;
            }
        }
        $sales_income_account_ids = array_unique($sales_income_account_ids);

        $accounts = Account::whereNull('deleted_at')->whereIn('id', $sales_income_account_ids)->get();
        foreach ($accounts as $account) {
            $account->account_transactions_to_correct = AccountTransaction::whereNull('account_transactions.deleted_at')
                ->whereNotNull('account_transactions.sell_line_id')
                ->where('account_transactions.account_id', $account->id)
                ->where('account_transactions.type', "credit")
                ->join('transaction_sell_lines', 'account_transactions.sell_line_id', '=', 'transaction_sell_lines.id')
                ->join('variations', 'transaction_sell_lines.variation_id', '=', 'variations.id')
                ->whereRaw('ROUND(transaction_sell_lines.unit_price_before_discount, 2) != ROUND(variations.sell_price_inc_tax, 2)')
                ->whereRaw('ROUND(transaction_sell_lines.unit_price_inc_tax * transaction_sell_lines.quantity, 2) != ROUND(account_transactions.amount, 2)')
                ->count();
        }

        return view('account.correct_sale_income_accounts_tax')->with(compact(
            'accounts'
        ));
    }

    public function updateSaleIncomeAccountsTax($id)
    {
        if ($id == "All") {
            $products = Product::get();
            $category_ids = [];
            foreach ($products as $product) {
                if (!empty($product->sub_category_id)) {
                    $category_ids[] = $product->sub_category_id;
                } elseif (!empty($product->category_id)) {
                    $category_ids[] = $product->category_id;
                }
            }
            // Remove duplicates
            $category_ids = array_unique($category_ids);

            $sales_income_account_ids = [];
            foreach ($category_ids as $category_id) {
                $category = Category::where('id', $category_id)->select('sales_income_account_id')->first();
                if (!empty($category) && !empty($category->sales_income_account_id)) {
                    $sales_income_account_ids[] = $category->sales_income_account_id;
                }
            }
            $sales_income_account_ids = array_unique($sales_income_account_ids);
            $accounts = Account::whereNull('deleted_at')->whereIn('id', $sales_income_account_ids)->get();
        } else {
            $accounts = Account::whereNull('deleted_at')->where('id', $id)->get();
        }

        foreach ($accounts as $account) {
            $account->account_transactions_to_correct = AccountTransaction::whereNull('account_transactions.deleted_at')
                ->whereNotNull('account_transactions.sell_line_id')
                ->where('account_transactions.account_id', $account->id)
                ->where('account_transactions.type', "credit")
                ->join('transaction_sell_lines', 'account_transactions.sell_line_id', '=', 'transaction_sell_lines.id')
                ->join('variations', 'transaction_sell_lines.variation_id', '=', 'variations.id')
                ->whereRaw('ROUND(transaction_sell_lines.unit_price_before_discount, 2) != ROUND(variations.sell_price_inc_tax, 2)')
                ->whereRaw('ROUND(transaction_sell_lines.unit_price_inc_tax * transaction_sell_lines.quantity, 2) != ROUND(account_transactions.amount, 2)')
                ->select('account_transactions.*', 'transaction_sell_lines.unit_price_inc_tax as unit_price_inc_tax_tsl', 'transaction_sell_lines.quantity as quantity_tsl')
                ->get();
            foreach ($account->account_transactions_to_correct as $account_transaction_to_correct) {
                $newAmount = $account_transaction_to_correct->unit_price_inc_tax_tsl * $account_transaction_to_correct->quantity_tsl;
                \Log::info("Updating AccountTransaction Tax - id: {$account_transaction_to_correct->id} prevAmount: {$account_transaction_to_correct->amount} newAmount: {$newAmount}");
                $account_transaction_to_correct->amount = $newAmount;
                $account_transaction_to_correct->update();
            }
        }

        return redirect()->route('accounts.correctSaleIncomeAccountsTax')->with('msg', "Account transactions tax updated");
    }

    public function correctCOGSAccountsTax()
    {
        $products = Product::get();
        $category_ids = [];
        foreach ($products as $product) {
            if (!empty($product->sub_category_id)) {
                $category_ids[] = $product->sub_category_id;
            } elseif (!empty($product->category_id)) {
                $category_ids[] = $product->category_id;
            }
        }
        // Remove duplicates
        $category_ids = array_unique($category_ids);

        $cogs_account_ids = [];
        foreach ($category_ids as $category_id) {
            $category = Category::where('id', $category_id)->select('cogs_account_id')->first();
            if (!empty($category) && !empty($category->cogs_account_id)) {
                $cogs_account_ids[] = $category->cogs_account_id;
            }
        }
        $cogs_account_ids = array_unique($cogs_account_ids);

        $accounts = Account::whereNull('deleted_at')->whereIn('id', $cogs_account_ids)->get();
        foreach ($accounts as $account) {
            $account->account_transactions_to_correct = AccountTransaction::whereNull('account_transactions.deleted_at')
                ->whereNotNull('account_transactions.sell_line_id')
                ->where('account_transactions.account_id', $account->id)
                ->where('account_transactions.type', "debit")
                ->join('transaction_sell_lines', 'account_transactions.sell_line_id', '=', 'transaction_sell_lines.id')
                ->whereRaw('ROUND(transaction_sell_lines.last_purchased_price * transaction_sell_lines.quantity, 2) != ROUND(account_transactions.amount, 2)')
                ->count();
        }

        return view('account.correct_cogs_accounts_tax')->with(compact(
            'accounts'
        ));
    }

    public function updateCOGSAccountsTax($id)
    {
        if ($id == "All") {
            $products = Product::get();
            $category_ids = [];
            foreach ($products as $product) {
                if (!empty($product->sub_category_id)) {
                    $category_ids[] = $product->sub_category_id;
                } elseif (!empty($product->category_id)) {
                    $category_ids[] = $product->category_id;
                }
            }
            // Remove duplicates
            $category_ids = array_unique($category_ids);

            $cogs_account_ids = [];
            foreach ($category_ids as $category_id) {
                $category = Category::where('id', $category_id)->select('cogs_account_id')->first();
                if (!empty($category) && !empty($category->cogs_account_id)) {
                    $cogs_account_ids[] = $category->cogs_account_id;
                }
            }
            $cogs_account_ids = array_unique($cogs_account_ids);
            $accounts = Account::whereNull('deleted_at')->whereIn('id', $cogs_account_ids)->get();
        } else {
            $accounts = Account::whereNull('deleted_at')->where('id', $id)->get();
        }

        foreach ($accounts as $account) {
            $account->account_transactions_to_correct = AccountTransaction::whereNull('account_transactions.deleted_at')
                ->whereNotNull('account_transactions.sell_line_id')
                ->where('account_transactions.account_id', $account->id)
                ->where('account_transactions.type', "debit")
                ->join('transaction_sell_lines', 'account_transactions.sell_line_id', '=', 'transaction_sell_lines.id')
                ->whereRaw('ROUND(transaction_sell_lines.last_purchased_price * transaction_sell_lines.quantity, 2) != ROUND(account_transactions.amount, 2)')
                ->select('account_transactions.*', 'transaction_sell_lines.last_purchased_price as last_purchased_price_tsl', 'transaction_sell_lines.quantity as quantity_tsl')
                ->get();
            foreach ($account->account_transactions_to_correct as $account_transaction_to_correct) {
                $newAmount = $account_transaction_to_correct->last_purchased_price_tsl * $account_transaction_to_correct->quantity_tsl;
                \Log::info("Updating COGS AccountTransaction Tax - id: {$account_transaction_to_correct->id} prevAmount: {$account_transaction_to_correct->amount} newAmount: {$newAmount}");
                $account_transaction_to_correct->amount = $newAmount;
                $account_transaction_to_correct->update();
            }
        }

        return redirect()->route('accounts.correctCOGSAccountsTax')->with('msg', "Account transactions tax updated");
    }

    public function correctSellLinesTax()
    {
        // get sell lines whose tax was calculated twice
        $transaction_sell_lines = TransactionSellLine::whereNull('transaction_sell_lines.deleted_at')
            ->whereNotNull('transaction_sell_lines.tax_id')
            ->join('variations', 'transaction_sell_lines.variation_id', '=', 'variations.id')
            ->join('tax_rates', 'transaction_sell_lines.tax_id', '=', 'tax_rates.id')
            ->whereRaw('ROUND(transaction_sell_lines.unit_price_inc_tax, 2) != ROUND(variations.sell_price_inc_tax, 2)')
            ->whereRaw('ROUND(transaction_sell_lines.unit_price_inc_tax / ((tax_rates.amount/100)+1), 2) = ROUND(variations.sell_price_inc_tax, 2)')
            ->count();

        return view('account.correct_transaction_sell_lines_tax')->with(compact(
            'transaction_sell_lines'
        ));
    }

    public function updateSellLinesTax()
    {
        // Get sell lines whose tax was calculated twice
        $transaction_sell_lines = TransactionSellLine::whereNull('transaction_sell_lines.deleted_at')
            ->whereNotNull('transaction_sell_lines.tax_id')
            ->join('variations', 'transaction_sell_lines.variation_id', '=', 'variations.id')
            ->join('tax_rates', 'transaction_sell_lines.tax_id', '=', 'tax_rates.id')
            ->whereRaw('ROUND(transaction_sell_lines.unit_price_inc_tax, 2) != ROUND(variations.sell_price_inc_tax, 2)')
            ->whereRaw('ROUND(transaction_sell_lines.unit_price_inc_tax / ((tax_rates.amount/100)+1), 2) = ROUND(variations.sell_price_inc_tax, 2)')
            ->select('transaction_sell_lines.id', 'transaction_sell_lines.unit_price_inc_tax', 'transaction_sell_lines.line_discount_type', 'transaction_sell_lines.line_discount_amount', 'variations.default_sell_price', 'variations.sell_price_inc_tax')
            ->get();

        foreach ($transaction_sell_lines as $line) {
            $new_item_tax = $line->sell_price_inc_tax - $line->default_sell_price;
            $new_unit_price_inc_tax = $line->sell_price_inc_tax;
            $unit_price_with_discount = $line->default_sell_price;
            if ($line->line_discount_amount > 0) {
                if ($line->line_discount_type == "fixed") {
                    $unit_price_with_discount = $unit_price_with_discount + $line->line_discount_amount;
                }
                if ($line->line_discount_type == "percentage") {
                    $unit_price_with_discount = $unit_price_with_discount * (1 + ($line->line_discount_amount / 100));
                }
            }
            \Log::info("Updating TransactionSellLine Tax - id: {$line->id} prev unit_price_inc_tax: {$line->unit_price_inc_tax} | new unit_price_inc_tax: {$new_unit_price_inc_tax}");
            TransactionSellLine::where('id', $line->id)->update([
                'item_tax' => $new_item_tax,
                'unit_price_inc_tax' => $new_unit_price_inc_tax,
                'unit_price_before_discount' => $line->default_sell_price,
                'unit_price' => $unit_price_with_discount,
            ]);
        }

        return redirect()->route('accounts.correctSellLinesTax')->with('msg', "Transaction lines tax updated");
    }

    public function correctSellLinesDecimalDifference()
    {
        $products = Product::get();
        $category_ids = [];
        foreach ($products as $product) {
            if (!empty($product->sub_category_id)) {
                $category_ids[] = $product->sub_category_id;
            } elseif (!empty($product->category_id)) {
                $category_ids[] = $product->category_id;
            }
        }
        // Remove duplicates
        $category_ids = array_unique($category_ids);

        $sales_income_account_ids = [];
        foreach ($category_ids as $category_id) {
            $category = Category::where('id', $category_id)->select('sales_income_account_id')->first();
            if (!empty($category) && !empty($category->sales_income_account_id)) {
                $sales_income_account_ids[] = $category->sales_income_account_id;
            }
        }
        $sales_income_account_ids = array_unique($sales_income_account_ids);

        // Get sell lines whose price has a small decimal difference
        $transaction_sell_lines = TransactionSellLine::whereNull('transaction_sell_lines.deleted_at')
            ->whereNotNull('transaction_sell_lines.tax_id')
            ->join('variations', 'transaction_sell_lines.variation_id', '=', 'variations.id')
            ->whereRaw('ROUND(transaction_sell_lines.unit_price_inc_tax, 2) != ROUND(variations.sell_price_inc_tax, 2)')
            ->whereRaw('ROUND((transaction_sell_lines.unit_price_inc_tax), 0) = ROUND(variations.sell_price_inc_tax, 0)')
            ->select('transaction_sell_lines.id', 'transaction_sell_lines.unit_price_inc_tax', 'transaction_sell_lines.line_discount_type', 'transaction_sell_lines.line_discount_amount', 'variations.default_sell_price', 'variations.sell_price_inc_tax')
            ->get();

        $account_transactions_to_correct_arr = [];
        // \Log::debug("correctSellLinesDecimalDifference",['transaction_sell_lines'=>$transaction_sell_lines,'sales_income_account_ids'=>$sales_income_account_ids]);
        foreach ($transaction_sell_lines as $line) {
            $account_transactions_to_correct = AccountTransaction::where('account_transactions.sell_line_id', $line->id)
                ->whereIn('account_transactions.account_id', $sales_income_account_ids)
                ->join('transaction_sell_lines', 'account_transactions.sell_line_id', '=', 'transaction_sell_lines.id')
                ->select('account_transactions.*', 'transaction_sell_lines.unit_price_inc_tax as unit_price_inc_tax_tsl', 'transaction_sell_lines.quantity as quantity_tsl')
                ->get();
            foreach ($account_transactions_to_correct as $account_transaction_to_correct) {
                $account_transactions_to_correct_arr[] = $account_transaction_to_correct->account_id;
            }
        }
        $account_transactions_to_correct_arr = array_unique($account_transactions_to_correct_arr);
        $account_transactions_to_correct_count = count($account_transactions_to_correct_arr);

        // get sell lines whose price has a small decimal difference
        $transaction_sell_lines = TransactionSellLine::whereNull('transaction_sell_lines.deleted_at')
            ->whereNotNull('transaction_sell_lines.tax_id')
            ->join('variations', 'transaction_sell_lines.variation_id', '=', 'variations.id')
            ->whereRaw('ROUND(transaction_sell_lines.unit_price_inc_tax, 2) != ROUND(variations.sell_price_inc_tax, 2)')
            ->whereRaw('ROUND((transaction_sell_lines.unit_price_inc_tax), 0) = ROUND(variations.sell_price_inc_tax, 0)')
            ->count();

        return view('account.correct_transaction_sell_lines_decimal_difference')->with(compact(
            'transaction_sell_lines',
            'account_transactions_to_correct_count'
        ));
    }

    public function updateSellLinesDecimalDifference()
    {
        $products = Product::get();
        $category_ids = [];
        foreach ($products as $product) {
            if (!empty($product->sub_category_id)) {
                $category_ids[] = $product->sub_category_id;
            } elseif (!empty($product->category_id)) {
                $category_ids[] = $product->category_id;
            }
        }
        // Remove duplicates
        $category_ids = array_unique($category_ids);

        $sales_income_account_ids = [];
        foreach ($category_ids as $category_id) {
            $category = Category::where('id', $category_id)->select('sales_income_account_id')->first();
            if (!empty($category) && !empty($category->sales_income_account_id)) {
                $sales_income_account_ids[] = $category->sales_income_account_id;
            }
        }
        $sales_income_account_ids = array_unique($sales_income_account_ids);

        // Get sell lines whose price has a small decimal difference
        $transaction_sell_lines = TransactionSellLine::whereNull('transaction_sell_lines.deleted_at')
            ->whereNotNull('transaction_sell_lines.tax_id')
            ->join('variations', 'transaction_sell_lines.variation_id', '=', 'variations.id')
            ->whereRaw('ROUND(transaction_sell_lines.unit_price_inc_tax, 2) != ROUND(variations.sell_price_inc_tax, 2)')
            ->whereRaw('ROUND((transaction_sell_lines.unit_price_inc_tax), 0) = ROUND(variations.sell_price_inc_tax, 0)')
            ->select('transaction_sell_lines.id', 'transaction_sell_lines.unit_price_inc_tax', 'transaction_sell_lines.line_discount_type', 'transaction_sell_lines.line_discount_amount', 'variations.default_sell_price', 'variations.sell_price_inc_tax')
            ->get();

        foreach ($transaction_sell_lines as $line) {
            $new_item_tax = $line->sell_price_inc_tax - $line->default_sell_price;
            $new_unit_price_inc_tax = $line->sell_price_inc_tax;
            $unit_price_with_discount = $line->default_sell_price;
            if ($line->line_discount_amount > 0) {
                if ($line->line_discount_type == "fixed") {
                    $unit_price_with_discount = $unit_price_with_discount + $line->line_discount_amount;
                }
                if ($line->line_discount_type == "percentage") {
                    $unit_price_with_discount = $unit_price_with_discount * (1 + ($line->line_discount_amount / 100));
                }
            }
            \Log::info("Updating TransactionSellLine Decimal Difference - id: {$line->id} prev unit_price_inc_tax: {$line->unit_price_inc_tax} | new unit_price_inc_tax: {$new_unit_price_inc_tax}");
            TransactionSellLine::where('id', $line->id)->update([
                'item_tax' => $new_item_tax,
                'unit_price_inc_tax' => $new_unit_price_inc_tax,
                'unit_price_before_discount' => $line->default_sell_price,
                'unit_price' => $unit_price_with_discount,
            ]);
            $account_transactions_to_correct = AccountTransaction::where('account_transactions.sell_line_id', $line->id)
                ->whereIn('account_transactions.account_id', $sales_income_account_ids)
                ->join('transaction_sell_lines', 'account_transactions.sell_line_id', '=', 'transaction_sell_lines.id')
                ->select('account_transactions.*', 'transaction_sell_lines.unit_price_inc_tax as unit_price_inc_tax_tsl', 'transaction_sell_lines.quantity as quantity_tsl')
                ->get();
            foreach ($account_transactions_to_correct as $account_transaction_to_correct) {
                $newAmount = $account_transaction_to_correct->unit_price_inc_tax_tsl * $account_transaction_to_correct->quantity_tsl;
                \Log::info("Updating AccountTransaction Decimal Difference - id: {$account_transaction_to_correct->id} prevAmount: {$account_transaction_to_correct->amount} newAmount: {$newAmount}");
                $account_transaction_to_correct->amount = $newAmount;
                $account_transaction_to_correct->update();
            }
        }

        return redirect()->route('accounts.correctSellLinesDecimalDifference')->with('msg', "Transaction lines Decimal Difference updated");
    }

    public function getAccountsReceivableSettlementCustomerPaymentToCredit()
    {
        $business_id = session()->get('user.business_id');
        $account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');
        $account_transactions = AccountTransaction::where('account_transactions.account_id', $account_id)
            ->where('account_transactions.type', "debit")
            ->where('account_transactions.sub_type', "deposit")
            ->join('transactions', 'account_transactions.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', "sell")
            ->where('transactions.sub_type', "settlement")
            ->join('settlements', 'transactions.invoice_no', '=', 'settlements.settlement_no')
            ->join('customer_payments', 'settlements.id', '=', 'customer_payments.settlement_no')
            ->whereNotNull('customer_payments.settlement_no')
            ->distinct('customer_payments.id')
            ->count();

        return view('account.update_accounts_receivable_settlement_customer_payment_to_credit')->with(compact(
            'account_transactions'
        ));
    }

    public function updateAccountsReceivableSettlementCustomerPaymentToCredit($id)
    {
        $business_id = session()->get('user.business_id');
        $account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');
        $account_transactions = AccountTransaction::where('account_transactions.account_id', $account_id)
            ->where('account_transactions.type', "debit")
            ->where('account_transactions.sub_type', "deposit")
            ->join('transactions', 'account_transactions.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', "sell")
            ->where('transactions.sub_type', "settlement")
            ->join('settlements', 'transactions.invoice_no', '=', 'settlements.settlement_no')
            ->join('customer_payments', 'settlements.id', '=', 'customer_payments.settlement_no')
            ->whereNotNull('customer_payments.settlement_no')
            ->distinct('customer_payments.id');

        \Log::info('updateAccountsReceivableSettlementCustomerPaymentToCredit', ['updated AccountTransaction ids' => $account_transactions->pluck('account_transactions.id')->toArray()]);

        $updated_count = $account_transactions->update(['account_transactions.type' => 'credit']);

        return redirect()->route('accounts.getAccountsReceivableSettlementCustomerPaymentToCredit')->with('msg', "Accounts Receivable, settlement customer payment, updated to credit");
    }

    public function getFinishedGoodsAccountPosSaleTax()
    {
        $account_id = $this->transactionUtil->account_exist_return_id('Finished Goods Account');
        $account_transactions = AccountTransaction::where('account_transactions.account_id', $account_id)
            ->where('account_transactions.type', "credit")
            ->whereNull('account_transactions.sub_type')
            ->join('transactions', 'account_transactions.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', "sell")
            ->whereNull('transactions.sub_type')
            ->join('transaction_sell_lines', 'account_transactions.sell_line_id', '=', 'transaction_sell_lines.id')
            ->whereNotNull('transaction_sell_lines.last_purchased_price')
            ->whereNotNull('transaction_sell_lines.quantity')
            ->whereRaw('ROUND(transaction_sell_lines.quantity * transaction_sell_lines.last_purchased_price, 2) != ROUND(account_transactions.amount, 2)')
            ->distinct('transaction_sell_lines.id')
            ->count();

        return view('account.updateFinishedGoodsAccountPosSaleTax')->with(compact(
            'account_transactions'
        ));
    }

    public function updateFinishedGoodsAccountPosSaleTax($id)
    {
        $account_id = $this->transactionUtil->account_exist_return_id('Finished Goods Account');
        $account_transactions = AccountTransaction::where('account_transactions.account_id', $account_id)
            ->where('account_transactions.type', "credit")
            ->whereNull('account_transactions.sub_type')
            ->join('transactions', 'account_transactions.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', "sell")
            ->whereNull('transactions.sub_type')
            ->join('transaction_sell_lines', 'account_transactions.sell_line_id', '=', 'transaction_sell_lines.id')
            ->whereNotNull('transaction_sell_lines.last_purchased_price')
            ->whereNotNull('transaction_sell_lines.quantity')
            ->whereRaw('ROUND(transaction_sell_lines.quantity * transaction_sell_lines.last_purchased_price, 2) != ROUND(account_transactions.amount, 2)')
            ->distinct('transaction_sell_lines.id')
            ->select('account_transactions.*', 'transaction_sell_lines.last_purchased_price', 'transaction_sell_lines.quantity')
            ->get();

        foreach ($account_transactions as $transaction) {
            \Log::info('updateFinishedGoodsAccountPosSaleTax', ['id' => $transaction->id, 'calc' => "$transaction->quantity * $transaction->last_purchased_price", 'prevAmount' => $transaction->amount, 'newAmount' => ($transaction->quantity * $transaction->last_purchased_price)]);
            $transaction->amount = $transaction->quantity * $transaction->last_purchased_price;
            $transaction->update();
        }

        return redirect()->route('accounts.getFinishedGoodsAccountPosSaleTax')->with('msg', "Finished Goods Account Pos Sales Tax updated");
    }

    public function getCashAccountPosSaleTax()
    {
        $account_id = $this->transactionUtil->account_exist_return_id('Cash');
        $account_transactions = AccountTransaction::where('account_transactions.account_id', $account_id)
            ->where('account_transactions.type', "debit")
            ->where('account_transactions.sub_type', "ledger_show")
            ->whereNotNull('account_transactions.transaction_payment_id')
            ->join('transactions', 'account_transactions.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', "sell")
            ->whereNull('transactions.sub_type')
            ->join('transaction_payments', 'account_transactions.transaction_payment_id', '=', 'transaction_payments.id')
            ->where('transaction_payments.method', "cash")
            ->where('transaction_payments.account_id', $account_id)
            ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
            ->whereNotNull('transaction_sell_lines.unit_price_inc_tax')
            ->whereNotNull('transaction_sell_lines.quantity')
            ->groupBy('account_transactions.id')
            ->havingRaw('ROUND(SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax), 2) != ROUND(account_transactions.amount, 2)')
            ->select('account_transactions.id as account_transaction_id', 'account_transactions.amount', DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as calculated_amount'))
            ->count();

        return view('account.updateCashAccountPosSaleTax')->with(compact(
            'account_transactions'
        ));
    }

    public function updateCashAccountPosSaleTax($id)
    {
        $account_id = $this->transactionUtil->account_exist_return_id('Cash');
        $account_transactions = AccountTransaction::where('account_transactions.account_id', $account_id)
            ->where('account_transactions.type', "debit")
            ->where('account_transactions.sub_type', "ledger_show")
            ->whereNotNull('account_transactions.transaction_payment_id')
            ->join('transactions', 'account_transactions.transaction_id', '=', 'transactions.id')
            ->where('transactions.type', "sell")
            ->whereNull('transactions.sub_type')
            ->join('transaction_payments', 'account_transactions.transaction_payment_id', '=', 'transaction_payments.id')
            ->where('transaction_payments.method', "cash")
            ->where('transaction_payments.account_id', $account_id)
            ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
            ->whereNotNull('transaction_sell_lines.unit_price_inc_tax')
            ->whereNotNull('transaction_sell_lines.quantity')
            ->groupBy('account_transactions.id')
            ->havingRaw('ROUND(SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax), 2) != ROUND(account_transactions.amount, 2)')
            ->select('account_transactions.id as account_transaction_id', 'account_transactions.amount', DB::raw('SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as calculated_amount'))
            ->get();

        foreach ($account_transactions as $transaction) {
            \Log::info('updateCashAccountPosSaleTax', [
                'id' => $transaction->account_transaction_id,
                'prevAmount' => $transaction->amount,
                'newAmount' => $transaction->calculated_amount
            ]);

            $transactionToUpdate = AccountTransaction::find($transaction->account_transaction_id);
            $transactionToUpdate->amount = $transaction->calculated_amount;
            $transactionToUpdate->save();
        }

        return redirect()->route('accounts.getCashAccountPosSaleTax')->with('msg', "Cash Account Pos Sales Tax updated");
    }

    public function correctAccountsProductWiseDiscount()
    {
        $products = Product::get();
        $category_ids = [];
        foreach ($products as $product) {
            if (!empty($product->sub_category_id)) {
                $category_ids[] = $product->sub_category_id;
            } elseif (!empty($product->category_id)) {
                $category_ids[] = $product->category_id;
            }
        }
        // Remove duplicates
        $category_ids = array_unique($category_ids);

        $sales_income_account_ids = [];
        foreach ($category_ids as $category_id) {
            $category = Category::where('id', $category_id)->select('sales_income_account_id')->first();
            if (!empty($category) && !empty($category->sales_income_account_id)) {
                $sales_income_account_ids[] = $category->sales_income_account_id;
            }
        }
        $sales_income_account_ids = array_unique($sales_income_account_ids);
        $payments_account_ids = [];
        $payments_account_ids[] = $this->transactionUtil->account_exist_return_id('Accounts Receivable');
        $payments_account_ids[] = Account::getAccountByAccountName('Cash')->id ?? 0;
        $payments_account_ids[] = Account::getAccountByAccountName('Cheques in Hand')->id ?? 0;

        $card_group_id = AccountGroup::getGroupByName('Card', true);
        $card_type_accounts = Account::where('asset_type', $card_group_id)
            ->where(DB::raw("REPLACE(`name`, '  ', ' ')"), '!=', 'Cards (Credit Debit) Account')
            ->pluck('id')
            ->toArray();

        $payments_account_ids = array_merge($payments_account_ids, $card_type_accounts);

        // get account transactions whose amount is not equal to the sell line unit_price_inc_tax (which includes the deducted discount) for products
        $account_transactions_to_correct = AccountTransaction::whereIn('account_transactions.account_id', $sales_income_account_ids)
            ->whereNotNull('account_transactions.account_id')
            ->where('account_transactions.account_id', '!=', 0)
            ->join('transactions', 'account_transactions.transaction_id', '=', 'transactions.id')
            ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
            ->join('accounts', 'account_transactions.account_id', '=', 'accounts.id')
            ->where('transactions.type', "sell")
            ->whereNull('transactions.sub_type')
            ->where('transaction_sell_lines.line_discount_amount', '>', 0)
            ->whereRaw('ROUND((transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax), 2) != ROUND(account_transactions.amount, 2)')
            ->select('account_transactions.*', DB::raw('(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as correct_amount'), 'accounts.name as account_name')
            ->get();

        // get account transactions whose amount is not equal to the total sell line unit_price_inc_tax and bill discount
        $payments_account_transactions_to_correct = AccountTransaction::whereIn('account_transactions.account_id', $payments_account_ids)
            ->whereNotNull('account_transactions.account_id')
            ->where('account_transactions.account_id', '!=', 0)
            ->join('transactions', 'account_transactions.transaction_id', '=', 'transactions.id')
            ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
            ->join('accounts', 'account_transactions.account_id', '=', 'accounts.id')
            ->where('transactions.type', "sell")
            ->whereNull('transactions.sub_type')
            ->where(function ($query) {
                $query->where('transaction_sell_lines.line_discount_amount', '>', 0)
                    ->orWhere('transactions.discount_amount', '>', 0);
            })
            ->groupBy(
                'account_transactions.id',
                'transactions.id',
                'accounts.id',
                'transactions.discount_type',
                'transactions.discount_amount'
            )
            ->havingRaw('ROUND(
            CASE 
                WHEN transactions.discount_type = "fixed" THEN 
                    SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) - transactions.discount_amount
                WHEN transactions.discount_type = "percentage" THEN 
                    SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) - ((transactions.discount_amount / 100) * SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax))
                ELSE 
                    SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax)
            END, 2) != ROUND(account_transactions.amount, 2)')
            ->select(
                'account_transactions.*',
                DB::raw('
                CASE 
                    WHEN transactions.discount_type = "fixed" THEN 
                        SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) - transactions.discount_amount
                    WHEN transactions.discount_type = "percentage" THEN 
                        SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) - ((transactions.discount_amount / 100) * SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax))
                    ELSE 
                        SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax)
                END as correct_amount
            '),
                'accounts.name as account_name'
            )
            ->get();

        $account_transactions_to_correct = $account_transactions_to_correct->merge($payments_account_transactions_to_correct);

        // \Log::debug("correctAccountsProductWiseDiscount",["account_transactions_to_correct"=>count($account_transactions_to_correct),"account_ids"=>$account_ids]);

        $account_ids = array_merge($payments_account_ids, $sales_income_account_ids);
        return view('account.correctAccountsProductWiseDiscount')->with(compact(
            'account_ids',
            'account_transactions_to_correct'
        ));
    }

    public function updateAccountsProductWiseDiscount($id)
    {
        $products = Product::get();
        $category_ids = [];
        foreach ($products as $product) {
            if (!empty($product->sub_category_id)) {
                $category_ids[] = $product->sub_category_id;
            } elseif (!empty($product->category_id)) {
                $category_ids[] = $product->category_id;
            }
        }
        // Remove duplicates
        $category_ids = array_unique($category_ids);

        $sales_income_account_ids = [];
        foreach ($category_ids as $category_id) {
            $category = Category::where('id', $category_id)->select('sales_income_account_id')->first();
            if (!empty($category) && !empty($category->sales_income_account_id)) {
                $sales_income_account_ids[] = $category->sales_income_account_id;
            }
        }
        $sales_income_account_ids = array_unique($sales_income_account_ids);
        $payments_account_ids = [];
        $payments_account_ids[] = $this->transactionUtil->account_exist_return_id('Accounts Receivable');
        $payments_account_ids[] = Account::getAccountByAccountName('Cash')->id ?? 0;
        $payments_account_ids[] = Account::getAccountByAccountName('Cheques in Hand')->id ?? 0;

        $card_group_id = AccountGroup::getGroupByName('Card', true);
        $card_type_accounts = Account::where('asset_type', $card_group_id)
            ->where(DB::raw("REPLACE(`name`, '  ', ' ')"), '!=', 'Cards (Credit Debit) Account')
            ->pluck('id')
            ->toArray();

        $payments_account_ids = array_merge($payments_account_ids, $card_type_accounts);

        // get account transactions whose amount is not equal to the sell line unit_price_inc_tax (which includes the deducted discount) for products
        $account_transactions_to_correct = AccountTransaction::whereIn('account_transactions.account_id', $sales_income_account_ids)
            ->whereNotNull('account_transactions.account_id')
            ->where('account_transactions.account_id', '!=', 0)
            ->join('transactions', 'account_transactions.transaction_id', '=', 'transactions.id')
            ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
            ->join('accounts', 'account_transactions.account_id', '=', 'accounts.id')
            ->where('transactions.type', "sell")
            ->whereNull('transactions.sub_type')
            ->where('transaction_sell_lines.line_discount_amount', '>', 0)
            ->whereRaw('ROUND((transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax), 2) != ROUND(account_transactions.amount, 2)')
            ->select('account_transactions.*', DB::raw('(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) as correct_amount'), 'accounts.name as account_name')
            ->get();

        // get account transactions whose amount is not equal to the total sell line unit_price_inc_tax and bill discount
        $payments_account_transactions_to_correct = AccountTransaction::whereIn('account_transactions.account_id', $payments_account_ids)
            ->whereNotNull('account_transactions.account_id')
            ->where('account_transactions.account_id', '!=', 0)
            ->join('transactions', 'account_transactions.transaction_id', '=', 'transactions.id')
            ->join('transaction_sell_lines', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
            ->join('accounts', 'account_transactions.account_id', '=', 'accounts.id')
            ->where('transactions.type', "sell")
            ->whereNull('transactions.sub_type')
            ->where(function ($query) {
                $query->where('transaction_sell_lines.line_discount_amount', '>', 0)
                    ->orWhere('transactions.discount_amount', '>', 0);
            })
            ->groupBy(
                'account_transactions.id',
                'transactions.id',
                'accounts.id',
                'transactions.discount_type',
                'transactions.discount_amount'
            )
            ->havingRaw('ROUND(
            CASE 
                WHEN transactions.discount_type = "fixed" THEN 
                    SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) - transactions.discount_amount
                WHEN transactions.discount_type = "percentage" THEN 
                    SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) - ((transactions.discount_amount / 100) * SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax))
                ELSE 
                    SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax)
            END, 2) != ROUND(account_transactions.amount, 2)')
            ->select(
                'account_transactions.*',
                DB::raw('
                CASE 
                    WHEN transactions.discount_type = "fixed" THEN 
                        SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) - transactions.discount_amount
                    WHEN transactions.discount_type = "percentage" THEN 
                        SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax) - ((transactions.discount_amount / 100) * SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax))
                    ELSE 
                        SUM(transaction_sell_lines.quantity * transaction_sell_lines.unit_price_inc_tax)
                END as correct_amount
            '),
                'accounts.name as account_name'
            )
            ->get();

        $account_transactions_to_correct = $account_transactions_to_correct->merge($payments_account_transactions_to_correct);

        foreach ($account_transactions_to_correct as $account_transaction_to_correct) {
            if ($id != "All") {
                if ($account_transaction_to_correct->account_id != $id) {
                    continue;
                }
            }
            \Log::info("Updating AccountTransaction Discount - id: {$account_transaction_to_correct->id} prevAmount: {$account_transaction_to_correct->amount} newAmount: {$account_transaction_to_correct->correct_amount}");
            $account_transaction_to_correct->amount = $account_transaction_to_correct->correct_amount;
            $account_transaction_to_correct->update();
        }

        return redirect()->route('accounts.correctAccountsProductWiseDiscount')->with('msg', "Account Transactions Product Wise Discount updated");
    }
}
