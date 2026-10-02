@php
    $unreadChatCount = Auth::check() 
        ? \App\Models\Message::where('receiver_id', Auth::id())->where('is_read', false)->count() 
        : 0;
@endphp

<!-- ============================================================
     WIDGET HỖ TRỢ KHÁCH HÀNG (LIÊN HỆ ADMIN / TƯ VẤN VIÊN)
     ============================================================ -->
<div id="support-chat-widget" style="position: fixed; bottom: 24px; right: 24px; z-index: 9998;">
    <!-- Nút tròn Hỗ Trợ Khách Hàng -->
    <button id="support-chat-toggle" class="btn rounded-circle shadow-lg d-flex align-items-center justify-content-center position-relative support-toggle-btn" 
            style="width: 60px; height: 60px; background: linear-gradient(135deg, #0f766e 0%, #059669 100%); color: #fff; border: 2px solid rgba(255,255,255,0.4);" 
            title="Hỗ trợ khách hàng (Gặp Admin)">
        <i class="fa-solid fa-headset fs-4"></i>
        
        <!-- Chấm đỏ số tin nhắn mới -->
        <span id="support-unread-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $unreadChatCount > 0 ? '' : 'd-none' }}" 
              style="font-size: 0.72rem; padding: 0.25rem 0.5rem; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">
            {{ $unreadChatCount }}
        </span>
        
        <!-- Nhãn nhỏ bên dưới icon -->
        <span class="support-btn-label">Hỗ trợ</span>
    </button>

    <!-- Khung chat popup Hỗ trợ khách hàng -->
    <div id="support-chat-popup" class="card shadow-lg border-0" 
         style="display: none; width: 370px; max-width: calc(100vw - 32px); border-radius: 20px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.2) !important;">
        
        <!-- Header -->
        <div class="card-header text-white d-flex justify-content-between align-items-center p-3" 
             style="background: linear-gradient(135deg, #0f766e 0%, #059669 100%); border-bottom: none;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="fa-solid fa-headset fs-5 text-white"></i>
                </div>
                <div>
                    <div class="fw-bold text-white small" style="font-size: 0.95rem;">Hỗ trợ khách hàng</div>
                    <small class="d-flex align-items-center text-white-50" style="font-size: 0.72rem;">
                        <i class="fa-solid fa-circle text-warning me-1" style="font-size: 0.45rem;"></i> Tư vấn viên trực tuyến
                    </small>
                </div>
            </div>
            <button id="support-chat-close" class="btn btn-sm text-white opacity-75 hover-opacity-100 p-1" aria-label="Close">
                <i class="fa-solid fa-xmark fs-5"></i>
            </button>
        </div>

        @auth
            <!-- Body: Lịch sử tin nhắn giữa User và Admin -->
            <div id="support-chat-messages" class="card-body p-3" style="height: 350px; overflow-y: auto; background: #f8fafc;">
                <div class="text-center text-muted mt-5 small"><i class="fa-solid fa-spinner fa-spin me-1"></i> Đang tải lịch sử hỗ trợ...</div>
            </div>

            <!-- Footer: Ô nhập tin nhắn -->
            <div class="card-footer p-2 border-top bg-white" id="support-chat-footer">
                <div class="input-group">
                    <input type="text" id="support-chat-input" class="form-control rounded-pill-start border-end-0 shadow-none" placeholder="Nhắn tin cho nhân viên hỗ trợ..." autocomplete="off">
                    <button id="support-send-btn" class="btn btn-success rounded-pill-end px-3" style="background-color: #059669; border-color: #059669;">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        @else
            <!-- Guest: Lời mời đăng nhập để liên hệ Admin -->
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center" style="height: 350px; background: #f8fafc;">
                <div class="rounded-circle bg-emerald-light p-3 mb-3 d-flex align-items-center justify-content-center" style="width: 70px; height: 70px; background: rgba(5, 150, 105, 0.1);">
                    <i class="fa-solid fa-user-lock text-success fs-2"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2">Đăng nhập để gặp Tư vấn viên</h6>
                <p class="text-muted small mb-4" style="line-height: 1.5;">
                    Vui lòng đăng nhập tài khoản để bộ phận Hỗ trợ khách hàng có thể tra cứu đơn hàng và lưu lịch sử giải đáp giúp bạn!
                </p>
                <div class="d-flex gap-2 w-100 justify-content-center mb-3">
                    <a href="{{ route('login') }}" class="btn btn-success btn-sm rounded-pill px-4" style="background: #059669;">
                        <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                        Đăng ký
                    </a>
                </div>
                <div class="pt-3 border-top w-100">
                    <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">Cần giải đáp thắc mắc ngay lập tức?</small>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 py-1" onclick="window.switchFromSupportToGemini()">
                        <i class="fa-solid fa-wand-magic-sparkles me-1 text-primary"></i> Chat với Trợ lý AI Gemini (24/7)
                    </button>
                </div>
            </div>
        @endauth
    </div>
</div>

<style>
.support-toggle-btn {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
.support-toggle-btn:hover {
    transform: scale(1.08) translateY(-2px);
    box-shadow: 0 10px 25px rgba(5, 150, 105, 0.45) !important;
}
.support-btn-label {
    position: absolute;
    bottom: -18px;
    font-size: 0.65rem;
    font-weight: 700;
    color: #0f766e;
    background: rgba(255, 255, 255, 0.95);
    padding: 1px 6px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    white-space: nowrap;
}
.support-msg-bubble {
    max-width: 82%;
    padding: 10px 14px;
    border-radius: 16px;
    font-size: 0.88rem;
    line-height: 1.45;
    word-break: break-word;
}
.support-user-bubble {
    background: linear-gradient(135deg, #0f766e 0%, #059669 100%);
    color: #fff;
    margin-left: auto;
    border-bottom-right-radius: 4px;
}
.support-admin-bubble {
    background: #ffffff;
    color: #1e293b;
    margin-right: auto;
    border-bottom-left-radius: 4px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 6px rgba(0,0,0,0.04);
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("support-chat-toggle");
    const chatPopup = document.getElementById("support-chat-popup");
    const closeBtn = document.getElementById("support-chat-close");
    const sendBtn = document.getElementById("support-send-btn");
    const input = document.getElementById("support-chat-input");
    const chatBox = document.getElementById("support-chat-messages");
    const chatBadge = document.getElementById("support-unread-badge");

    if (!toggleBtn) return;

    function escapeHtml(text) {
        if (!text) return "";
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Mở khung chat hỗ trợ
    window.openSupportChat = function() {
        // Đóng chat AI Gemini nếu đang mở
        if (window.closeGeminiChat) {
            window.closeGeminiChat();
        }

        chatPopup.style.display = "block";
        toggleBtn.classList.add("d-none");
        if (chatBadge) {
            chatBadge.classList.add("d-none");
            chatBadge.textContent = "0";
        }

        @auth
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            fetch("{{ route('user.chat.markRead') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Accept": "application/json"
                }
            }).catch(() => {});

            loadMessages();
            if (input) setTimeout(() => input.focus(), 200);
        @endauth
    };

    // Đóng khung chat hỗ trợ
    window.closeSupportChat = function() {
        chatPopup.style.display = "none";
        toggleBtn.classList.remove("d-none");
    };

    // Chuyển nhanh từ Hỗ trợ khách hàng sang Gemini AI
    window.switchFromSupportToGemini = function() {
        window.closeSupportChat();
        if (window.openGeminiChat) {
            window.openGeminiChat();
        }
    };

    toggleBtn.onclick = window.openSupportChat;
    closeBtn.onclick = window.closeSupportChat;

    @auth
        // --- LOAD TIN NHẮN GIỮA USER VÀ ADMIN ---
        function loadMessages() {
            if (!chatBox) return;
            fetch("{{ route('user.chat.messages') }}")
                .then(res => res.json())
                .then(messages => {
                    let html = "";
                    if (messages.length === 0) {
                        html = `
                            <div class="text-center text-muted mt-5 small">
                                <i class="fa-solid fa-headset fs-2 mb-2 d-block text-success opacity-50"></i>
                                Chào bạn! Đây là kênh <strong>Hỗ trợ khách hàng</strong>.<br>
                                Hãy để lại tin nhắn, nhân viên CSKH VUA TABLET sẽ phản hồi sớm nhất!
                            </div>
                        `;
                    } else {
                        messages.forEach(msg => {
                            const isMe = (msg.sender_id == "{{ Auth::id() }}");
                            let timeStr = "";
                            if (msg.created_at) {
                                const d = new Date(msg.created_at);
                                timeStr = d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                            }

                            html += `
                                <div class="d-flex mb-2 ${isMe ? 'justify-content-end' : 'justify-content-start'}">
                                    <div class="support-msg-bubble ${isMe ? 'support-user-bubble' : 'support-admin-bubble'}">
                                        <small class="d-block fw-semibold mb-1 opacity-75" style="font-size: 0.7rem;">
                                            ${isMe ? 'Bạn' : '<i class="fa-solid fa-shield-halved text-success me-1"></i>Chuyên viên hỗ trợ'}
                                        </small>
                                        <div>${escapeHtml(msg.content)}</div>
                                        ${timeStr ? `<div class="text-end opacity-75 mt-1" style="font-size: 0.65rem;">${timeStr}</div>` : ''}
                                    </div>
                                </div>
                            `;
                        });
                    }
                    chatBox.innerHTML = html;
                    chatBox.scrollTop = chatBox.scrollHeight;
                })
                .catch(err => console.error("Lỗi tải tin nhắn hỗ trợ:", err));
        }

        // --- GỬI TIN NHẮN TỚI ADMIN ---
        function sendMessage() {
            if (!input) return;
            let message = input.value.trim();
            if (message === "") return;

            input.disabled = true;
            if (sendBtn) sendBtn.disabled = true;

            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

            fetch("{{ route('user.chat.send') }}", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    "Content-Type": "application/json",
                    "Accept": "application/json"
                },
                body: JSON.stringify({ message: message })
            })
            .then(res => res.json())
            .then(data => {
                input.value = "";
                input.disabled = false;
                if (sendBtn) sendBtn.disabled = false;
                input.focus();
                loadMessages();
            })
            .catch(err => {
                console.error("Lỗi gửi tin nhắn:", err);
                input.disabled = false;
                if (sendBtn) sendBtn.disabled = false;
            });
        }

        if (sendBtn) sendBtn.onclick = sendMessage;

        if (input) {
            input.addEventListener("keypress", function(e) {
                if (e.key === "Enter") {
                    sendMessage();
                }
            });
        }

        // Tự động kiểm tra tin nhắn mới mỗi 3.5 giây nếu popup đang mở
        setInterval(() => {
            if (chatPopup && chatPopup.style.display === "block") {
                loadMessages();
            }
        }, 3500);

        // Polling thông báo nền (tin nhắn mới + cập nhật đơn hàng)
        function pollNotifications() {
            fetch("{{ route('user.notifications.unread') }}")
                .then(res => res.json())
                .then(data => {
                    if (chatBadge && chatPopup.style.display !== "block") {
                        if (data.unread_messages > 0) {
                            chatBadge.textContent = data.unread_messages;
                            chatBadge.classList.remove("d-none");
                        } else {
                            chatBadge.classList.add("d-none");
                        }
                    }

                    const orderBadge = document.getElementById("nav-order-badge");
                    if (orderBadge) {
                        if (data.unread_orders > 0) {
                            orderBadge.textContent = data.unread_orders;
                            orderBadge.classList.remove("d-none");
                        } else {
                            orderBadge.classList.add("d-none");
                        }
                    }
                })
                .catch(() => {});
        }

        setInterval(pollNotifications, 5000);
    @endauth
});
</script>
