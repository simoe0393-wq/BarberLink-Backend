<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barbershop;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PaymentController extends Controller
{
    // Owner Routes
    public function uploadProof(Request $request)
    {
        $request->validate([
            'proof_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user = $request->user();
        if ($user->role !== 'owner') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $salon = Barbershop::where('owner_id', $user->id)->first();
        if (!$salon) {
            return response()->json(['success' => false, 'message' => 'Salon not found'], 404);
        }

        if ($request->hasFile('proof_image')) {
            $path = $request->file('proof_image')->store('payments', 'public');

            Payment::create([
                'salon_id' => $salon->id,
                'amount' => 100.00, // Default activation fee
                'proof_image' => $path,
                'payment_status' => 'pending',
            ]);

            // Update status to pending_review
            $salon->status = 'pending_review';
            $salon->save();

            return response()->json([
                'success' => true,
                'status' => 'pending_review'
            ]);
        }

        return response()->json(['success' => false, 'message' => 'No image uploaded'], 400);
    }

    public function getPaymentStatus(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'owner') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $salon = Barbershop::where('owner_id', $user->id)->first();
        if (!$salon) {
            return response()->json(['success' => false, 'message' => 'Salon not found'], 404);
        }

        return response()->json([
            'success' => true,
            'status' => $salon->status
        ]);
    }

    // Admin Routes
    public function getPendingPayments(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $payments = Payment::where('payment_status', 'pending')
            ->join('barbershops', 'payments.salon_id', '=', 'barbershops.id')
            ->join('users', 'barbershops.owner_id', '=', 'users.id')
            ->select(
                'payments.id as payment_id',
                'barbershops.salon_name',
                'users.name as owner_name',
                'users.phone',
                'payments.proof_image',
                'payments.created_at',
                'payments.amount'
            )->get();

        return response()->json([
            'success' => true,
            'data' => $payments
        ]);
    }

    public function activatePayment(Request $request, $id)
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $payment = Payment::find($id);
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }

        $payment->payment_status = 'approved';
        $payment->save();

        $salon = Barbershop::find($payment->salon_id);
        if ($salon) {
            $salon->status = 'active';
            
            if (!$salon->qr_token) {
                $salon->qr_token = 'BL-SALON-' . Str::uuid();
                $qrImageName = 'qrcodes/' . $salon->qr_token . '.svg';
                Storage::disk('public')->put($qrImageName, QrCode::format('svg')->size(300)->generate($salon->qr_token));
                $salon->qr_image_path = $qrImageName;
            }
            
            $salon->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Account activated successfully',
            'salon_status' => 'active'
        ]);
    }

    public function rejectPayment(Request $request, $id)
    {
        $user = $request->user();
        if ($user->role !== 'admin') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $payment = Payment::find($id);
        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Payment not found'], 404);
        }

        $payment->payment_status = 'rejected';
        $payment->save();

        $salon = Barbershop::find($payment->salon_id);
        if ($salon) {
            $salon->status = 'rejected';
            $salon->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Payment rejected',
            'salon_status' => 'rejected'
        ]);
    }
}
