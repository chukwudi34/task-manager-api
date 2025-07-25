<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\User;
use Illuminate\Http\Request;

class Usercontroller extends Controller
{
    public function addNewUser(Request $request)
    {
        try {

            $validated = $request->validate([
                'email' => 'required|email|unique:users,email',
                'full_name' => 'required|string|max:255',
            ]);

            $user = User::create([
                'email' => $validated['email'],
                'full_name' => $validated['full_name']
            ]);

            return response()->json(['message' => 'User created successfully', 'status' => true, 'data' => $user], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while fetching tasks.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/plan",
     *     summary="Get the details of the Pro plan",
     *     description="Returns details of the predefined 'pro' plan if it exists.",
     *     tags={"Plans"},
     *     @OA\Response(
     *         response=200,
     *         description="Plan fetched successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Plan fetched successfully."),
     *             @OA\Property(property="status", type="boolean", example=true),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 ref="#/components/schemas/Plan"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plan not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="Plan not found in the system."),
     *             @OA\Property(property="status", type="boolean", example=false)
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Server error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="An error occurred while retrieving the plan."),
     *             @OA\Property(property="error", type="string", example="SQLSTATE[42S02]: Base table or view not found..."),
     *             @OA\Property(property="status", type="boolean", example=false)
     *         )
     *     )
     * )
     */
    public function plan()
    {
        try {
            $planType = strtolower('pro');

            $plan = Plan::where('name', $planType)->first();

            if (is_null($plan)) {
                return response()->json([
                    'message' => 'Plan not found in the system.',
                    'status' => false
                ], 404);
            }

            return response()->json([
                'message' => 'Plan fetched successfully.',
                'data' => $plan,
                'status' => true
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'An error occurred while retrieving the plan.',
                'error' => $th->getMessage(),
                'status' => false
            ], 500);
        }
    }
}
