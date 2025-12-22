<?php

namespace App\Http\Services;

use App\Http\Traits\ApiResponse;
use App\Models\Invoice;
use App\Models\OwnedCard;
use App\Models\PromoterRatio;
use App\Models\PromotionActivity;
use App\Models\ProvisionalData;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProcessCardsPaymentService
{
    use ApiResponse;

    public function processCardsPayment($data)
    {
        try {
            $provisionalData = ProvisionalData::where('uniqueId', $data['provisionalData_id'])->firstOrFail();

            // Retrieve activity_id from ProvisionalData details
            $metadata = json_decode($provisionalData->metadata, true);

            $activityId = $metadata['activity_id'] ?? null;

            $activity = $activityId
                ? PromotionActivity::where('id', $activityId)->firstOrFail()
                : null;

            $invoice = Invoice::where('invoice_number', $data['invoice_number'])->firstOrFail();

            $ratios = PromoterRatio::find(1);

            if ($invoice->status == 'paid') {
                return $this->errorResponse("This Invoice is already Finshied .");
            }

            $cards = $metadata['items'] ?? [];


            DB::beginTransaction();

            foreach ($cards as $card) {
                $cardId = $card['id'] ?? null;
                $quantity = isset($card['quantity']) ? intval($card['quantity']) : 1;
                $durationRaw = $card['duration'] ?? null;

                if ($durationRaw && preg_match('/(\d+)/', $durationRaw, $matches)) {
                    $months = intval($matches[1]);
                } else {
                    $months = 12;
                }

                for ($i = 0; $i < max(1, $quantity); $i++) {

                    $cardNumber = implode('', [
                        str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
                        str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
                        str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
                        str_pad(random_int(1000, 9999), 4, '0', STR_PAD_LEFT),
                    ]);

                    $cvv = str_pad((string) random_int(0, 999), 3, '0', STR_PAD_LEFT);

                    $issueDate = Carbon::now();
                    $expiryDate = (clone $issueDate)->addMonths($months);

                    OwnedCard::create([
                        'cvv' => $cvv,
                        'owner_id' => $invoice->owner_id,
                        'issue_date' => $issueDate,
                        'expiry_date' => $expiryDate,
                        'owner_type' => $invoice->owner_type,
                        'card_number' => $cardNumber,
                        'status' => 'active',
                        'card_id' => $cardId,
                        'price' => isset($card['price']) ? (float) $card['price'] : null,
                        'title' => $card['title'] ?? null,
                    ]);
                }
            }


            $invoice->update(['status' => 'paid', 'payment_date' => now()]);

            if ($activity) {

                // Fallback IP
                $ip = $metadata->ip_address
                    ?? ($data ? $data->ip() : null);

                // Fallback Device Type
                $device = $metadata->device_type
                    ?? ($data ? $data->header('User-Agent') : null);

                $activity->update([
                    'is_active' => true,
                    'commission_amount' => $ratios->purchase_ratio,
                    'country' => $metadata->country ?? null,
                    'ip_address' => $ip ?? null,
                    'device_type' => $device ?? null,
                    'activity_at' => now(),
                ]);
            }

            $provisionalData->delete();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Cards created and invoice updated successfully.',
                'invoice_id' => $invoice->id,
            ], 200);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("ERROR in processCardsPayment", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $this->errorResponse($e->getMessage(), 500);
        }
    }
}
