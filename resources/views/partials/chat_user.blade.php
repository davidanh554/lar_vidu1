@auth
@php
    $unreadChatCount = \App\Models\Message::where('receiver_id', Auth::id())->where('is_read', false)->count();
@endphp
<div id="chat-box" style="position: fixed; bottom: 24px; right: 24px; z-index: 9999;">
    <!-- Nút mở chat tròn nổi bật -->
    <button id="chat-toggle" class="btn rounded-circle shadow-lg d-flex align-items-center justify-content-center position-relative" style="width: 60px; height: 60px; font-size: 15px;" title="Chat với chúng tôi">
        <i class="fa-solid fa-comments fs-4"></i>
        <!-- Chấm đỏ số tin nhắn mới -->
        <span id="chat-unread-badge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger {{ $unreadChatCount > 0 ? '' : 'd-none' }}" style="font-size: 0.72rem; padding: 0.25rem 0.5rem; border: 2px solid #fff; box-shadow: 0 2px 6px rgba(0,0,0,0.3);">
            {{ $unreadChatCount }}
        </span>
    </button>

    <!-- Khung chat popup Liquid Glass -->
    <div id="chat-popup" class="card shadow-lg border-0" style="display: none; width: 360px; border-radius: 20px; overflow: hidden;">
        <div class="card-header text-dark d-flex justify-content-between align-items-center p-3" id="chat-header">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-dark bg-opacity-20 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                    <i class="fa-solid fa-headset fs-5 text-dark"></i>
                </div>
                <div>
                    <div class="fw-bold text-dark small" style="font-size: 0.95rem;">Hỗ trợ VUA TABLET</div>
                    <small class="d-flex align-items-center text-dark-50" style="font-size: 0.72rem; color: rgba(2, 44, 22, 0.8);">
                        <i class="fa-solid fa-circle text-success me-1" style="font-size: 0.45rem;"></i> Trực tuyến 24/7
                    </small>
                </div>
            </div>
            <button id="chat-close" class="btn btn-sm btn-close" aria-label="Close"></button>
        </div>

        <div id="chat-messages" class="card-body p-3" style="height: 340px; overflow-y: auto;">
            <div class="text-center text-muted mt-5 small"><i class="fa-solid fa-spinner fa-spin me-1"></i> Đang tải lịch sử...</div>
        </div>

        <div class="card-footer p-2 border-top" id="chat-footer">
            <div class="input-group">
                <input type="text" id="chat-input" class="form-control rounded-pill-start border-end-0" placeholder="Nhập tin nhắn..." autocomplete="off">
                <button id="send-btn" class="btn btn-modern-primary rounded-pill-end px-3">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("chat-toggle");
    const chatPopup = document.getElementById("chat-popup");
    const closeBtn = document.getElementById("chat-close");
    const sendBtn = document.getElementById("send-btn");
    const input = document.getElementById("chat-input");
    const chatBox = document.getElementById("chat-messages");
    const chatBadge = document.getElementById("chat-unread-badge");

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

    // --- MỞ / ĐÓNG CHAT ---
    toggleBtn.onclick = () => {
        chatPopup.style.display = "block";
        toggleBtn.classList.add("d-none");
        if (chatBadge) {
            chatBadge.classList.add("d-none");
            chatBadge.textContent = "0";
        }
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        fetch("{{ route('user.chat.markRead') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            }
        }).catch(() => {});

        loadMessages();
        setTimeout(() => input.focus(), 200);
    };

    closeBtn.onclick = () => {
        chatPopup.style.display = "none";
        toggleBtn.classList.remove("d-none");
    };

    // --- LOAD TIN NHẮN ---
    function loadMessages() {
        fetch("{{ route('user.chat.messages') }}")
            .then(res => res.json())
            .then(messages => {
                let html = "";
                if (messages.length === 0) {
                    html = `
                        <div class="text-center text-muted mt-5 small">
                            <i class="fa-regular fa-comments fs-2 mb-2 d-block opacity-50"></i>
                            Bắt đầu cuộc trò chuyện với Quản trị viên để được tư vấn chọn máy!
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
                            <div class="message-row ${isMe ? 'user-msg' : 'admin-msg'} mb-2">
                                <div class="msg-bubble">
                                    <small class="d-block fw-semibold mb-1 opacity-75" style="font-size: 0.7rem;">
                                        ${isMe ? 'Bạn' : '<i class="fa-solid fa-shield-halved me-1"></i>Hỗ trợ viên'}
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
            .catch(err => console.error("Lỗi tải tin nhắn:", err));
    }

    // --- GỬI TIN NHẮN ---
    function sendMessage() {
        let message = input.value.trim();
        if (message === "") return;

        input.disabled = true;
        sendBtn.disabled = true;

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
            sendBtn.disabled = false;
            input.focus();
            loadMessages();
        })
        .catch(err => {
            console.error("Lỗi gửi tin:", err);
            input.disabled = false;
            sendBtn.disabled = false;
        });
    }

    sendBtn.onclick = sendMessage;

    input.addEventListener("keypress", function(e) {
        if (e.key === "Enter") {
            sendMessage();
        }
    });

    setInterval(() => {
        if (chatPopup.style.display === "block") {
            loadMessages();
        }
    }, 3000);

    // Kiểm tra thông báo nền (tin nhắn mới + cập nhật đơn hàng)
    function pollNotifications() {
        fetch("{{ route('user.notifications.unread') }}")
            .then(res => res.json())
            .then(data => {
                // Cập nhật badge tin nhắn nếu khung chat đang đóng
                if (chatBadge && chatPopup.style.display !== "block") {
                    if (data.unread_messages > 0) {
                        chatBadge.textContent = data.unread_messages;
                        chatBadge.classList.remove("d-none");
                    } else {
                        chatBadge.classList.add("d-none");
                    }
                }

                // Cập nhật badge trên nút "Đơn hàng của tôi"
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
});
</script>
@endauth
