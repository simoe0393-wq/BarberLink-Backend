<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Customer;
use App\Models\Barbershop;
use App\Models\Notification;
use Carbon\Carbon;

class BookingController extends Controller
{
    // Customer APIs
    public function createBooking(Request $request)
    {
        $request->validate([
            'booking_date' => 'required|date|after_or_equal:today',
            'booking_time' => 'required|date_format:H:i',
            'service_type' => 'required|string|in:Haircut,Beard Trim,Full Service',
        ]);

        $user = $request->user();
        if ($user->role !== 'customer') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        $customer = Customer::where('user_id', $user->id)->first();
        if (!$customer || !$customer->linked_salon_id) {
            return response()->json(['success' => false, 'message' => 'You must link a salon first'], 400);
        }

        // Prevent double booking at the same salon for the exact same time
        $conflict = Booking::where('salon_id', $customer->linked_salon_id)
            ->where('booking_date', $request->booking_date)
            ->where('booking_time', $request->booking_time . ':00')
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->exists();

        if ($conflict) {
            return response()->json(['success' => false, 'message' => 'This time slot is already booked'], 409);
        }

        $booking = Booking::create([
            'customer_id' => $customer->id,
            'salon_id' => $customer->linked_salon_id,
            'booking_date' => $request->booking_date,
            'booking_time' => $request->booking_time,
            'service_type' => $request->service_type,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Booking created successfully',
            'data' => $booking
        ], 201);
    }

    public function getHistory(Request $request)
    {
        $user = $request->user();
        $customer = Customer::where('user_id', $user->id)->first();

        $bookings = Booking::where('customer_id', $customer->id)
            ->join('barbershops', 'bookings.salon_id', '=', 'barbershops.id')
            ->select('bookings.*', 'barbershops.salon_name')
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc')
            ->get();

        return response()->json(['success' => true, 'data' => $bookings]);
    }

    public function cancelBooking(Request $request, $id)
    {
        $user = $request->user();
        $customer = Customer::where('user_id', $user->id)->first();
        
        $booking = Booking::where('id', $id)->where('customer_id', $customer->id)->first();
        
        if (!$booking) {
            return response()->json(['success' => false, 'message' => 'Booking not found'], 404);
        }

        if (!in_array($booking->status, ['pending', 'confirmed'])) {
            return response()->json(['success' => false, 'message' => 'Cannot cancel this booking'], 400);
        }

        $booking->status = 'cancelled';
        $booking->save();

        return response()->json(['success' => true, 'message' => 'Booking cancelled']);
    }

    // Owner APIs
    public function getTodayBookings(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'owner') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $salon = Barbershop::where('owner_id', $user->id)->first();

        $today = Carbon::today()->toDateString();
        $bookings = Booking::where('salon_id', $salon->id)
            ->where('booking_date', $today)
            ->join('customers', 'bookings.customer_id', '=', 'customers.id')
            ->join('users', 'customers.user_id', '=', 'users.id')
            ->select('bookings.*', 'users.name as customer_name', 'users.phone')
            ->orderBy('booking_time', 'asc')
            ->get();

        return response()->json(['success' => true, 'data' => $bookings]);
    }

    public function getAllBookings(Request $request)
    {
        $user = $request->user();
        if ($user->role !== 'owner') {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        $salon = Barbershop::where('owner_id', $user->id)->first();

        $query = Booking::where('salon_id', $salon->id)
            ->join('customers', 'bookings.customer_id', '=', 'customers.id')
            ->join('users', 'customers.user_id', '=', 'users.id')
            ->select('bookings.*', 'users.name as customer_name', 'users.phone')
            ->orderBy('booking_date', 'desc')
            ->orderBy('booking_time', 'desc');

        if ($request->has('date')) {
            $query->where('booking_date', $request->date);
        }
        if ($request->has('status')) {
            $query->where('bookings.status', $request->status);
        }

        return response()->json(['success' => true, 'data' => $query->get()]);
    }

    private function notifyCustomer($customerId, $message)
    {
        $customer = Customer::find($customerId);
        if ($customer) {
            Notification::create([
                'user_id' => $customer->user_id,
                'message' => $message,
                'status' => 'unread'
            ]);
        }
    }

    private function getSalonBooking(Request $request, $id)
    {
        $salon = Barbershop::where('owner_id', $request->user()->id)->first();
        return Booking::where('id', $id)->where('salon_id', $salon->id)->first();
    }

    public function confirmBooking(Request $request, $id)
    {
        $booking = $this->getSalonBooking($request, $id);
        if (!$booking) return response()->json(['success' => false, 'message' => 'Booking not found'], 404);

        $booking->status = 'confirmed';
        $booking->save();

        $this->notifyCustomer($booking->customer_id, "Your booking on {$booking->booking_date} at {$booking->booking_time} has been confirmed.");

        return response()->json(['success' => true, 'message' => 'Booking confirmed']);
    }

    public function rejectBooking(Request $request, $id)
    {
        $booking = $this->getSalonBooking($request, $id);
        if (!$booking) return response()->json(['success' => false, 'message' => 'Booking not found'], 404);

        $booking->status = 'rejected';
        $booking->save();

        $this->notifyCustomer($booking->customer_id, "Sorry, your booking on {$booking->booking_date} at {$booking->booking_time} was rejected.");

        return response()->json(['success' => true, 'message' => 'Booking rejected']);
    }

    public function completeBooking(Request $request, $id)
    {
        $booking = $this->getSalonBooking($request, $id);
        if (!$booking) return response()->json(['success' => false, 'message' => 'Booking not found'], 404);

        $booking->status = 'completed';
        $booking->save();

        $this->notifyCustomer($booking->customer_id, "Thank you for your visit! Your booking is completed.");

        return response()->json(['success' => true, 'message' => 'Booking completed']);
    }

    public function rescheduleBooking(Request $request, $id)
    {
        $request->validate([
            'new_date' => 'required|date|after_or_equal:today',
            'new_time' => 'required|date_format:H:i',
        ]);

        $booking = $this->getSalonBooking($request, $id);
        if (!$booking) return response()->json(['success' => false, 'message' => 'Booking not found'], 404);

        // Check conflict again
        $conflict = Booking::where('salon_id', $booking->salon_id)
            ->where('id', '!=', $booking->id)
            ->where('booking_date', $request->new_date)
            ->where('booking_time', $request->new_time . ':00')
            ->whereNotIn('status', ['cancelled', 'rejected'])
            ->exists();

        if ($conflict) {
            return response()->json(['success' => false, 'message' => 'This time slot is already booked'], 409);
        }

        $booking->booking_date = $request->new_date;
        $booking->booking_time = $request->new_time;
        $booking->status = 'rescheduled';
        $booking->save();

        $this->notifyCustomer($booking->customer_id, "Your booking has been rescheduled to {$request->new_date} at {$request->new_time}.");

        return response()->json(['success' => true, 'message' => 'Booking rescheduled']);
    }
}
