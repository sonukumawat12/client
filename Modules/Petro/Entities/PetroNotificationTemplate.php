<?php

namespace Modules\Petro\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Business;

class PetroNotificationTemplate extends Model
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
    public static function getTemplate($business_id, $template_for)
    {
        $notif_template = PetroNotificationTemplate::where('business_id', $business_id)
                                                        ->where('template_for', $template_for)
                                                        ->first();
                                                        
        $business = Business::where('id', $business_id)->first();                                                
        $phone_nos =  empty($business->sms_settings) ? '' : $business->sms_settings['msg_phone_nos'];
        
        $template = [
            'sms_body' => !empty($notif_template->sms_body) ? $notif_template->sms_body : '',
            'template_for' => $template_for,
            'auto_send_sms' => !empty($notif_template->auto_send_sms) ? 1 : 0,
            'phone_nos' => $notif_template->phone_nos ?? $phone_nos
        ];

        return $template;
    }

    public static function notifications()
    {
        
  
        return [
            'settlements' => [
                'name' => __('petro::lang.settlement'),
                'extra_tags' => ['{settlement_no}, {settlement_date},{pump_operator_name}', '{settlement_pumps}', '{total_sale_amount}', '{total_cash}', '{total_cards}', '{total_credit_sales}', '{total_short}', '{total_loans}', '{total_cheques}', '{cash_deposit}', '{total_expenses}', '{total_excess}', '{loan_payments}', '{owners_drawings}']
            ],
            
            'edit_settlements' => [
                'name' => __('petro::lang.edit_settlement'),
                'extra_tags' => ['{settlement_no}, {editted_date}', '{original_details}', '{editted_details}', '{user_editted}']
            ],
            
            'day_end_settlement' => [
                'name' => __('petro::lang.day_end_settlement'),
                'extra_tags' => ['{date},{total_sale}', '{pumpers_worked}', '{pumps}',  '{total_cash}','{total_cards}','{total_credit_sales}','{total_short}','{total_loans}','{total_cheques}', '{cash_deposit}', '{total_expenses}', '{total_excess}', '{loan_payments}', '{owners_drawings}','{tank_product_qty_difference}','{fuel_category_products}', '{product_sold_qty}', '{bulk_sale_qty}']
            ],
            'stock_and_dip_details' => [
                'name' => __('petro::lang.stock_and_dip_details'),
                'extra_tags' => ['{date_entered},{time_entered}','{dip_details}','{opening_stock}','{received_stock}','{sold_qty}','{testing_qty}','{balance_stock}','{dip_stock}','{difference_Stock}']
            ],
            
            'load_received' => [
                'name' => __('petro::lang.load_received'),
                'extra_tags' => ['{date},{load_details}']
            ],
            
            'daily_collection' => [
                'name' => __('petro::lang.daily_collection'),
                'extra_tags' => ['{date},{time}', '{pump_operator}', '{amount}', '{pumper_cummulative_amount}', '{total_amount}']
            ],
            
            'pumper_dashboard_cash_deposit' => [
                'name' => __('petro::lang.pumper_dashboard_cash_deposit'),
                'extra_tags' => ['{date},{time}', '{pump_operator}', '{amount}']
            ],
            
            'pumper_dashboard_credit_sales' => [
                'name' => __('petro::lang.pumper_dashboard_credit_sales'),
                'extra_tags' => ['{date}, {time}', '{pump_operator}', '{customer}','{amount}','{order_no}','{cumulative_amount}', '{customer_reference}']
            ],
            
            'pumper_dashboard_credit_sales_customer' => [
                'name' => __('petro::lang.pumper_dashboard_credit_sales_customer'),
                'extra_tags' => ['{date}, {time}', '{pump_operator}', '{customer}','{amount}','{order_no}','{cumulative_amount}', '{customer_reference}']
            ]
        ];
    }
}
