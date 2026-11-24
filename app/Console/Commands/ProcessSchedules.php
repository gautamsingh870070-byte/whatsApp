<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ProcessSchedules extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:process-schedules';
    protected $description = 'Process scheduled WhatsApp campaigns';

    public function handle()
    {
        $timezone = config('app.timezone', 'Asia/Kolkata');
        $now = \Carbon\Carbon::now($timezone);
        $this->info("Checking for scheduled messages at {$now} ({$timezone})...");

        $schedules = \App\Models\ScheduledMessage::where('status', 'pending')
            ->where('scheduled_at', '<=', $now)
            ->get();

        if ($schedules->isEmpty()) {
            $this->info("No pending schedules found.");
            return;
        }

        $whatsAppService = new \App\Services\WhatsAppService();
        $controller = new \App\Http\Controllers\WhatsAppController($whatsAppService);

        foreach ($schedules as $schedule) {
            $schedule->status = 'processing';
            $schedule->save();
            $this->info("Processing schedule ID {$schedule->id}...");

            try {
                // Call the controller's logic (we will abstract this or just process it here)
                $numbers = preg_split('/[\s,]+/', $schedule->phone_numbers, -1, PREG_SPLIT_NO_EMPTY);
                $successCount = 0;
                $failCount = 0;

                foreach ($numbers as $number) {
                    $number = preg_replace('/[^0-9]/', '', $number);
                    if (empty($number)) continue;
                    if (strlen($number) == 10) $number = '91' . $number;

                    if ($schedule->campaign_name === 'jaspers_market_plain_text_v1') {
                        $response = $whatsAppService->sendJasperTemplate($number, $schedule->campaign_name);
                    } elseif ($schedule->campaign_name === 'stm_digital') {
                        $response = $whatsAppService->sendTicketTemplate($number, 'stm_digital', 'en', '');
                    } elseif ($schedule->campaign_name === 'stm_customer_support' || $schedule->campaign_name === env('WHATSAPP_TICKET_TEMPLATE', 'stm_customer_support')) {
                        $response = $whatsAppService->sendTicketTemplate($number, $schedule->campaign_name, 'en_US', '');
                    } elseif ($schedule->campaign_name === '3p_direct_integration_test_template') {
                        $response = $whatsAppService->sendDirectIntegrationTemplate($number);
                    } elseif ($schedule->campaign_name !== 'custom_campaign' && !empty($schedule->campaign_name)) {
                        $response = $whatsAppService->sendTicketTemplate($number, $schedule->campaign_name, 'en_US', '');
                    } else {
                        $response = $whatsAppService->sendCustomMessage(
                            $number,
                            $schedule->message_text ?? '',
                            $schedule->media_id,
                            $schedule->media_type,
                            $schedule->visit_url,
                            $schedule->call_number
                        );
                    }

                    $apiResponseToSave = $response['response'] ?? [];
                    if (!empty($schedule->message_text)) {
                        $apiResponseToSave['sent_text'] = $schedule->message_text;
                    }

                    \App\Models\WhatsAppLog::create([
                        'user_id'       => $schedule->user_id,
                        'phone_number'  => $number,
                        'template_name' => $schedule->campaign_name,
                        'message_type'  => $schedule->media_type ?? 'text',
                        'status'        => ($response['success'] ?? false) ? 'success' : 'failed',
                        'api_response'  => $apiResponseToSave,
                    ]);

                    if ($response['success']) $successCount++;
                    else $failCount++;
                }

                $schedule->status = 'completed';
                $schedule->save();
                $this->info("Completed. Success: {$successCount}, Failed: {$failCount}");

            } catch (\Exception $e) {
                $schedule->status = 'failed';
                $schedule->save();
                $this->error("Failed to process schedule ID {$schedule->id}: " . $e->getMessage());
            }
        }
    }
}
