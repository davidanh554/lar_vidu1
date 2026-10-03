<!-- ============================================================
     WIDGET CHATBOT AI (TỰ ĐỘNG TRẢ LỜI 24/7) - PHONGMOBILE LIGHT TECH
     ============================================================ -->
<div id="gemini-chat-widget" style="position: fixed; bottom: 88px; right: 24px; z-index: 9998;">
    <!-- Nút tròn Chatbot AI màu tím indigo với icon đũa phép y hệt ảnh mẫu -->
    <button id="gemini-chat-toggle" class="btn rounded-circle d-flex align-items-center justify-content-center position-relative gemini-toggle-btn" 
            style="width: 56px; height: 56px; background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important; border: 2px solid #ffffff !important; box-shadow: 0 10px 25px rgba(79, 70, 229, 0.45) !important;" 
            title="Trợ lý AI PhongMobile (Tự động 24/7)">
        <i class="fa-solid fa-wand-magic-sparkles text-white fs-5"></i>
    </button>

    <!-- Khung chat popup AI Minimalist White -->
    <div id="gemini-chat-popup" class="card border-0" 
         style="display: none; width: 380px; max-width: calc(100vw - 32px); height: 540px; max-height: calc(100vh - 48px); border-radius: 20px; overflow: hidden; background: #ffffff !important; border: 1px solid #e2e8f0 !important; box-shadow: 0 20px 50px rgba(0, 0, 0, 0.15) !important;">
        
        <!-- Header: Indigo Accent -->
        <div class="card-header d-flex justify-content-between align-items-center p-3 text-white" 
             style="background: #4f46e5 !important; border-bottom: 1px solid rgba(255, 255, 255, 0.1) !important;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                     style="width: 38px; height: 38px; background: #ffffff; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);">
                    <i class="fa-solid fa-wand-magic-sparkles" style="font-size: 1.05rem; color: #4f46e5;"></i>
                </div>
                <div>
                    <div class="fw-bold text-white small" style="font-size: 0.95rem;">
                        Trợ lý AI VUA TABLET
                    </div>
                    <small class="d-flex align-items-center text-white-50" style="font-size: 0.72rem;">
                        <span class="d-inline-block rounded-circle me-1" style="width: 7px; height: 7px; background: #10b981; box-shadow: 0 0 8px #10b981;"></span> Sẵn sàng tư vấn 24/7
                    </small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-1">
                @auth
                <button id="gemini-chat-clear" class="btn btn-sm text-white-50 hover-text-white p-1" title="Làm mới cuộc trò chuyện" style="line-height: 1;">
                    <i class="fa-solid fa-rotate-right fs-6 text-white"></i>
                </button>
                @endauth
                <button id="gemini-chat-close" class="btn btn-sm text-white-50 hover-text-white p-1" title="Đóng khung chat" style="font-size: 1.25rem; line-height: 1;">
                    <i class="fa-solid fa-xmark text-white"></i>
                </button>
            </div>
        </div>

        @auth
            <!-- Thanh câu hỏi gợi ý nhanh (Chips) -->
            <div class="gemini-chips-container">
                <button type="button" class="gemini-chip" data-prompt="Tư vấn cho tôi iPad dưới 10 triệu đáng mua nhất hiện nay">
                    <span>iPad dưới 10tr?</span>
                </button>
                <button type="button" class="gemini-chip" data-prompt="So sánh iPad Air 5 M1 và iPad Pro M2">
                    <span>So sánh Air 5 & Pro M2</span>
                </button>
                <button type="button" class="gemini-chip" data-prompt="Tư vấn tablet vẽ vời, học tập và ghi chép mượt mà kèm bút">
                    <span>Tablet học tập & vẽ</span>
                </button>
                <button type="button" class="gemini-chip" data-prompt="Chính sách bảo hành và đổi trả tablet của shop như thế nào?">
                    <span>Chính sách bảo hành</span>
                </button>
            </div>

            <!-- Khung hiển thị tin nhắn -->
            <div id="gemini-chat-messages" class="card-body p-3 flex-grow-1" style="height: 320px; overflow-y: auto; background: #f8fafc !important;">
                <!-- Tin nhắn chào mừng mặc định -->
                <div class="d-flex mb-3 align-items-start">
                    <div class="gemini-msg-bubble gemini-bot-bubble">
                        <p class="mb-2 fw-semibold" style="font-size: 0.88rem; color: #4f46e5;">Xin chào, {{ Auth::user()->name }}! Tôi là Trợ lý AI VUA TABLET.</p>
                        <p class="mb-2">Tôi nắm rõ toàn bộ thông tin giá bán & cấu hình các dòng máy tính bảng chính hãng.</p>
                        <p class="mb-0 small text-muted">Hãy chọn câu hỏi gợi ý bên trên hoặc nhập nội dung bất kỳ để được tư vấn ngay nhé!</p>
                    </div>
                </div>
            </div>

            <!-- Footer: Ô nhập câu hỏi -->
            <div class="card-footer p-2 border-top bg-white" id="gemini-chat-footer">
                <div class="input-group">
                    <input type="text" id="gemini-chat-input" class="form-control rounded-pill-start border-end-0 shadow-none" 
                           style="background: #f8fafc; color: #0f172a; border-color: #e2e8f0; font-size: 0.88rem;" 
                           placeholder="Hỏi AI về iPad, Galaxy Tab, giá bán, so sánh..." autocomplete="off">
                    <button id="gemini-send-btn" class="btn rounded-pill-end px-3 fw-bold" 
                            style="background: #4f46e5 !important; color: #ffffff !important; border: 1px solid #4f46e5 !important; font-size: 0.88rem;">
                        Gửi
                    </button>
                </div>
                <div class="text-center mt-1">
                    <small class="text-muted" style="font-size: 0.72rem;">
                        AI trả lời tự động • <a href="javascript:void(0)" onclick="window.switchFromGeminiToSupport()" class="fw-bold text-decoration-none" style="color: #4f46e5 !important;">Hỗ trợ khách hàng</a>
                    </small>
                </div>
            </div>
        @else
            <!-- Guest: Yêu cầu đăng nhập để nhận tư vấn AI -->
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center" style="height: 460px; background: #ffffff !important;">
                <div class="rounded-circle p-3 mb-3 d-flex align-items-center justify-content-center" 
                     style="width: 64px; height: 64px; background: #eef2ff; border: 1px solid #c7d2fe; box-shadow: 0 4px 15px rgba(79, 70, 229, 0.15);">
                    <i class="fa-solid fa-wand-magic-sparkles text-primary fs-3" style="color: #4f46e5 !important;"></i>
                </div>
                <h6 class="fw-bold text-dark mb-2" style="font-size: 1.05rem;">Đăng nhập để nhận tư vấn AI</h6>
                <p class="text-muted small mb-4 px-2" style="line-height: 1.6; font-size: 0.84rem;">
                    Vui lòng đăng nhập tài khoản để Trợ lý AI có thể tư vấn mẫu máy, so sánh cấu hình và đề xuất ưu đãi tốt nhất cho bạn!
                </p>
                <div class="d-flex gap-2 w-100 justify-content-center mb-3">
                    <a href="{{ route('login') }}" class="btn btn-sm rounded-pill px-4 fw-bold text-white" 
                       style="background: #4f46e5; border: 1px solid #4f46e5; box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25);">
                        Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-dark btn-sm rounded-pill px-4">
                        Đăng ký
                    </a>
                </div>
            </div>
        @endauth
    </div>
</div>

<!-- Thư viện Marked.js để hiển thị định dạng Markdown -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<style>
.gemini-toggle-btn {
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.gemini-toggle-btn:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 14px 30px rgba(79, 70, 229, 0.6) !important;
}
.gemini-chips-container {
    background: #ffffff;
    padding: 10px 12px;
    border-bottom: 1px solid #f1f5f9;
    display: flex;
    gap: 8px;
    overflow-x: auto;
    white-space: nowrap;
    scrollbar-width: none;
    -webkit-overflow-scrolling: touch;
}
.gemini-chips-container::-webkit-scrollbar {
    display: none;
}
.gemini-chip {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    height: 32px;
    padding: 0 13px;
    font-size: 0.78rem;
    font-weight: 600;
    color: #475569;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 9999px;
    cursor: pointer;
    flex-shrink: 0;
    line-height: 1;
    transition: all 0.2s ease;
}
.gemini-chip:hover {
    background: #eef2ff;
    color: #4f46e5;
    border-color: #c7d2fe;
    transform: translateY(-1px);
}
.gemini-msg-bubble {
    max-width: 85%;
    padding: 10px 14px;
    border-radius: 16px;
    font-size: 0.88rem;
    line-height: 1.5;
    word-break: break-word;
}
.gemini-user-bubble {
    background: #4f46e5 !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    box-shadow: 0 3px 10px rgba(79, 70, 229, 0.25) !important;
    margin-left: auto;
    border-bottom-right-radius: 4px;
}
.gemini-bot-bubble {
    background: #ffffff !important;
    color: #1e293b !important;
    border: 1px solid #e2e8f0 !important;
    border-bottom-left-radius: 4px;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04) !important;
}
.gemini-bot-bubble p:last-child {
    margin-bottom: 0;
}
.gemini-bot-bubble ul {
    margin-bottom: 0.5rem;
    padding-left: 1.2rem;
}
.gemini-bot-bubble a {
    color: #4f46e5;
    font-weight: 700;
    text-decoration: underline;
}
.gemini-bot-bubble a:hover {
    color: #4338ca;
}
.gemini-typing {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 8px 12px;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    font-size: 0.8rem;
    color: #64748b;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
}
.gemini-typing-dot {
    width: 6px;
    height: 6px;
    background: #4f46e5;
    border-radius: 50%;
    animation: geminiTyping 1.4s infinite ease-in-out both;
}
.gemini-typing-dot:nth-child(1) { animation-delay: -0.32s; }
.gemini-typing-dot:nth-child(2) { animation-delay: -0.16s; }
@keyframes geminiTyping {
    0%, 80%, 100% { transform: scale(0); opacity: 0.4; }
    40% { transform: scale(1); opacity: 1; }
}
.hover-text-white:hover {
    color: #ffffff !important;
}
</style>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const toggleBtn = document.getElementById("gemini-chat-toggle");
    const chatPopup = document.getElementById("gemini-chat-popup");
    const closeBtn = document.getElementById("gemini-chat-close");
    const clearBtn = document.getElementById("gemini-chat-clear");
    const sendBtn = document.getElementById("gemini-send-btn");
    const input = document.getElementById("gemini-chat-input");
    const chatBox = document.getElementById("gemini-chat-messages");
    const chips = document.querySelectorAll(".gemini-chip");

    if (!toggleBtn) return;

    // Lịch sử hội thoại
    let conversationHistory = [];

    // Cấu hình marked.js an toàn
    if (typeof marked !== "undefined") {
        marked.setOptions({
            breaks: true,
            gfm: true
        });
    }

    // Mở khung chat Gemini AI
    window.openGeminiChat = function() {
        if (window.closeSupportChat) {
            window.closeSupportChat();
        }

        chatPopup.style.display = "flex";
        chatPopup.style.flexDirection = "column";
        toggleBtn.classList.add("d-none");
        if (input) setTimeout(() => input.focus(), 200);
        if (chatBox) chatBox.scrollTop = chatBox.scrollHeight;
    };

    // Đóng khung chat Gemini AI
    window.closeGeminiChat = function() {
        chatPopup.style.display = "none";
        toggleBtn.classList.remove("d-none");
    };

    // Chuyển nhanh từ Gemini AI sang Hỗ trợ khách hàng
    window.switchFromGeminiToSupport = function() {
        window.closeGeminiChat();
        if (window.openSupportChat) {
            window.openSupportChat();
        }
    };

    if (toggleBtn) toggleBtn.onclick = window.openGeminiChat;
    if (closeBtn) closeBtn.onclick = window.closeGeminiChat;

    // Nếu chưa đăng nhập thì không khởi tạo logic gửi chat
    if (!chatBox || !sendBtn || !input) return;

    const defaultGreetingHtml = `
        <div class="d-flex mb-3 align-items-start">
            <div class="gemini-msg-bubble gemini-bot-bubble">
                <p class="mb-2 fw-semibold" style="font-size: 0.88rem; color: #00f59b;">Xin chào, {{ Auth::check() ? Auth::user()->name : 'Bạn' }}! Tôi là Trợ lý AI VUA TABLET.</p>
                <p class="mb-2">Tôi nắm rõ toàn bộ thông tin kho máy tính bảng & chính sách của cửa hàng.</p>
                <p class="mb-0 small text-white-50">Hãy chọn câu hỏi gợi ý bên trên hoặc nhập nội dung bất kỳ để được tư vấn ngay lập tức nhé!</p>
            </div>
        </div>
    `;

    // Tải lịch sử chat từ Database của tài khoản
    function loadChatHistory() {
        fetch("{{ route('ai.chat.history') }}", {
            headers: {
                "Accept": "application/json"
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.success && Array.isArray(data.messages) && data.messages.length > 0) {
                chatBox.innerHTML = "";
                conversationHistory = [];
                data.messages.forEach(msg => {
                    const role = msg.role;
                    const content = msg.content;
                    if (role === 'user') {
                        appendUserMessage(content, false);
                    } else {
                        appendBotMessage(content, false);
                    }
                    conversationHistory.push({ role: role, text: content });
                });
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        })
        .catch(err => {
            console.error("Lỗi khi tải lịch sử chat AI:", err);
        });
    }

    // Tải lịch sử ngay khi trang load
    loadChatHistory();

    // Xóa lịch sử chat trong CSDL của tài khoản
    if (clearBtn) {
        clearBtn.onclick = function() {
            if (confirm("Bạn có muốn xóa toàn bộ lịch sử trò chuyện với Trợ lý AI không?")) {
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                fetch("{{ route('ai.chat.clear') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": csrfToken,
                        "Accept": "application/json"
                    }
                })
                .then(res => res.json())
                .then(data => {
                    conversationHistory = [];
                    sessionStorage.removeItem("gemini_chat_history");
                    chatBox.innerHTML = `
                        <div class="d-flex mb-3 align-items-start">
                            <div class="gemini-msg-bubble gemini-bot-bubble">
                                <p class="mb-2 fw-semibold" style="font-size: 0.88rem; color: #00f59b;">Đã làm mới cuộc hội thoại!</p>
                                <p class="mb-0 small text-white-50">Lịch sử chat của bạn đã được xóa. Bạn cần em tư vấn thêm dòng máy tính bảng nào không ạ?</p>
                            </div>
                        </div>
                    `;
                })
                .catch(err => {
                    console.error("Lỗi khi xóa lịch sử:", err);
                });
            }
        };
    }

    // Thêm tin nhắn của User vào giao diện
    function appendUserMessage(text, save = true) {
        const div = document.createElement("div");
        div.className = "d-flex mb-2 justify-content-end";
        div.innerHTML = `
            <div class="gemini-msg-bubble gemini-user-bubble">
                ${escapeHtml(text)}
            </div>
        `;
        chatBox.appendChild(div);
        chatBox.scrollTop = chatBox.scrollHeight;

        if (save) {
            conversationHistory.push({ role: 'user', text: text });
            saveHistory();
        }
    }

    // Thêm tin nhắn của Bot vào giao diện
    function appendBotMessage(text, save = true) {
        const div = document.createElement("div");
        div.className = "d-flex mb-3 align-items-start";

        let htmlContent = "";
        if (typeof marked !== "undefined") {
            htmlContent = marked.parse(text);
        } else {
            htmlContent = escapeHtml(text).replace(/\n/g, '<br>');
        }

        div.innerHTML = `
            <div class="gemini-msg-bubble gemini-bot-bubble">
                ${htmlContent}
            </div>
        `;
        chatBox.appendChild(div);
        chatBox.scrollTop = chatBox.scrollHeight;

        if (save) {
            conversationHistory.push({ role: 'model', text: text });
            saveHistory();
        }
    }

    // Hiển thị hiệu ứng "Đang suy nghĩ..."
    function showTypingIndicator() {
        const id = "gemini-typing-loader";
        const div = document.createElement("div");
        div.id = id;
        div.className = "d-flex mb-3 align-items-start";
        div.innerHTML = `
            <div class="gemini-typing">
                <span class="gemini-typing-dot"></span>
                <span class="gemini-typing-dot"></span>
                <span class="gemini-typing-dot"></span>
                <span class="ms-1 small">Đang suy nghĩ...</span>
            </div>
        `;
        chatBox.appendChild(div);
        chatBox.scrollTop = chatBox.scrollHeight;
        return id;
    }

    function removeTypingIndicator(id) {
        const el = document.getElementById(id);
        if (el) el.remove();
    }

    function saveHistory() {
        try {
            sessionStorage.setItem("gemini_chat_history", JSON.stringify(conversationHistory.slice(-12)));
        } catch (e) {}
    }

    function escapeHtml(text) {
        if (!text) return "";
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    // Gửi tin nhắn tới API Gemini
    function sendGeminiMessage(messageText) {
        const text = messageText || input.value.trim();
        if (!text) return;

        appendUserMessage(text, true);
        if (input) input.value = "";

        if (input) input.disabled = true;
        if (sendBtn) sendBtn.disabled = true;

        const typingId = showTypingIndicator();
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

        fetch("{{ route('ai.chat.send') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken,
                "Accept": "application/json"
            },
            body: JSON.stringify({
                message: text,
                history: conversationHistory.slice(-8)
            })
        })
        .then(res => {
            if (res.status === 401) {
                window.location.href = "{{ route('login') }}";
                return;
            }
            return res.json();
        })
        .then(data => {
            if (!data) return;
            removeTypingIndicator(typingId);
            if (data.require_login && data.redirect) {
                window.location.href = data.redirect;
                return;
            }
            if (data && data.reply) {
                appendBotMessage(data.reply, true);
            } else {
                appendBotMessage("Dạ em chưa nhận được phản hồi từ hệ thống. Bạn vui lòng thử lại nhé!");
            }
        })
        .catch(err => {
            console.error("Gemini Chat Error:", err);
            removeTypingIndicator(typingId);
            appendBotMessage("Dạ kết nối tới trợ lý AI gặp gián đoạn tạm thời. Bạn vui lòng thử lại hoặc bấm **Hỗ trợ khách hàng** để chat trực tiếp với nhân viên nhé!");
        })
        .finally(() => {
            if (input) {
                input.disabled = false;
                input.focus();
            }
            if (sendBtn) sendBtn.disabled = false;
        });
    }

    if (sendBtn) {
        sendBtn.onclick = () => sendGeminiMessage();
    }

    if (input) {
        input.addEventListener("keypress", function (e) {
            if (e.key === "Enter") {
                sendGeminiMessage();
            }
        });
    }

    chips.forEach(chip => {
        chip.onclick = function() {
            const prompt = this.getAttribute("data-prompt");
            if (prompt) {
                sendGeminiMessage(prompt);
            }
        };
    });
});
</script>
