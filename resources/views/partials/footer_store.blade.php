<footer class="footer-store mt-auto py-5 bg-white text-muted border-top">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="d-flex align-items-center mb-3">
                    <a href="{{ route('home') }}" class="brand-logo-wrap">
                        <span class="brand-logo-text" style="font-size: 1.85rem;">
                            Vua<span class="brand-gradient">Tablet</span>
                        </span>
                    </a>
                </div>
                <p class="small text-muted mb-3" style="line-height: 1.6;">
                    Hệ thống bán lẻ máy tính bảng và phụ kiện công nghệ cao cấp chính hãng hàng đầu. Cam kết chất lượng, máy mới 100%, bảo hành 12 tháng uy tín.
                </p>
                <div class="d-flex gap-3 fs-5 text-secondary">
                    <a href="#" class="text-secondary hover-primary"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="text-secondary hover-primary"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#" class="text-secondary hover-primary"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#" class="text-secondary hover-primary"><i class="fa-brands fa-instagram"></i></a>
                </div>
            </div>

            <div class="col-6 col-lg-2">
                <h6 class="text-dark fw-bold mb-3">Máy tính bảng</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ route('home') }}" class="text-decoration-none text-secondary">Tất cả sản phẩm</a></li>
                    <li><a href="{{ route('home', ['category_id' => 1]) }}" class="text-decoration-none text-secondary">iPad (Apple)</a></li>
                    <li><a href="{{ route('home', ['category_id' => 2]) }}" class="text-decoration-none text-secondary">Galaxy Tab</a></li>
                    <li><a href="{{ route('home', ['category_id' => 3]) }}" class="text-decoration-none text-secondary">Xiaomi Pad</a></li>
                    <li><a href="{{ route('cart.index') }}" class="text-decoration-none text-secondary">Giỏ hàng của bạn</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-3">
                <h6 class="text-dark fw-bold mb-3">Chính sách & Hỗ trợ</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ route('orders.index') }}" class="text-decoration-none text-secondary">Tra cứu đơn hàng GHN</a></li>
                    <li><span class="text-secondary">Bảo hành chính hãng 12T</span></li>
                    <li><span class="text-secondary">Đổi mới trong 30 ngày</span></li>
                    <li><span class="text-secondary">MoMo & COD toàn quốc</span></li>
                </ul>
            </div>

            <div class="col-lg-3">
                <h6 class="text-dark fw-bold mb-3">Tổng đài hỗ trợ</h6>
                <div class="p-3 rounded-4 bg-light border mb-2">
                    <div class="small text-muted mb-1">Hotline tư vấn (Miễn phí):</div>
                    <div class="fw-bold text-dark fs-5">1800 6868</div>
                </div>
                <div class="small text-muted">
                    Làm việc: 8h00 - 21h30 (Cả T7, CN)
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pt-4 mt-4 border-top small text-secondary">
            <div>© {{ date('Y') }} <strong>VUA TABLET</strong>. Bản quyền thuộc về Vua Tablet Store.</div>
            <div class="mt-2 mt-md-0 d-flex gap-3">
                <span>Giao vận: <strong>GHN Express</strong></span>
                <span>•</span>
                <span>Cổng thanh toán: <strong>MoMo & COD</strong></span>
            </div>
        </div>
    </div>
</footer>
