<?php

namespace Modules\Superadmin\Entities;

use Illuminate\Database\Eloquent\Model;
use App\NotificationTemplate;

class DefaultNotificationTemplate extends Model
{
    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Retrives notification template from database
     *
     * @param  int  $business_id
     * @param  string  $template_for
     * @return array $template
     */
    public static function getTemplate($template_for)
    {
        $notif_template = DefaultNotificationTemplate::where('template_for', $template_for)
                                                        ->first();
        $template = [
            'subject' => !empty($notif_template->subject) ? $notif_template->subject : '',
            'sms_body' => !empty($notif_template->sms_body) ? $notif_template->sms_body : '',
            'whatsapp_text' => !empty($notif_template->whatsapp_text) ? $notif_template->whatsapp_text : '',
            'email_body' => !empty($notif_template->email_body) ? $notif_template->email_body
                             : '',
            'template_for' => $template_for,
            'cc' => !empty($notif_template->cc) ? $notif_template->cc : '',
            'bcc' => !empty($notif_template->bcc) ? $notif_template->bcc : '',
            'auto_send' => !empty($notif_template->auto_send) ? 1 : 0,
            'auto_send_sms' => !empty($notif_template->auto_send_sms) ? 1 : 0,
            'auto_send_wa_notif' => !empty($notif_template->auto_send_wa_notif)
             ? 1 : 0
        ];

        return $template;
    }
    
    public static function createTemplates($business_id){
        $templates = DefaultNotificationTemplate::all();
        foreach($templates as $one){
            // check existence
            $exists = NotificationTemplate::where('business_id', $business_id)
                                                        ->where('template_for', $one->template_for)
                                                        ->first();
            if(empty($exists)){
                $data = $one->toArray();
                $data['business_id'] = $business_id;
                NotificationTemplate::create($data);
            }
        }
    }

    public static function customerNotifications()
    {
        
  
        return [
            'new_sale' => [
                'name' => __('lang_v1.new_sale'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_name}', '{invoice_number}', '{invoice_url}', '{total_amount}', '{paid_amount}', '{due_amount}', '{cumulative_due_amount}', '{due_date}', '{location_name}', '{location_address}', '{location_email}', '{location_phone}', '{location_custom_field_1}', '{location_custom_field_2}', '{location_custom_field_3}', '{location_custom_field_4}']
            ],
            'credit_sale' => [
                'name' => __('Credit Sale'),
                'extra_tags' => ['{transaction_date},{business_name}', '{contact_name}', '{total_amount}',  '{due_amount}','{paid_amount}','{due_amount}','{invoice_number}','{cumulative_due_amount}']
            ],
            'payment_received' => [
                'name' => __('lang_v1.payment_received'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_name}', '{invoice_number}', '{payment_ref_number}', '{received_amount}']
            ],
            'customer_payment_deleted' => [
                'name' => __('lang_v1.payment_deleted'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_name}', '{payment_ref_number}', '{received_amount}']
            ],
            'customer_payment_editted' => [
                'name' => __('lang_v1.payment_editted'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_name}', '{payment_ref_number}', '{received_amount}']
            ],
            'customer_sale_deleted' => [
                'name' => __('lang_v1.sale_deleted'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_name}', '{invoice_number}', '{amount}']
            ],
            'payment_reminder' => [
                'name' =>  __('lang_v1.payment_reminder'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_name}', '{invoice_number}', '{due_amount}', '{cumulative_due_amount}', '{due_date}']
            ],
            'new_booking' => [
                    'name' => __('lang_v1.new_booking'),
                    'extra_tags' => self::bookingNotificationTags()
                ],
            'new_quotation' => [
                'name' => __('lang_v1.new_quotation'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_name}', '{invoice_number}', '{total_amount}', '{quote_url}', '{location_name}', '{location_address}', '{location_email}', '{location_phone}', '{location_custom_field_1}', '{location_custom_field_2}', '{location_custom_field_3}', '{location_custom_field_4}']
            ],
        ];
    }

    public static function generalNotifications()
    {
        return [
            'send_ledger' => [
                'name' => __('lang_v1.send_ledger'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_name}', '{balance_due}']
            ],
            'transaction_deleted' => [
                'name' => 'Account Transaction Delete',
                'extra_tags' => ['{transaction_date},{transaction_type}', '{account_name}', '{amount}', '{transaction_date}','{invoice_no}','{staff}']
            ],
            'transaction_changed' => [
                'name' => "Account Transaction Modify",
                'extra_tags' => ['{transaction_date},{transaction_type}', '{account_name}', '{amount}', '{transaction_date}','{invoice_no}','{staff}']
            ],
            'deposit' => [
                'name' => "Deposits",
                'extra_tags' => ['{transaction_date},{amount}', '{account}', '{date}', '{staff}']
            ],
            'transfer' => [
                'name' => "Transfers",
                'extra_tags' => ['{transaction_date},{amount}', '{account}', '{date}', '{staff}']
            ],
            'expense_created' => [
                'name' => "Expense Added",
                'extra_tags' => ['{transaction_date},{ref}', '{amount}', '{account}','{staff}']
            ],
            'expense_deleted' => [
                'name' => "Expense Deleted",
                'extra_tags' => ['{transaction_date},{ref}', '{amount}', '{account}','{staff}']
            ],
            'expense_changed' => [
                'name' => "Expense Changed",
                'extra_tags' => ['{transaction_date},{ref}', '{amount}', '{account}','{staff}']
            ],
            'cash_deposit' => [
                'name' => "Cash Deposit",
                'extra_tags' => ['{transaction_date},{bank}', '{amount}', '{account}','{time}']
            ],
            'general_payment_deleted' => [
                'name' => __('lang_v1.payment_deleted'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_name}', '{payment_ref_number}', '{received_amount}']
            ],
            'general_payment_editted' => [
                'name' => __('lang_v1.payment_editted'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_name}', '{payment_ref_number}', '{received_amount}']
            ],
            'general_expense_deleted' => [
                'name' => __('lang_v1.expense_deleted'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_name}', '{expense_number}', '{amount}']
            ],
            'general_purchase_deleted' => [
                'name' => __('lang_v1.purchase_deleted'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_name}', '{purchase_ref_number}', '{amount}']
            ],
             'general_sale_deleted' => [
                'name' => __('lang_v1.sale_deleted'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_name}', '{invoice_number}', '{amount}']
            ],
        ];
    }

    public static function supplierNotifications()
    {
        return [
            'new_order' => [
                'name' => __('lang_v1.new_order'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_business_name}', '{contact_name}', '{order_ref_number}', '{total_amount}', '{received_amount}', '{due_amount}', '{location_name}', '{location_address}', '{location_email}', '{location_phone}', '{location_custom_field_1}', '{location_custom_field_2}', '{location_custom_field_3}', '{location_custom_field_4}']
            ],
            'payment_paid' => [
                'name' => __('lang_v1.payment_paid'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_business_name}', '{contact_name}', '{order_ref_number}', '{payment_ref_number}', '{paid_amount}']
            ],
            'items_received' => [
                'name' =>  __('lang_v1.items_received'), 
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_business_name}', '{contact_name}', '{order_ref_number}'],
            ],
            'items_pending' => [
                'name' => __('lang_v1.items_pending'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_business_name}', '{contact_name}', '{order_ref_number}']
            ],
            
            'supplier_payment_deleted' => [
                'name' => __('lang_v1.payment_deleted'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_name}', '{payment_ref_number}', '{received_amount}']
            ],
            'supplier_payment_editted' => [
                'name' => __('lang_v1.payment_editted'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_name}', '{payment_ref_number}', '{received_amount}']
            ],
            'supplier_expense_deleted' => [
                'name' => __('lang_v1.expense_deleted'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_name}', '{expense_number}', '{amount}']
            ],
            'supplier_purchase_deleted' => [
                'name' => __('lang_v1.purchase_deleted'),
                'extra_tags' => ['{transaction_date},{business_name}', '{business_logo}', '{contact_name}', '{purchase_ref_number}', '{amount}']
            ],
        ];
    }

    public static function notificationTags()
    {
        return ['{transaction_date},{contact_name}', '{invoice_number}', '{total_amount}',
        '{paid_amount}', '{due_amount}', '{business_name}', '{business_logo}', '{cumulative_due_amount}', '{due_date}', '{contact_business_name}'];
    }

    public static function bookingNotificationTags()
    {
        return ['{transaction_date},{contact_name}', '{table}', '{start_time}',
        '{end_time}', '{location}', '{service_staff}', '{correspondent}', '{business_name}', '{business_logo}', '{location_name}', '{location_address}', '{location_email}', '{location_phone}', '{location_custom_field_1}', '{location_custom_field_2}', '{location_custom_field_3}', '{location_custom_field_4}'];
    }

    
}
