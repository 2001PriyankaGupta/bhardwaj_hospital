<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $payments = Payment::with(['appointment', 'patient'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.payments.index', compact('payments'));
    }

    public function show($id)
    {
        $payment = Payment::with(['appointment.doctor', 'patient'])->find($id);
        if (! $payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }

        $html = view('admin.payments.partials.details', compact('payment'))->render();
        return response()->json(['success' => true, 'html' => $html]);
    }

    public function markAsPaid(Request $request, $id)
    {
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
