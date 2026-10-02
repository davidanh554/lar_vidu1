<!-- ============================================================
     WIDGET CHATBOT AI GEMINI (TỰ ĐỘNG TRẢ LỜI 24/7) - DARK TECH EDITION
     ============================================================ -->
<div id="gemini-chat-widget" style="position: fixed; bottom: 88px; right: 24px; z-index: 9998;">
    <!-- Nút tròn Chatbot AI Gemini Dark Minimalist -->
    <button id="gemini-chat-toggle" class="btn rounded-circle shadow-lg d-flex align-items-center justify-content-center position-relative gemini-toggle-btn" 
            style="width: 54px; height: 54px; background: #151a26; border: 1.5px solid rgba(52, 211, 153, 0.4); color: #fff;" 
            title="Trợ lý AI Gemini (Tự động 24/7)">
        <i class="fa-solid fa-wand-magic-sparkles fs-5" style="color: #34d399;"></i>
        
        <!-- Huy hiệu AI nhỏ gọn -->
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success shadow-sm" 
              style="font-size: 0.62rem; padding: 2px 6px; border: 2px solid #0b0f19; font-weight: 700;">
            AI
        </span>
    </button>

    <!-- Khung chat popup Gemini AI Dark Theme -->
    <div id="gemini-chat-popup" class="card shadow-lg border-0" 
         style="display: none; width: 380px; max-width: calc(100vw - 32px); height: 540px; max-height: calc(100vh - 48px); border-radius: 18px; overflow: hidden; background: #0f172a; border: 1px solid rgba(255, 255, 255, 0.12) !important; box-shadow: 0 20px 45px rgba(0, 0, 0, 0.7) !important;">
        
        <!-- Header: Dark Tech -->
        <div class="card-header d-flex justify-content-between align-items-center p-3" 
             style="background: #1e293b; border-bottom: 1px solid rgba(255, 255, 255, 0.08);">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center" 
                     style="width: 36px; height: 36px; background: rgba(52, 211, 153, 0.15); border: 1px solid rgba(52, 211, 153, 0.3);">
                    <i class="fa-solid fa-robot" style="color: #34d399; font-size: 0.95rem;"></i>
                </div>
                <div>
                    <div class="fw-bold text-white small d-flex align-items-center gap-1" style="font-size: 0.92rem;">
                        <span>Trợ lý AI Gemini</span>
                        <span class="badge rounded-pill" style="font-size: 0.6rem; background: rgba(255,255,255,0.1); color: #cbd5e1;">Google AI</span>
                    </div>
                    <small class="d-flex align-items-center text-white-50" style="font-size: 0.72rem;">
                        <i class="fa-solid fa-circle text-success me-1" style="font-size: 0.45rem;"></i> Trực tuyến 24/7
                    </small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-1">
                <button id="gemini-chat-clear" class="btn btn-sm text-white-50 hover-text-white p-1" title="Làm mới cuộc trò chuyện">
                    <i class="fa-solid fa-rotate-right fs-6"></i>
                </button>
                <button id="gemini-chat-close" class="btn btn-sm text-white-50 hover-text-white p-1" title="Đóng khung chat">
                    <i class="fa-solid fa-xmark fs-5"></i>
                </button>
            </div>
        </div>

        <!-- Thanh câu hỏi gợi ý nhanh (Chips) - ĐÃ SỬA LỖI MẤT CHỮ VÀ CÂN ĐỐI PADDING -->
        <div class="gemini-chips-container">
            <button type="button" class="gemini-chip" data-prompt="Tư vấn cho tôi iPad dưới 10 triệu đáng mua nhất hiện nay">
                <span>💡 iPad dưới 10 triệu?</span>
            </button>
            <button type="button" class="gemini-chip" data-prompt="Tôi cần iPad vẽ vời và học tập thì nên chọn loại nào tốt nhất?">
                <span>🎨 Vẽ & Ghi chép</span>
            </button>
            <button type="button" class="gemini-chip" data-prompt="So sánh ưu nhược điểm giữa iPad Air 5 và iPad Pro M2">
                <span>⚡ So sánh Air 5 & Pro M2</span>
            </button>
            <button type="button" class="gemini-chip" data-prompt="Chính sách bảo hành và đổi trả của shop như thế nào?">
                <span>🛡️ Chính sách bảo hành</span>
            </button>
            <button type="button" class="gemini-chip" data-prompt="Shop giao hàng qua đơn vị nào và mất bao lâu?">
                <span>🚚 Thời gian giao hàng</span>
            </button>
        </div>

        <!-- Khung hiển thị tin nhắn (Dark Mode) -->
        <div id="gemini-chat-messages" class="card-body p-3 flex-grow-1" style="height: 320px; overflow-y: auto; background: #0b0f19;">
            <!-- Tin nhắn chào mừng mặc định -->
            <div class="d-flex mb-3 align-items-start gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                     style="width: 32px; height: 32px; background: rgba(52, 211, 153, 0.15); border: 1px solid rgba(52, 211, 153, 0.3);">
                    <i class="fa-solid fa-robot" style="color: #34d399; font-size: 0.85rem;"></i>
                </div>
                <div class="gemini-msg-bubble gemini-bot-bubble">
                    <p class="mb-2 fw-semibold text-emerald" style="font-size: 0.84rem; color: #34d399;">👋 Xin chào! Tôi là Trợ lý AI VUA TABLET.</p>
                    <p class="mb-2">Tôi được tích hợp trí tuệ nhân tạo <strong>Google Gemini</strong>, nắm rõ toàn bộ kho máy tính bảng & chính sách của cửa hàng.</p>
                    <p class="mb-0 small text-white-50">Hãy chọn câu hỏi gợi ý bên trên hoặc nhập nội dung bất kỳ để được tư vấn ngay lập tức nhé!</p>
                </div>
            </div>
        </div>

        <!-- Footer: Ô nhập câu hỏi (Dark Mode) -->
        <div class="card-footer p-2 border-top" id="gemini-chat-footer" style="background: #1e293b; border-color: rgba(255, 255, 255, 0.08) !important;">
            <div class="input-group">
                <input type="text" id="gemini-chat-input" class="form-control rounded-pill-start border-end-0 shadow-none" 
                       style="background: #0b0f19; color: #ffffff; border-color: rgba(255, 255, 255, 0.15); font-size: 0.85rem;" 
                       placeholder="Hỏi AI về iPad, giá bán, so sánh..." autocomplete="off">
                <button id="gemini-send-btn" class="btn rounded-pill-end px-3 text-white" 
                        style="background: #059669; border: 1px solid #059669;">
                    <i class="fa-solid fa-paper-plane"></i>
                </button>
            </div>
            <div class="text-center mt-1">
                <small class="text-white-50" style="font-size: 0.68rem;">
                    AI trả lời tự động. Cần người thật? Bấm <a href="javascript:void(0)" onclick="window.switchFromGeminiToSupport()" class="text-success fw-bold text-decoration-none" style="color: #34d399 !important;">Hỗ trợ khách hàng</a>
                </small>
            </div>
        </div>
    </div>
</div>

<!-- Thư viện Marked.js để hiển thị định dạng Markdown -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>

<style>
.gemini-toggle-btn {
    transition: all 0.2s ease;
}
.gemini-toggle-btn:hover {
    transform: translateY(-2px);
    background: #1e293b !important;
    border-color: #34d399 !important;
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.5) !important;
}
.gemini-chips-container {
    background: #141c2b;
    padding: 10px 12px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08);
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
    padding: 0 12px;
    font-size: 0.78rem;
    font-weight: 500;
    color: #cbd5e1;
    background: #1e293b;
    border: 1px solid rgba(255, 255, 255, 0.12);
    border-radius: 9999px;
    cursor: pointer;
    flex-shrink: 0;
    line-height: 1;
    transition: all 0.2s ease;
}
.gemini-chip:hover {
    background: #334155;
    color: #34d399;
    border-color: rgba(52, 211, 153, 0.5);
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
    background: #059669;
    color: #ffffff;
    margin-left: auto;
    border-bottom-right-radius: 4px;
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
}
.gemini-bot-bubble {
    background: #1e293b;
    color: #e2e8f0;
    border-bottom-left-radius: 4px;
    border: 1px solid rgba(255, 255, 255, 0.08);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15);
}
.gemini-bot-bubble p:last-child {
    margin-bottom: 0;
}
.gemini-bot-bubble ul {
    margin-bottom: 0.5rem;
    padding-left: 1.2rem;
}
.gemini-bot-bubble a {
    color: #34d399;
    font-weight: 600;
    text-decoration: underline;
}
.gemini-bot-bubble a:hover {
    color: #6ee7b7;
}
.gemini-typing {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 8px 12px;
    background: #1e293b;
    border: 1px solid rgba(255, 255, 255, 0.08);
    border-radius: 14px;
    font-size: 0.8rem;
    color: #94a3b8;
}
.gemini-typing-dot {
    width: 6px;
    height: 6px;
    background: #34d399;
    border-radius: 50%;
    animation: geminiTyping 1.4s infinite ease-in-out both;
}
.gemini-typing-dot:nth-child(1) { animation-delay: -0.32s; }
.gemini-typing-dot:nth-child(2) { animation-delay: -0.16s; }
@keyframes geminiTyping {
    0%, 80%, 100% { transform: scale(0); opacity: 0.5; }
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
                    <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                         style="width: 32px; height: 32px; background: rgba(52, 211, 153, 0.15); border: 1px solid rgba(52, 211, 153, 0.3);">
                        <i class="fa-solid fa-robot" style="color: #34d399; font-size: 0.85rem;"></i>
                    </div>
                    <div class="gemini-msg-bubble gemini-bot-bubble">
                        <p class="mb-2 fw-semibold text-emerald" style="font-size: 0.84rem; color: #34d399;">👋 Đã làm mới cuộc hội thoại!</p>
                        <p class="mb-0 small text-white-50">Bạn có thắc mắc gì về các dòng máy iPad hoặc chính sách của VUA TABLET? Hãy hỏi em nhé!</p>
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
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                 style="width: 30px; height: 30px; background: rgba(52, 211, 153, 0.15); border: 1px solid rgba(52, 211, 153, 0.3);">
                <i class="fa-solid fa-robot" style="color: #34d399; font-size: 0.8rem;"></i>
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
            <div class="rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" 
                 style="width: 30px; height: 30px; background: rgba(52, 211, 153, 0.15); border: 1px solid rgba(52, 211, 153, 0.3);">
                <i class="fa-solid fa-robot" style="color: #34d399; font-size: 0.8rem;"></i>
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
