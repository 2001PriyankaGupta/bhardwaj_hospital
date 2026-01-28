<?php

// app/Http/Controllers/Admin/InvoiceController.php
namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Patient;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InvoiceController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $invoices = Invoice::with(['patient'])
            ->latest()
            ->paginate(10);

        return view($user->user_type.'.invoice.index', compact('invoices'));
    }

    public function create()
    {
         $user = Auth::user();
        $patients = Patient::all();

        return view($user->user_type.'.invoice.create', compact('patients'));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'invoice_date' => 'required|date',
            'items' => 'required|array'
        ]);

        $invoice = Invoice::create([
            'patient_id' => $request->patient_id,
            'invoice_date' => $request->invoice_date,
            'due_date' => $request->due_date,
            'notes' => $request->notes
        ]);

        // Add invoice items
        $totalAmount = 0;
        foreach ($request->items as $item) {
            $invoice->items()->create([
                'description' => $item['description'],
                'amount' => $item['amount'],
                'type' => $item['type'],
                'service_date' => $item['service_date']
            ]);
            $totalAmount += $item['amount'];
        }

        // Update total amount
        $invoice->update([
            'total_amount' => $totalAmount,
            'due_amount' => $totalAmount
        ]);

        return redirect()->route($user->user_type.'.invoices.index')
            ->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice)
    {
        $user = Auth::user();
        $invoice->load(['patient','items', 'payments']);

        return view($user->user_type.'.invoice.show', compact('invoice'));
    }

    public function addPayment(Request $request, Invoice $invoice)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date'
        ]);

        $payment = $invoice->payments()->create([
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'transaction_id' => $request->transaction_id,
            'notes' => $request->notes,
            'payment_date' => $request->payment_date,
            'patient_id' => $invoice->patient_id,
            'appointment_id' => $request->appointment_id ?? $invoice->appointment_id,
            'status' => 'completed'
        ]);

        // Update invoice amounts
        $newPaidAmount = $invoice->paid_amount + $request->amount;
        $newDueAmount = $invoice->total_amount - $newPaidAmount;

        $status = 'pending';
        if ($newDueAmount <= 0) {
            $status = 'paid';
        } elseif ($newPaidAmount > 0) {
            $status = 'partial';
        }

        // Link payment to invoice and appointment if present
        $invoice->payment_id = $payment->id;
        if ($payment->appointment_id) {
            $invoice->appointment_id = $payment->appointment_id;
            // Mark appointment scheduled since admin recorded payment
            $appt = $payment->appointment_id ? \App\Models\Appointment::find($payment->appointment_id) : null;
            if ($appt) {
                $appt->status = 'scheduled';
                $appt->save();
            }
        }

        $invoice->update([
            'paid_amount' => $newPaidAmount,
            'due_amount' => $newDueAmount,
            'status' => $status
        ]);

        return back()->with('success', 'Payment added successfully.');
    }

    public function download(Invoice $invoice)
    {
        $user = Auth::user();
        $data = [
            'invoice' => $invoice,
        ];

        $pdf = PDF::loadView($user->user_type.'.invoice.pdf', $data);

        return $pdf->download('invoice-'.$invoice->invoice_number.'.pdf');
    }
}
