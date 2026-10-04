<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CartController extends Controller
{
    // Lấy giỏ hàng của User từ DB 
    private function getCart()
    {
        if (auth()->check()) {
            return auth()->user()->cart ?? [];
        }
        return session('cart', []);
    }

    // Lưu giỏ hàng vào DB hoặc Session
    private function saveCart($cart)
    {
        if (auth()->check()) {
            $user = auth()->user();
            $user->cart = $cart;
            $user->save();
        } else {
            session()->put('cart', $cart);
        }
    }

    public function index()
    {
        if (!auth()->check()) {
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập tài khoản để xem và quản lý giỏ hàng của bạn!');
        }

        $cart = $this->getCart();
        $productIds = !empty($cart) ? array_unique(array_column($cart, 'id')) : [];
        $products = !empty($productIds) ? Product::whereIn('id', $productIds)->get()->keyBy('id') : collect();
        return view('products.cart', compact('cart', 'products'));
    }

    public function add(Request $request, $id)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('products.show', $id);
        }

        $isAjax = $request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest';

        if (!auth()->check()) {
            if ($isAjax) {
                return response()->json([
                    'success'  => false,
                    'message'  => 'Vui lòng đăng nhập để thêm sản phẩm vào giỏ hàng!',
                    'redirect' => route('login'),
                ], 401);
            }
            return redirect()->route('login')->with('warning', 'Vui lòng đăng nhập để thực hiện thao tác!');
        }

        if (!auth()->user()->hasVerifiedEmail()) {
            if ($isAjax) {
                return response()->json([
                    'success'  => false,
                    'message'  => 'Bạn cần xác thực email trước khi mua hàng!',
                    'redirect' => route('verification.notice'),
                ], 403);
            }
            return redirect()->route('verification.notice')
                ->with('warning', 'Bạn cần xác thực email trước khi mua hàng!');
        }

        $product = Product::findOrFail($id);

        // Kiểm tra tồn kho sản phẩm: Nếu hết hàng (stock_quantity <= 0) thì không cho thêm vào giỏ
        if ((int)$product->stock_quantity <= 0) {
            $msg = "Sản phẩm \"{$product->name}\" hiện đã hết hàng!";
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        $cart = $this->getCart();

        // Lấy thông tin màu, số lượng từ Form
        $quantity = max(1, (int)$request->input('quantity', 1));
        $color = trim((string)$request->input('color', 'Tiêu chuẩn'));
        if (empty($color)) {
            $color = 'Tiêu chuẩn';
        }

        // Kiểm tra tồn kho của màu sắc này
        $availableStock = $product->getStockForColor($color);
        if ($availableStock <= 0) {
            $msg = "Màu \"{$color}\" của sản phẩm hiện đã hết hàng!";
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        // Tạo key riêng cho sản phẩm kèm màu sắc
        $cartKey = $id . '_' . Str::slug($color);

        $currentInCart = isset($cart[$cartKey]) ? (int)$cart[$cartKey]['quantity'] : 0;
        if (($currentInCart + $quantity) > $availableStock) {
            $canAdd = max(0, $availableStock - $currentInCart);
            if ($canAdd <= 0) {
                $msg = "Bạn đã thêm tối đa {$availableStock} sản phẩm màu \"{$color}\" vào giỏ hàng rồi!";
            } else {
                $msg = "Kho chỉ còn {$availableStock} sản phẩm màu \"{$color}\". Bạn chỉ có thể thêm thêm {$canAdd} sản phẩm!";
            }
            if ($isAjax) {
                return response()->json(['success' => false, 'message' => $msg], 422);
            }
            return redirect()->back()->with('error', $msg);
        }

        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity'] += $quantity;
        } else {
            $cart[$cartKey] = [
                'id'       => $product->id,
                'name'     => $product->name,
                'quantity' => $quantity,
                'price'    => $product->sale_price ?? $product->price,
                'image'    => $product->image,
                'color'    => $color
            ];
        }

        $this->saveCart($cart);
        $totalCartCount = array_sum(array_column($cart, 'quantity'));

        // Nếu người dùng bấm "Mua ngay", chuyển thẳng tới giỏ hàng
        if ($request->input('action') === 'buy_now') {
            if ($isAjax) {
                return response()->json([
                    'success'    => true,
                    'message'    => 'Đã chuyển sản phẩm vào giỏ hàng!',
                    'cart_count' => $totalCartCount,
                    'redirect'   => route('cart.index'),
                ]);
            }
            return redirect()->route('cart.index')->with('success', 'Đã chuyển sản phẩm vào giỏ hàng!');
        }

        $successMsg = "Đã thêm {$quantity} sản phẩm (Màu: {$color}) vào giỏ hàng thành công!";
        if ($isAjax) {
            return response()->json([
                'success'    => true,
                'message'    => $successMsg,
                'cart_count' => $totalCartCount,
            ]);
        }

        return redirect()->back()->with('success', $successMsg);
    }

    public function update(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = $this->getCart();
            if (isset($cart[$request->id])) {
                $productId = $cart[$request->id]['id'];
                $color = $cart[$request->id]['color'] ?? null;
                $product = Product::find($productId);

                $requestedQty = max(1, (int)$request->quantity);
                $isCapped = false;
                $availableStock = 9999;

                if ($product) {
                    $availableStock = $product->getStockForColor($color);
                    if ($requestedQty > $availableStock) {
                        $requestedQty = max(1, $availableStock);
                        $isCapped = true;
                    }
                }

                $cart[$request->id]['quantity'] = $requestedQty;
                $this->saveCart($cart);

                if ($request->ajax() || $request->wantsJson()) {
                    $itemSubtotal = $cart[$request->id]['price'] * $cart[$request->id]['quantity'];
                    $total = 0;
                    foreach ($cart as $item) {
                        $total += $item['price'] * $item['quantity'];
                    }
                    return response()->json([
                        'success' => true,
                        'message' => $isCapped ? "Kho chỉ còn tối đa {$availableStock} sản phẩm màu này!" : 'Cập nhật giỏ hàng thành công!',
                        'is_capped' => $isCapped,
                        'max_stock' => $availableStock,
                        'item_id' => $request->id,
                        'quantity' => $cart[$request->id]['quantity'],
                        'subtotal' => $itemSubtotal,
                        'formatted_subtotal' => number_format($itemSubtotal, 0, ',', '.') . 'đ',
                        'cart_total' => $total,
                        'formatted_cart_total' => number_format($total, 0, ',', '.') . 'đ'
                    ]);
                }

                session()->flash('success', $isCapped ? "Kho chỉ còn tối đa {$availableStock} sản phẩm!" : 'Cập nhật giỏ hàng thành công!');
            }
        }
        return redirect()->back();
    }

    /**
     * Thay đổi màu sắc trực tiếp từ trong giỏ hàng
     */
    public function changeColor(Request $request)
    {
        $request->validate([
            'id' => 'required|string',
            'new_color' => 'required|string',
        ]);

        $oldKey = $request->input('id');
        $newColor = trim((string)$request->input('new_color'));
        $cart = $this->getCart();

        if (!isset($cart[$oldKey])) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy sản phẩm trong giỏ!'], 404);
            }
            return redirect()->back()->with('error', 'Không tìm thấy sản phẩm trong giỏ!');
        }

        $item = $cart[$oldKey];
        $product = Product::find($item['id']);

        if (!$product) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => 'Sản phẩm không tồn tại!'], 404);
            }
            return redirect()->back()->with('error', 'Sản phẩm không tồn tại!');
        }

        $availableStock = $product->getStockForColor($newColor);
        if ($availableStock <= 0) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'message' => "Màu \"{$newColor}\" hiện đã hết hàng!"], 400);
            }
            return redirect()->back()->with('error', "Màu \"{$newColor}\" hiện đã hết hàng!");
        }

        $newKey = $product->id . '_' . Str::slug($newColor);

        if ($newKey === $oldKey) {
            return response()->json(['success' => true, 'message' => 'Màu sắc không thay đổi.']);
        }

        $qty = (int)$item['quantity'];

        if (isset($cart[$newKey])) {
            // Nếu màu mới đã có trong giỏ hàng, gộp số lượng (không vượt quá tồn kho của màu đó)
            $mergedQty = $cart[$newKey]['quantity'] + $qty;
            $cart[$newKey]['quantity'] = min($availableStock, $mergedQty);
            unset($cart[$oldKey]);
        } else {
            // Đổi sang màu mới
            $cart[$newKey] = $item;
            $cart[$newKey]['color'] = $newColor;
            $cart[$newKey]['quantity'] = min($availableStock, $qty);
            unset($cart[$oldKey]);
        }

        $this->saveCart($cart);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Đã đổi sang màu \"{$newColor}\" thành công!",
                'new_id' => $newKey,
                'reload' => true
            ]);
        }

        return redirect()->back()->with('success', "Đã đổi sang màu \"{$newColor}\" thành công!");
    }

    public function remove($id)
    {
        $cart = $this->getCart();
        if (isset($cart[$id])) {
            unset($cart[$id]);
            $this->saveCart($cart);
        }
        return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ!');
    }
}