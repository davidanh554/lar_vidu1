<footer class="footer-store mt-auto py-5 bg-dark text-white-50 border-top border-secondary border-opacity-25">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="d-flex align-items-center mb-3">
                    <span class="fs-4 fw-bold text-white tracking-tight">VUA TABLET</span>
                </div>
                <p class="small text-muted mb-3">
                    Hệ thống bán lẻ máy tính bảng và phụ kiện công nghệ cao cấp chính hãng hàng đầu Việt Nam. Cam kết chất lượng, bảo hành uy tín.
                </p>
                <div class="d-flex gap-3 fs-5 text-secondary">
                    <a href="#" class="text-secondary hover-primary"><i class="fa-brands fa-facebook"></i></a>
                    <a href="#" class="text-secondary hover-primary"><i class="fa-brands fa-tiktok"></i></a>
                    <a href="#" class="text-secondary hover-primary"><i class="fa-brands fa-youtube"></i></a>
                    <a href="#" class="text-secondary hover-primary"><i class="fa-brands fa-instagram"></i></a>
                </div>
            </div>

            <div class="col-6 col-lg-2">
                <h6 class="text-white fw-bold mb-3">Sản phẩm</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ route('home') }}" class="text-decoration-none text-secondary hover-white">Tất cả sản phẩm</a></li>
                    <li><a href="{{ route('home', ['category_id' => 1]) }}" class="text-decoration-none text-secondary hover-white">Máy tính bảng</a></li>
                    <li><a href="{{ route('home', ['category_id' => 2]) }}" class="text-decoration-none text-secondary hover-white">Phụ kiện & Bàn phím</a></li>
                    <li><a href="{{ route('cart.index') }}" class="text-decoration-none text-secondary hover-white">Giỏ hàng của bạn</a></li>
                </ul>
            </div>

            <div class="col-6 col-lg-3">
                <h6 class="text-white fw-bold mb-3">Chính sách & Hỗ trợ</h6>
                <ul class="list-unstyled small d-flex flex-column gap-2 mb-0">
                    <li><a href="{{ route('orders.index') }}" class="text-decoration-none text-secondary hover-white">Tra cứu đơn hàng GHN</a></li>
                    <li><span class="text-secondary">Bảo hành chính hãng 12T</span></li>
                    <li><span class="text-secondary">Đổi mới trong 30 ngày</span></li>
                    <li><span class="text-secondary">MoMo & COD toàn quốc</span></li>
                </ul>
            </div>

            <div class="col-lg-3">
                <h6 class="text-white fw-bold mb-3">Tổng đài hỗ trợ</h6>
                <div class="p-3 rounded-4 bg-white bg-opacity-5 border border-white border-opacity-10 mb-2">
                    <div class="small text-muted mb-1">Hotline tư vấn (Miễn phí):</div>
                    <div class="fw-bold text-white fs-5">1800 6868</div>
                </div>
                <div class="small text-muted">
                    Làm việc: 8h00 - 21h30 (Cả T7, CN)
                </div>
            </div>
        </div>

        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center pt-4 mt-4 border-top border-white border-opacity-10 small text-secondary">
            <div>© {{ date('Y') }} <strong>VUA TABLET</strong>. Bản quyền thuộc về Vua Tablet Store.</div>
            <div class="mt-2 mt-md-0 d-flex gap-3">
                <span>Giao vận: <strong>GHN Express</strong></span>
                <span>•</span>
                <span>Cổng thanh toán: <strong>MoMo & COD</strong></span>
            </div>
        </div>
    </div>
</footer>
