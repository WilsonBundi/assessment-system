<?php
/**
 * Floating Chat Widget View
 * Include this in main layout to show chatbot everywhere
 */
?>

<div id="chat-widget" class="chat-widget">
    <!-- Chat Button -->
    <button id="chat-toggle" class="chat-toggle" title="Open AI Assistant">
        <span class="chat-icon">💬</span>
        <span class="chat-badge">!</span>
    </button>

    <!-- Chat Box -->
    <div id="chat-container" class="chat-container">
        <!-- Header -->
        <div class="chat-header">
            <div class="chat-title">
                🤖 TP Assessment Assistant
            </div>
            <div class="chat-actions">
                <button id="chat-clear" class="chat-action-btn" title="Clear history">
                    🗑️
                </button>
                <button id="chat-close" class="chat-action-btn" title="Close">
                    ✕
                </button>
            </div>
        </div>

        <!-- Messages -->
        <div id="chat-messages" class="chat-messages">
            <div class="chat-message assistant-message">
                <div class="message-content">
                    <p>👋 Hello! I'm your TP Assessment Assistant. I'm here to help with grading, competency standards, and assessment guidance. What can I help you with?</p>
                </div>
            </div>
        </div>

        <!-- Input -->
        <div class="chat-input-area">
            <form id="chat-form" onsubmit="return sendChatMessage(event);">
                <input 
                    type="text" 
                    id="chat-input" 
                    class="chat-input" 
                    placeholder="Ask me about grading, standards, or assessments..."
                    autocomplete="off">
                <button type="submit" class="chat-send-btn" title="Send">
                    ➤
                </button>
            </form>
        </div>

        <!-- Footer -->
        <div class="chat-footer">
            <small>💡 Tips: Ask about BE, AE, ME, EE | Competencies | Assessment help</small>
        </div>
    </div>
</div>

<style>
    /* Chat Widget Styling */
    .chat-widget {
        position: fixed;
        bottom: 20px;
        right: 20px;
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        z-index: 9999;
    }

    .chat-toggle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #5B9BD5 0%, #2E75B6 100%);
        color: white;
        border: 3px solid white;
        cursor: pointer;
        font-size: 28px;
        box-shadow: 0 4px 12px rgba(91, 155, 213, 0.4);
        transition: all 0.3s ease;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .chat-icon {
        font-size: 32px;
        line-height: 1;
    }

    .chat-toggle:hover {
        transform: scale(1.1);
        box-shadow: 0 6px 20px rgba(91, 155, 213, 0.6);
    }

    .chat-toggle:active {
        transform: scale(0.95);
    }

    .chat-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #ff4444;
        color: white;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: bold;
    }

    .chat-container {
        position: absolute;
        bottom: 80px;
        right: 0;
        width: 420px;
        height: 600px;
        background: white;
        border-radius: 12px;
        box-shadow: 0 5px 40px rgba(0, 0, 0, 0.16);
        display: none;
        flex-direction: column;
        opacity: 0;
        transform: translateY(10px);
        transition: all 0.3s ease;
    }

    .chat-container.open {
        display: flex;
        opacity: 1;
        transform: translateY(0);
    }

    .chat-header {
        background: linear-gradient(135deg, #5B9BD5 0%, #2E75B6 100%);
        color: white;
        padding: 16px;
        border-radius: 12px 12px 0 0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .chat-title {
        font-weight: 600;
        font-size: 16px;
        display: flex;
        gap: 8px;
        align-items: center;
    }

    .chat-actions {
        display: flex;
        gap: 8px;
    }

    .chat-action-btn {
        background: rgba(255, 255, 255, 0.2);
        border: none;
        color: white;
        width: 32px;
        height: 32px;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.2s;
    }

    .chat-action-btn:hover {
        background: rgba(255, 255, 255, 0.3);
    }

    .chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }

    .chat-message {
        display: flex;
        margin-bottom: 8px;
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .user-message {
        justify-content: flex-end;
    }

    .message-content {
        max-width: 75%;
        padding: 10px 14px;
        border-radius: 12px;
        line-height: 1.4;
        font-size: 14px;
    }

    .user-message .message-content {
        background: #5B9BD5;
        color: white;
        border-radius: 12px 4px 12px 12px;
    }

    .assistant-message .message-content {
        background: #f0f0f0;
        color: #333;
        border-radius: 4px 12px 12px 12px;
    }

    .message-content p {
        margin: 0;
        white-space: pre-wrap;
        word-wrap: break-word;
    }

    .message-content strong {
        color: #5B9BD5;
    }

    .assistant-message .message-content strong {
        color: #2E75B6;
    }

    .typing-indicator {
        display: flex;
        gap: 4px;
        align-items: center;
    }

    .typing-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        background: #999;
        animation: typing 1.4s infinite;
    }

    .typing-dot:nth-child(2) {
        animation-delay: 0.2s;
    }

    .typing-dot:nth-child(3) {
        animation-delay: 0.4s;
    }

    @keyframes typing {
        0%, 60%, 100% {
            opacity: 0.5;
            transform: translateY(0);
        }
        30% {
            opacity: 1;
            transform: translateY(-10px);
        }
    }

    .chat-input-area {
        padding: 12px;
        border-top: 1px solid #eee;
    }

    #chat-form {
        display: flex;
        gap: 8px;
    }

    .chat-input {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 6px;
        padding: 10px 12px;
        font-size: 14px;
        font-family: inherit;
        transition: border-color 0.2s;
    }

    .chat-input:focus {
        outline: none;
        border-color: #5B9BD5;
        box-shadow: 0 0 0 2px rgba(91, 155, 213, 0.1);
    }

    .chat-send-btn {
        background: #5B9BD5;
        color: white;
        border: none;
        padding: 10px 14px;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.2s;
    }

    .chat-send-btn:hover {
        background: #2E75B6;
    }

    .chat-footer {
        padding: 8px 12px;
        background: #f9f9f9;
        border-top: 1px solid #eee;
        text-align: center;
        font-size: 12px;
        color: #666;
        border-radius: 0 0 12px 12px;
    }

    /* Scrollbar Styling */
    .chat-messages::-webkit-scrollbar {
        width: 6px;
    }

    .chat-messages::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    .chat-messages::-webkit-scrollbar-thumb {
        background: #5B9BD5;
        border-radius: 3px;
    }

    .chat-messages::-webkit-scrollbar-thumb:hover {
        background: #2E75B6;
    }

    @media (max-width: 480px) {
        .chat-widget {
            bottom: 10px;
            right: 10px;
        }

        .chat-toggle {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }

        .chat-container {
            width: calc(100vw - 20px);
            height: 80vh;
            bottom: 60px;
            right: -10px;
        }

        .message-content {
            max-width: 85%;
        }
    }
</style>

<script>
    // Chat Widget JavaScript
    const chatToggle = document.getElementById('chat-toggle');
    const chatClose = document.getElementById('chat-close');
    const chatClear = document.getElementById('chat-clear');
    const chatContainer = document.getElementById('chat-container');
    const chatMessages = document.getElementById('chat-messages');
    const chatForm = document.getElementById('chat-form');
    const chatInput = document.getElementById('chat-input');

    // Toggle chat window
    chatToggle.addEventListener('click', () => {
        chatContainer.classList.toggle('open');
        if (chatContainer.classList.contains('open')) {
            chatInput.focus();
        }
    });

    // Close chat
    chatClose.addEventListener('click', () => {
        chatContainer.classList.remove('open');
    });

    // Clear history
    chatClear.addEventListener('click', () => {
        if (confirm('Clear chat history?')) {
            fetch('/chat/clear', { method: 'POST' });
            chatMessages.innerHTML = '';
            
            const welcomeMsg = document.createElement('div');
            welcomeMsg.className = 'chat-message assistant-message';
            welcomeMsg.innerHTML = '<div class="message-content"><p>👋 Chat cleared! How can I help you today?</p></div>';
            chatMessages.appendChild(welcomeMsg);
        }
    });

    // Send message
    async function sendChatMessage(event) {
        event.preventDefault();
        
        const message = chatInput.value.trim();
        if (!message) return;

        // Add user message
        addMessage(message, 'user');
        chatInput.value = '';

        // Show typing indicator
        showTypingIndicator();

        try {
            // Get assessment ID from URL if available
            const urlParams = new URLSearchParams(window.location.search);
            const assessmentId = urlParams.get('id') || null;

            const response = await fetch('/chat/send', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-CSRF-Token': document.querySelector('meta[name="csrf-token"]')?.content || '',
                },
                body: new URLSearchParams({
                    message: message,
                    assessment_id: assessmentId,
                })
            });

            const data = await response.json();
            removeTypingIndicator();

            if (data.success) {
                addMessage(data.message, 'assistant');
            } else {
                addMessage('Sorry, I encountered an error. Please try again.', 'assistant');
            }

            // Auto-scroll
            chatMessages.scrollTop = chatMessages.scrollHeight;
        } catch (error) {
            removeTypingIndicator();
            addMessage('Connection error. Please try again.', 'assistant');
        }
    }

    // Add message to chat
    function addMessage(text, role) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `chat-message ${role}-message`;
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        
        // Format text with markdown-like styling
        let formattedText = text
            .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
            .replace(/\n/g, '<br>');
        
        contentDiv.innerHTML = `<p>${formattedText}</p>`;
        messageDiv.appendChild(contentDiv);
        chatMessages.appendChild(messageDiv);
        
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Show typing indicator
    function showTypingIndicator() {
        const messageDiv = document.createElement('div');
        messageDiv.className = 'chat-message assistant-message';
        messageDiv.id = 'typing-indicator';
        messageDiv.innerHTML = '<div class="message-content typing-indicator"><div class="typing-dot"></div><div class="typing-dot"></div><div class="typing-dot"></div></div>';
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }

    // Remove typing indicator
    function removeTypingIndicator() {
        const indicator = document.getElementById('typing-indicator');
        if (indicator) indicator.remove();
    }

    // Form submission
    chatForm.addEventListener('submit', sendChatMessage);
</script>
