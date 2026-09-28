<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    /**
     * Trang Trung Tâm Khách Hàng / Hồ sơ & Lộ trình đơn mua
     */
    public function index(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        // 1. Toàn bộ đơn hàng của khách hàng (để tính thống kê & đếm số lượng)
        $allOrders = Order::where('user_id', $user->id)
            ->with(['items.product', 'reviews'])
            ->latest('created_at')
            ->get();

        // Đơn hàng gần nhất
        $latestOrder = $allOrders->first();

        // 2. Thống kê theo 4 nhóm trạng thái chuẩn Shopee
        $pendingCount = $allOrders->filter(function($o) {
            return in_array($o->shipping_status, ['not_shipped', 'pending']) 
                && !in_array($o->status, ['cancelled']) 
                && $o->shipping_status !== 'cancelled';
        })->count();

        $pickingCount = $allOrders->filter(function($o) {
            return in_array($o->shipping_status, ['ready_to_pick', 'picking']) 
                && !in_array($o->status, ['cancelled']) 
                && $o->shipping_status !== 'cancelled';
        })->count();

        $deliveringCount = $allOrders->filter(function($o) {
            return in_array($o->shipping_status, ['delivering', 'storing']) 
                && !in_array($o->status, ['cancelled']) 
                && $o->shipping_status !== 'cancelled';
        })->count();

        $deliveredCount = $allOrders->filter(function($o) {
            return $o->shipping_status === 'delivered';
        })->count();

        $cancelledCount = $allOrders->filter(function($o) {
            return in_array($o->status, ['cancelled']) || $o->shipping_status === 'cancelled';
        })->count();

        // 3. Phân trang đơn hàng có hỗ trợ lọc tab theo trạng thái
        $statusTab = $request->query('status', 'all');

        $ordersQuery = Order::where('user_id', $user->id)
            ->with(['items.product', 'reviews'])
            ->latest('created_at');

        if ($statusTab === 'pending') {
            $ordersQuery->whereIn('shipping_status', ['not_shipped', 'pending'])
                        ->where('status', '!=', 'cancelled')
                        ->where('shipping_status', '!=', 'cancelled');
        } elseif ($statusTab === 'picking') {
            $ordersQuery->whereIn('shipping_status', ['ready_to_pick', 'picking'])
                        ->where('status', '!=', 'cancelled')
                        ->where('shipping_status', '!=', 'cancelled');
        } elseif ($statusTab === 'delivering') {
            $ordersQuery->whereIn('shipping_status', ['delivering', 'storing'])
                        ->where('status', '!=', 'cancelled')
                        ->where('shipping_status', '!=', 'cancelled');
        } elseif ($statusTab === 'delivered') {
            $ordersQuery->where('shipping_status', 'delivered')
                        ->where('status', '!=', 'cancelled');
        } elseif ($statusTab === 'cancelled') {
            $ordersQuery->where(function($q) {
                $q->where('status', 'cancelled')
                  ->orWhere('shipping_status', 'cancelled');
            });
        }

        $orders = $ordersQuery->paginate(8)->withQueryString();

        // Đơn hàng hiển thị trên khối lộ trình: ưu tiên đơn mới nhất của nhóm trạng thái đang chọn
        $trackingOrder = ($statusTab !== 'all' && $orders->isNotEmpty())
            ? $orders->first()
            : $latestOrder;

        // Tổng chi tiêu tích lũy
        $totalSpent = $allOrders->where('status', 'paid')->sum('total_price');

        return view('user.profile', compact(
            'user',
            'latestOrder',
            'trackingOrder',
            'orders',
            'statusTab',
            'allOrders',
            'pendingCount',
            'pickingCount',
            'deliveringCount',
            'deliveredCount',
            'cancelledCount',
            'totalSpent'
        ));
    }

    /**
     * Cập nhật thông tin cá nhân
     */
    public function update(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name'  => 'required|string|max:100',
            'email' => 'required|email|max:150|unique:users,email,' . $user->id,
        ], [
            'name.required'  => 'Vui lòng nhập họ và tên.',
            'email.required' => 'Vui lòng nhập địa chỉ email.',
            'email.unique'   => 'Email này đã được sử dụng bởi tài khoản khác.',
        ]);

        $user->update($validated);

        return redirect()->route('profile')->with('success', 'Thông tin cá nhân đã được cập nhật thành công!');
    }

    /**
     * Đổi mật khẩu
     */
    public function changePassword(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'current_password'      => 'required|string',
            'password'              => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            'current_password.required' => 'Vui lòng nhập mật khẩu hiện tại.',
            'password.required'         => 'Vui lòng nhập mật khẩu mới.',
            'password.min'              => 'Mật khẩu mới phải có ít nhất 6 ký tự.',
            'password.confirmed'        => 'Xác nhận mật khẩu mới không khớp.',
        ]);

        if (!Hash::check($validated['current_password'], $user->password)) {
            return back()->withErrors(['current_password' => 'Mật khẩu hiện tại không chính xác.']);
        }

        $user->update([
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('profile')->with('success', 'Đổi mật khẩu tài khoản thành công!');
    }
}
