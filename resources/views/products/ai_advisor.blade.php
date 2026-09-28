@extends('layouts.store')

@section('title', 'AI Chọn iPad Hộ Tôi - Tư Vấn Mua Sắm Thông Minh | VUA TABLET')

@section('content')
<div class="container my-5" style="max-width: 920px;">
    <!-- Header giới thiệu -->
    <div class="text-center mb-5">
        <div class="d-inline-flex align-items-center gap-2 px-3 py-1 rounded-pill mb-3" style="background: rgba(0, 255, 135, 0.1); border: 1px solid rgba(0, 255, 135, 0.35);">
            <i class="fa-solid fa-wand-magic-sparkles text-warning"></i>
            <span class="text-emerald-laser fw-bold small" style="letter-spacing: 0.5px;">HỆ THỐNG GỢI Ý MUA SẮM THÔNG MINH</span>
        </div>
        <h1 class="fw-extrabold display-5 mb-2 text-gradient-laser">AI CHỌN IPAD HỘ TÔI</h1>
        <p class="text-white-50 mx-auto" style="max-width: 600px; font-size: 0.95rem;">
            Không cần đọc thông số phức tạp. Trả lời 5 câu hỏi nhanh — Thuật toán AI sẽ tìm ra chiếc iPad chân ái chuẩn xác 100% cho nhu cầu của bạn!
        </p>
    </div>

    <!-- Khung Quiz tương tác Liquid Glass -->
    <div class="quiz-card-wrapper shadow-lg">
        <!-- Thanh tiến trình các bước -->
        <div class="quiz-progress-bar-container">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="small text-white-50">Câu hỏi <strong id="step-number" class="text-emerald-laser">1</strong> / 5</span>
                <span id="step-label" class="small fw-semibold text-emerald-laser">Nhu cầu chính</span>
            </div>
            <div class="progress" style="height: 6px; background: rgba(255, 255, 255, 0.1); border-radius: 999px;">
                <div id="quiz-progress-fill" class="progress-bar bg-emerald-laser" style="width: 20%; transition: width 0.35s ease;"></div>
            </div>
        </div>

        <form id="ai-quiz-form" class="p-4 p-md-5">
            @csrf

            <!-- ================= CÂU HỎI 1: NHU CẦU CHÍNH ================= -->
            <div class="quiz-step-pane active" id="step-1">
                <h3 class="fw-bold text-white mb-2">1. Bạn mua iPad chủ yếu để làm gì?</h3>
                <p class="text-white-50 small mb-4">Hãy chọn nhu cầu bạn sẽ dùng nhiều nhất trên thiết bị</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="quiz-choice-card">
                            <input type="radio" name="purpose" value="study" class="d-none" checked>
                            <div class="choice-card-inner">
                                <div class="choice-icon">📚</div>
                                <div>
                                    <div class="choice-title">Học tập & Ghi chép</div>
                                    <small class="choice-desc">Học online, đọc PDF, slide giáo trình, viết note ghi chú bài giảng</small>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="col-md-6">
                        <label class="quiz-choice-card">
                            <input type="radio" name="purpose" value="draw" class="d-none">
                            <div class="choice-card-inner">
                                <div class="choice-icon">🎨</div>
                                <div>
                                    <div class="choice-title">Vẽ & Thiết kế đồ họa</div>
                                    <small class="choice-desc">Vẽ Procreate, chỉnh màu ảnh Lightroom, vẽ phác thảo minh họa</small>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="col-md-6">
                        <label class="quiz-choice-card">
                            <input type="radio" name="purpose" value="entertainment" class="d-none">
                            <div class="choice-card-inner">
                                <div class="choice-icon">🎬</div>
                                <div>
                                    <div class="choice-title">Xem phim & Giải trí</div>
                                    <small class="choice-desc">Lướt TikTok, Youtube, Netflix, đọc truyện tranh, xem tin tức</small>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="col-md-6">
                        <label class="quiz-choice-card">
                            <input type="radio" name="purpose" value="gaming" class="d-none">
                            <div class="choice-card-inner">
                                <div class="choice-icon">🎮</div>
                                <div>
                                    <div class="choice-title">Chơi game đồ họa cao</div>
                                    <small class="choice-desc">Chiến Genshin Impact, PUBG Mobile, Liên Quân mượt mà 60-120 FPS</small>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="col-12">
                        <label class="quiz-choice-card">
                            <input type="radio" name="purpose" value="work" class="d-none">
                            <div class="choice-card-inner">
                                <div class="choice-icon">💼</div>
                                <div>
                                    <div class="choice-title">Công việc văn phòng & Đa nhiệm</div>
                                    <small class="choice-desc">Xử lý Excel nặng, gửi email, đa nhiệm nhiều cửa sổ Stage Manager như laptop</small>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button type="button" class="btn btn-emerald-glow rounded-pill px-4 btn-next-step" data-target="2">
                        Tiếp theo <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- ================= CÂU HỎI 2: NGÂN SÁCH ================= -->
            <div class="quiz-step-pane" id="step-2" style="display: none;">
                <h3 class="fw-bold text-white mb-2">2. Ngân sách dự kiến của bạn là bao nhiêu?</h3>
                <p class="text-white-50 small mb-4">Hệ thống sẽ gợi ý sản phẩm tối ưu hiệu năng tốt nhất trong túi tiền của bạn</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="quiz-choice-card">
                            <input type="radio" name="budget" value="under_8m" class="d-none">
                            <div class="choice-card-inner">
                                <div class="choice-icon">💰</div>
                                <div>
                                    <div class="choice-title">Dưới 8 triệu</div>
                                    <small class="choice-desc">Mức giá tiết kiệm nhất, đáp ứng hoàn hảo các nhu cầu cơ bản</small>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="col-md-6">
                        <label class="quiz-choice-card">
                            <input type="radio" name="budget" value="8m_15m" class="d-none" checked>
                            <div class="choice-card-inner">
                                <div class="choice-icon">💵</div>
                                <div>
                                    <div class="choice-title">Từ 8 - 15 triệu (Phổ biến nhất)</div>
                                    <small class="choice-desc">Thiết kế viền mỏng hiện đại, cổng Type-C, dùng bền bỉ 4-5 năm</small>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="col-md-6">
                        <label class="quiz-choice-card">
                            <input type="radio" name="budget" value="15m_25m" class="d-none">
                            <div class="choice-card-inner">
                                <div class="choice-icon">💎</div>
                                <div>
                                    <div class="choice-title">Từ 15 - 25 triệu (Cận cao cấp)</div>
                                    <small class="choice-desc">Trang bị chip Apple Silicon M-series cực mạnh, hỗ trợ bút Pro</small>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="col-md-6">
                        <label class="quiz-choice-card">
                            <input type="radio" name="budget" value="above_25m" class="d-none">
                            <div class="choice-card-inner">
                                <div class="choice-icon">👑</div>
                                <div>
                                    <div class="choice-title">Trên 25 triệu (Đỉnh chóp)</div>
                                    <small class="choice-desc">Màn hình OLED 120Hz ProMotion cao cấp nhất, hiệu năng M4 vô đối</small>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-light rounded-pill px-4 btn-prev-step" data-target="1">
                        <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                    </button>
                    <button type="button" class="btn btn-emerald-glow rounded-pill px-4 btn-next-step" data-target="3">
                        Tiếp theo <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- ================= CÂU HỎI 3: DÙNG BÚT APPLE PENCIL ================= -->
            <div class="quiz-step-pane" id="step-3" style="display: none;">
                <h3 class="fw-bold text-white mb-2">3. Bạn có dự định dùng bút cảm ứng (Apple Pencil) không?</h3>
                <p class="text-white-50 small mb-4">Mỗi dòng iPad sẽ tương thích với các loại bút riêng (Pencil USB-C, Pencil 2 hoặc Pencil Pro)</p>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="quiz-choice-card">
                            <input type="radio" name="pencil" value="frequent" class="d-none" checked>
                            <div class="choice-card-inner text-center flex-column">
                                <div class="choice-icon mb-2">✏️</div>
                                <div class="choice-title mb-1">Rất thường xuyên</div>
                                <small class="choice-desc">Viết bài, vẽ nghệ thuật, ghi chú note mỗi ngày (cần sạc không dây từ tính)</small>
                            </div>
                        </label>
                    </div>

                    <div class="col-md-4">
                        <label class="quiz-choice-card">
                            <input type="radio" name="pencil" value="sometimes" class="d-none">
                            <div class="choice-card-inner text-center flex-column">
                                <div class="choice-icon mb-2">✍️</div>
                                <div class="choice-title mb-1">Thỉnh thoảng</div>
                                <small class="choice-desc">Ký văn bản, gạch chân tài liệu, vẽ nguệch ngoạc nhẹ nhàng</small>
                            </div>
                        </label>
                    </div>

                    <div class="col-md-4">
                        <label class="quiz-choice-card">
                            <input type="radio" name="pencil" value="none" class="d-none">
                            <div class="choice-card-inner text-center flex-column">
                                <div class="choice-icon mb-2">❌</div>
                                <div class="choice-title mb-1">Không dùng bút</div>
                                <small class="choice-desc">Chỉ vuốt chạm bằng ngón tay, không cần tính năng sạc bút nam châm</small>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-light rounded-pill px-4 btn-prev-step" data-target="2">
                        <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                    </button>
                    <button type="button" class="btn btn-emerald-glow rounded-pill px-4 btn-next-step" data-target="4">
                        Tiếp theo <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- ================= CÂU HỎI 4: KÍCH THƯỚC MONG MUỐN ================= -->
            <div class="quiz-step-pane" id="step-4" style="display: none;">
                <h3 class="fw-bold text-white mb-2">4. Bạn mong muốn kích thước màn hình như thế nào?</h3>
                <p class="text-white-50 small mb-4">Kích thước ảnh hưởng trực tiếp tới cân nặng và độ tiện lợi khi mang theo</p>

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="quiz-choice-card">
                            <input type="radio" name="size" value="compact" class="d-none">
                            <div class="choice-card-inner text-center flex-column">
                                <div class="choice-icon mb-2">📱</div>
                                <div class="choice-title mb-1">Siêu nhỏ gọn (~8.3")</div>
                                <small class="choice-desc">Dễ dàng cầm 1 tay, nhét vừa túi xách nhỏ, cực kỳ linh hoạt (Dòng iPad Mini)</small>
                            </div>
                        </label>
                    </div>

                    <div class="col-md-4">
                        <label class="quiz-choice-card">
                            <input type="radio" name="size" value="standard" class="d-none" checked>
                            <div class="choice-card-inner text-center flex-column">
                                <div class="choice-icon mb-2">💻</div>
                                <div class="choice-title mb-1">Tiêu chuẩn (~11")</div>
                                <small class="choice-desc">Cân đối vàng giữa không gian hiển thị và sự gọn nhẹ khi bỏ ba lô</small>
                            </div>
                        </label>
                    </div>

                    <div class="col-md-4">
                        <label class="quiz-choice-card">
                            <input type="radio" name="size" value="large" class="d-none">
                            <div class="choice-card-inner text-center flex-column">
                                <div class="choice-icon mb-2">🖥️</div>
                                <div class="choice-title mb-1">Màn hình lớn (>=13")</div>
                                <small class="choice-desc">Không gian vẽ và làm việc khổng lồ, thay thế máy tính để bàn</small>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-light rounded-pill px-4 btn-prev-step" data-target="3">
                        <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                    </button>
                    <button type="button" class="btn btn-emerald-glow rounded-pill px-4 btn-next-step" data-target="5">
                        Tiếp theo <i class="fa-solid fa-arrow-right ms-2"></i>
                    </button>
                </div>
            </div>

            <!-- ================= CÂU HỎI 5: YẾU TỐ ƯU TIÊN NHẤT ================= -->
            <div class="quiz-step-pane" id="step-5" style="display: none;">
                <h3 class="fw-bold text-white mb-2">5. Bạn ưu tiên yếu tố nào nhất trong những tiêu chí sau?</h3>
                <p class="text-white-50 small mb-4">Câu hỏi cuối cùng giúp AI xác định trọng số chính xác nhất</p>

                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="quiz-choice-card">
                            <input type="radio" name="priority" value="performance" class="d-none" checked>
                            <div class="choice-card-inner">
                                <div class="choice-icon">⚡</div>
                                <div>
                                    <div class="choice-title">Hiệu năng vi xử lý (Chip mạnh)</div>
                                    <small class="choice-desc">Ưu tiên máy chip M2/M4 mạnh mẽ, dùng bền bỉ 4-5 năm không lag</small>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="col-md-6">
                        <label class="quiz-choice-card">
                            <input type="radio" name="priority" value="screen" class="d-none">
                            <div class="choice-card-inner">
                                <div class="choice-icon">👁️</div>
                                <div>
                                    <div class="choice-title">Chất lượng hiển thị màn hình</div>
                                    <small class="choice-desc">Tần số quét 120Hz mượt mà, độ phân giải cao, tương phản rực rỡ</small>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="col-md-6">
                        <label class="quiz-choice-card">
                            <input type="radio" name="priority" value="budget" class="d-none">
                            <div class="choice-card-inner">
                                <div class="choice-icon">🏷️</div>
                                <div>
                                    <div class="choice-title">Tiết kiệm chi phí tối đa</div>
                                    <small class="choice-desc">Mức giá rẻ nhất mà vẫn đáp ứng tốt tất cả các tác vụ mong muốn</small>
                                </div>
                            </div>
                        </label>
                    </div>

                    <div class="col-md-6">
                        <label class="quiz-choice-card">
                            <input type="radio" name="priority" value="battery" class="d-none">
                            <div class="choice-card-inner">
                                <div class="choice-icon">🔋</div>
                                <div>
                                    <div class="choice-title">Pin trâu & Cân nặng nhẹ</div>
                                    <small class="choice-desc">Thời lượng pin bền bỉ, máy nhẹ nhàng dễ cầm trên tay cả ngày</small>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="d-flex justify-content-between mt-4">
                    <button type="button" class="btn btn-outline-light rounded-pill px-4 btn-prev-step" data-target="4">
                        <i class="fa-solid fa-arrow-left me-2"></i> Quay lại
                    </button>
                    <button type="button" id="btn-submit-quiz" class="btn btn-emerald-glow rounded-pill px-5 fw-bold shadow-lg">
                        <i class="fa-solid fa-brain me-2"></i> XEM KẾT QUẢ AI
                    </button>
                </div>
            </div>
        </form>

        <!-- ================= MÀN HÌNH LOADING PHÂN TÍCH ================= -->
        <div id="quiz-loading-pane" class="p-5 text-center" style="display: none;">
            <div class="ai-radar-scanner mb-4">
                <div class="scanner-circle"></div>
                <i class="fa-solid fa-brain fs-1 text-emerald-laser"></i>
            </div>
            <h4 class="fw-bold text-white mb-2">AI Đang Phân Tích Dữ Liệu...</h4>
            <p id="ai-loading-status" class="text-emerald-laser small mb-0 font-monospace">Đang khớp 5 tiêu chí với cơ sở dữ liệu iPad...</p>
        </div>

        <!-- ================= MÀN HÌNH KẾT QUẢ ================= -->
        <div id="quiz-result-pane" class="p-4 p-md-5" style="display: none;">
            <div class="text-center mb-4">
                <span class="badge bg-emerald-laser text-dark px-3 py-1 fs-6 fw-bold rounded-pill mb-2">
                    <i class="fa-solid fa-medal me-1"></i> KẾT QUẢ ĐỀ XUẤT CHO BẠN
                </span>
                <h2 class="fw-bold text-white mb-1">CHIẾC IPAD PHÙ HỢP NHẤT</h2>
                <p class="text-white-50 small mb-0">Thuật toán đối soát thông số kỹ thuật và ngân sách tối ưu</p>
            </div>

            <!-- Card Quán Quân (Top 1) -->
            <div class="top-result-card mb-4">
                <div class="row g-4 align-items-center">
                    <div class="col-md-5 text-center">
                        <div class="top-product-img-box">
                            <span class="top-match-badge" id="top-match-percent">95% Phù hợp</span>
                            <img id="top-product-img" src="" alt="Top Product" class="img-fluid">
                        </div>
                    </div>
                    <div class="col-md-7">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <span class="badge bg-warning text-dark fw-bold"><i class="fa-solid fa-crown me-1"></i> LỰA CHỌN SỐ 1</span>
                            <span id="top-product-specs" class="text-white-50 small"></span>
                        </div>
                        <h3 id="top-product-name" class="fw-extrabold text-white mb-2"></h3>

                        <div class="d-flex align-items-baseline gap-2 mb-3">
                            <span id="top-product-price" class="fs-3 fw-bold text-emerald-laser"></span>
                            <span id="top-product-original-price" class="text-decoration-line-through text-white-50 small"></span>
                        </div>

                        <!-- Danh sách lý do tích xanh -->
                        <div class="p-3 rounded-3 mb-4" style="background: rgba(0, 255, 135, 0.06); border: 1px solid rgba(0, 255, 135, 0.25);">
                            <div class="fw-bold text-white small mb-2"><i class="fa-solid fa-circle-check text-emerald-laser me-1"></i> Vì sao chiếc máy này sinh ra dành cho bạn?</div>
                            <ul id="top-reasons-list" class="list-unstyled mb-0 small text-light d-flex flex-column gap-2">
                            </ul>
                        </div>

                        <div class="d-flex flex-wrap gap-2">
                            <a id="top-view-btn" href="#" class="btn btn-emerald-glow rounded-pill px-4 fw-bold">
                                <i class="fa-solid fa-eye me-1"></i> Xem chi tiết sản phẩm
                            </a>
                            <button type="button" class="btn btn-outline-light rounded-pill px-3" onclick="resetQuiz()">
                                <i class="fa-solid fa-rotate-right me-1"></i> Thử lại bài trắc nghiệm
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hai phương án thay thế: Tiết kiệm hơn & Nâng cấp hơn -->
            <div class="row g-3" id="alternative-container">
                <div class="col-md-6" id="alt-saving-col" style="display: none;">
                    <div class="alt-choice-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-success-subtle text-success small"><i class="fa-solid fa-piggy-bank me-1"></i> Tiết kiệm chi phí hơn</span>
                            <span id="alt-saving-match" class="small text-emerald-laser fw-bold">85% phù hợp</span>
                        </div>
                        <h6 id="alt-saving-name" class="fw-bold text-white mb-1"></h6>
                        <div id="alt-saving-price" class="text-emerald-laser fw-bold mb-2"></div>
                        <a id="alt-saving-link" href="#" class="btn btn-sm btn-outline-light rounded-pill w-100">
                            Xem phương án này <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>

                <div class="col-md-6" id="alt-upgrade-col" style="display: none;">
                    <div class="alt-choice-card">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="badge bg-warning-subtle text-warning small"><i class="fa-solid fa-rocket me-1"></i> Cấu hình cao cấp hơn</span>
                            <span id="alt-upgrade-match" class="small text-warning fw-bold">80% phù hợp</span>
                        </div>
                        <h6 id="alt-upgrade-name" class="fw-bold text-white mb-1"></h6>
                        <div id="alt-upgrade-price" class="text-emerald-laser fw-bold mb-2"></div>
                        <a id="alt-upgrade-link" href="#" class="btn btn-sm btn-outline-light rounded-pill w-100">
                            Xem phương án này <i class="fa-solid fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Gradient Text Theme */
.text-gradient-laser {
    background: linear-gradient(135deg, #00ff87 0%, #34d399 50%, #ffffff 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
}
.text-emerald-laser {
    color: #00ff87 !important;
}
.bg-emerald-laser {
    background: linear-gradient(90deg, #059669 0%, #00ff87 100%) !important;
}

/* Quiz Card Container Liquid Glass */
.quiz-card-wrapper {
    background: linear-gradient(145deg, rgba(20, 28, 28, 0.85) 0%, rgba(13, 17, 22, 0.95) 100%);
    border: 1px solid rgba(0, 255, 135, 0.35);
    border-radius: 28px;
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    overflow: hidden;
}
.quiz-progress-bar-container {
    padding: 20px 32px 0 32px;
}

/* Quiz Choice Cards */
.quiz-choice-card {
    display: block;
    cursor: pointer;
    user-select: none;
    height: 100%;
}
.choice-card-inner {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 16px;
    padding: 16px 18px;
    display: flex;
    align-items: center;
    gap: 14px;
    transition: all 0.25s ease;
    height: 100%;
}
.choice-icon {
    font-size: 2rem;
    line-height: 1;
    flex-shrink: 0;
}
.choice-title {
    font-weight: 700;
    color: #f8fafc;
    font-size: 0.98rem;
}
.choice-desc {
    color: #94a3b8;
    font-size: 0.8rem;
    line-height: 1.35;
    display: block;
}

/* Hover & Selected State */
.quiz-choice-card:hover .choice-card-inner {
    border-color: rgba(0, 255, 135, 0.5);
    background: rgba(0, 255, 135, 0.05);
    transform: translateY(-2px);
}
.quiz-choice-card input[type="radio"]:checked + .choice-card-inner {
    border-color: #00ff87;
    background: rgba(0, 255, 135, 0.12);
    box-shadow: 0 0 20px rgba(0, 255, 135, 0.3);
}
.quiz-choice-card input[type="radio"]:checked + .choice-card-inner .choice-title {
    color: #00ff87;
}

/* Emerald Glow Button */
.btn-emerald-glow {
    background: linear-gradient(135deg, #059669 0%, #10b981 50%, #00ff87 100%);
    color: #064e3b;
    font-weight: 700;
    border: none;
    box-shadow: 0 0 20px rgba(0, 255, 135, 0.5);
    transition: all 0.25s ease;
}
.btn-emerald-glow:hover {
    background: linear-gradient(135deg, #047857 0%, #059669 50%, #34d399 100%);
    color: #ffffff;
    box-shadow: 0 0 30px rgba(0, 255, 135, 0.8);
    transform: translateY(-1px);
}

/* Radar Animation Loading */
.ai-radar-scanner {
    position: relative;
    width: 100px;
    height: 100px;
    margin: 0 auto;
    display: flex;
    align-items: center;
    justify-content: center;
}
.scanner-circle {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 2px solid #00ff87;
    animation: radarPulse 1.8s ease-out infinite;
}
@keyframes radarPulse {
    0% { transform: scale(0.6); opacity: 1; }
    100% { transform: scale(1.6); opacity: 0; }
}

/* Result Cards */
.top-result-card {
    background: rgba(255, 255, 255, 0.04);
    border: 2px solid #00ff87;
    box-shadow: 0 0 35px rgba(0, 255, 135, 0.25);
    border-radius: 20px;
    padding: 24px;
}
.top-product-img-box {
    position: relative;
    background: rgba(0, 0, 0, 0.4);
    border-radius: 16px;
    padding: 20px;
    border: 1px solid rgba(255, 255, 255, 0.08);
}
.top-product-img-box img {
    max-height: 220px;
    object-fit: contain;
}
.top-match-badge {
    position: absolute;
    top: 10px;
    left: 10px;
    background: linear-gradient(135deg, #ef4444 0%, #f59e0b 100%);
    color: #ffffff;
    font-size: 0.78rem;
    font-weight: 800;
    padding: 4px 10px;
    border-radius: 999px;
    box-shadow: 0 0 12px rgba(245, 158, 11, 0.6);
}

.alt-choice-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 14px;
    padding: 16px;
    height: 100%;
}
</style>

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {
    const stepLabels = {
        1: 'Nhu cầu chính',
        2: 'Khoảng ngân sách',
        3: 'Bút Apple Pencil',
        4: 'Kích thước mong muốn',
        5: 'Yếu tố ưu tiên nhất'
    };

    // Chuyển step
    document.querySelectorAll('.btn-next-step').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetStep = this.getAttribute('data-target');
            goToStep(targetStep);
        });
    });

    document.querySelectorAll('.btn-prev-step').forEach(btn => {
        btn.addEventListener('click', function() {
            const targetStep = this.getAttribute('data-target');
            goToStep(targetStep);
        });
    });

    function goToStep(stepNum) {
        document.querySelectorAll('.quiz-step-pane').forEach(el => el.style.display = 'none');
        const targetEl = document.getElementById('step-' + stepNum);
        if (targetEl) {
            targetEl.style.display = 'block';
            document.getElementById('step-number').innerText = stepNum;
            document.getElementById('step-label').innerText = stepLabels[stepNum] || '';
            document.getElementById('quiz-progress-fill').style.width = (stepNum * 20) + '%';
        }
    }

    // Submit Quiz & Xử lý AI
    const submitBtn = document.getElementById('btn-submit-quiz');
    const form = document.getElementById('ai-quiz-form');
    const loadingPane = document.getElementById('quiz-loading-pane');
    const resultPane = document.getElementById('quiz-result-pane');
    const loadingStatus = document.getElementById('ai-loading-status');

    submitBtn.addEventListener('click', function() {
        // Ẩn form, hiển thị loading scan
        form.style.display = 'none';
        loadingPane.style.display = 'block';

        // Mô phỏng text loading AI quét dữ liệu
        const statusMsgs = [
            'Đang phân tích 5 tiêu chí của bạn...',
            'Đang đối soát thông số chip Apple Silicon & Màn hình...',
            'Đang tính toán trọng số tương thích ngân sách...',
            'Đã tìm thấy sản phẩm hoàn hảo nhất!'
        ];
        let idx = 0;
        const msgTimer = setInterval(() => {
            idx++;
            if (idx < statusMsgs.length) {
                loadingStatus.innerText = statusMsgs[idx];
            }
        }, 600);

        // Lấy dữ liệu form
        const formData = new FormData(form);

        fetch("{{ route('ai.recommend') }}", {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: formData
        })
        .then(res => res.json())
        .then(res => {
            clearInterval(msgTimer);
            setTimeout(() => {
                loadingPane.style.display = 'none';
                if (res.success) {
                    renderResult(res);
                } else {
                    alert(res.message || 'Có lỗi xảy ra!');
                    resetQuiz();
                }
            }, 1200);
        })
        .catch(err => {
            clearInterval(msgTimer);
            console.error('AI Error:', err);
            loadingPane.style.display = 'none';
            alert('Lỗi kết nối máy chủ AI!');
            resetQuiz();
        });
    });

    function renderResult(data) {
        const top = data.top1;
        document.getElementById('top-product-name').innerText = top.name;
        document.getElementById('top-match-percent').innerText = top.match_percentage + '% Phù hợp';
        document.getElementById('top-product-price').innerText = top.price_formatted;
        document.getElementById('top-product-img').src = top.image;
        document.getElementById('top-product-specs').innerText = `Chip ${top.chip} • ${top.screen_size} • ${top.storage}`;
        document.getElementById('top-view-btn').href = top.url;

        if (top.original_price_formatted) {
            document.getElementById('top-product-original-price').innerText = top.original_price_formatted;
        } else {
            document.getElementById('top-product-original-price').innerText = '';
        }

        // Render lý do tích xanh
        const reasonsList = document.getElementById('top-reasons-list');
        let reasonsHtml = '';
        top.reasons.forEach(r => {
            reasonsHtml += `<li><i class="fa-solid fa-check text-emerald-laser me-2"></i>${r}</li>`;
        });
        reasonsList.innerHTML = reasonsHtml;

        // Phương án tiết kiệm
        const altSaving = data.saving_choice;
        const savingCol = document.getElementById('alt-saving-col');
        if (altSaving) {
            document.getElementById('alt-saving-name').innerText = altSaving.name;
            document.getElementById('alt-saving-price').innerText = altSaving.price_formatted;
            document.getElementById('alt-saving-match').innerText = altSaving.match_percentage + '% phù hợp';
            document.getElementById('alt-saving-link').href = altSaving.url;
            savingCol.style.display = 'block';
        } else {
            savingCol.style.display = 'none';
        }

        // Phương án nâng cấp
        const altUpgrade = data.upgrade_choice;
        const upgradeCol = document.getElementById('alt-upgrade-col');
        if (altUpgrade) {
            document.getElementById('alt-upgrade-name').innerText = altUpgrade.name;
            document.getElementById('alt-upgrade-price').innerText = altUpgrade.price_formatted;
            document.getElementById('alt-upgrade-match').innerText = altUpgrade.match_percentage + '% phù hợp';
            document.getElementById('alt-upgrade-link').href = altUpgrade.url;
            upgradeCol.style.display = 'block';
        } else {
            upgradeCol.style.display = 'none';
        }

        resultPane.style.display = 'block';
    }

    window.resetQuiz = function() {
        resultPane.style.display = 'none';
        loadingPane.style.display = 'none';
        form.style.display = 'block';
        goToStep(1);
    };
});
</script>
@endpush
@endsection
