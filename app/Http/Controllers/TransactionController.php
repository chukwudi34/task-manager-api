<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * @OA\Post(
     *     path="/payment/initialize",
     *     summary="Initialize a payment and create a user if needed",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name","email","amount"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="email", type="string", format="email"),
     *             @OA\Property(property="amount", type="number", format="float"),
     *             @OA\Property(property="type", type="string", example="mock")
     *         )
     *     ),
     *     @OA\Response(response=200, description="Payment initialized"),
     *     @OA\Response(response=422, description="Validation error"),
     *     @OA\Response(response=500, description="Server error")
     * )
     */
    public function initializePayment(Request $request)
    {
        try {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email',
                'plan' => 'required|string',
                'amount' => 'required|numeric|min:0'
            ]);

            $user = User::updateOrCreate(
                ['email' => $validated['email']],
                ['full_name' => $validated['full_name']]
            );

            // $plan_type = strtolower($validated['plan']);
            $plan = Plan::where('name', 'Pro')->first();

            if (is_null($plan)) {
                return response()->json(['message' => 'This Plan Schedule is not set', 'status' => false], 400);
            }

            // Create transaction
            $transaction = Transaction::create([
                'subscriber_id' => $user->id,
                'amount_paid' => $validated['amount'],
                'status' => 'pending',
                'plan_id' =>  $plan->id
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => false], 500);
        }
    }

    public function verifyPayment(array $reQueryResponse)
    {
        return $reQueryResponse;
        try {
            // Ensure the necessary keys exist in the response
            if (
                !isset($reQueryResponse['status']) ||
                !isset($reQueryResponse['data']['transaction_status']) ||
                !isset($reQueryResponse['data']['refrence_id'])
            ) {
                return response()->json([
                    'message' => 'Invalid payment response structure.'
                ], 400);
            }

            $referenceId = $reQueryResponse['data']['refrence_id'];
            $transaction = null;

            if (
                $reQueryResponse['status'] === 'success' &&
                $reQueryResponse['data']['transaction_status'] === 'approved'
            ) {
                $transaction = Transaction::where('refrence_id', $referenceId)->first();

                if ($transaction) {
                    $transaction->update([
                        'amount_paid' => $reQueryResponse['data']['original_amount'] ?? 0,
                        'transaction_status' => 'approved',
                        'raw_response' => json_encode($reQueryResponse),
                    ]);
                }
            } else {
                $transaction = Transaction::where('transaction_batch_id', $referenceId)->first();

                if ($transaction) {
                    $transaction->update([
                        'transaction_status' => 'declined',
                        'amount_paid' => 0,
                    ]);
                }
            }

            if ($transaction) {
                return response()->json([
                    'message' => 'Transaction updated successfully.',
                    'data' => $transaction->fresh()
                ], 200);
            }

            return response()->json([
                'message' => 'Transaction not found.'
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while verifying the payment.',
                'error' => $th->getMessage()
            ], 500);
        }
    }
}
