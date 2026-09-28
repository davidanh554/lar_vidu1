@php
    $checkoutItems = $cart ?? ($checkoutItems ?? []);
    $total = $totalPrice ?? ($total ?? 0);
@endphp
@include('products.checkout')
