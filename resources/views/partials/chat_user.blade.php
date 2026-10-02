@php
    $unreadChatCount = Auth::check() 
        ? \App\Models\Message::where('receiver_id', Auth::id())->where('is_read', false)->count() 
        : 0;
@endphp

<!-- ============================================================
     WIDGET HỖ TRỢ KHÁCH HÀNG (LIÊN HỆ ADMIN) - DARK TECH EDITION
     ============================================================ -->
<div id="support-chat-widget" style="position: fixed; bottom: 24px; right: 24px; z-index: 9998;">
    <!-- Nút tròn Hỗ Trợ Khách Hàng Liquid Glass iOS 27 -->
    <button id="support-chat-toggle" class="btn rounded-circle d-flex align-items-center justify-content-center position-relative support-toggle-btn" 
            style="width: 56px; height: 56px; background: linear-gradient(135deg, rgba(255, 255, 255, 0.18) 0%, rgba(15, 23, 42, 0.75) 100%) !important; backdrop-filter: blur(28px) saturate(210%) !important; -webkit-backdrop-filter: blur(28px) saturate(210%) !important; border: 1.5px solid rgba(255, 255, 255, 0.25) !important; border-top: 1.5px solid rgba(255, 255, 255, 0.75) !important; color: #fff; box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.45), 0 14px 35px rgba(0, 0, 0, 0.6) !important;" 
            title="Hỗ trợ khách hàng (Gặp Admin)">
        <i class="fa-solid fa-headset fs-5 text-white"></i>
        
        <!-- Chấm đỏ số tin nhắn mới -->
        <span id="support-unread-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $unreadChatCount > 0 ? '' : 'd-none' }}" 
              style="font-size: 0.65rem; padding: 2px 6px; border: 2px solid #0b0f19; font-weight: 700;">
            {{ $unreadChatCount }}
        </span>
    </button>

    <!-- Khung chat popup Hỗ trợ khách hàng Liquid Glass iOS 27 -->
    <div id="support-chat-popup" class="card border-0" 
         style="display: none; width: 380px; max-width: calc(100vw - 32px); height: 540px; max-height: calc(100vh - 48px); border-radius: 20px; overflow: hidden; background: rgba(15, 23, 42, 0.78) !important; backdrop-filter: blur(35px) saturate(210%) contrast(106%) !important; -webkit-backdrop-filter: blur(35px) saturate(210%) contrast(106%) !important; border: 1px solid rgba(255, 255, 255, 0.18) !important; border-top: 1.5px solid rgba(255, 255, 255, 0.55) !important; box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -1px 2px rgba(0, 0, 0, 0.35), 0 25px 60px rgba(0, 0, 0, 0.8) !important;">
        
        <!-- Header -->
        <div class="card-header d-flex justify-content-between align-items-center p-3" 
             style="background: rgba(30, 41, 59, 0.55) !important; backdrop-filter: blur(20px) !important; -webkit-backdrop-filter: blur(20px) !important; border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                     style="width: 36px; height: 36px; background: rgba(59, 130, 246, 0.2); border: 1px solid rgba(59, 130, 246, 0.4);">
                    <i class="fa-solid fa-headset" style="color: #60a5fa; font-size: 0.95rem;"></i>
                </div>
                <div>
                    <div class="fw-bold text-white small" style="font-size: 0.92rem;">Hỗ trợ khách hàng</div>
                    <small class="d-flex align-items-center text-white-50" style="font-size: 0.72rem;">
                        <span class="d-inline-block rounded-circle me-1" style="width: 7px; height: 7px; background: #00f59b; box-shadow: 0 0 8px #00f59b;"></span> Tư vấn viên trực tuyến
                    </small>
                </div>
            </div>
            <button id="support-chat-close" class="btn btn-sm text-white-50 hover-text-white p-1" title="Đóng">
                <i class="fa-solid fa-xmark fs-5"></i>
            </button>
        </div>

        @auth
            <!-- Body: Lịch sử tin nhắn giữa User và Admin (Liquid Glass Background) -->
            <div id="support-chat-messages" class="card-body p-3" style="height: 395px; overflow-y: auto; background: rgba(11, 15, 25, 0.6) !important;">
                <div class="text-center text-white-50 mt-5 small"><i class="fa-solid fa-spinner fa-spin me-1 text-emerald"></i> Đang tải lịch sử hỗ trợ...</div>
            </div>

            <!-- Footer: Ô nhập tin nhắn (Liquid Glass) -->
            <div class="card-footer p-2 border-top" id="support-chat-footer" style="background: rgba(30, 41, 59, 0.6) !important; backdrop-filter: blur(20px) !important; -webkit-backdrop-filter: blur(20px) !important; border-color: rgba(255, 255, 255, 0.1) !important;">
                <div class="input-group">
                    <input type="text" id="support-chat-input" class="form-control rounded-pill-start border-end-0 shadow-none" 
                           style="background: rgba(11, 15, 25, 0.7); color: #ffffff; border-color: rgba(255, 255, 255, 0.18); font-size: 0.85rem;" 
                           placeholder="Nhắn tin cho nhân viên hỗ trợ..." autocomplete="off">
                    <button id="support-send-btn" class="btn rounded-pill-end px-3 fw-bold" 
                            style="background: linear-gradient(135deg, #00f59b 0%, #00d674 100%) !important; color: #022c16 !important; border: 1px solid rgba(255, 255, 255, 0.4) !important; border-top: 1.5px solid rgba(255, 255, 255, 0.7) !important; box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.6) !important;">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </div>
            </div>
        @else
            <!-- Guest: Lời mời đăng nhập để liên hệ Admin -->
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center" style="height: 440px; background: rgba(11, 15, 25, 0.6) !important;">
                <div class="rounded-circle p-3 mb-3 d-flex align-items-center justify-content-center" 
                     style="width: 64px; height: 64px; background: rgba(0, 245, 155, 0.15); border: 1px solid rgba(0, 245, 155, 0.35);">
                    <i class="fa-solid fa-user-lock fs-3" style="color: #00f59b;"></i>
                </div>
                <h6 class="fw-bold text-white mb-2">Đăng nhập để gặp Tư vấn viên</h6>
                <p class="text-white-50 small mb-4" style="line-height: 1.5; font-size: 0.82rem;">
                    Vui lòng đăng nhập tài khoản để bộ phận Hỗ trợ khách hàng có thể kiểm tra đơn hàng và lưu lịch sử giải đáp giúp bạn!
                </p>
                <div class="d-flex gap-2 w-100 justify-content-center mb-3">
                    <a href="{{ route('login') }}" class="btn btn-sm rounded-pill px-4 fw-bold" style="background: linear-gradient(135deg, #00f59b 0%, #00d674 100%); color: #022c16; border: 1px solid rgba(255, 255, 255, 0.4); box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.5);">
                        <i class="fa-solid fa-arrow-right-to-bracket me-1"></i> Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-sm rounded-pill px-3 opacity-75">
                        Đăng ký
                    </a>
                </div>
                <div class="pt-3 border-top w-100" style="border-color: rgba(255, 255, 255, 0.1) !important;">
                    <small class="text-white-50 d-block mb-2" style="font-size: 0.75rem;">Cần giải đáp thắc mắc ngay lập tức?</small>
                    <button type="button" class="btn btn-sm rounded-pill px-3 py-1 text-white fw-semibold" 
                            style="background: rgba(255, 255, 255, 0.08); border: 1px solid rgba(0, 245, 155, 0.4); font-size: 0.82rem;" 
                            onclick="window.switchFromSupportToGemini()">
                        Chat với Trợ lý AI (24/7)
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
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.25) 0%, rgba(15, 23, 42, 0.85) 100%) !important;
    border-color: rgba(255, 255, 255, 0.6) !important;
    box-shadow: 0 0 25px rgba(255, 255, 255, 0.35), inset 0 2px 4px rgba(255, 255, 255, 0.6) !important;
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
    background: linear-gradient(135deg, #00f59b 0%, #00d674 100%) !important;
    color: #022c16 !important;
    font-weight: 700 !important;
    border: 1px solid rgba(255, 255, 255, 0.4) !important;
    border-top: 1.5px solid rgba(255, 255, 255, 0.7) !important;
    box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.6), 0 4px 15px rgba(0, 0, 0, 0.35) !important;
    margin-left: auto;
    border-bottom-right-radius: 4px;
}
.support-admin-bubble {
    background: rgba(30, 41, 59, 0.65) !important;
    backdrop-filter: blur(18px) !important;
    -webkit-backdrop-filter: blur(18px) !important;
    color: #f1f5f9;
    margin-right: auto;
    border-bottom-left-radius: 4px;
    border: 1px solid rgba(255, 255, 255, 0.14) !important;
    border-top: 1.5px solid rgba(255, 255, 255, 0.35) !important;
    box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.25), 0 4px 15px rgba(0, 0, 0, 0.25) !important;
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
                            <div class="text-center text-white-50 mt-5 small">
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

        setInterval(() => {
            if (chatPopup && chatPopup.style.display === "block") {
                loadMessages();
            }
        }, 3500);

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
