<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Barbershop;
use App\Models\Customer;

class QrController extends Controller
{
    // Owner APIs
    public function getOwnerQr(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'owner') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $salon = Barbershop::where('owner_id', $user->id)->first();
        if (!$salon || !$salon->qr_token) {
            return response()->json(['success' => false, 'message' => 'QR Code not available or salon not active'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'salon_id' => $salon->id,
                'salon_name' => $salon->salon_name,
                'qr_token' => $salon->qr_token,
                'qr_image_url' => asset('storage/' . $salon->qr_image_path),
            ]
        ]);
    }

    // Customer APIs
    public function linkSalon(Request $request)
    {
        $request->validate([
            'qr_token' => 'required|string',
        ]);

        $user = $request->user();
        if ($user->role !== 'customer') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $salon = Barbershop::where('qr_token', $request->qr_token)->first();
        
        if (!$salon) {
            return response()->json(['success' => false, 'message' => 'Invalid QR Code'], 404);
        }

        if ($salon->status !== 'active') {
            return response()->json(['success' => false, 'message' => 'Salon is not active'], 400);
        }

        $customer = Customer::where('user_id', $user->id)->first();
        if (!$customer) {
            return response()->json(['success' => false, 'message' => 'Customer profile not found'], 404);
        }

        $customer->linked_salon_id = $salon->id;
        $customer->save();

        return response()->json([
            'success' => true,
            'message' => 'Successfully linked to ' . $salon->salon_name,
            'salon_name' => $salon->salon_name
        ]);
    }

    public function getLinkedSalon(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'customer') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $customer = Customer::where('user_id', $user->id)->first();
        if (!$customer || !$customer->linked_salon_id) {
            return response()->json([
                'success' => true,
                'linked_status' => false
            ]);
        }

        $salon = Barbershop::find($customer->linked_salon_id);

        return response()->json([
            'success' => true,
            'linked_status' => true,
            'data' => [
                'salon_id' => $salon->id,
                'salon_name' => $salon->salon_name,
                'address' => $salon->address,
            ]
        ]);
    }

    public function unlinkSalon(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'customer') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $customer = Customer::where('user_id', $user->id)->first();
        if ($customer) {
            $customer->linked_salon_id = null;
            $customer->save();
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully unlinked from salon'
        ]);
    }
}
