<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Barbershop;
use App\Models\Payment;
use App\Models\Customer;
use App\Models\Booking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class AdminDashboardController extends Controller
{
    public function dashboard()
    {
        $today = Carbon::today()->toDateString();
        
        $todayBookings = Booking::where('booking_date', $today)->count();
        $todayActivations = Payment::where('payment_status', 'approved')->whereDate('updated_at', $today)->count();
        $todayEarnings = $todayActivations * 100; // 100 MAD per activation

        $totalSalons = Barbershop::count();
        $activeSalons = Barbershop::where('status', 'active')->count();
        $pendingSalons = Barbershop::where('status', 'pending_review')->count();
        $totalCustomers = Customer::count();
        $totalBookings = Booking::count();
        $totalEarnings = Payment::where('payment_status', 'approved')->sum('amount');

        return view('admin.dashboard', compact(
            'todayBookings', 'todayActivations', 'todayEarnings',
            'totalSalons', 'activeSalons', 'pendingSalons',
            'totalCustomers', 'totalBookings', 'totalEarnings'
        ));
    }

    public function activations()
    {
        // Get all pending review payments
        $pendingPayments = Payment::where('payment_status', 'pending')
            ->join('barbershops', 'payments.salon_id', '=', 'barbershops.id')
            ->join('users', 'barbershops.owner_id', '=', 'users.id')
            ->select(
                'payments.id as payment_id',
                'barbershops.id as salon_id',
                'barbershops.salon_name',
                'users.name as owner_name',
                'users.phone',
                'payments.proof_image',
                'payments.created_at',
                'payments.amount'
            )->orderBy('payments.created_at', 'desc')->get();

        return view('admin.activations', compact('pendingPayments'));
    }

    public function activateSalon($id)
    {
        $payment = Payment::find($id);
        if (!$payment) return back()->with('error', 'Payment not found');

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

        return back()->with('success', 'Salon activated successfully!');
    }

    public function rejectSalon($id)
    {
        $payment = Payment::find($id);
        if (!$payment) return back()->with('error', 'Payment not found');

        $payment->payment_status = 'rejected';
        $payment->save();

        $salon = Barbershop::find($payment->salon_id);
        if ($salon) {
            $salon->status = 'rejected';
            $salon->save();
        }

        return back()->with('success', 'Salon activation rejected.');
    }

    public function salons(Request $request)
    {
        $query = Barbershop::join('users', 'barbershops.owner_id', '=', 'users.id')
            ->select('barbershops.*', 'users.name as owner_name', 'users.phone');
        
        if ($request->has('status') && $request->status !== '') {
            $query->where('barbershops.status', $request->status);
        }
        if ($request->has('search') && $request->search !== '') {
            $query->where('barbershops.salon_name', 'like', '%' . $request->search . '%')
                  ->orWhere('users.name', 'like', '%' . $request->search . '%');
        }

        $salons = $query->paginate(10);
        return view('admin.salons', compact('salons'));
    }

    public function activateSalonDirect($id)
    {
        $salon = Barbershop::findOrFail($id);
        $salon->status = 'active';
        
        if (!$salon->qr_token) {
            $salon->qr_token = 'BL-SALON-' . Str::uuid();
            $qrImageName = 'qrcodes/' . $salon->qr_token . '.svg';
            Storage::disk('public')->put($qrImageName, QrCode::format('svg')->size(300)->generate($salon->qr_token));
            $salon->qr_image_path = $qrImageName;
        }
        $salon->save();
        
        Payment::where('salon_id', $id)->update(['payment_status' => 'approved']);

        return back()->with('success', 'تم تفعيل الصالون بنجاح.');
    }

    public function suspendSalon($id)
    {
        $salon = Barbershop::findOrFail($id);
        $salon->status = 'suspended';
        $salon->save();
        return back()->with('success', 'Salon suspended successfully.');
    }

    public function deleteSalon($id)
    {
        $salon = Barbershop::findOrFail($id);
        $salon->delete();
        return back()->with('success', 'Salon deleted successfully.');
    }

    public function payments(Request $request)
    {
        $query = Payment::join('barbershops', 'payments.salon_id', '=', 'barbershops.id')
            ->join('users', 'barbershops.owner_id', '=', 'users.id')
            ->select('payments.*', 'barbershops.salon_name', 'users.name as owner_name');
        
        if ($request->has('status') && $request->status !== '') {
            $query->where('payments.payment_status', $request->status);
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('admin.payments', compact('payments'));
    }

    public function customers()
    {
        $customers = Customer::join('users', 'customers.user_id', '=', 'users.id')
            ->leftJoin('barbershops', 'customers.linked_salon_id', '=', 'barbershops.id')
            ->select('customers.*', 'users.name', 'users.phone', 'barbershops.salon_name')
            ->withCount('bookings')
            ->paginate(15);
            
        return view('admin.customers', compact('customers'));
    }

    public function bookings(Request $request)
    {
        $query = Booking::join('customers', 'bookings.customer_id', '=', 'customers.id')
            ->join('users', 'customers.user_id', '=', 'users.id')
            ->join('barbershops', 'bookings.salon_id', '=', 'barbershops.id')
            ->select('bookings.*', 'users.name as customer_name', 'barbershops.salon_name');

        if ($request->has('status') && $request->status !== '') {
            $query->where('bookings.status', $request->status);
        }
        if ($request->has('date') && $request->date !== '') {
            $query->where('bookings.booking_date', $request->date);
        }
        if ($request->has('salon_id') && $request->salon_id !== '') {
            $query->where('bookings.salon_id', $request->salon_id);
        }

        $bookings = $query->orderBy('booking_date', 'desc')->orderBy('booking_time', 'desc')->paginate(20);
        $salons = Barbershop::all();
        
        return view('admin.bookings', compact('bookings', 'salons'));
    }

    public function notifications()
    {
        $notifications = \App\Models\Notification::join('users', 'notifications.user_id', '=', 'users.id')
            ->select('notifications.*', 'users.name', 'users.role')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return view('admin.notifications', compact('notifications'));
    }

    public function settings()
    {
        $settings = \App\Models\Setting::pluck('value', 'key')->toArray();
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $data = $request->except('_token');
        foreach ($data as $key => $value) {
            \App\Models\Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
        return back()->with('success', 'Settings updated successfully.');
    }
}
