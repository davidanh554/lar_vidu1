<?php

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\User\OrderController;
use App\Http\Controllers\User\GHNController;
use App\Http\Controllers\User\MomoController;
use App\Http\Controllers\User\ChatController as UserChatController;
use App\Http\Controllers\Admin\ChatController as AdminChatController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\CouponController as AdminCouponController;
use App\Http\Controllers\Admin\VideoController as AdminVideoController;
use App\Http\Controllers\Admin\CoinController as AdminCoinController;
use App\Http\Controllers\Admin\FinanceController;
use App\Http\Controllers\User\LuckyWheelController;
use App\Http\Controllers\User\AIAdvisorController;
use App\Http\Controllers\User\VideoShoppingController;

// 1. Routes Xác thực (Auth)
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');

// 2. Routes Công khai dành cho Khách hàng
Route::get('/', [ProductController::class, 'index'])->name('home');
Route::get('/products/{product}', [ProductController::class, 'show'])->name('products.show');

// AI Tư vấn chọn iPad hộ tôi
Route::get('/ai-advisor', [AIAdvisorController::class, 'index'])->name('ai.advisor');
Route::post('/ai-advisor/recommend', [AIAdvisorController::class, 'recommend'])->name('ai.recommend');

// Video Shopping (Lướt video nhận quà / Freeship & Tích luỹ Xu)
Route::get('/videos', [VideoShoppingController::class, 'index'])->name('videos.index');
Route::post('/videos/claim-reward', [VideoShoppingController::class, 'claimReward'])->name('videos.claimReward');
Route::post('/videos/exchange-voucher', [VideoShoppingController::class, 'exchangeVoucher'])->name('videos.exchangeVoucher');
Route::get('/videos/coins-status', [VideoShoppingController::class, 'getCoinsStatus'])->name('videos.coinsStatus');
Route::post('/videos/{video}/like', [VideoShoppingController::class, 'toggleLike'])->name('videos.like');

// 3. Routes Giỏ hàng (Bắt buộc đăng nhập để xem và quản lý giỏ hàng)
Route::middleware(['auth'])->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::post('/cart/change-color', [CartController::class, 'changeColor'])->name('cart.changeColor');
    Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
});

// 4. Routes Xác thực Email
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('home')->with('success', 'Xác thực email thành công!');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');



// 6. GHN Locations API Routes
Route::prefix('locations')->name('locations.')->group(function () {
    Route::get('/provinces', [GHNController::class, 'getProvinces'])->name('provinces');
    Route::get('/districts/{provinceId}', [GHNController::class, 'getDistricts'])->name('districts');
    Route::get('/wards/{districtId}', [GHNController::class, 'getWards'])->name('wards');
    Route::post('/calculate-fee', [GHNController::class, 'getShippingFee'])->name('fee');
});

// 6. Routes Quản trị (Admin) - Cần đăng nhập & có vai trò admin
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('products', AdminProductController::class);

    // Chat Admin
    Route::get('/chat/users', [AdminChatController::class, 'getUsers'])->name('chat.users');
    Route::get('/chat/messages/{userId}', [AdminChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/chat/send', [AdminChatController::class, 'send'])->name('chat.send');

    // Quản lý đơn hàng
    Route::post('/orders/bulk-update-status', [AdminOrderController::class, 'bulkUpdateStatus'])->name('orders.bulkUpdateStatus');
    Route::resource('orders', AdminOrderController::class)->except(['update']);
    Route::post('/orders/{order}/update-status', [AdminOrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Báo cáo doanh thu & biểu đồ
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/charts', [AdminReportController::class, 'charts'])->name('reports.charts');
    Route::get('/reports/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reports.reviews');

    // Quản lý & Báo cáo Đánh giá sản phẩm
    Route::get('/reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::delete('/reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Quản lý người dùng
    Route::resource('users', AdminUserController::class);
    Route::post('/users/{user}/adjust-spins', [AdminUserController::class, 'adjustSpins'])->name('users.adjustSpins');
    Route::post('/users/{user}/adjust-coins', [AdminUserController::class, 'adjustCoins'])->name('users.adjustCoins');

    // Quản lý mã giảm giá (Coupons)
    Route::resource('coupons', AdminCouponController::class);
    Route::post('/coupons/{coupon}/toggle', [AdminCouponController::class, 'toggle'])->name('coupons.toggle');

    // Quản lý Video Reels
    Route::resource('videos', AdminVideoController::class);
    Route::post('/videos/{video}/toggle', [AdminVideoController::class, 'toggle'])->name('videos.toggle');

    // Quản lý & Cấu hình Hạn mức Xu
    Route::get('/coins', [AdminCoinController::class, 'index'])->name('coins.index');
    Route::post('/coins/settings', [AdminCoinController::class, 'updateSettings'])->name('coins.settings');
    Route::post('/coins/adjust/{user}', [AdminCoinController::class, 'adjustUserCoins'])->name('coins.adjust');

    // Quản lý Giao dịch & Thống kê tài chính (Finance)
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index');
    Route::get('/finance/transactions', [FinanceController::class, 'transactions'])->name('finance.transactions');
    Route::patch('/finance/{order}/status', [FinanceController::class, 'updateStatus'])->name('finance.update-status');
});

// Vòng quay may mắn (Lucky Wheel API)
Route::get('/lucky-wheel/status', [LuckyWheelController::class, 'getStatus'])->name('luckywheel.status');
Route::post('/lucky-wheel/spin', [LuckyWheelController::class, 'spin'])->name('luckywheel.spin');
Route::get('/lucky-wheel/my-coupons', [LuckyWheelController::class, 'myCoupons'])->name('luckywheel.myCoupons');

// Chat User
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::post('/chat/send', [UserChatController::class, 'send'])->name('chat.send');
    Route::get('/chat/messages', [UserChatController::class, 'getMessages'])->name('chat.messages');
});

// ==========================================
// 5. Khu vực Thanh toán & MoMo
// ==========================================
// Webhook & Callback từ MoMo (Không dùng middleware auth vì bên MoMo gọi sang)
Route::post('/payment/momo/ipn', [MomoController::class, 'ipn'])->name('payment.momo.ipn');
Route::get('/payment/momo/callback', [MomoController::class, 'callback'])->name('user.payment.momo.callback');

Route::middleware(['auth'])->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout', [CheckoutController::class, 'process'])->name('checkout.process');
    Route::post('/checkout/apply-coupon', [CheckoutController::class, 'applyCoupon'])->name('checkout.applyCoupon');
    Route::post('/checkout/remove-coupon', [CheckoutController::class, 'removeCoupon'])->name('checkout.removeCoupon');

    Route::get('/payment', [OrderController::class, 'index'])->name('payment.index');
    Route::post('/payment/process', [OrderController::class, 'processPayment'])->name('payment.process');

    // 6. Trang Trung Tâm Khách Hàng / Hồ sơ & Đơn mua
    Route::get('/profile', [\App\Http\Controllers\User\ProfileController::class, 'index'])->name('profile');
    Route::post('/profile/update', [\App\Http\Controllers\User\ProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [\App\Http\Controllers\User\ProfileController::class, 'changePassword'])->name('profile.changePassword');

    Route::get('/orders', [OrderController::class, 'orderHistory'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');

    // Các route khởi tạo & thanh toán lại MoMo
    Route::get('/orders/{order}/start-momo', [MomoController::class, 'start'])->name('orders.momo.start');
    Route::get('/orders/{order}/pay/momo', [MomoController::class, 'payAgain'])->name('orders.momo.pay');

    // Xác nhận đã nhận hàng
    Route::post('/orders/{order}/confirm-delivery', [OrderController::class, 'confirmDelivery'])->name('orders.confirmDelivery');

    // Đánh giá sản phẩm
    Route::post('/reviews', [\App\Http\Controllers\User\ReviewController::class, 'store'])->name('reviews.store');
    Route::post('/reviews/{review}/helpful', [\App\Http\Controllers\User\ReviewController::class, 'helpful'])->name('reviews.helpful');
});
