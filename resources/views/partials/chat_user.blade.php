@php
    $unreadChatCount = Auth::check() 
        ? \App\Models\Message::where('receiver_id', Auth::id())->where('is_read', false)->count() 
        : 0;
@endphp

<!-- ============================================================
     WIDGET HỖ TRỢ KHÁCH HÀNG (LIÊN HỆ ADMIN) - DARK TECH EDITION
     ============================================================ -->
<div id="support-chat-widget" style="position: fixed; bottom: 24px; right: 24px; z-index: 9998;">
    <!-- Nút tròn Hỗ Trợ Khách Hàng Clean White Style -->
    <button id="support-chat-toggle" class="btn rounded-circle d-flex align-items-center justify-content-center position-relative support-toggle-btn shadow-sm" 
            style="width: 56px; height: 56px; background: #ffffff !important; border: 2px solid #cbd5e1 !important; color: #0f172a !important; box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12) !important;" 
            title="Hỗ trợ khách hàng (Gặp Admin)">
        <span class="fw-bold" style="font-size: 0.82rem; letter-spacing: 0.5px;">CSKH</span>
        
        <!-- Chấm đỏ số tin nhắn mới -->
        <span id="support-unread-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $unreadChatCount > 0 ? '' : 'd-none' }}" 
              style="font-size: 0.65rem; padding: 2px 6px; border: 2px solid #ffffff; font-weight: 700;">
            {{ $unreadChatCount }}
        </span>
    </button>

    <!-- Khung chat popup Hỗ trợ khách hàng Clean Minimalist White -->
    <div id="support-chat-popup" class="card border-0" 
         style="display: none; width: 380px; max-width: calc(100vw - 32px); height: 540px; max-height: calc(100vh - 48px); border-radius: 20px; overflow: hidden; background: #ffffff !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15) !important;">
        
        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center p-3 text-white" 
             style="background: #0f172a !important; border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                     style="width: 38px; height: 38px; background: #ffffff; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);">
                    <i class="fa-solid fa-headset" style="font-size: 1.1rem; color: #0f172a;"></i>
                </div>
                <div>
                    <div class="fw-bold text-white small" style="font-size: 0.95rem;">Hỗ trợ khách hàng</div>
                    <small class="d-flex align-items-center text-white-50" style="font-size: 0.72rem;">
                        <span class="d-inline-block rounded-circle me-1" style="width: 7px; height: 7px; background: #10b981; box-shadow: 0 0 8px #10b981;"></span> Tư vấn viên trực tuyến
                    </small>
                </div>
            </div>
            <button id="support-chat-close" class="btn btn-sm text-white-50 hover-text-white p-1" title="Đóng" style="font-size: 1.25rem; line-height: 1;">
                <i class="fa-solid fa-xmark text-white"></i>
            </button>
        </div>

        @auth
            <!-- Body: Lịch sử tin nhắn giữa User và Admin -->
            <div id="support-chat-messages" class="card-body p-3" style="height: 410px; overflow-y: auto; background: #f8fafc !important;">
                <div class="text-center text-muted mt-5 small"><i class="fa-solid fa-spinner fa-spin me-1 text-primary"></i> Đang tải lịch sử hỗ trợ...</div>
            </div>

            <!-- Footer: Ô nhập tin nhắn -->
            <div class="card-footer p-2 border-top bg-white" id="support-chat-footer">
                <div class="input-group">
                    <input type="text" id="support-chat-input" class="form-control rounded-pill-start border-end-0 shadow-none" 
                           style="background: #f8fafc; color: #0f172a; border-color: #e2e8f0; font-size: 0.88rem;" 
                           placeholder="Nhắn tin cho nhân viên hỗ trợ..." autocomplete="off">
                    <button id="support-send-btn" class="btn rounded-pill-end px-3 fw-bold" 
                            style="background: #0f172a !important; color: #ffffff !important; border: 1px solid #0f172a !important; font-size: 0.88rem;">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
                <div class="text-center mt-1">
                    <small class="text-muted" style="font-size: 0.72rem;">
                        Nhân viên CSKH VUA TABLET • <a href="javascript:void(0)" onclick="window.switchFromSupportToGemini()" class="fw-bold text-decoration-none" style="color: #4f46e5 !important;">Chat với AI 24/7</a>
                    </small>
                </div>
            </div>
        @else
            <!-- Guest: Lời mời đăng nhập để liên hệ Admin -->
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center" style="height: 460px; background: #ffffff !important;">
                <div class="rounded-circle p-3 mb-3 d-flex align-items-center justify-content-center" 
                     style="width: 64px; height: 64px; background: #eef2ff; border: 1.5px solid #c7d2fe; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.15);">
                    <i class="fa-solid fa-headset" style="color: #4f46e5; font-size: 1.8rem;"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2" style="font-size: 1.05rem;">Đăng nhập để gặp Tư vấn viên</h6>
                <p class="text-muted small mb-4 px-2" style="line-height: 1.6; font-size: 0.84rem;">
                    Vui lòng đăng nhập tài khoản để bộ phận Hỗ trợ khách hàng có thể kiểm tra đơn hàng và giải đáp chi tiết cho bạn!
                </p>
                <div class="d-flex gap-2 w-100 justify-content-center mb-3">
                    <a href="{{ route('login') }}" class="btn btn-dark btn-sm rounded-pill px-4 fw-bold">
                        Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-secondary btn-sm rounded-pill px-4">
                        Đăng ký
                    </a>
                </div>
                <div class="pt-3 border-top w-100">
                    <small class="text-muted d-block mb-2" style="font-size: 0.75rem;">Muốn trải nghiệm tư vấn thông minh?</small>
                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 text-primary fw-semibold" 
                            style="background: #eef2ff; border: 1px solid #c7d2fe; font-size: 0.82rem;" 
                            onclick="window.switchFromSupportToGemini()">
                        Khám phá Trợ lý AI (24/7)
                    </button>
                </div>
            </div>
        @endauth
    </div>
</div>

<style>
.support-toggle-btn {
    transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
}
.support-toggle-btn:hover {
    transform: translateY(-2px) scale(1.05);
    background: #f8fafc !important;
    border-color: #94a3b8 !important;
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.18) !important;
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
    background: #4f46e5 !important;
    color: #ffffff !important;
    font-weight: 500 !important;
    box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25) !important;
    margin-left: auto;
    border-bottom-right-radius: 4px;
}
.support-admin-bubble {
    background: #ffffff !important;
    color: #0f172a !important;
    margin-right: auto;
    border-bottom-left-radius: 4px;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.03) !important;
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
                    "X-Requested-With": "XMLHttpRequest",
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
            fetch("{{ route('user.chat.messages') }}", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "application/json"
                }
            })
                .then(res => res.json())
                .then(messages => {
                    let html = "";
                    if (messages.length === 0) {
                        html = `
                            <div class="text-center text-muted mt-5 small">
                                <i class="fa-solid fa-headset fs-2 mb-2 d-block text-primary opacity-75"></i>
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
                                             ${isMe ? 'Bạn' : '<i class="fa-solid fa-shield-halved text-primary me-1"></i>Chuyên viên hỗ trợ'}
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
                    "X-Requested-With": "XMLHttpRequest",
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

        setInterval(() => {
            if (chatPopup && chatPopup.style.display === "block") {
                loadMessages();
            }
        }, 3500);

        function pollNotifications() {
            fetch("{{ route('user.notifications.unread') }}", {
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    "Accept": "application/json"
                }
            })
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
