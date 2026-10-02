<!-- ============================================================
     WIDGET CHATBOT AI GEMINI (TỰ ĐỘNG TRẢ LỜI 24/7)
     ============================================================ -->
<div id="gemini-chat-widget" style="position: fixed; bottom: 96px; right: 24px; z-index: 9998;">
    <!-- Nút tròn Chatbot AI Gemini nổi bật -->
    <button id="gemini-chat-toggle" class="btn rounded-circle shadow-lg d-flex align-items-center justify-content-center position-relative gemini-toggle-btn" 
            style="width: 60px; height: 60px; background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #db2777 100%); color: #fff; border: 2px solid rgba(255,255,255,0.4);" 
            title="Trợ lý AI Gemini (Tự động 24/7)">
        <i class="fa-solid fa-wand-magic-sparkles fs-4"></i>
        
        <!-- Huy hiệu AI phát sáng -->
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger shadow-sm" 
              style="font-size: 0.65rem; border: 2px solid #fff; padding: 0.25rem 0.45rem;">
            AI 24/7
        </span>
        
        <!-- Nhãn nhỏ bên dưới icon -->
        <span class="gemini-btn-label">AI Tư vấn</span>
    </button>

    <!-- Khung chat popup Gemini AI -->
    <div id="gemini-chat-popup" class="card shadow-lg border-0" 
         style="display: none; width: 380px; max-width: calc(100vw - 32px); height: 550px; max-height: calc(100vh - 48px); border-radius: 20px; overflow: hidden; box-shadow: 0 20px 45px rgba(124, 58, 237, 0.25) !important;">
        
        <!-- Header -->
        <div class="card-header text-white d-flex justify-content-between align-items-center p-3" 
             style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #db2777 100%); border-bottom: none;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle bg-white bg-opacity-20 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="fa-solid fa-robot fs-5 text-white"></i>
                </div>
                <div>
                    <div class="fw-bold text-white small d-flex align-items-center gap-1" style="font-size: 0.95rem;">
                        <span>Trợ lý AI Gemini</span>
                        <span class="badge bg-white text-dark rounded-pill px-2 py-0" style="font-size: 0.62rem; font-weight: 700;">Google AI</span>
                    </div>
                    <small class="d-flex align-items-center text-white-50" style="font-size: 0.72rem;">
                        <i class="fa-solid fa-circle text-info me-1" style="font-size: 0.45rem;"></i> Trả lời tự động tức thì
                    </small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-1">
                <button id="gemini-chat-clear" class="btn btn-sm text-white opacity-75 hover-opacity-100 p-1" title="Làm mới cuộc trò chuyện">
                    <i class="fa-solid fa-rotate-right fs-6"></i>
                </button>
                <button id="gemini-chat-close" class="btn btn-sm text-white opacity-75 hover-opacity-100 p-1" title="Đóng khung chat">
                    <i class="fa-solid fa-xmark fs-5"></i>
                </button>
            </div>
        </div>

        <!-- Thanh câu hỏi gợi ý nhanh (Chips) -->
        <div class="bg-light p-2 border-bottom d-flex gap-2 overflow-x-auto gemini-chips-container" style="white-space: nowrap; scrollbar-width: none;">
            <button class="btn btn-sm btn-white border rounded-pill shadow-xs py-1 px-2 text-dark small gemini-chip" data-prompt="Tư vấn cho tôi iPad dưới 10 triệu đáng mua nhất hiện nay">
                💡 iPad dưới 10 triệu?
            </button>
            <button class="btn btn-sm btn-white border rounded-pill shadow-xs py-1 px-2 text-dark small gemini-chip" data-prompt="Tôi cần iPad vẽ vời và học tập thì nên chọn loại nào tốt nhất?">
                🎨 Vẽ & Ghi chép
            </button>
            <button class="btn btn-sm btn-white border rounded-pill shadow-xs py-1 px-2 text-dark small gemini-chip" data-prompt="So sánh ưu nhược điểm giữa iPad Air 5 và iPad Pro M2">
                ⚡ So sánh Air 5 & Pro M2
            </button>
            <button class="btn btn-sm btn-white border rounded-pill shadow-xs py-1 px-2 text-dark small gemini-chip" data-prompt="Chính sách bảo hành và đổi trả của shop như thế nào?">
                🛡️ Chính sách bảo hành
            </button>
            <button class="btn btn-sm btn-white border rounded-pill shadow-xs py-1 px-2 text-dark small gemini-chip" data-prompt="Shop giao hàng qua đơn vị nào và mất bao lâu?">
                🚚 Thời gian giao hàng
            </button>
        </div>

        <!-- Khung hiển thị tin nhắn -->
        <div id="gemini-chat-messages" class="card-body p-3 flex-grow-1" style="height: 320px; overflow-y: auto; background: #f8fafc;">
            <!-- Tin nhắn chào mừng mặc định -->
            <div class="d-flex mb-3 align-items-start gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white" 
                     style="width: 32px; height: 32px; background: linear-gradient(135deg, #4f46e5, #7c3aed); font-size: 0.85rem;">
                    <i class="fa-solid fa-robot"></i>
                </div>
                <div class="gemini-msg-bubble gemini-bot-bubble">
                    <p class="mb-2 fw-semibold text-primary" style="font-size: 0.82rem;">👋 Xin chào! Tôi là Trợ lý AI VUA TABLET.</p>
                    <p class="mb-2">Tôi được tích hợp trí tuệ nhân tạo <strong>Google Gemini</strong>, am hiểu toàn bộ kho máy tính bảng & chính sách của cửa hàng.</p>
                    <p class="mb-0 small text-muted">Hãy chọn câu hỏi gợi ý bên trên hoặc nhập nội dung bất kỳ để được tư vấn ngay lập tức nhé!</p>
                </div>
            </div>
        </div>

        <!-- Footer: Ô nhập câu hỏi -->
        <div class="card-footer p-2 border-top bg-white" id="gemini-chat-footer">
            <div class="input-group">
                <input type="text" id="gemini-chat-input" class="form-control rounded-pill-start border-end-0 shadow-none" 
                       placeholder="Hỏi AI về iPad, giá bán, so sánh..." autocomplete="off">
                <button id="gemini-send-btn" class="btn rounded-pill-end px-3 text-white" 
                        style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
            <div class="text-center mt-1">
                <small class="text-muted" style="font-size: 0.68rem;">
                    AI trả lời tự động. Cần gặp nhân viên thật? Bấm <a href="javascript:void(0)" onclick="window.switchFromGeminiToSupport()" class="text-success fw-bold text-decoration-none">Hỗ trợ khách hàng</a>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Thư viện Marked.js để hiển thị định dạng Markdown tuyệt đẹp -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<style>
.gemini-toggle-btn {
    animation: geminiPulse 3s infinite;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
@keyframes geminiPulse {
    0% {
        box-shadow: 0 4px 15px rgba(124, 58, 237, 0.4);
    }
    50% {
        box-shadow: 0 4px 28px rgba(219, 39, 119, 0.7);
    }
    100% {
        box-shadow: 0 4px 15px rgba(124, 58, 237, 0.4);
    }
}
.gemini-toggle-btn:hover {
    transform: scale(1.08) translateY(-2px);
    box-shadow: 0 10px 30px rgba(124, 58, 237, 0.6) !important;
}
.gemini-btn-label {
    position: absolute;
    bottom: -18px;
    font-size: 0.65rem;
    font-weight: 700;
    color: #6366f1;
    background: rgba(255, 255, 255, 0.95);
    padding: 1px 6px;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    white-space: nowrap;
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
    background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
    color: #fff;
    margin-left: auto;
    border-bottom-right-radius: 4px;
    box-shadow: 0 2px 8px rgba(79, 70, 229, 0.25);
}
.gemini-bot-bubble {
    background: #ffffff;
    color: #1e293b;
    border-bottom-left-radius: 4px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
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
    font-weight: 600;
    text-decoration: underline;
}
.gemini-bot-bubble a:hover {
    color: #db2777;
}
.gemini-typing {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 8px 12px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    font-size: 0.8rem;
    color: #64748b;
}
.gemini-typing-dot {
    width: 6px;
    height: 6px;
    background: #7c3aed;
    border-radius: 50%;
    animation: geminiTyping 1.4s infinite ease-in-out both;
}
.gemini-typing-dot:nth-child(1) { animation-delay: -0.32s; }
.gemini-typing-dot:nth-child(2) { animation-delay: -0.16s; }
@keyframes geminiTyping {
    0%, 80%, 100% { transform: scale(0); opacity: 0.5; }
    40% { transform: scale(1); opacity: 1; }
}
.gemini-chips-container::-webkit-scrollbar {
    display: none;
}
.gemini-chip {
    transition: all 0.2s ease;
    font-size: 0.76rem !important;
}
.gemini-chip:hover {
    background: #f1f5f9;
    border-color: #7c3aed !important;
    color: #7c3aed !important;
    transform: translateY(-1px);
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

    // Lịch sử hội thoại trong phiên (sessionStorage)
    let conversationHistory = [];
    try {
        const saved = sessionStorage.getItem("gemini_chat_history");
        if (saved) {
            conversationHistory = JSON.parse(saved);
        }
    } catch (e) {}

    // Cấu hình marked.js an toàn
    if (typeof marked !== "undefined") {
        marked.setOptions({
            breaks: true,
            gfm: true
        });
    }

    // Hiển thị lại các tin nhắn đã lưu trong phiên
    function renderSavedHistory() {
        if (conversationHistory.length > 0) {
            conversationHistory.forEach(item => {
                if (item.role === 'user') {
                    appendUserMessage(item.text, false);
                } else if (item.role === 'model') {
                    appendBotMessage(item.text, false);
                }
            });
        }
    }
    renderSavedHistory();

    // Mở khung chat Gemini AI
    window.openGeminiChat = function() {
        // Đóng chat Hỗ trợ khách hàng nếu đang mở
        if (window.closeSupportChat) {
            window.closeSupportChat();
        }

        chatPopup.style.display = "flex";
        chatPopup.style.flexDirection = "column";
        toggleBtn.classList.add("d-none");
        if (input) setTimeout(() => input.focus(), 200);
        chatBox.scrollTop = chatBox.scrollHeight;
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

    toggleBtn.onclick = window.openGeminiChat;
    closeBtn.onclick = window.closeGeminiChat;

    // Xóa lịch sử chat
    clearBtn.onclick = function() {
        if (confirm("Bạn có muốn làm mới cuộc trò chuyện với Trợ lý AI không?")) {
            conversationHistory = [];
            sessionStorage.removeItem("gemini_chat_history");
            chatBox.innerHTML = `
                <div class="d-flex mb-3 align-items-start gap-2">
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white" 
                         style="width: 32px; height: 32px; background: linear-gradient(135deg, #4f46e5, #7c3aed); font-size: 0.85rem;">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <div class="gemini-msg-bubble gemini-bot-bubble">
                        <p class="mb-2 fw-semibold text-primary" style="font-size: 0.82rem;">👋 Đã làm mới cuộc hội thoại!</p>
                        <p class="mb-0 small text-muted">Bạn có thắc mắc gì về các dòng máy iPad hoặc chính sách của VUA TABLET? Hãy hỏi em nhé!</p>
                    </div>
                </div>
            `;
        }
    };

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
        div.className = "d-flex mb-3 align-items-start gap-2";

        let htmlContent = "";
        if (typeof marked !== "undefined") {
            htmlContent = marked.parse(text);
        } else {
            htmlContent = escapeHtml(text).replace(/\n/g, '<br>');
        }

        div.innerHTML = `
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white" 
                 style="width: 30px; height: 30px; background: linear-gradient(135deg, #4f46e5, #7c3aed); font-size: 0.8rem;">
                <i class="fa-solid fa-robot"></i>
            </div>
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
        div.className = "d-flex mb-3 align-items-start gap-2";
        div.innerHTML = `
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0 text-white" 
                 style="width: 30px; height: 30px; background: linear-gradient(135deg, #4f46e5, #7c3aed); font-size: 0.8rem;">
                <i class="fa-solid fa-robot"></i>
            </div>
            <div class="gemini-typing">
                <span class="gemini-typing-dot"></span>
                <span class="gemini-typing-dot"></span>
                <span class="gemini-typing-dot"></span>
                <span class="ms-1 small">Gemini đang suy nghĩ...</span>
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
        .then(res => res.json())
        .then(data => {
            removeTypingIndicator(typingId);
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

    // Xử lý khi click vào các chip câu hỏi gợi ý nhanh
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
