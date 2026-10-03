<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trang Quản Trị - Tablet Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    @include('admin.layouts.nav')

    <div class="container-fluid my-4">
        <div class="row">
            <div class="col-md-3 col-lg-2">
                @include('admin.layouts.sidebar')
            </div>
            <div class="col-md-9 col-lg-10">
                @if (session('success'))
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        {{ session('error') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Container thông báo tin nhắn mới dạng Toast nổi bật -->
    <div id="admin-chat-toast-container" style="position: fixed; bottom: 85px; right: 24px; z-index: 10000; display: flex; flex-direction: column; gap: 10px; max-width: 330px; pointer-events: none;"></div>

    <!-- Admin Modern Chat Widget -->
    <div id="admin-chat-box" style="position: fixed; bottom: 24px; right: 24px; z-index: 9999;">
        @php
            $adminUnreadCount = \App\Models\Message::where('receiver_id', auth()->id())->where('is_read', false)->count();
        @endphp
        <button id="chat-toggle" class="btn shadow-lg d-flex align-items-center gap-2 position-relative">
            <span>Tư vấn khách hàng</span>
            <span id="admin-chat-badge" class="badge rounded-pill bg-danger {{ $adminUnreadCount > 0 ? '' : 'd-none' }}" style="font-size: 0.72rem; padding: 0.25rem 0.5rem;">
                {{ $adminUnreadCount }}
            </span>
        </button>

        <div id="chat-popup" class="card shadow-lg border-0" style="display: none; width: 640px; height: 570px; border-radius: 20px; overflow: hidden;">
            <!-- Modal Header -->
            <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center border-0" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;">
                <div>
                    <div class="fw-bold text-white small" style="font-size: 0.95rem;">Trung tâm Hỗ trợ Khách hàng</div>
                    <small class="text-white-50" style="font-size: 0.75rem;">Trò chuyện và tư vấn trực tiếp cho người dùng</small>
                </div>
                <button id="chat-close" class="btn btn-sm btn-close btn-close-white" aria-label="Close"></button>
            </div>

            <!-- Modal Body (2 Columns) -->
            <div class="row g-0 flex-grow-1" style="height: calc(100% - 66px);">
                <!-- User List Sidebar (Left Column) -->
                <div class="col-5 border-end bg-light d-flex flex-column" style="height: 100%;">
                    <div class="p-2 border-bottom bg-white">
                        <div class="input-group input-group-sm">
                            <input type="text" id="user-search-input" class="form-control bg-light" placeholder="Tìm theo tên, ID, email..." style="font-size: 0.8rem;">
                        </div>
                    </div>
                    <div id="user-list" class="flex-grow-1" style="overflow-y: auto;">
                        <div class="p-3 text-center text-muted small">Đang tải danh sách...</div>
                    </div>
                </div>

                <!-- Chat & Message Pane (Right Column) -->
                <div class="col-7 d-flex flex-column bg-white" style="height: 100%;">
                    <!-- Selected user status header -->
                    <div id="active-user-header" class="p-2 px-3 border-bottom bg-light d-flex align-items-center justify-content-between" style="min-height: 48px;">
                        <div class="text-muted small">
                            Chọn khách hàng bên trái để nhắn tin
                        </div>
                    </div>

                    <!-- Messages list -->
                    <div id="chat-messages" class="flex-grow-1 p-3" style="overflow-y: auto; background: #f8fafc;">
                        <div class="text-center mt-5 text-muted small">
                            Chọn một khách hàng để xem lịch sử trò chuyện
                        </div>
                    </div>

                    <!-- Input area -->
                    <div class="p-2 border-top bg-white">
                        <div class="input-group">
                            <input type="text" id="chat-input" class="form-control rounded-pill-start border-end-0" placeholder="Nhập câu trả lời..." autocomplete="off" disabled>
                            <button id="send-btn" class="btn btn-modern-primary rounded-pill-end px-3" disabled>
                                Gửi
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
    @keyframes slideInUp {
        from { transform: translateY(20px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }
    @keyframes badgePulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.4); }
        100% { transform: scale(1); }
    }
    .badge-pulse-animation {
        animation: badgePulse 0.35s ease-in-out 3 !important;
    }
    .user-item-unread {
        border-left: 3px solid #ef4444 !important;
        background-color: #f0f7ff !important;
    }
    .user-item {
        transition: background-color 0.15s ease;
    }
    .user-item:hover {
        background-color: #f1f5f9 !important;
    }
    .user-item.active {
        background-color: #e0e7ff !important;
        border-left: 3px solid #4f46e5 !important;
    }
    .admin-chat-toast {
        pointer-events: auto;
        background: #ffffff;
        border-left: 4px solid #4f46e5;
        border-radius: 12px;
        padding: 12px 14px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.18);
        border: 1px solid #e2e8f0;
        animation: slideInUp 0.3s ease;
    }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    let currentUserId = null;
    let currentUserName = "";
    let currentUserEmail = "";
    let allUsersData = [];
    let knownMessageTimestamps = {};
    let isFirstLoad = true;

    const chatToggle = document.getElementById("chat-toggle");
    const chatPopup = document.getElementById("chat-popup");
    const chatClose = document.getElementById("chat-close");
    const userListEl = document.getElementById("user-list");
    const userSearchInput = document.getElementById("user-search-input");
    const chatMessages = document.getElementById("chat-messages");
    const chatInput = document.getElementById("chat-input");
    const sendBtn = document.getElementById("send-btn");
    const activeUserHeader = document.getElementById("active-user-header");
    const adminChatBadge = document.getElementById("admin-chat-badge");

    function escapeHtml(text) {
        if (!text) return "";
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Âm thanh chuông báo tin nhắn mới
    function playChatNotificationSound() {
        try {
            const AudioContext = window.AudioContext || window.webkitAudioContext;
            if (!AudioContext) return;
            const ctx = new AudioContext();
            const now = ctx.currentTime;
            
            const osc = ctx.createOscillator();
            const gain = ctx.createGain();
            osc.connect(gain);
            gain.connect(ctx.destination);
            
            osc.type = 'sine';
            osc.frequency.setValueAtTime(587.33, now); // D5
            osc.frequency.setValueAtTime(880, now + 0.1); // A5
            
            gain.gain.setValueAtTime(0.2, now);
            gain.gain.exponentialRampToValueAtTime(0.01, now + 0.35);
            
            osc.start(now);
            osc.stop(now + 0.35);
        } catch (e) {
            // Trình duyệt có thể chặn âm thanh nếu chưa tương tác
        }
    }

    // Hiển thị Toast thông báo khi khách nhắn tin
    function showAdminChatToast(user) {
        const container = document.getElementById("admin-chat-toast-container");
        if (!container) return;

        const toast = document.createElement("div");
        toast.className = "admin-chat-toast shadow-lg";
        toast.innerHTML = `
            <div class="d-flex align-items-center justify-content-between mb-1">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <span class="badge rounded-pill bg-danger" style="font-size: 0.65rem;">TIN MỚI</span>
                    <strong class="small text-dark text-truncate" style="max-width: 145px;">${escapeHtml(user.name)}</strong>
                </div>
                <small class="text-muted" style="font-size: 0.68rem;">vừa xong</small>
            </div>
            <div class="small text-muted text-truncate mb-2" style="font-size: 0.8rem; max-width: 280px;">${escapeHtml(user.last_message || 'Có tin nhắn mới')}</div>
            <div class="d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2 btn-toast-close" style="font-size: 0.72rem;">Đóng</button>
                <button type="button" class="btn btn-sm btn-primary py-0 px-3 fw-bold btn-toast-reply" style="font-size: 0.72rem; background: #4f46e5; border-color: #4f46e5;">Trả lời</button>
            </div>
        `;

        toast.querySelector('.btn-toast-close').onclick = () => toast.remove();
        toast.querySelector('.btn-toast-reply').onclick = () => {
            window.openChatWithUser(user.id, user.name, user.email);
            toast.remove();
        };

        container.appendChild(toast);

        // Tự biến mất sau 7s
        setTimeout(() => {
            if (toast.parentElement) {
                toast.style.opacity = '0';
                toast.style.transition = 'opacity 0.4s';
                setTimeout(() => toast.remove(), 400);
            }
        }, 7000);
    }

    window.openChatWithUser = function(userId, userName, userEmail) {
        if (chatPopup) chatPopup.style.display = "block";
        window.selectUser(userId, userName, userEmail);
    };

    // Mở / Đóng popup
    if (chatToggle) {
        chatToggle.onclick = () => {
            chatPopup.style.display = "block";
            loadUsers();
        };
    }

    if (chatClose) {
        chatClose.onclick = () => {
            chatPopup.style.display = "none";
        };
    }

    // 1. Tải danh sách User & Tin nhắn mới
    function loadUsers(silent = false) {
        fetch("{{ route('admin.chat.users') }}")
            .then(res => res.json())
            .then(data => {
                const users = Array.isArray(data) ? data : (data.users || []);
                const totalUnread = data.total_unread !== undefined ? data.total_unread : 0;

                // 1. Cập nhật Badge trên nút Chat toggle
                if (adminChatBadge) {
                    adminChatBadge.textContent = totalUnread;
                    if (totalUnread > 0) {
                        adminChatBadge.classList.remove("d-none");
                    } else {
                        adminChatBadge.classList.add("d-none");
                    }
                }

                // 2. Phát hiện tin nhắn mới gửi từ khách hàng
                if (Array.isArray(users)) {
                    users.forEach(u => {
                        const prevTime = knownMessageTimestamps[u.id];
                        // Nếu đây không phải lần load đầu tiên, user có tin nhắn mới hơn và do khách gửi
                        if (!isFirstLoad && prevTime !== undefined && u.last_message_timestamp > prevTime && !u.last_message_is_mine) {
                            const isCurrentlyChatting = (chatPopup && chatPopup.style.display === "block" && currentUserId == u.id);
                            if (!isCurrentlyChatting) {
                                playChatNotificationSound();
                                showAdminChatToast(u);
                                if (adminChatBadge) {
                                    adminChatBadge.classList.add("badge-pulse-animation");
                                    setTimeout(() => adminChatBadge.classList.remove("badge-pulse-animation"), 1200);
                                }
                            }
                        }
                        knownMessageTimestamps[u.id] = u.last_message_timestamp || 0;
                    });
                    isFirstLoad = false;
                }

                // 3. Cập nhật dữ liệu người dùng (Backend đã sắp xếp người mới nhắn lên đầu)
                allUsersData = users;
                renderUserList();

                // 4. Nếu khung chat đang mở và đang chọn một user, tự động tải tin nhắn mới của user đó
                if (chatPopup && chatPopup.style.display === "block" && currentUserId) {
                    loadMessages(true);
                }
            })
            .catch(err => console.error("Lỗi tải user:", err));
    }

    // Render danh sách User kèm tìm kiếm, hiển thị tin nhắn gần nhất và badge chưa đọc
    function renderUserList() {
        if (!userListEl) return;
        const scrollPos = userListEl.scrollTop;

        const query = (userSearchInput?.value || "").toLowerCase().trim();
        const filtered = allUsersData.filter(u => {
            if (!query) return true;
            return (u.name && u.name.toLowerCase().includes(query)) ||
                   (u.email && u.email.toLowerCase().includes(query)) ||
                   (String(u.id).includes(query));
        });

        if (filtered.length === 0) {
            userListEl.innerHTML = '<div class="p-3 text-center text-muted small">Không tìm thấy người dùng</div>';
            return;
        }

        let html = "";
        filtered.forEach(user => {
            let isActive = (currentUserId == user.id);
            let activeClass = isActive ? 'active' : '';
            let initial = (user.name || 'U').charAt(0).toUpperCase();
            let hasUnread = (user.unread_count && user.unread_count > 0);
            let unreadBadge = hasUnread 
                ? `<span class="badge rounded-pill bg-danger ms-1" style="font-size: 0.68rem; padding: 2px 6px;">${user.unread_count}</span>` 
                : '';
            
            let messageSnippet = "";
            if (user.last_message) {
                let prefix = user.last_message_is_mine ? '<span class="text-muted">Bạn: </span>' : '<span class="text-primary fw-semibold">Khách: </span>';
                let timeStr = user.last_message_time || '';
                messageSnippet = `
                    <div class="d-flex align-items-center justify-content-between mt-1" style="font-size: 0.73rem;">
                        <span class="${hasUnread ? 'fw-bold text-dark' : 'text-muted'} text-truncate" style="max-width: 120px;">
                            ${prefix}${escapeHtml(user.last_message)}
                        </span>
                        <span class="text-muted small ms-1 flex-shrink-0" style="font-size: 0.65rem;">${timeStr}</span>
                    </div>
                `;
            } else {
                messageSnippet = `
                    <div class="text-muted text-truncate mt-1" style="font-size: 0.72rem;">
                        ${escapeHtml(user.email)}
                    </div>
                `;
            }

            html += `
                <div class="user-item p-2 px-3 border-bottom ${activeClass} ${hasUnread && !isActive ? 'user-item-unread' : ''}" 
                     style="cursor: pointer;"
                     onclick="selectUser(${user.id}, '${escapeHtml(user.name)}', '${escapeHtml(user.email)}', this)">
                    <div class="d-flex align-items-center gap-2">
                        <div class="position-relative flex-shrink-0">
                            <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 0.88rem;">
                                ${initial}
                            </div>
                            ${hasUnread && !isActive ? '<span class="position-absolute top-0 start-100 translate-middle p-1 bg-danger border border-light rounded-circle" style="width: 10px; height: 10px;"></span>' : ''}
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex align-items-center justify-content-between">
                                <strong class="${hasUnread ? 'text-dark fw-bold' : 'text-dark'} text-truncate small" style="max-width: 105px;">${escapeHtml(user.name)}</strong>
                                <div class="d-flex align-items-center gap-1">
                                    <span class="badge bg-primary-subtle text-primary font-monospace border border-primary-subtle" style="font-size: 0.65rem;">#${user.id}</span>
                                    ${unreadBadge}
                                </div>
                            </div>
                            ${messageSnippet}
                        </div>
                    </div>
                </div>
            `;
        });

        userListEl.innerHTML = html;
        userListEl.scrollTop = scrollPos;
    }

    if (userSearchInput) {
        userSearchInput.addEventListener("input", renderUserList);
    }

    // 2. Chọn User để trò chuyện
    window.selectUser = function(userId, userName, userEmail, element) {
        currentUserId = userId;
        currentUserName = userName;
        currentUserEmail = userEmail;

        // Xóa badge chưa đọc ngay lập tức trên UI cho user này
        const userObj = allUsersData.find(u => u.id == userId);
        if (userObj && userObj.unread_count > 0) {
            const countToSubtract = userObj.unread_count;
            userObj.unread_count = 0;
            if (adminChatBadge) {
                const currentBadgeCount = parseInt(adminChatBadge.textContent) || 0;
                const newCount = Math.max(0, currentBadgeCount - countToSubtract);
                adminChatBadge.textContent = newCount;
                if (newCount === 0) adminChatBadge.classList.add("d-none");
            }
            renderUserList();
        }

        // Cập nhật Header hội thoại hiển thị đầy đủ Tên, ID và Email
        activeUserHeader.innerHTML = `
            <div class="d-flex align-items-center gap-2 overflow-hidden">
                <span class="badge bg-primary text-white font-monospace px-2 py-1 rounded-pill" style="font-size: 0.72rem;">
                    ID: #${userId}
                </span>
                <div class="text-truncate">
                    <strong class="text-dark small">${escapeHtml(userName)}</strong>
                    <span class="text-muted small d-inline-block text-truncate" style="max-width: 150px; font-size: 0.72rem;">(${escapeHtml(userEmail)})</span>
                </div>
            </div>
            <span class="badge-soft badge-soft-success py-1 px-2" style="font-size: 0.68rem;">
                Trực tuyến
            </span>
        `;

        // Kích hoạt ô nhập và nút gửi
        chatInput.disabled = false;
        sendBtn.disabled = false;
        chatInput.focus();

        // Highlight mục được chọn
        document.querySelectorAll('.user-item').forEach(el => el.classList.remove('active'));
        if (element) {
            element.classList.add('active');
        }

        loadMessages();
    };

    // 3. Tải tin nhắn của User đang chọn
    function loadMessages(silent = false) {
        if (!currentUserId) return;

        fetch(`/admin/chat/messages/${currentUserId}`)
            .then(res => res.json())
            .then(messages => {
                let html = "";
                if (messages.length === 0) {
                    html = `
                        <div class="text-center text-muted mt-5 small">
                            Chưa có tin nhắn nào với <strong>${escapeHtml(currentUserName)}</strong>.<br>
                            Hãy nhập tin nhắn bên dưới để bắt đầu tư vấn!
                        </div>
                    `;
                } else {
                    messages.forEach(msg => {
                        let isMe = (msg.sender_id == "{{ Auth::id() }}");
                        let senderTitle = isMe ? 'Bạn (Admin)' : escapeHtml(currentUserName);
                        let timeStr = "";
                        if (msg.created_at) {
                            const d = new Date(msg.created_at);
                            timeStr = d.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                        }

                        html += `
                            <div class="message-row ${isMe ? 'user-msg' : 'admin-msg'}">
                                <div class="msg-bubble">
                                    <small class="d-block fw-semibold mb-1 opacity-75" style="font-size: 0.7rem;">
                                        ${senderTitle}
                                    </small>
                                    <div>${escapeHtml(msg.content)}</div>
                                    ${timeStr ? `<div class="text-end opacity-75 mt-1" style="font-size: 0.65rem;">${timeStr}</div>` : ''}
                                </div>
                            </div>
                        `;
                    });
                }

                const isNearBottom = (chatMessages.scrollHeight - chatMessages.scrollTop - chatMessages.clientHeight) < 90;
                chatMessages.innerHTML = html;
                if (!silent || isNearBottom) {
                    chatMessages.scrollTop = chatMessages.scrollHeight;
                }
            })
            .catch(err => console.error("Lỗi tải tin nhắn:", err));
    }

    // 4. Gửi tin nhắn
    function sendMessage() {
        let message = chatInput.value.trim();
        if (!message || !currentUserId) return;

        chatInput.disabled = true;
        sendBtn.disabled = true;

        fetch("{{ route('admin.chat.send') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                message: message,
                user_id: currentUserId
            })
        })
        .then(res => res.json())
        .then(data => {
            chatInput.value = "";
            chatInput.disabled = false;
            sendBtn.disabled = false;
            chatInput.focus();

            // Cập nhật ngay tin nhắn cuối cùng trong danh sách user
            const activeUser = allUsersData.find(u => u.id == currentUserId);
            if (activeUser) {
                activeUser.last_message = message;
                activeUser.last_message_is_mine = true;
                activeUser.last_message_timestamp = Math.floor(Date.now() / 1000);
                activeUser.last_message_time = (new Date()).toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                // Re-sort để người này ở đầu
                allUsersData.sort((a, b) => (b.last_message_timestamp || 0) - (a.last_message_timestamp || 0));
                renderUserList();
            }

            loadMessages();
        })
        .catch(err => {
            console.error("Lỗi gửi tin:", err);
            chatInput.disabled = false;
            sendBtn.disabled = false;
        });
    }

    if (sendBtn) sendBtn.onclick = sendMessage;
    if (chatInput) {
        chatInput.onkeypress = (e) => { 
            if (e.key === 'Enter') sendMessage(); 
        };
    }

    // 5. Polling nền liên tục mỗi 3s (cập nhật thông báo và tự động sắp xếp khách mới nhắn lên đầu)
    pollAdminChat();
    setInterval(pollAdminChat, 3000);
    </script>
</body>
</html>
