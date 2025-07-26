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
                'amount' => 'required|numeric|min:0',
                'user_id' => 'sometimes'
            ]);

            // Safely extract user_id
            $user_id = $validated['user_id'] ?? null;

            if (is_null($user_id)) {
                $user = User::updateOrCreate(
                    ['email' => $validated['email']],
                    ['full_name' => $validated['full_name']]
                );
                $user_id = $user->id;
            }

            $plan = Plan::where('name', 'Pro')->first();

            if (is_null($plan)) {
                return response()->json(['message' => 'This Plan Schedule is not set', 'status' => false], 400);
            }

            $transaction = Transaction::create([
                'subscriber_id' => $user_id,
                'amount' => $validated['amount'],
                'status' => 'pending',
                'plan_id' => $plan->id
            ]);

            return response()->json(['message' => 'Transaction Successful', 'status' => true, 'data' => $transaction], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage(), 'status' => false], 500);
        }
    }


    public function verifyPayment(Request $request)
    {
        try {
            $reQueryResponse = $request->all(); // Get full request payload

            if (
                !isset($reQueryResponse['reference']) ||
                !isset($reQueryResponse['tran_id'])
            ) {
                return response()->json([
                    'message' => 'Invalid request data.'
                ], 400);
            }

            $status = strtolower($reQueryResponse['reference']['status']);
            $reference = $reQueryResponse['reference']['reference'];
            $message = strtolower($reQueryResponse['reference']['message']);
            $transaction_id = $reQueryResponse['tran_id'];

            $transaction = Transaction::find($transaction_id);

            if ($transaction) {
                if ($status === 'success' && $message === 'approved') {
                    $transaction->update([
                        'status' => 'approved',
                        'raw_response' => json_encode($reQueryResponse),
                    ]);
                } else {
                    $transaction->update([
                        'status' => 'declined',
                        'raw_response' => json_encode($reQueryResponse),
                    ]);
                }

                return response()->json([
                    'message' => 'Transaction updated successfully.',
                    'data' => $transaction->fresh(),
                    'status' => true
                ], 200);
            }

            return response()->json([
                'message' => 'Transaction not found.',
                'status' => false
            ], 404);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while verifying the payment.',
                'error' => $th->getMessage(),
                'status' => false
            ], 500);
        }
    }
}
