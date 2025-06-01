<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Superadmin\Entities\Subscription;
use App\System;
use Carbon\Carbon;
use App\Utils\BusinessUtil;
use Illuminate\Support\Facades\Log;

class SubscriptionReminder extends Command
{
    protected $signature = 'subscription:send-reminders';
    protected $description = 'Send SMS reminders for subscriptions nearing expiration';

    public function handle()
    {
        $this->info('Sending subscription reminders...');

        // Fetch reminder days from System table
        $reminder_days = System::whereIn('key', [
                'first_reminder_days',
                'second_reminder_days',
                'third_reminder_days'
            ])
            ->pluck('value')
            ->map(fn($v) => (int) $v)
            ->unique()
            ->sortDesc();

        // Get all active subscriptions
        $subscriptions = Subscription::where('status', 'approved')->get();

        foreach ($subscriptions as $subscription) {
            if ($subscription->end_date) {
                $end_date = Carbon::parse($subscription->end_date);
                $today = Carbon::today();
                $diff_in_days = $today->diffInDays($end_date, false);
                $business = $subscription->business;

                if ($reminder_days->contains($diff_in_days)) {
                    // Get contact details (you may need to adjust these fields)
                    $package_details = $subscription->package_details;

                    // Decode reminder phones (ensure it's an array)
                    $reminder_phones = [];
                    if (!empty($package_details->reminder_phone)) {
                        $decoded = is_array($package_details->reminder_phone)
                            ? $package_details->reminder_phone
                            : json_decode($package_details->reminder_phone, true);

                        // Filter out null or empty entries
                        $reminder_phones = array_filter($decoded, fn($phone) => !empty($phone));
                    }


                    // Send SMS to each valid phone
                    foreach ($reminder_phones as $phone) {
                        $message = $package_details->message_content ?? 'Your subscription is about to expire. Please renew it.';

                        $businessUtil = new BusinessUtil();
                        $sms_settings = empty($business->sms_settings)
                            ? $businessUtil->defaultSmsSettings()
                            : $business->sms_settings;
                        // $phonesArray = explode(',', $phones);
                        $data = [
                            'sms_settings' => $sms_settings,
                            'mobile_number' => $phone,
                            'sms_body' => strip_tags($message) // Optional: remove HTML tags
                        ];
                        
                        $businessUtil->sendSms($data, 'transaction_changed');
                        
                        Log::info("SMS sent to {$phone} in {$diff_in_days} days before expiration.");
                    }
                }
            }
        }

        $this->info('All reminders processed.');
    }

}
