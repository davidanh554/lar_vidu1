<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Coupon;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index()
    {
        $coupons = Coupon::withCount('userCoupons')->latest()->paginate(15);
        return view('admin.coupons.index', compact('coupons'));
    }

    public function create()
    {
        return view('admin.coupons.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code'            => 'required|string|max:50|unique:coupons,code',
            'title'           => 'required|string|max:255',
            'type'            => 'required|in:fixed,percent',
            'value'           => 'required|numeric|min:0',
            'max_discount'    => 'nullable|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'quantity'        => 'nullable|integer|min:1',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
        ]);

        Coupon::create([
            'code'            => strtoupper(trim($request->code)),
            'title'           => $request->title,
            'type'            => $request->type,
            'value'           => $request->value,
            'max_discount'    => $request->max_discount,
            'min_order_value' => $request->min_order_value ?? 0,
            'quantity'        => $request->quantity,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'is_active'       => $request->has('is_active'),
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Tạo mã giảm giá mới thành công!');
    }

    public function edit(Coupon $coupon)
    {
        return view('admin.coupons.edit', compact('coupon'));
    }

    public function update(Request $request, Coupon $coupon)
    {
        $request->validate([
            'code'            => 'required|string|max:50|unique:coupons,code,' . $coupon->id,
            'title'           => 'required|string|max:255',
            'type'            => 'required|in:fixed,percent',
            'value'           => 'required|numeric|min:0',
            'max_discount'    => 'nullable|numeric|min:0',
            'min_order_value' => 'nullable|numeric|min:0',
            'quantity'        => 'nullable|integer|min:0',
            'start_date'      => 'nullable|date',
            'end_date'        => 'nullable|date|after_or_equal:start_date',
        ]);

        $coupon->update([
            'code'            => strtoupper(trim($request->code)),
            'title'           => $request->title,
            'type'            => $request->type,
            'value'           => $request->value,
            'max_discount'    => $request->max_discount,
            'min_order_value' => $request->min_order_value ?? 0,
            'quantity'        => $request->quantity,
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'is_active'       => $request->has('is_active'),
        ]);

        return redirect()->route('admin.coupons.index')->with('success', 'Cập nhật mã giảm giá thành công!');
    }

    public function toggle(Coupon $coupon)
    {
        $coupon->update(['is_active' => !$coupon->is_active]);
        $statusText = $coupon->is_active ? 'Kích hoạt' : 'Tạm khóa';
        return back()->with('success', "Đã {$statusText} mã giảm giá {$coupon->code}!");
    }

    public function destroy(Coupon $coupon)
    {
        $coupon->delete();
        return redirect()->route('admin.coupons.index')->with('success', 'Đã xóa mã giảm giá!');
    }
}
