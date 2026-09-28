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

    <!-- Admin Modern Chat Widget -->
    <div id="admin-chat-box" style="position: fixed; bottom: 24px; right: 24px; z-index: 9999;">
        <button id="chat-toggle" class="btn shadow-lg d-flex align-items-center gap-2">
            <i class="fa-solid fa-headset fs-5"></i>
            <span>Tư vấn khách hàng</span>
        </button>

        <div id="chat-popup" class="card shadow-lg border-0" style="display: none; width: 620px; height: 560px; border-radius: 20px; overflow: hidden;">
            <!-- Modal Header -->
            <div class="card-header bg-dark text-white p-3 d-flex justify-content-between align-items-center border-0" style="background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%) !important;">
                <div class="d-flex align-items-center gap-2">
                    <div class="rounded-circle bg-primary bg-opacity-25 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                        <i class="fa-solid fa-comments text-primary"></i>
                    </div>
                    <div>
                        <div class="fw-bold text-white small" style="font-size: 0.95rem;">Trung tâm Hỗ trợ Khách hàng</div>
                        <small class="text-white-50" style="font-size: 0.75rem;">Trò chuyện và tư vấn trực tiếp cho người dùng</small>
                    </div>
                </div>
                <button id="chat-close" class="btn btn-sm btn-close btn-close-white" aria-label="Close"></button>
            </div>

            <!-- Modal Body (2 Columns) -->
            <div class="row g-0 flex-grow-1" style="height: calc(100% - 66px);">
                <!-- User List Sidebar (Left Column) -->
                <div class="col-5 border-end bg-light d-flex flex-column" style="height: 100%;">
                    <div class="p-2 border-bottom bg-white">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass" style="font-size: 0.75rem;"></i></span>
                            <input type="text" id="user-search-input" class="form-control bg-light border-start-0" placeholder="Tìm theo tên, ID, email..." style="font-size: 0.8rem;">
                        </div>
                    </div>
                    <div id="user-list" class="flex-grow-1" style="overflow-y: auto;">
                        <div class="p-3 text-center text-muted small"><i class="fa-solid fa-spinner fa-spin me-1"></i> Đang tải danh sách...</div>
                    </div>
                </div>

                <!-- Chat & Message Pane (Right Column) -->
                <div class="col-7 d-flex flex-column bg-white" style="height: 100%;">
                    <!-- Selected user status header -->
                    <div id="active-user-header" class="p-2 px-3 border-bottom bg-light d-flex align-items-center justify-content-between" style="min-height: 48px;">
                        <div class="text-muted small">
                            <i class="fa-solid fa-circle-info me-1 text-primary"></i> Chọn khách hàng bên trái để nhắn tin
                        </div>
                    </div>

                    <!-- Messages list -->
                    <div id="chat-messages" class="flex-grow-1 p-3" style="overflow-y: auto; background: #f8fafc;">
                        <div class="text-center mt-5 text-muted small">
                            <i class="fa-regular fa-comment-dots fs-1 mb-2 d-block opacity-50"></i>
                            Chọn một khách hàng để xem lịch sử trò chuyện
                        </div>
                    </div>

                    <!-- Input area -->
                    <div class="p-2 border-top bg-white">
                        <div class="input-group">
                            <input type="text" id="chat-input" class="form-control rounded-pill-start border-end-0" placeholder="Nhập câu trả lời..." autocomplete="off" disabled>
                            <button id="send-btn" class="btn btn-modern-primary rounded-pill-end px-3" disabled>
                                <i class="fa-solid fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    let currentUserId = null;
    let currentUserName = "";
    let currentUserEmail = "";
    let allUsersData = [];

    const chatToggle = document.getElementById("chat-toggle");
    const chatPopup = document.getElementById("chat-popup");
    const chatClose = document.getElementById("chat-close");
    const userListEl = document.getElementById("user-list");
    const userSearchInput = document.getElementById("user-search-input");
    const chatMessages = document.getElementById("chat-messages");
    const chatInput = document.getElementById("chat-input");
    const sendBtn = document.getElementById("send-btn");
    const activeUserHeader = document.getElementById("active-user-header");

    function escapeHtml(text) {
        if (!text) return "";
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

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

    // 1. Tải danh sách User
    function loadUsers() {
        fetch("{{ route('admin.chat.users') }}")
            .then(res => res.json())
            .then(users => {
                allUsersData = users;
                renderUserList();
            })
            .catch(err => console.error("Lỗi tải user:", err));
    }

    // Render danh sách User kèm tìm kiếm
    function renderUserList() {
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

            html += `
                <div class="user-item p-2 px-3 border-bottom ${activeClass}" 
                     style="cursor: pointer;"
                     onclick="selectUser(${user.id}, '${escapeHtml(user.name)}', '${escapeHtml(user.email)}', this)">
                    <div class="d-flex align-items-center gap-2">
                        <div class="rounded-circle bg-primary bg-opacity-10 text-primary fw-bold d-flex align-items-center justify-content-center" style="width: 34px; height: 34px; font-size: 0.85rem; flex-shrink: 0;">
                            ${initial}
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <strong class="text-dark text-truncate small" style="max-width: 115px;">${escapeHtml(user.name)}</strong>
                                <span class="badge bg-primary-subtle text-primary font-monospace border border-primary-subtle" style="font-size: 0.68rem;">#${user.id}</span>
                            </div>
                            <div class="text-muted text-truncate" style="font-size: 0.72rem;">
                                <i class="fa-regular fa-envelope me-1 text-secondary" style="font-size: 0.68rem;"></i>${escapeHtml(user.email)}
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });

        userListEl.innerHTML = html;
    }

    if (userSearchInput) {
        userSearchInput.addEventListener("input", renderUserList);
    }

    // 2. Chọn User để trò chuyện
    window.selectUser = function(userId, userName, userEmail, element) {
        currentUserId = userId;
        currentUserName = userName;
        currentUserEmail = userEmail;

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
                <i class="fa-solid fa-circle text-success me-1" style="font-size: 0.45rem;"></i> Trực tuyến
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
    function loadMessages() {
        if (!currentUserId) return;

        fetch(`/admin/chat/messages/${currentUserId}`)
            .then(res => res.json())
            .then(messages => {
                let html = "";
                if (messages.length === 0) {
                    html = `
                        <div class="text-center text-muted mt-5 small">
                            <i class="fa-regular fa-comments fs-2 mb-2 d-block opacity-50"></i>
                            Chưa có tin nhắn nào với <strong>${escapeHtml(currentUserName)}</strong>.<br>
                            Hãy nhập tin nhắn bên dưới để bắt đầu tư vấn!
                        </div>
                    `;
                } else {
                    messages.forEach(msg => {
                        let isMe = (msg.sender_id == "{{ Auth::id() }}");
                        let senderTitle = isMe ? '<i class="fa-solid fa-shield-halved me-1"></i>Bạn (Admin)' : '<i class="fa-regular fa-user me-1"></i>' + escapeHtml(currentUserName);
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

                chatMessages.innerHTML = html;
                chatMessages.scrollTop = chatMessages.scrollHeight;
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

    // 5. Polling tự động làm mới mỗi 3s
    setInterval(() => {
        if (chatPopup.style.display === "block" && currentUserId) {
            loadMessages();
        }
    }, 3000);
    </script>
</body>
</html>
