@if(Auth::guard('member')->check())
<!-- Floating Live Chat Widget for Member -->
<div id="member-chat-root" class="fixed bottom-6 right-6 z-[9999] font-sans antialiased text-slate-800">
    <!-- Floating Trigger Button -->
    <div class="relative">
        <button id="member-chat-toggle-btn" type="button"
                onclick="toggleMemberChat()"
                class="flex items-center gap-2.5 px-4 py-3 rounded-full bg-gradient-to-r from-sky-600 to-indigo-600 text-white shadow-xl hover:shadow-2xl hover:scale-105 active:scale-95 transition-all duration-200 border-2 border-white/40 focus:outline-none">
            <div class="relative flex items-center justify-center">
                <!-- Chat Icon -->
                <svg id="member-chat-icon-msg" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                </svg>
                <!-- Close Icon (when open) -->
                <svg id="member-chat-icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <!-- Online Status Dot on Button -->
                <span id="member-btn-online-dot" class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-slate-400 border-2 border-white"></span>
            </div>
            <span class="text-xs font-bold tracking-wide pr-1 hidden sm:inline-block">Tanya Pustakawan</span>
            <!-- Unread Badge -->
            <span id="member-chat-unread-badge" class="hidden absolute -top-2 -right-2 px-2 py-0.5 min-w-[20px] text-[11px] font-black text-white bg-rose-500 rounded-full border-2 border-white shadow animate-bounce text-center">0</span>
        </button>
    </div>

    <!-- Chat Window Container -->
    <div id="member-chat-window" class="hidden fixed sm:absolute bottom-20 right-0 sm:right-0 w-[95vw] sm:w-[400px] max-w-[440px] h-[550px] max-h-[85vh] bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col overflow-hidden transition-all duration-300 transform scale-95 opacity-0 origin-bottom-right">
        <!-- Header -->
        <div class="px-5 py-4 bg-gradient-to-r from-sky-600 via-brand-600 to-indigo-600 text-white flex items-center justify-between shadow-md">
            <div class="flex items-center gap-3">
                <div class="relative">
                    <div class="w-10 h-10 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center font-bold text-white shadow-inner">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span id="member-header-status-dot" class="absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-slate-400 border-2 border-white"></span>
                </div>
                <div>
                    <h4 class="font-bold text-sm tracking-tight leading-tight">Layanan Chat Pustaka</h4>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span id="member-header-status-pulse" class="inline-block w-2 h-2 rounded-full bg-slate-400"></span>
                        <span id="member-header-status-text" class="text-[11px] font-medium text-sky-100">Memeriksa status...</span>
                    </div>
                </div>
            </div>
            <button type="button" onclick="toggleMemberChat()" class="p-1.5 rounded-xl hover:bg-white/20 text-white/90 hover:text-white transition-colors" title="Tutup Chat">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Status Notice Banner -->
        <div id="member-chat-notice-banner" class="px-4 py-2 bg-slate-100 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 text-[11px] text-slate-600 dark:text-slate-300 flex items-center gap-2">
            <svg class="w-4 h-4 text-sky-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span id="member-chat-notice-text">Silakan tanyakan info skripsi, buku, atau bebas pustaka kepada petugas.</span>
        </div>

        <!-- Chat Messages Container -->
        <div id="member-chat-messages-container" class="flex-grow p-4 overflow-y-auto space-y-3 bg-slate-50/70 dark:bg-slate-950/60 text-xs">
            <div id="member-chat-loading" class="text-center py-8 text-slate-400">
                <div class="inline-block animate-spin rounded-full h-6 w-6 border-2 border-sky-500 border-t-transparent"></div>
                <p class="mt-2 text-xs">Memuat pesan...</p>
            </div>
        </div>

        <!-- Attachment Preview Bar (hidden by default) -->
        <div id="member-chat-file-preview-bar" class="hidden px-3 py-2 bg-sky-50 dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs">
            <div class="flex items-center gap-2 overflow-hidden">
                <div id="member-file-thumb-container" class="w-8 h-8 rounded-lg bg-sky-100 dark:bg-slate-700 flex items-center justify-center shrink-0 overflow-hidden text-sky-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                </div>
                <div class="truncate">
                    <p id="member-preview-filename" class="font-semibold text-slate-800 dark:text-slate-100 truncate text-[11px]">filename.jpg</p>
                    <p id="member-preview-filesize" class="text-[10px] text-slate-400">0 KB</p>
                </div>
            </div>
            <button type="button" onclick="cancelMemberAttachment()" class="p-1 rounded-full text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-950/40 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Chat Input Footer -->
        <form id="member-chat-form" onsubmit="sendMemberMessage(event)" class="p-3 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex items-end gap-2">
            <!-- Hidden File Input -->
            <input type="file" id="member-chat-file"
                   accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.txt,.zip"
                   onchange="handleMemberFileChange(this)"
                   class="hidden">

            <!-- Attachment Button (Clip) -->
            <button type="button" onclick="document.getElementById('member-chat-file').click()"
                    class="p-2.5 rounded-2xl text-slate-400 hover:text-sky-600 hover:bg-sky-50 dark:hover:bg-slate-800 transition-colors shrink-0"
                    title="Lampirkan Gambar atau File">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                </svg>
            </button>

            <!-- Textarea Input -->
            <div class="flex-grow relative">
                <textarea id="member-chat-input"
                          rows="1"
                          maxlength="2000"
                          placeholder="Ketik pesan untuk pustakawan..."
                          onkeydown="handleMemberChatKeydown(event)"
                          class="w-full resize-none max-h-24 px-3.5 py-2.5 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-sky-500 focus:outline-none transition-all"></textarea>
            </div>

            <!-- Send Button -->
            <button id="member-chat-send-btn" type="submit"
                    class="p-2.5 rounded-2xl bg-gradient-to-r from-sky-600 to-indigo-600 text-white hover:from-sky-500 hover:to-indigo-500 shadow-md hover:shadow-lg active:scale-95 disabled:opacity-50 disabled:pointer-events-none transition-all flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                </svg>
            </button>
        </form>
    </div>
</div>

<script>
    (function() {
        let isChatOpen = false;
        let lastMessageCount = 0;
        let pollingInterval = null;
        let statusPollingInterval = null;
        let selectedFile = null;

        const csrfToken = '{{ csrf_token() }}';
        const statusUrl = '{{ route("member.chat.status") }}';
        const messagesUrl = '{{ route("member.chat.messages") }}';
        const sendUrl = '{{ route("member.chat.send") }}';

        // Web Audio API Beep
        function playChimeSound() {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'sine';
                osc.frequency.setValueAtTime(587.33, ctx.currentTime); // D5
                osc.frequency.exponentialRampToValueAtTime(880, ctx.currentTime + 0.15); // A5

                gain.gain.setValueAtTime(0.08, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.35);

                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.35);
            } catch (e) {}
        }

        window.toggleMemberChat = function() {
            const win = document.getElementById('member-chat-window');
            const iconMsg = document.getElementById('member-chat-icon-msg');
            const iconClose = document.getElementById('member-chat-icon-close');
            const unreadBadge = document.getElementById('member-chat-unread-badge');

            isChatOpen = !isChatOpen;

            if (isChatOpen) {
                win.classList.remove('hidden');
                setTimeout(() => {
                    win.classList.remove('scale-95', 'opacity-0');
                    win.classList.add('scale-100', 'opacity-100');
                }, 10);
                iconMsg.classList.add('hidden');
                iconClose.classList.remove('hidden');
                unreadBadge.classList.add('hidden');
                unreadBadge.innerText = '0';

                loadMemberMessages();
                if (pollingInterval) clearInterval(pollingInterval);
                pollingInterval = setInterval(loadMemberMessages, 3000);

                setTimeout(() => {
                    const input = document.getElementById('member-chat-input');
                    if (input) input.focus();
                }, 150);
            } else {
                win.classList.remove('scale-100', 'opacity-100');
                win.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    win.classList.add('hidden');
                }, 200);
                iconMsg.classList.remove('hidden');
                iconClose.classList.add('hidden');

                if (pollingInterval) {
                    clearInterval(pollingInterval);
                    pollingInterval = null;
                }
            }
        };

        window.handleMemberFileChange = function(input) {
            if (!input.files || input.files.length === 0) return;
            const file = input.files[0];

            if (file.size > 10 * 1024 * 1024) {
                alert('Ukuran file maksimal adalah 10 MB.');
                input.value = '';
                return;
            }

            selectedFile = file;
            const previewBar = document.getElementById('member-chat-file-preview-bar');
            const filenameEl = document.getElementById('member-preview-filename');
            const filesizeEl = document.getElementById('member-preview-filesize');
            const thumbContainer = document.getElementById('member-file-thumb-container');

            filenameEl.innerText = file.name;
            filesizeEl.innerText = (file.size / 1024 > 1024)
                ? (file.size / (1024 * 1024)).toFixed(1) + ' MB'
                : Math.round(file.size / 1024) + ' KB';

            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    thumbContainer.innerHTML = `<img src="${e.target.result}" class="w-full h-full object-cover">`;
                };
                reader.readAsDataURL(file);
            } else {
                thumbContainer.innerHTML = `<svg class="w-4 h-4 text-sky-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>`;
            }

            previewBar.classList.remove('hidden');
        };

        window.cancelMemberAttachment = function() {
            selectedFile = null;
            const input = document.getElementById('member-chat-file');
            if (input) input.value = '';
            const previewBar = document.getElementById('member-chat-file-preview-bar');
            if (previewBar) previewBar.classList.add('hidden');
        };

        function updateOnlineStatusUI(isOnline) {
            const btnDot = document.getElementById('member-btn-online-dot');
            const headerDot = document.getElementById('member-header-status-dot');
            const headerPulse = document.getElementById('member-header-status-pulse');
            const headerText = document.getElementById('member-header-status-text');
            const noticeText = document.getElementById('member-chat-notice-text');

            if (isOnline) {
                btnDot.className = 'absolute -top-1 -right-1 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white ring-2 ring-emerald-400/50';
                headerDot.className = 'absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-emerald-400 border-2 border-white';
                headerPulse.className = 'inline-block w-2 h-2 rounded-full bg-emerald-300 animate-ping';
                headerText.innerText = 'Pustakawan Online (Siap Membantu)';
                headerText.className = 'text-[11px] font-semibold text-emerald-200';
                noticeText.innerText = 'Pustakawan sedang bertugas dan siap merespons pertanyaan Anda.';
            } else {
                btnDot.className = 'absolute -top-1 -right-1 w-3 h-3 rounded-full bg-slate-400 border-2 border-white';
                headerDot.className = 'absolute -bottom-0.5 -right-0.5 w-3.5 h-3.5 rounded-full bg-slate-400 border-2 border-white';
                headerPulse.className = 'inline-block w-2 h-2 rounded-full bg-slate-400';
                headerText.innerText = 'Pustakawan Sedang Offline';
                headerText.className = 'text-[11px] font-medium text-sky-200/80';
                noticeText.innerText = 'Pustakawan sedang offline. Anda tetap dapat mengirimkan pesan, pesan akan dibalas saat online.';
            }
        }

        function updateBlockedStateUI(isBlocked) {
            const noticeBanner = document.getElementById('member-chat-notice-banner');
            const noticeText = document.getElementById('member-chat-notice-text');
            const input = document.getElementById('member-chat-input');
            const sendBtn = document.getElementById('member-chat-send-btn');
            const clipBtn = document.querySelector('#member-chat-form button[title="Lampirkan Gambar atau File"]');

            if (isBlocked) {
                if (noticeBanner) {
                    noticeBanner.className = 'px-4 py-2.5 bg-rose-50 dark:bg-rose-950/80 border-b border-rose-200 dark:border-rose-800 text-[11px] text-rose-700 dark:text-rose-200 flex items-center gap-2 font-medium';
                }
                if (noticeText) {
                    noticeText.innerText = '⚠️ Akses chat Anda telah DIBLOKIR oleh petugas perpustakaan karena pelanggaran ketentuan. Anda tidak dapat mengirim pesan atau berkas.';
                }
                if (input) {
                    input.disabled = true;
                    input.placeholder = 'Akses chat Anda sedang diblokir oleh petugas.';
                }
                if (sendBtn) sendBtn.disabled = true;
                if (clipBtn) clipBtn.disabled = true;
                cancelMemberAttachment();
            } else {
                if (noticeBanner && noticeBanner.classList.contains('bg-rose-50')) {
                    noticeBanner.className = 'px-4 py-2 bg-slate-100 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 text-[11px] text-slate-600 dark:text-slate-300 flex items-center gap-2';
                }
                if (input && input.placeholder === 'Akses chat Anda sedang diblokir oleh petugas.') {
                    input.disabled = false;
                    input.placeholder = 'Ketik pesan untuk pustakawan...';
                }
                if (sendBtn && input && !input.disabled) sendBtn.disabled = false;
                if (clipBtn) clipBtn.disabled = false;
            }
        }

        function checkMemberStatus() {
            fetch(statusUrl, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                updateOnlineStatusUI(data.librarian_online);
                updateBlockedStateUI(Boolean(data.is_blocked));

                if (!isChatOpen && data.unread_count > 0) {
                    const badge = document.getElementById('member-chat-unread-badge');
                    badge.innerText = data.unread_count;
                    badge.classList.remove('hidden');
                }
            })
            .catch(() => {});
        }

        function renderAttachmentHtml(msg, isMember) {
            if (!msg.attachment_url) return '';

            if (msg.attachment_type === 'image') {
                return `
                    <div class="mt-1.5 mb-1 overflow-hidden rounded-xl border border-white/20">
                        <a href="${msg.attachment_url}" target="_blank" title="Klik untuk memperbesar foto">
                            <img src="${msg.attachment_url}" alt="${escapeHtml(msg.attachment_name || 'Foto')}"
                                 class="max-h-48 w-auto max-w-full rounded-xl object-contain hover:scale-105 transition-transform duration-200 bg-black/10">
                        </a>
                    </div>
                `;
            } else {
                const bgBox = isMember ? 'bg-white/15 hover:bg-white/25 text-white' : 'bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-650 text-slate-800 dark:text-slate-100';
                return `
                    <div class="mt-1.5 mb-1">
                        <a href="${msg.attachment_url}" target="_blank" download
                           class="flex items-center gap-2.5 p-2 rounded-xl ${bgBox} transition-all border border-black/5">
                            <div class="p-2 rounded-lg bg-black/10 shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <div class="min-w-0 flex-grow">
                                <p class="font-bold text-[11px] truncate">${escapeHtml(msg.attachment_name || 'Dokumen')}</p>
                                <p class="text-[9px] opacity-75">Klik untuk mengunduh</p>
                            </div>
                        </a>
                    </div>
                `;
            }
        }

        function renderMessages(messages) {
            const container = document.getElementById('member-chat-messages-container');
            const loading = document.getElementById('member-chat-loading');
            if (loading) loading.remove();

            if (messages.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12 px-4 text-slate-400 dark:text-slate-500">
                        <div class="w-12 h-12 mx-auto mb-3 rounded-full bg-sky-50 dark:bg-slate-800 flex items-center justify-center text-sky-500">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                        </div>
                        <p class="font-semibold text-slate-600 dark:text-slate-300">Belum ada percakapan</p>
                        <p class="text-[11px] mt-1 text-slate-400">Silakan kirim pesan atau lampirkan dokumen untuk memulai chat.</p>
                    </div>
                `;
                return;
            }

            let html = '';
            messages.forEach(msg => {
                const isMember = msg.sender_type === 'member';
                const hasAttachment = Boolean(msg.attachment_url);

                let showText = true;
                if (hasAttachment && (msg.message.startsWith('[Foto:') || msg.message.startsWith('[File:'))) {
                    showText = false;
                }

                if (isMember) {
                    // Bubble Member (Kanan)
                    html += `
                        <div class="flex flex-col items-end">
                            <div class="max-w-[85%] rounded-2xl rounded-br-none px-4 py-2.5 bg-gradient-to-r from-sky-600 to-indigo-600 text-white shadow-sm break-words leading-relaxed text-xs">
                                ${renderAttachmentHtml(msg, true)}
                                ${showText ? escapeHtml(msg.message).replace(/\\n/g, '<br>') : ''}
                            </div>
                            <div class="flex items-center gap-1 mt-1 text-[10px] text-slate-400">
                                <span>${msg.time}</span>
                                <span>•</span>
                                <span>${msg.is_read ? 'Dibaca' : 'Terkirim'}</span>
                            </div>
                        </div>
                    `;
                } else {
                    // Bubble Pustakawan / Admin (Kiri)
                    html += `
                        <div class="flex flex-col items-start">
                            <span class="text-[10px] font-bold text-sky-600 dark:text-sky-400 mb-0.5 ml-1">${escapeHtml(msg.sender_name)}</span>
                            <div class="max-w-[85%] rounded-2xl rounded-bl-none px-4 py-2.5 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 border border-slate-200/80 dark:border-slate-700 shadow-sm break-words leading-relaxed text-xs">
                                ${renderAttachmentHtml(msg, false)}
                                ${showText ? escapeHtml(msg.message).replace(/\\n/g, '<br>') : ''}
                            </div>
                            <div class="flex items-center gap-1 mt-1 text-[10px] text-slate-400 ml-1">
                                <span>${msg.time}</span>
                            </div>
                        </div>
                    `;
                }
            });

            container.innerHTML = html;
            container.scrollTop = container.scrollHeight;
        }

        function loadMemberMessages() {
            fetch(messagesUrl, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                updateOnlineStatusUI(data.librarian_online);
                updateBlockedStateUI(Boolean(data.is_blocked));

                if (data.messages && data.messages.length > lastMessageCount) {
                    const lastMsg = data.messages[data.messages.length - 1];
                    if (lastMessageCount > 0 && lastMsg.sender_type === 'admin') {
                        playChimeSound();
                    }
                    lastMessageCount = data.messages.length;
                    renderMessages(data.messages);
                } else if (lastMessageCount === 0 && data.messages) {
                    lastMessageCount = data.messages.length;
                    renderMessages(data.messages);
                }
            })
            .catch(() => {});
        }

        window.handleMemberChatKeydown = function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('member-chat-form').requestSubmit();
            }
        };

        window.sendMemberMessage = function(e) {
            e.preventDefault();
            const input = document.getElementById('member-chat-input');
            const btn = document.getElementById('member-chat-send-btn');
            const text = input.value.trim();

            if (!text && !selectedFile) return;

            btn.disabled = true;

            const formData = new FormData();
            if (text) formData.append('message', text);
            if (selectedFile) formData.append('attachment', selectedFile);

            fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                if (data.success) {
                    input.value = '';
                    cancelMemberAttachment();
                    loadMemberMessages();
                } else {
                    alert(data.error || 'Gagal mengirim pesan.');
                }
            })
            .catch(() => {
                btn.disabled = false;
                alert('Terjadi kendala koneksi ke server.');
            });
        };

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.innerText = text;
            return div.innerHTML;
        }

        // Initialize status polling
        checkMemberStatus();
        statusPollingInterval = setInterval(checkMemberStatus, 20000);
    })();
</script>
@endif
