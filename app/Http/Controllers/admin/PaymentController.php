<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Appointment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $payments = Payment::with(['appointment', 'patient'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view($user->user_type.'.payments.index', compact('payments'));
    }

    public function show($id)
    {
         $user = Auth::user();
        $payment = Payment::with(['appointment.doctor', 'patient'])->find($id);
        if (! $payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }

        $html = view($user->user_type.'.payments.partials.details', compact('payment'))->render();
        return response()->json(['success' => true, 'html' => $html]);
    }

    public function markAsPaid(Request $request, $id)
    {
         $user = Auth::user();
        $payment = Payment::find($id);
        if (! $payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }

        $payment->update([
            'status' => 'completed',
            'transaction_id' => $payment->transaction_id ?? 'manual_' . now()->timestamp
        ]);

        // Optionally schedule appointment
        if ($payment->appointment_id) {
            $appointment = $payment->appointment;
            if ($appointment) {
                $appointment->status = 'scheduled';
                $appointment->save();
            }
        }

        return response()->json(['success' => true, 'message' => 'Payment marked as paid']);
    }
}
