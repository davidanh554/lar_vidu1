@extends('layouts.store')

@section('title', 'Câu Hỏi Thường Gặp (FAQ) - VUA TABLET')

@section('content')
<div class="container my-4 my-lg-5" style="max-width: 1000px;">
    <!-- Header -->
    <div class="text-center mb-5">
        <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill fw-bold mb-2">
            <i class="fa-solid fa-circle-question me-1"></i> TRUNG TÂM TRỢ GIÚP
        </span>
        <h1 class="h2 fw-bold text-dark mb-3">Câu Hỏi Thường Gặp (FAQ)</h1>
        <p class="text-muted mx-auto" style="max-width: 600px;">
            Giải đáp chi tiết mọi thắc mắc về chính sách bảo hành, đổi trả, giao hàng GHN, tích xu đổi thưởng và quyền lợi hội viên tại VUA TABLET.
        </p>
    </div>

    <!-- Nhóm 1: Bảo hành & Đổi trả -->
    <div class="card card-modern p-4 mb-4 border-0 shadow-sm rounded-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <div class="rounded-circle bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-shield-halved fs-5"></i>
            </div>
            <h4 class="h5 fw-bold mb-0 text-dark">1. Chính Sách Bảo Hành & Đổi Mới</h4>
        </div>

        <div class="accordion accordion-flush" id="faqAccordionWarranty">
            <div class="accordion-item border-bottom">
                <h2 class="accordion-header" id="headingW1">
                    <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseW1" aria-expanded="false" aria-controls="collapseW1">
                        Sản phẩm máy tính bảng tại Vua Tablet được bảo hành thế nào?
                    </button>
                </h2>
                <div id="collapseW1" class="accordion-collapse collapse" aria-labelledby="headingW1" data-bs-parent="#faqAccordionWarranty">
                    <div class="accordion-body text-muted small lh-lg">
                        Tất cả các sản phẩm Apple iPad, Samsung Galaxy Tab, Xiaomi Pad bán ra tại Vua Tablet đều được cam kết bảo hành chính hãng <strong>12 tháng</strong>. Bạn có thể mang máy đến trực tiếp các trung tâm bảo hành ủy quyền của hãng trên toàn quốc hoặc gửi về cửa hàng để được hỗ trợ chuyển phát bảo hành miễn phí.
                    </div>
                </div>
            </div>

            <div class="accordion-item border-bottom">
                <h2 class="accordion-header" id="headingW2">
                    <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseW2" aria-expanded="false" aria-controls="collapseW2">
                        Điều kiện áp dụng chính sách "Đổi mới trong 30 ngày"?
                    </button>
                </h2>
                <div id="collapseW2" class="accordion-collapse collapse" aria-labelledby="headingW2" data-bs-parent="#faqAccordionWarranty">
                    <div class="accordion-body text-muted small lh-lg">
                        Trong 30 ngày đầu tiên kể từ khi nhận hàng, nếu máy phát sinh lỗi phần cứng do nhà sản xuất (như lỗi màn hình, nguồn, loa, camera...), Vua Tablet cam kết <strong>đổi máy mới 100%</strong> ngay lập tức mà không mất thêm bất kỳ chi phí nào. Quý khách vui lòng giữ lại đầy đủ vỏ hộp và phụ kiện đi kèm.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nhóm 2: Giao hàng & Thanh toán -->
    <div class="card card-modern p-4 mb-4 border-0 shadow-sm rounded-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <div class="rounded-circle bg-success bg-opacity-10 text-success d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-truck-fast fs-5"></i>
            </div>
            <h4 class="h5 fw-bold mb-0 text-dark">2. Giao Hàng GHN Express & Thanh Toán</h4>
        </div>

        <div class="accordion accordion-flush" id="faqAccordionShipping">
            <div class="accordion-item border-bottom">
                <h2 class="accordion-header" id="headingS1">
                    <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseS1" aria-expanded="false" aria-controls="collapseS1">
                        Cước phí vận chuyển được tính như thế nào?
                    </button>
                </h2>
                <div id="collapseS1" class="accordion-collapse collapse" aria-labelledby="headingS1" data-bs-parent="#faqAccordionShipping">
                    <div class="accordion-body text-muted small lh-lg">
                        Hệ thống kết nối trực tiếp với <strong>API Giao Hàng Nhanh (GHN)</strong> để tự động tính chính xác phí giao hàng dựa trên Tỉnh/Thành phố, Quận/Huyện, Phường/Xã nơi nhận và trọng lượng máy. Cước phí hiển thị minh bạch 100% tại trang thanh toán trước khi bạn đặt hàng.
                    </div>
                </div>
            </div>

            <div class="accordion-item border-bottom">
                <h2 class="accordion-header" id="headingS2">
                    <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseS2" aria-expanded="false" aria-controls="collapseS2">
                        Tôi có được kiểm tra hàng trước khi thanh toán không?
                    </button>
                </h2>
                <div id="collapseS2" class="accordion-collapse collapse" aria-labelledby="headingS2" data-bs-parent="#faqAccordionShipping">
                    <div class="accordion-body text-muted small lh-lg">
                        <strong>HOÀN TOÀN CÓ THỂ!</strong> Mọi đơn hàng gửi qua GHN đều cho phép khách hàng <strong>đồng kiểm mở hộp</strong> trước mặt nhân viên giao hàng để kiểm tra đúng mẫu mã, màu sắc, tình trạng máy trước khi thanh toán COD hoặc ký nhận.
                    </div>
                </div>
            </div>

            <div class="accordion-item border-bottom">
                <h2 class="accordion-header" id="headingS3">
                    <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseS3" aria-expanded="false" aria-controls="collapseS3">
                        Vua Tablet hỗ trợ những phương thức thanh toán nào?
                    </button>
                </h2>
                <div id="collapseS3" class="accordion-collapse collapse" aria-labelledby="headingS3" data-bs-parent="#faqAccordionShipping">
                    <div class="accordion-body text-muted small lh-lg">
                        Chúng tôi hỗ trợ 2 hình thức thanh toán tiện lợi:
                        <ul class="mb-0 ps-3 mt-1">
                            <li><strong>Thanh toán khi nhận hàng (COD):</strong> Trả tiền mặt cho shipper GHN sau khi nhận và kiểm tra máy.</li>
                            <li><strong>Cổng thanh toán MoMo:</strong> Quét mã QR MoMo, chuyển khoản qua ví MoMo hoặc thẻ ATM nội địa Napas tức thì với độ bảo mật cao nhất.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nhóm 3: Tích Xu & Ưu đãi -->
    <div class="card card-modern p-4 mb-4 border-0 shadow-sm rounded-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <div class="rounded-circle bg-warning bg-opacity-10 text-warning d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-coins fs-5"></i>
            </div>
            <h4 class="h5 fw-bold mb-0 text-dark">3. Tích Xu Thưởng & Mã Giảm Giá (Coupon)</h4>
        </div>

        <div class="accordion accordion-flush" id="faqAccordionCoins">
            <div class="accordion-item border-bottom">
                <h2 class="accordion-header" id="headingC1">
                    <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseC1" aria-expanded="false" aria-controls="collapseC1">
                        Làm thế nào để nhận Xu Vua Tablet miễn phí mỗi ngày?
                    </button>
                </h2>
                <div id="collapseC1" class="accordion-collapse collapse" aria-labelledby="headingC1" data-bs-parent="#faqAccordionCoins">
                    <div class="accordion-body text-muted small lh-lg">
                        Bạn có thể nhận Xu thưởng bằng 2 cách thú vị:
                        <ul class="mb-0 ps-3 mt-1">
                            <li><strong>Xem Video Review giải trí:</strong> Lướt video ngắn tại mục <em>"Video Sắm Tablet"</em>, xem đủ 30 giây để nhận xu thưởng tự động vào ví.</li>
                            <li><strong>Vòng quay may mắn:</strong> Mỗi ngày bạn được tặng 1 lượt quay miễn phí để trúng từ 5 đến 50 Xu.</li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="accordion-item border-bottom">
                <h2 class="accordion-header" id="headingC2">
                    <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseC2" aria-expanded="false" aria-controls="collapseC2">
                        1 Xu quy đổi được bao nhiêu tiền và cách sử dụng?
                    </button>
                </h2>
                <div id="collapseC2" class="accordion-collapse collapse" aria-labelledby="headingC2" data-bs-parent="#faqAccordionCoins">
                    <div class="accordion-body text-muted small lh-lg">
                        Tỷ lệ quy đổi tiêu chuẩn là: <strong>1 Xu = 500 VNĐ</strong>. Khi đặt hàng tại trang Thanh toán, bạn chỉ cần tick chọn <em>"Dùng Xu Vua Tablet để giảm tiền"</em>, hệ thống sẽ tự động trừ số tiền tương ứng vào tổng giá trị đơn hàng.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Nhóm 4: Xếp hạng thành viên -->
    <div class="card card-modern p-4 mb-4 border-0 shadow-sm rounded-4">
        <div class="d-flex align-items-center gap-2 mb-3">
            <div class="rounded-circle bg-info bg-opacity-10 text-info d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                <i class="fa-solid fa-crown fs-5"></i>
            </div>
            <h4 class="h5 fw-bold mb-0 text-dark">4. Xếp Hạng Hội Viên (Loyalty Tiers)</h4>
        </div>

        <div class="accordion accordion-flush" id="faqAccordionTiers">
            <div class="accordion-item border-bottom">
                <h2 class="accordion-header" id="headingT1">
                    <button class="accordion-button collapsed fw-semibold text-dark" type="button" data-bs-toggle="collapse" data-bs-target="#collapseT1" aria-expanded="false" aria-controls="collapseT1">
                        Hệ thống xếp hạng thành viên gồm những bậc nào?
                    </button>
                </h2>
                <div id="collapseT1" class="accordion-collapse collapse" aria-labelledby="headingT1" data-bs-parent="#faqAccordionTiers">
                    <div class="accordion-body text-muted small lh-lg">
                        Cấp bậc được tính dựa trên tổng chi tiêu tích lũy từ các đơn hàng thành công của bạn:
                        <ul class="mb-0 ps-3 mt-1">
                            <li><strong>Thành viên Đồng:</strong> Dưới 10 triệu đồng (Mặc định).</li>
                            <li><strong>Thành viên Bạc:</strong> Từ 10 triệu đến dưới 25 triệu đồng (Hệ số x1.2 Xu thưởng).</li>
                            <li><strong>Thành viên Vàng VIP:</strong> Từ 25 triệu đến dưới 50 triệu đồng (Hệ số x1.5 Xu thưởng).</li>
                            <li><strong>Thành viên Kim Cương VIP:</strong> Từ 50 triệu đồng trở lên (Hệ số x2.0 Xu thưởng, miễn phí ship trọn đời).</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Hộp hỗ trợ nhanh dưới chân trang -->
    <div class="p-4 rounded-4 text-center mt-5 text-white" style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%);">
        <h4 class="fw-bold mb-2">Bạn vẫn chưa tìm được câu trả lời?</h4>
        <p class="small opacity-90 mb-3 mx-auto" style="max-width: 500px;">
            Đội ngũ hỗ trợ và Trợ lý AI CSKH luôn sẵn sàng giải đáp mọi thắc mắc của bạn 24/7.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <a href="tel:18006868" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-primary">
                <i class="fa-solid fa-phone me-1"></i> Gọi Hotline 1800 6868
            </a>
            <button type="button" onclick="document.getElementById('gemini-chat-toggle')?.click()" class="btn btn-outline-light rounded-pill px-4 py-2 fw-bold">
                <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Hỏi Trợ Lý AI Chatbot
            </button>
        </div>
    </div>
</div>
@endsection
