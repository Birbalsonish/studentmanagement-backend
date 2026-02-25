<?php

namespace App\Http\Controllers;

use App\Models\Fee;
use App\Models\Student;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class FeeReceiptController extends Controller
{
    /**
     * Generate fee receipt PDF
     */
    public function generateReceipt($feeId)
    {
        $fee = Fee::with(['student'])->findOrFail($feeId);
        
        // Get student details with enrollment (for class info)
        $student = Student::with(['enrollments.class'])->find($fee->student_id);
        
        // Get current enrollment/class
        $currentEnrollment = $student->enrollments()
            ->where('status', 'Active')
            ->latest()
            ->first();
        
        $data = [
            'fee' => $fee,
            'student' => $student,
            'currentClass' => $currentEnrollment ? $currentEnrollment->class : null,
            'receiptNumber' => 'REC-' . str_pad($fee->id, 6, '0', STR_PAD_LEFT),
            'generatedDate' => now()->format('d M Y, h:i A'),
        ];

        $pdf = PDF::loadView('receipts.fee-receipt', $data);
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        
        return $pdf->download("fee-receipt-{$data['receiptNumber']}.pdf");
    }

    /**
     * Preview receipt (for testing)
     */
    public function previewReceipt($feeId)
    {
        $fee = Fee::with(['student'])->findOrFail($feeId);
        
        $student = Student::with(['enrollments.class'])->find($fee->student_id);
        
        $currentEnrollment = $student->enrollments()
            ->where('status', 'Active')
            ->latest()
            ->first();
        
        $data = [
            'fee' => $fee,
            'student' => $student,
            'currentClass' => $currentEnrollment ? $currentEnrollment->class : null,
            'receiptNumber' => 'REC-' . str_pad($fee->id, 6, '0', STR_PAD_LEFT),
            'generatedDate' => now()->format('d M Y, h:i A'),
        ];

        return view('receipts.fee-receipt', $data);
    }

    /**
     * Get receipt data as JSON (for frontend preview)
     */
    public function getReceiptData($feeId)
    {
        $fee = Fee::with(['student'])->findOrFail($feeId);
        
        $student = Student::with(['enrollments.class'])->find($fee->student_id);
        
        $currentEnrollment = $student->enrollments()
            ->where('status', 'Active')
            ->latest()
            ->first();
        
        return response()->json([
            'success' => true,
            'data' => [
                'fee' => $fee,
                'student' => $student,
                'currentClass' => $currentEnrollment ? $currentEnrollment->class : null,
                'receiptNumber' => 'REC-' . str_pad($fee->id, 6, '0', STR_PAD_LEFT),
            ]
        ]);
    }

    /**
     * Record a payment and generate receipt
     */
    public function recordPaymentAndGenerateReceipt(Request $request, $feeId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
            'transaction_id' => 'nullable|string',
            'remarks' => 'nullable|string',
        ]);

        $fee = Fee::findOrFail($feeId);

        // Update paid amount
        $newPaidAmount = $fee->paid_amount + $request->amount;
        
        // Don't allow overpayment
        if ($newPaidAmount > $fee->amount) {
            return response()->json([
                'success' => false,
                'message' => 'Payment amount exceeds pending balance'
            ], 422);
        }

        $fee->paid_amount = $newPaidAmount;
        $fee->pending_amount = $fee->amount - $newPaidAmount;

        // Update status
        if ($fee->paid_amount >= $fee->amount) {
            $fee->status = 'Paid';
        } elseif ($fee->paid_amount > 0) {
            $fee->status = 'Partial';
        } else {
            $fee->status = 'Pending';
        }

        $fee->save();

        // Generate receipt
        $student = Student::with(['enrollments.class'])->find($fee->student_id);
        
        $currentEnrollment = $student->enrollments()
            ->where('status', 'Active')
            ->latest()
            ->first();
        
        $data = [
            'fee' => $fee->fresh(),
            'student' => $student,
            'currentClass' => $currentEnrollment ? $currentEnrollment->class : null,
            'receiptNumber' => 'REC-' . str_pad($fee->id, 6, '0', STR_PAD_LEFT),
            'generatedDate' => now()->format('d M Y, h:i A'),
            'paymentAmount' => $request->amount,
            'paymentMethod' => $request->payment_method,
            'transactionId' => $request->transaction_id,
        ];

        $pdf = PDF::loadView('receipts.fee-receipt', $data);
        $pdf->setPaper('A4', 'portrait');
        
        // Save PDF to storage
        $fileName = "receipt-{$data['receiptNumber']}-" . time() . ".pdf";
        $pdf->save(storage_path("app/public/receipts/{$fileName}"));

        return response()->json([
            'success' => true,
            'message' => 'Payment recorded and receipt generated',
            'data' => [
                'fee' => $fee->fresh(),
                'receipt_url' => asset("storage/receipts/{$fileName}")
            ]
        ]);
    }
}