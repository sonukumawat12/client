<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Modules\Hms\Entities\HmsTransactionClass;
use App\Utils\Util;
use Carbon\Carbon;

class SendDepartureReminderSms extends Command
{
    protected $signature = 'sms:send-departure-reminders';

    protected $description = 'Send SMS to guests with departure time less than 1 day away';

    public function handle()
    {
        $now = Carbon::now();
        $oneDayFromNow = $now->copy()->addDay();

        $transactions = HmsTransactionClass::whereBetween('hms_booking_departure_date_time', [$now, $oneDayFromNow])->get();

        foreach ($transactions as $transaction) {
            // Assume there's a phone number field like $transaction->guest_phone or similar
            $phone1 = $transaction->mobile_no ?? null;
            $phone2 = $transaction->whatsapp_no ?? null;

            // if ($phone1) {
                // Replace this with your SMS sending logic
                // $message = "Reminder: Your booking departs on " . Carbon::parse($transaction->hms_booking_departure_date_time)->format('d M Y, h:i A');
                
                // // Example log (replace with actual SMS service call)
                // \Log::info("SMS sent to {$phone}: {$message}");
                Util::print_sms($transaction->id, $phone1, 'departure_reminder');
                Util::print_sms($transaction->id, $phone2, 'departure_reminder');

                // Optionally call an SMS service like:
                // SmsService::send($phone, $message);
            // }
        }

        $this->info('Departure reminder SMSs sent.');
    }
}
