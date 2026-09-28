<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Hiển thị danh sách người dùng.
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index', compact('users'));
    }

    /**
     * Hiển thị form tạo người dùng mới.
     */
    public function create()
    {
        return view('admin.users.create');
    }

    /**
     * Lưu người dùng mới vào CSDL.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role'     => 'required|in:admin,user,customer',
        ]);

        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('admin.users.index')->with('success', 'Thêm người dùng thành công.');
    }

    /**
     * Hiển thị chi tiết người dùng.
     */
    public function show(User $user)
    {
        return view('admin.users.show', compact('user'));
    }

    /**
     * Hiển thị form chỉnh sửa người dùng.
     */
    public function edit(User $user)
    {
        return view('admin.users.edit', compact('user'));
    }

    /**
     * Cập nhật thông tin người dùng.
     */
    public function update(Request $request, User $user)
    {
        $request->validate([
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|unique:users,email,' . $user->id,
            'role'              => 'required|in:admin,user,customer',
            'daily_spins_limit' => 'nullable|integer|min:0|max:100',
            'spins_left'        => 'nullable|integer|min:0|max:100',
            'coins'             => 'nullable|integer|min:0',
            'daily_coins_limit' => 'nullable|integer|min:0|max:1000',
        ]);

        $data = [
            'name'              => $request->name,
            'email'             => $request->email,
            'role'              => $request->role,
            'daily_spins_limit' => $request->input('daily_spins_limit', 1),
            'spins_left'        => $request->input('spins_left', $user->spins_left),
            'coins'             => $request->input('coins', $user->coins),
            'daily_coins_limit' => $request->input('daily_coins_limit', $user->daily_coins_limit ?? 10),
        ];

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.users.index')->with('success', 'Cập nhật thông tin, lượt quay và hạn mức xu thành công.');
    }

    /**
     * Xóa người dùng.
     */
    public function destroy(User $user)
    {
        if ($user->role === 'admin') {
            return redirect()->route('admin.users.index')->with('error', 'Không thể xóa tài khoản Quản trị viên (Admin) để đảm bảo an toàn hệ thống.');
        }

        if ($user->id === auth()->id()) {
            return redirect()->route('admin.users.index')->with('error', 'Bạn không thể tự xóa tài khoản của chính mình.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'Xóa người dùng thành công.');
    }

    /**
     * Tặng thêm hoặc chỉnh nhanh lượt quay cho người dùng.
     */
    public function adjustSpins(Request $request, User $user)
    {
        $request->validate([
            'daily_spins_limit' => 'nullable|integer|min:0|max:100',
            'add_spins'         => 'nullable|integer|min:-100|max:100',
        ]);

        if ($request->has('daily_spins_limit')) {
            $user->daily_spins_limit = (int)$request->daily_spins_limit;
        }

        if ($request->filled('add_spins')) {
            $user->spins_left = max(0, $user->spins_left + (int)$request->add_spins);
        }

        $user->save();

        return back()->with('success', "Đã cập nhật lượt quay cho người dùng {$user->name}!");
    }

    /**
     * Quản trị viên kiểm soát hạn mức nhận xu và nạp/trừ xu cho người dùng.
     */
    public function adjustCoins(Request $request, User $user)
    {
        $request->validate([
            'daily_coins_limit' => 'nullable|integer|min:0|max:1000',
            'add_coins'         => 'nullable|integer|min:-10000|max:10000',
        ]);

        if ($request->has('daily_coins_limit')) {
            $user->daily_coins_limit = (int)$request->daily_coins_limit;
        }

        if ($request->filled('add_coins')) {
            $user->coins = max(0, $user->coins + (int)$request->add_coins);
        }

        $user->save();

        return back()->with('success', "Đã cập nhật hạn mức xu & số dư xu cho người dùng {$user->name}!");
    }
}
