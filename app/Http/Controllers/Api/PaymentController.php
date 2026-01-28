<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Payment;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Patient;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaymentController extends Controller
{
    
    public function verifyPayment(Request $request)
    {
        // Validate input
        $validator = Validator::make($request->all(), [
            'payment_id' => 'required|string',
            'razorpay_order_id' => 'nullable|string',
            'appointment_id' => 'nullable|exists:appointments,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            $patient = Patient::where('user_id', $user->id)->first();
            if (!$patient) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient record not found'
                ], 403);
            }

            $razorpayKey = config('services.razorpay.key');
            $razorpaySecret = config('services.razorpay.secret');

            if (!$razorpayKey || !$razorpaySecret) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment gateway not configured'
                ], 500);
            }

            Log::info('Verifying Razorpay payment', [
                'payment_id' => $request->payment_id,
                'order_id' => $request->razorpay_order_id,
                'patient_id' => $patient->id
            ]);

            // Step 1: Get payment details from Razorpay
            $paymentResponse = Http::withBasicAuth($razorpayKey, $razorpaySecret)
                ->get("https://api.razorpay.com/v1/payments/{$request->payment_id}");

            if ($paymentResponse->failed()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment verification failed!',
                    'details' => 'This payment was not successfully captured. If the amount has been deducted, it will be automatically refunded within 5-7 business days.'
                ], 400);
            }

            $paymentData = $paymentResponse->json();

            // Safer logging: avoid storing full PII in logs
            Log::info('Razorpay payment response', [
                'id' => $paymentData['id'] ?? null,
                'order_id' => $paymentData['order_id'] ?? null,
                'status' => $paymentData['status'] ?? null,
                'amount' => $paymentData['amount'] ?? null,
                'currency' => $paymentData['currency'] ?? null,
                'method' => $paymentData['method'] ?? null,
            ]);

            // Verify client-provided signature (if present) to prevent tampering
            if ($request->has('razorpay_order_id') && $request->has('razorpay_signature')) {
                $orderId = $request->razorpay_order_id;
                $signature = $request->razorpay_signature;
                $expected = hash_hmac('sha256', $orderId . '|' . $request->payment_id, $razorpaySecret);
                if (!hash_equals($expected, $signature)) {
                    Log::warning('Invalid razorpay signature', ['order_id' => $orderId, 'payment_id' => $request->payment_id]);
                    return response()->json(['success' => false, 'message' => 'Invalid razorpay signature'], 400);
                }
            }

            // Step 2: Check if payment needs capture (authorized status)
            if ($paymentData['status'] === 'authorized') {
                $amountToCapture = $paymentData['amount'];
                
                $captureResponse = Http::withBasicAuth($razorpayKey, $razorpaySecret)
                    ->post("https://api.razorpay.com/v1/payments/{$request->payment_id}/capture", [
                        'amount' => $amountToCapture
                    ]);

                if ($captureResponse->failed()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Payment capture failed!',
                        'details' => 'This payment was not successfully captured. If the amount has been deducted, it will be automatically refunded.'
                    ], 400);
                }
                
                $paymentData = $captureResponse->json();
            }

            if ($paymentData['status'] !== 'captured') {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment not captured!',
                    'payment_status' => $paymentData['status']
                ], 400);
            }

            // Safer lookup: prefer transaction_id match first, then fallback to order id when provided
            $existingPayment = Payment::where('transaction_id', $request->payment_id)->first();

            if (!$existingPayment && $request->filled('razorpay_order_id')) {
                $existingPayment = Payment::whereJsonContains('meta->razorpay_order_id', $request->razorpay_order_id)->first();
            }

            if ($existingPayment) {
                // Verify captured amount matches expected (amounts are in paise on Razorpay)
                $expectedAmount = intval(round($existingPayment->amount * 100));
                $capturedAmount = intval($paymentData['amount'] ?? 0);
                if ($capturedAmount !== $expectedAmount) {
                    Log::error('Captured amount mismatch for existing payment', ['payment_id' => $existingPayment->id, 'expected' => $expectedAmount, 'captured' => $capturedAmount]);
                    return response()->json(['success' => false, 'message' => 'Captured amount mismatch'], 400);
                }

                $existingPayment->update([
                    'status' => 'completed',
                    'transaction_id' => $request->payment_id,
                    'meta' => array_merge($existingPayment->meta ?? [], [
                        'razorpay_verification_data' => $paymentData,
                        'razorpay_payment_id' => $request->payment_id,
                        'verified_at' => now()->toDateTimeString(),
                        'payment_method' => $paymentData['method'] ?? 'card'
                    ])
                ]);

                $payment = $existingPayment;
                Log::info('Updated existing payment', ['payment_id' => $payment->id]);
            } else {
                $appointmentId = $request->appointment_id;
                
                if ($appointmentId) {
                    $appointment = Appointment::find($appointmentId);
                    if (!$appointment) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Appointment not found'
                        ], 404);
                    }
                    
                    // Check if patient owns this appointment
                    if ($appointment->patient_id !== $patient->id) {
                        return response()->json([
                            'success' => false,
                            'message' => 'This appointment does not belong to you'
                        ], 403);
                    }
                }

                // Convert amount from paise to rupees
                $amountInRupees = intval($paymentData['amount'] ?? 0) / 100;

                $payment = Payment::create([
                    'appointment_id' => $appointmentId,
                    'patient_id' => $patient->id,
                    'amount' => $amountInRupees,
                    'currency' => $paymentData['currency'] ?? 'INR',
                    'payment_method' => $paymentData['method'] ?? 'card',
                    'status' => 'completed',
                    'transaction_id' => $request->payment_id,
                    'meta' => array_merge($request->all(), [
                        'razorpay_order_id' => $request->razorpay_order_id,
                        'razorpay_payment_id' => $request->payment_id,
                        'razorpay_verification_data' => $paymentData,
                        'verified_at' => now()->toDateTimeString(),
                        'bank' => $paymentData['bank'] ?? null,
                        'wallet' => $paymentData['wallet'] ?? null,
                        'vpa' => $paymentData['vpa'] ?? null,
                        'email' => $paymentData['email'] ?? null,
                        'contact' => $paymentData['contact'] ?? null
                    ])
                ]);

                Log::info('Created new payment record', ['payment_id' => $payment->id]);
            }

            // Step 5: Update appointment status if applicable
            if ($payment->appointment_id) {
                $appointment = Appointment::find($payment->appointment_id);
                if ($appointment) {
                    $appointment->status = 'scheduled';
                    $appointment->save();
                    Log::info('Appointment scheduled', ['appointment_id' => $appointment->id]);
                }
            }

            // Step 6: Create invoice
            $invoice = Invoice::firstOrCreate([
                'payment_id' => $payment->id
            ], [
                'appointment_id' => $payment->appointment_id,
                'patient_id' => $payment->patient_id,
                'total_amount' => $payment->amount,
                'paid_amount' => $payment->amount,
                'due_amount' => 0,
                'status' => 'paid',
                'invoice_date' => now()->toDateString(),
                'due_date' => now()->toDateString(),
                'notes' => 'Payment processed via Razorpay | Transaction: ' . $request->payment_id,
            ]);

            Log::info('Invoice created or exists', ['invoice_id' => $invoice->id]);

            // Step 7: Prepare response
            $response = [
                'success' => true,
                'message' => 'Payment verified and processed successfully',
                'data' => [
                    'payment' => [
                        'id' => $payment->id,
                        'transaction_id' => $payment->transaction_id,
                        'amount' => $payment->amount,
                        'currency' => $payment->currency,
                        'status' => $payment->status,
                        'payment_method' => $payment->payment_method,
                        'created_at' => $payment->created_at
                    ],
                    'invoice' => [
                        'id' => $invoice->id,
                        'invoice_number' => $invoice->id, // Adjust if you have invoice_number field
                        'total_amount' => $invoice->total_amount,
                        'paid_amount' => $invoice->paid_amount,
                        'status' => $invoice->status,
                        'invoice_date' => $invoice->invoice_date
                    ],
                    'appointment' => $payment->appointment_id ? [
                        'id' => $appointment->id,
                        'status' => $appointment->status,
                        'scheduled_date' => $appointment->appointment_date // Adjust field name as per your schema
                    ] : null
                ]
            ];

            return response()->json($response);

        } catch (\Throwable $e) {
            Log::error('Payment verification error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'An error occurred during payment verification',
                'error' => env('APP_DEBUG') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    
    public function getPaymentDetails($razorpayPaymentId)
    {
        try {
            $razorpayKey = config('services.razorpay.key');
            $razorpaySecret = config('services.razorpay.secret');

            if (!$razorpayKey || !$razorpaySecret) {
                return response()->json([
                    'success' => false,
                    'message' => 'Payment gateway not configured'
                ], 500);
            }

            $response = Http::withBasicAuth($razorpayKey, $razorpaySecret)
                ->get("https://api.razorpay.com/v1/payments/{$razorpayPaymentId}");

            if ($response->successful()) {
                return response()->json([
                    'success' => true,
                    'data' => $response->json()
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'Payment not found'
            ], 404);

        } catch (\Throwable $e) {
            Log::error('Get payment details error', [
                'message' => $e->getMessage(),
                'payment_id' => $razorpayPaymentId
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch payment details'
            ], 500);
        }
    }
}