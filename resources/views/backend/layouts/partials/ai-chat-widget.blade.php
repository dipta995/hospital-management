@php
    $aiAdmin = Auth::guard('admin')->user();
    $canAiChat = $aiAdmin && $aiAdmin->can('ai.chat');
@endphp

@if($canAiChat)
<style>
    #ai-chat-fab {
        position: fixed;
        bottom: 24px;
        right: 24px;
        z-index: 1050;
        width: 52px;
        height: 52px;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        background: #0f172a;
        color: #fff;
        box-shadow: 0 8px 24px rgba(15, 23, 42, 0.2);
        font-size: 1.1rem;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    #ai-chat-fab:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 28px rgba(15, 23, 42, 0.28);
    }

    #ai-chat-panel {
        position: fixed;
        bottom: 88px;
        right: 24px;
        width: min(420px, calc(100vw - 32px));
        height: 560px;
        z-index: 1050;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(15, 23, 42, 0.15);
        display: none;
        flex-direction: column;
        overflow: hidden;
    }

    #ai-chat-panel.open { display: flex; }

    #ai-chat-panel .ai-chat-head {
        background: #0f172a;
        color: #fff;
        padding: 16px 18px;
        font-weight: 600;
        font-size: 0.95rem;
        letter-spacing: 0.01em;
    }

    #ai-chat-panel .ai-chat-head small {
        display: block;
        font-weight: 400;
        opacity: 0.7;
        font-size: 0.75rem;
        margin-top: 2px;
    }

    #ai-chat-suggestions {
        padding: 10px 14px 0;
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .ai-chat-chip {
        border: 1px solid #e2e8f0;
        background: #fff;
        border-radius: 999px;
        padding: 5px 10px;
        font-size: 0.72rem;
        color: #475569;
        cursor: pointer;
        max-width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .ai-chat-chip:hover { border-color: #94a3b8; background: #f1f5f9; }

    #ai-chat-messages {
        flex: 1;
        overflow-y: auto;
        padding: 16px;
        background: #f8fafc;
    }

    .ai-chat-bubble {
        max-width: 85%;
        padding: 10px 14px;
        border-radius: 12px;
        margin-bottom: 10px;
        font-size: 0.875rem;
        line-height: 1.5;
        white-space: pre-wrap;
    }

    .ai-chat-bubble.user {
        margin-left: auto;
        background: #0f172a;
        color: #f8fafc;
        border-bottom-right-radius: 4px;
    }

    .ai-chat-bubble.assistant {
        background: #fff;
        border: 1px solid #e2e8f0;
        color: #334155;
        border-bottom-left-radius: 4px;
    }

    .ai-chat-bubble.typing {
        color: #94a3b8;
        font-style: italic;
    }

    #ai-chat-form {
        display: flex;
        gap: 8px;
        padding: 12px 14px;
        border-top: 1px solid #e2e8f0;
        background: #fff;
    }

    #ai-chat-input {
        flex: 1;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 10px 12px;
        font-size: 0.875rem;
        background: #f8fafc;
    }

    #ai-chat-input:focus {
        outline: none;
        border-color: #94a3b8;
        background: #fff;
    }
</style>

<button type="button" id="ai-chat-fab" title="{{ t('ai.chat_support') }}">
    <i class="fas fa-comment-dots"></i>
</button>

<div id="ai-chat-panel">
    <div class="ai-chat-head d-flex justify-content-between align-items-start">
        <div>
            {{ t('ai.chat_title') }}
            <small>{{ t('ai.chat_subtitle') }}</small>
        </div>
        <button type="button" class="btn btn-sm btn-outline-light border-0" id="ai-chat-close" style="opacity:0.85;">
            <i class="fas fa-times"></i>
        </button>
    </div>
    <div id="ai-chat-suggestions"></div>
    <div id="ai-chat-messages"></div>
    <form id="ai-chat-form">
        @csrf
        <input type="text" id="ai-chat-input" placeholder="{{ t('ai.chat_placeholder') }}" maxlength="2000" autocomplete="off">
        <button type="submit" class="btn btn-dark btn-sm px-3"><i class="fas fa-arrow-up"></i></button>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var fab = document.getElementById('ai-chat-fab');
    var panel = document.getElementById('ai-chat-panel');
    var closeBtn = document.getElementById('ai-chat-close');
    var form = document.getElementById('ai-chat-form');
    var input = document.getElementById('ai-chat-input');
    var messages = document.getElementById('ai-chat-messages');
    var suggestionsEl = document.getElementById('ai-chat-suggestions');
    var sessionId = null;
    var chatUrl = @json(route('admin.ai.chat'));
    var historyUrl = @json(route('admin.ai.chat.history'));
    var suggestionsUrl = @json(route('admin.ai.chat.suggestions'));
    var csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    var welcomeShown = false;
    var typingEl = null;
    var typingLabel = @json(t('ai.typing'));

    var appendBubble = function (role, text, extraClass) {
        var div = document.createElement('div');
        div.className = 'ai-chat-bubble ' + role + (extraClass ? ' ' + extraClass : '');
        div.textContent = text;
        messages.appendChild(div);
        messages.scrollTop = messages.scrollHeight;
        return div;
    };

    var showTyping = function () {
        if (typingEl) return;
        typingEl = appendBubble('assistant', typingLabel, 'typing');
    };

    var hideTyping = function () {
        if (typingEl) {
            typingEl.remove();
            typingEl = null;
        }
    };

    var showWelcome = function () {
        if (welcomeShown || messages.children.length > 0) return;
        welcomeShown = true;
        appendBubble('assistant', @json(t('ai.chat_welcome')));
    };

    var loadSuggestions = function () {
        fetch(suggestionsUrl, {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (!suggestionsEl || !data || !data.suggestions) return;
                suggestionsEl.innerHTML = data.suggestions.map(function (s) {
                    return '<button type="button" class="ai-chat-chip" data-text="' + s.replace(/"/g, '&quot;') + '">' + s + '</button>';
                }).join('');
                suggestionsEl.querySelectorAll('.ai-chat-chip').forEach(function (chip) {
                    chip.addEventListener('click', function () {
                        input.value = chip.getAttribute('data-text') || '';
                        form.dispatchEvent(new Event('submit', { cancelable: true }));
                    });
                });
            });
    };

    var loadHistory = function () {
        fetch(historyUrl + (sessionId ? '?session_id=' + sessionId : ''), {
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
            credentials: 'same-origin',
        })
            .then(function (r) { return r.ok ? r.json() : null; })
            .then(function (data) {
                if (!data) { showWelcome(); return; }
                if (data.session_id) sessionId = data.session_id;
                messages.innerHTML = '';
                if (data.messages && data.messages.length) {
                    data.messages.forEach(function (m) {
                        appendBubble(m.role === 'user' ? 'user' : 'assistant', m.content);
                    });
                } else {
                    showWelcome();
                }
            })
            .catch(showWelcome);
    };

    var sendMessage = function (text) {
        text = (text || '').trim();
        if (!text) return;
        appendBubble('user', text);
        input.value = '';
        input.disabled = true;
        showTyping();

        fetch(chatUrl, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrf,
                'X-Requested-With': 'XMLHttpRequest',
            },
            credentials: 'same-origin',
            body: JSON.stringify({ message: text, session_id: sessionId }),
        })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                hideTyping();
                if (data.session_id) sessionId = data.session_id;
                if (data.reply) appendBubble('assistant', data.reply);
            })
            .catch(function () {
                hideTyping();
                appendBubble('assistant', @json(t('ai.request_failed')));
            })
            .finally(function () {
                input.disabled = false;
                input.focus();
            });
    };

    fab?.addEventListener('click', function () {
        panel.classList.toggle('open');
        if (panel.classList.contains('open')) {
            if (messages.children.length === 0) loadHistory();
            if (!suggestionsEl.children.length) loadSuggestions();
        }
    });

    closeBtn?.addEventListener('click', function () {
        panel.classList.remove('open');
    });

    form?.addEventListener('submit', function (e) {
        e.preventDefault();
        sendMessage(input.value);
    });
});
</script>
@endif
