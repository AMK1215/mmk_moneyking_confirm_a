<?php

namespace App\Http\Controllers\Api\V1\Webhook;

use App\Enums\StatusCode;
use App\Http\Controllers\Controller;
use App\Http\Requests\Slot\CancelBetNResultRequest;
use App\Models\User;
use App\Models\Webhook\Bet;
use App\Models\Webhook\BetNResult;
use App\Models\Webhook\Result;
use App\Services\PlaceBetWebhookService;
use App\Traits\UseWebhook;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CancelBetNResultController extends Controller
{
    use UseWebhook;

    public function handleCancelBetNResult(CancelBetNResultRequest $request): JsonResponse
    {
        $transactions = $request->getTransactions();

        DB::beginTransaction();
        try {
            foreach ($transactions as $transaction) {
                $player = $this->getPlayer($transaction['PlayerId']);
                if (! $player) {
                    return $this->buildErrorResponse(StatusCode::InvalidPlayerPassword);
                }

                if (! $this->validateSignature($transaction)) {
                    return $this->buildErrorResponse(StatusCode::InvalidSignature, $player->wallet->balanceFloat ?? 0);
                }

                // Check for TranID not found
                $existingtranId = BetNResult::where('tran_id', $transaction['TranId'])->first();

                if ($existingtranId && $existingtranId->status === 'processed') {
                    // Log the cancellation attempt for an already processed transaction
                    Log::info('Cancellation not allowed - result already processed', ['TranId' => $existingtranId->tran_id]);

                    // Return a success response with the player's current balance
                    $balance = $request->getMember()->balanceFloat;

                    return $this->buildErrorResponse(StatusCode::NotEligibleCancel, $balance);
                }

                // $existingTransaction = BetNResult::where('tran_id', $transaction['TranId'])->first();

                // if ($this->isTransactionProcessed($existingTransaction)) {
                //     return $this->buildErrorResponse(StatusCode::NotEligibleCancel, $player->wallet->balanceFloat);
                // }

                // Check if a result exists for the round (cannot cancel if result exists)
                // $associatedResult = Bet::where('bet_id', $transaction['TranId'])->first();
                // if ($associatedResult) {
                //     Log::info('Cancellation not allowed - result already processed', ['TranId' => $transaction['TranId']]);

                //     // Return 900500 Not Eligible Cancel without adjusting balance
                //     return $this->buildErrorResponse(StatusCode::NotEligibleCancel, $player->wallet->balanceFloat);
                // }

                $this->processTransaction($existingtranId, $transaction, $player);
            }

            DB::commit();

            return $this->buildSuccessResponse($player->wallet->balanceFloat ?? 0);
        } catch (\Exception $e) {
            DB::rollBack();
            $this->logException($e);

            return response()->json(['message' => 'Failed to handle CancelBetNResult'], 500);
        }
    }

    private function getPlayer(string $playerId): ?User
    {
        return User::where('user_name', $playerId)->first();
    }

    private function validateSignature(array $transaction): bool
    {
        $generatedSignature = $this->generateSignature($transaction);
        Log::debug('Signature validation', [
            'generated_signature' => $generatedSignature,
            'provided_signature' => $transaction['Signature'],
            'transaction_data' => $transaction, // Optional: log the entire transaction for detailed debugging
        ]);

        return $generatedSignature === $transaction['Signature'];
    }

    private function isTransactionProcessed(?BetNResult $existingTransaction): bool
    {
        return $existingTransaction && $existingTransaction->status === 'processed';
    }

    private function processTransaction(?BetNResult $existingTransaction, array $transaction, User $player): void
    {
        if ($existingTransaction) {
            $existingTransaction->status = 'processed';
            $existingTransaction->save();
        } else {
            BetNResult::create([
                'user_id' => $player->id,
                'operator_id' => $transaction['OperatorId'],
                'request_date_time' => $transaction['RequestDateTime'],
                'signature' => $transaction['Signature'],
                'player_id' => $transaction['PlayerId'],
                'currency' => $transaction['Currency'],
                'tran_id' => $transaction['TranId'],
                'game_code' => $transaction['GameCode'],
                'tran_date_time' => $transaction['TranDateTime'],
                'status' => 'processed',
            ]);
        }
    }

    private function buildSuccessResponse(float $newBalance): JsonResponse
    {
        return response()->json([
            'Status' => StatusCode::OK->value,
            'Description' => 'Success',
            'ResponseDateTime' => now()->format('Y-m-d H:i:s'),
            'Balance' => round($newBalance, 4),
        ]);
    }

    private function buildErrorResponse(StatusCode $statusCode, float $balance = 0): JsonResponse
    {
        return response()->json([
            'Status' => $statusCode->value,
            'Description' => $statusCode->name,
            'ResponseDateTime' => now()->format('Y-m-d H:i:s'),
            'Balance' => round($balance, 4),
        ]);
    }

    private function generateSignature(array $transaction): string
    {
        return md5(
            'CancelBetNResult'.
            $transaction['TranId'].
            $transaction['RequestDateTime'].
            $transaction['OperatorId'].
            config('game.api.secret_key').
            $transaction['PlayerId']
        );
    }

    private function logException(\Exception $e): void
    {
        Log::error('Failed to handle CancelBetNResult', [
            'error' => $e->getMessage(),
            'line' => $e->getLine(),
            'file' => $e->getFile(),
        ]);
    }
}