<!-- ============================================================
     WIDGET CHATBOT AI (TỰ ĐỘNG TRẢ LỜI 24/7) - DARK TECH EDITION
     ============================================================ -->
<div id="gemini-chat-widget" style="position: fixed; bottom: 88px; right: 24px; z-index: 9998;">
    <!-- Nút tròn Chatbot AI Liquid Glass iOS 27 -->
    <button id="gemini-chat-toggle" class="btn rounded-circle d-flex align-items-center justify-content-center position-relative gemini-toggle-btn" 
            style="width: 56px; height: 56px; background: linear-gradient(135deg, rgba(255, 255, 255, 0.18) 0%, rgba(15, 23, 42, 0.75) 100%) !important; backdrop-filter: blur(28px) saturate(210%) !important; -webkit-backdrop-filter: blur(28px) saturate(210%) !important; border: 1.5px solid rgba(0, 245, 155, 0.5) !important; border-top: 1.5px solid rgba(255, 255, 255, 0.8) !important; color: #00f59b !important; font-weight: 800; font-size: 1.18rem; letter-spacing: 0.5px; box-shadow: inset 0 2px 4px rgba(255, 255, 255, 0.45), 0 14px 35px rgba(0, 0, 0, 0.6) !important;" 
            title="Trợ lý AI (Tự động 24/7)">
        AI
    </button>

    <!-- Khung chat popup AI Liquid Glass iOS 27 -->
    <div id="gemini-chat-popup" class="card border-0" 
         style="display: none; width: 380px; max-width: calc(100vw - 32px); height: 540px; max-height: calc(100vh - 48px); border-radius: 20px; overflow: hidden; background: rgba(15, 23, 42, 0.78) !important; backdrop-filter: blur(35px) saturate(210%) contrast(106%) !important; -webkit-backdrop-filter: blur(35px) saturate(210%) contrast(106%) !important; border: 1px solid rgba(255, 255, 255, 0.18) !important; border-top: 1.5px solid rgba(255, 255, 255, 0.55) !important; box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.4), inset 0 -1px 2px rgba(0, 0, 0, 0.35), 0 25px 60px rgba(0, 0, 0, 0.8) !important;">
        
        <!-- Header: Liquid Glass -->
        <div class="card-header d-flex justify-content-between align-items-center p-3" 
             style="background: rgba(30, 41, 59, 0.55) !important; backdrop-filter: blur(20px) !important; -webkit-backdrop-filter: blur(20px) !important; border-bottom: 1px solid rgba(255, 255, 255, 0.12) !important;">
            <div class="d-flex align-items-center gap-2">
                <div>
                    <div class="fw-bold text-white small" style="font-size: 0.95rem;">
                        Trợ lý AI
                    </div>
                    <small class="d-flex align-items-center text-white-50" style="font-size: 0.72rem;">
                        <span class="d-inline-block rounded-circle me-1" style="width: 7px; height: 7px; background: #00f59b; box-shadow: 0 0 8px #00f59b;"></span> Trực tuyến 24/7
                    </small>
                </div>
            </div>
            <div class="d-flex align-items-center gap-1">
                @auth
                <button id="gemini-chat-clear" class="btn btn-sm text-white-50 hover-text-white p-1" title="Làm mới cuộc trò chuyện" style="line-height: 1;">
                    <i class="fa-solid fa-rotate-right fs-6"></i>
                </button>
                @endauth
                <button id="gemini-chat-close" class="btn btn-sm text-white-50 hover-text-white p-1" title="Đóng khung chat" style="font-size: 1.25rem; line-height: 1;">
                    &times;
                </button>
            </div>
        </div>

        @auth
            <!-- Thanh câu hỏi gợi ý nhanh (Chips) - Liquid Glass Pills -->
            <div class="gemini-chips-container">
                <button type="button" class="gemini-chip" data-prompt="Tư vấn cho tôi iPad dưới 10 triệu đáng mua nhất hiện nay">
                    <span>iPad dưới 10 triệu?</span>
                </button>
                <button type="button" class="gemini-chip" data-prompt="Tôi cần iPad vẽ vời và học tập thì nên chọn loại nào tốt nhất?">
                    <span>Vẽ & Ghi chép</span>
                </button>
                <button type="button" class="gemini-chip" data-prompt="So sánh ưu nhược điểm giữa iPad Air 5 và iPad Pro M2">
                    <span>So sánh Air 5 & Pro M2</span>
                </button>
                <button type="button" class="gemini-chip" data-prompt="Chính sách bảo hành và đổi trả của shop như thế nào?">
                    <span>Chính sách bảo hành</span>
                </button>
                <button type="button" class="gemini-chip" data-prompt="Shop giao hàng qua đơn vị nào và mất bao lâu?">
                    <span>Thời gian giao hàng</span>
                </button>
            </div>

            <!-- Khung hiển thị tin nhắn (Liquid Glass Background) -->
            <div id="gemini-chat-messages" class="card-body p-3 flex-grow-1" style="height: 320px; overflow-y: auto; background: rgba(11, 15, 25, 0.6) !important;">
                <!-- Tin nhắn chào mừng mặc định -->
                <div class="d-flex mb-3 align-items-start">
                    <div class="gemini-msg-bubble gemini-bot-bubble">
                        <p class="mb-2 fw-semibold" style="font-size: 0.88rem; color: #00f59b;">Xin chào, {{ Auth::user()->name }}! Tôi là Trợ lý AI VUA TABLET.</p>
                        <p class="mb-2">Tôi nắm rõ toàn bộ thông tin kho máy tính bảng & chính sách của cửa hàng.</p>
                        <p class="mb-0 small text-white-50">Hãy chọn câu hỏi gợi ý bên trên hoặc nhập nội dung bất kỳ để được tư vấn ngay lập tức nhé!</p>
                    </div>
                </div>
            </div>

            <!-- Footer: Ô nhập câu hỏi (Liquid Glass) -->
            <div class="card-footer p-2 border-top" id="gemini-chat-footer" style="background: rgba(30, 41, 59, 0.6) !important; backdrop-filter: blur(20px) !important; -webkit-backdrop-filter: blur(20px) !important; border-color: rgba(255, 255, 255, 0.1) !important;">
                <div class="input-group">
                    <input type="text" id="gemini-chat-input" class="form-control rounded-pill-start border-end-0 shadow-none" 
                           style="background: rgba(11, 15, 25, 0.7); color: #ffffff; border-color: rgba(255, 255, 255, 0.18); font-size: 0.85rem;" 
                           placeholder="Hỏi AI về iPad, giá bán, so sánh..." autocomplete="off">
                    <button id="gemini-send-btn" class="btn rounded-pill-end px-3 fw-bold" 
                            style="background: linear-gradient(135deg, #00f59b 0%, #00d674 100%) !important; color: #022c16 !important; border: 1px solid rgba(255, 255, 255, 0.4) !important; border-top: 1.5px solid rgba(255, 255, 255, 0.7) !important; box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.6) !important; font-size: 0.85rem;">
                        Gửi
                    </button>
                </div>
                <div class="text-center mt-1">
                    <small class="text-white-50" style="font-size: 0.68rem;">
                        AI trả lời tự động. Cần người thật? Bấm <a href="javascript:void(0)" onclick="window.switchFromGeminiToSupport()" class="fw-bold text-decoration-none" style="color: #00f59b !important;">Hỗ trợ khách hàng</a>
                    </small>
                </div>
            </div>
        @else
            <!-- Guest: Yêu cầu đăng nhập để nhận tư vấn AI -->
            <div class="card-body p-4 text-center d-flex flex-column justify-content-center align-items-center" style="height: 460px; background: rgba(11, 15, 25, 0.6) !important;">
                <div class="rounded-circle p-3 mb-3 d-flex align-items-center justify-content-center" 
                     style="width: 64px; height: 64px; background: rgba(0, 245, 155, 0.15); border: 1.5px solid rgba(0, 245, 155, 0.4); box-shadow: 0 0 25px rgba(0, 245, 155, 0.25);">
                    <span class="fw-bold" style="color: #00f59b; font-size: 1.4rem; letter-spacing: 0.5px;">AI</span>
                </div>
                <h6 class="fw-bold text-white mb-2" style="font-size: 1.05rem;">Đăng nhập để nhận tư vấn AI</h6>
                <p class="text-white-50 small mb-4 px-2" style="line-height: 1.6; font-size: 0.84rem;">
                    Vui lòng đăng nhập tài khoản để Trợ lý AI có thể tư vấn mẫu máy, so sánh cấu hình và đề xuất ưu đãi phù hợp nhất dành riêng cho bạn!
                </p>
                <div class="d-flex gap-2 w-100 justify-content-center mb-3">
                    <a href="{{ route('login') }}" class="btn btn-sm rounded-pill px-4 fw-bold" 
                       style="background: linear-gradient(135deg, #00f59b 0%, #00d674 100%); color: #022c16; border: 1px solid rgba(255, 255, 255, 0.4); box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.5), 0 4px 15px rgba(0, 245, 155, 0.35);">
                        Đăng nhập
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-sm rounded-pill px-4" 
                       style="border-color: rgba(255, 255, 255, 0.25); background: rgba(255, 255, 255, 0.05);">
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
    transition: all 0.28s cubic-bezier(0.16, 1, 0.3, 1);
}
.gemini-toggle-btn:hover {
    transform: translateY(-2px) scale(1.05);
    background: linear-gradient(135deg, rgba(0, 245, 155, 0.25) 0%, rgba(15, 23, 42, 0.85) 100%) !important;
    border-color: #00f59b !important;
    box-shadow: 0 0 25px rgba(0, 245, 155, 0.85), inset 0 2px 4px rgba(255, 255, 255, 0.6) !important;
}
.gemini-chips-container {
    background: rgba(15, 23, 42, 0.45);
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
    padding: 0 13px;
    font-size: 0.78rem;
    font-weight: 600;
    color: #f1f5f9;
    background: rgba(255, 255, 255, 0.07);
    backdrop-filter: blur(14px);
    -webkit-backdrop-filter: blur(14px);
    border: 1px solid rgba(255, 255, 255, 0.16);
    border-top: 1px solid rgba(255, 255, 255, 0.4);
    box-shadow: inset 0 1px 0 rgba(255, 255, 255, 0.22);
    border-radius: 9999px;
    cursor: pointer;
    flex-shrink: 0;
    line-height: 1;
    transition: all 0.25s cubic-bezier(0.16, 1, 0.3, 1);
}
.gemini-chip:hover {
    background: rgba(0, 245, 155, 0.18);
    color: #00f59b;
    border-color: #00f59b;
    box-shadow: 0 0 16px rgba(0, 245, 155, 0.6), inset 0 1px 0 rgba(255, 255, 255, 0.4);
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
    background: linear-gradient(135deg, #00f59b 0%, #00d674 100%) !important;
    color: #022c16 !important;
    font-weight: 700 !important;
    border: 1px solid rgba(255, 255, 255, 0.4) !important;
    border-top: 1.5px solid rgba(255, 255, 255, 0.7) !important;
    box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.6), 0 4px 15px rgba(0, 0, 0, 0.35) !important;
    margin-left: auto;
    border-bottom-right-radius: 4px;
}
.gemini-bot-bubble {
    background: rgba(30, 41, 59, 0.65) !important;
    backdrop-filter: blur(18px) !important;
    -webkit-backdrop-filter: blur(18px) !important;
    color: #f1f5f9;
    border-bottom-left-radius: 4px;
    border: 1px solid rgba(255, 255, 255, 0.14) !important;
    border-top: 1.5px solid rgba(255, 255, 255, 0.35) !important;
    box-shadow: inset 0 1px 1px rgba(255, 255, 255, 0.25), 0 4px 15px rgba(0, 0, 0, 0.25) !important;
}
.gemini-bot-bubble p:last-child {
    margin-bottom: 0;
}
.gemini-bot-bubble ul {
    margin-bottom: 0.5rem;
    padding-left: 1.2rem;
}
.gemini-bot-bubble a {
    color: #00f59b;
    font-weight: 700;
    text-decoration: underline;
}
.gemini-bot-bubble a:hover {
    color: #26ffb2;
}
.gemini-typing {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    padding: 8px 12px;
    background: rgba(30, 41, 59, 0.65);
    backdrop-filter: blur(18px);
    border: 1px solid rgba(255, 255, 255, 0.14);
    border-top: 1.5px solid rgba(255, 255, 255, 0.35);
    border-radius: 14px;
    font-size: 0.8rem;
    color: #cbd5e1;
}
.gemini-typing-dot {
    width: 6px;
    height: 6px;
    background: #00f59b;
    box-shadow: 0 0 6px #00f59b;
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

    // Xóa lịch sử chat
    if (clearBtn) {
        clearBtn.onclick = function() {
            if (confirm("Bạn có muốn làm mới cuộc trò chuyện với Trợ lý AI không?")) {
                conversationHistory = [];
                sessionStorage.removeItem("gemini_chat_history");
                chatBox.innerHTML = `
                    <div class="d-flex mb-3 align-items-start">
                        <div class="gemini-msg-bubble gemini-bot-bubble">
                            <p class="mb-2 fw-semibold" style="font-size: 0.88rem; color: #00f59b;">Đã làm mới cuộc hội thoại!</p>
                            <p class="mb-0 small text-white-50">Bạn có thắc mắc gì về các dòng máy iPad hoặc chính sách của VUA TABLET? Hãy nhắn tin để em tư vấn nhé!</p>
                        </div>
                    </div>
                `;
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
