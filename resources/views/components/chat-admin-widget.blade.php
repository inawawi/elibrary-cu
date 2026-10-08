@if(Auth::guard('web')->check())
<!-- Floating Live Chat Widget for Admin / Pustakawan -->
<div id="admin-chat-root" class="fixed bottom-6 right-6 z-[9999] font-sans antialiased text-slate-800">
    <!-- Floating Trigger Button -->
    <div class="relative">
        <button id="admin-chat-toggle-btn" type="button"
                onclick="toggleAdminChat()"
                class="flex items-center gap-2.5 px-4 py-3 rounded-full bg-gradient-to-r from-emerald-600 to-teal-700 text-white shadow-xl hover:shadow-2xl hover:scale-105 active:scale-95 transition-all duration-200 border-2 border-white/40 focus:outline-none">
            <div class="relative flex items-center justify-center">
                <!-- Chat Icon -->
                <svg id="admin-chat-icon-msg" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z" />
                </svg>
                <!-- Close Icon (when open) -->
                <svg id="admin-chat-icon-close" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                <!-- Online Pulse Dot -->
                <span class="absolute -top-1 -right-1 w-3 h-3 rounded-full bg-emerald-300 border-2 border-white animate-pulse"></span>
            </div>
            <span class="text-xs font-bold tracking-wide pr-1 hidden sm:inline-block">Layanan Chat Member</span>
            <!-- Unread Badge Counter -->
            <span id="admin-chat-unread-badge" class="hidden absolute -top-2 -right-2 px-2 py-0.5 min-w-[22px] text-[11px] font-black text-white bg-rose-500 rounded-full border-2 border-white shadow animate-bounce text-center">0</span>
        </button>
    </div>

    <!-- Admin Chat Window Container -->
    <div id="admin-chat-window" class="hidden fixed sm:absolute bottom-20 right-0 sm:right-0 w-[95vw] sm:w-[720px] max-w-[760px] h-[580px] max-h-[85vh] bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-slate-800 flex flex-col overflow-hidden transition-all duration-300 transform scale-95 opacity-0 origin-bottom-right">
        <!-- Main Top Bar -->
        <div class="px-5 py-3.5 bg-gradient-to-r from-emerald-700 via-teal-700 to-slate-900 text-white flex items-center justify-between shadow-md">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-2xl bg-white/20 backdrop-blur-md flex items-center justify-center font-bold text-white shadow-inner">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                </div>
                <div>
                    <h4 class="font-bold text-sm tracking-tight leading-tight flex items-center gap-2">
                        <span>Pusat Layanan Chat Mahasiswa & Member</span>
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-emerald-500/30 text-emerald-200 text-[10px] font-semibold">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-ping"></span> Live
                        </span>
                    </h4>
                    <p class="text-[11px] text-emerald-200/90 font-medium">Bertindak sebagai: {{ Auth::guard('web')->user()->realname ?? Auth::guard('web')->user()->username }}</p>
                </div>
            </div>
            <button type="button" onclick="toggleAdminChat()" class="p-1.5 rounded-xl hover:bg-white/20 text-white/90 hover:text-white transition-colors" title="Tutup Chat">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Two Column Layout: Left Room List, Right Conversation -->
        <div class="flex-grow flex flex-col sm:flex-row overflow-hidden">
            <!-- Left Column: Chatrooms List -->
            <div id="admin-chat-sidebar" class="w-full sm:w-[280px] shrink-0 border-r border-slate-200 dark:border-slate-800 flex flex-col bg-slate-50/70 dark:bg-slate-950/40">
                <!-- Search Input -->
                <div class="p-3 border-b border-slate-200 dark:border-slate-800">
                    <div class="relative">
                        <input type="text"
                               id="admin-chat-search-input"
                               placeholder="Cari nama atau NIM..."
                               oninput="handleAdminChatSearch(this.value)"
                               class="w-full pl-8 pr-3 py-1.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-xs text-slate-800 dark:text-slate-100 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <svg class="w-4 h-4 text-slate-400 absolute left-2.5 top-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                </div>

                <!-- Room Items List -->
                <div id="admin-chat-rooms-list" class="flex-grow overflow-y-auto divide-y divide-slate-100 dark:divide-slate-800/60">
                    <div class="text-center py-10 text-slate-400 text-xs">
                        <div class="inline-block animate-spin rounded-full h-5 w-5 border-2 border-emerald-500 border-t-transparent"></div>
                        <p class="mt-2 text-xs">Memuat daftar chat...</p>
                    </div>
                </div>
            </div>

            <!-- Right Column: Conversation Thread -->
            <div id="admin-chat-thread" class="flex-grow flex flex-col bg-white dark:bg-slate-900 overflow-hidden">
                <!-- Conversation Header -->
                <div id="admin-chat-conv-header" class="px-4 py-2.5 bg-slate-100/90 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <button type="button" onclick="backToAdminRooms()" class="sm:hidden p-1 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <div>
                            <h5 id="admin-chat-active-name" class="font-bold text-xs text-slate-900 dark:text-white leading-tight">Pilih Ruang Chat</h5>
                            <p id="admin-chat-active-meta" class="text-[10px] text-slate-500 dark:text-slate-400">Pilih anggota di sebelah kiri untuk melihat pesan</p>
                        </div>
                    </div>
                    <span id="admin-chat-active-tag" class="hidden px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 text-[10px] font-bold">Terhubung</span>
                </div>

                <!-- Messages Container -->
                <div id="admin-chat-messages-container" class="flex-grow p-4 overflow-y-auto space-y-3 bg-slate-50/50 dark:bg-slate-950/40 text-xs">
                    <div id="admin-chat-empty-state" class="h-full flex flex-col items-center justify-center text-center p-6 text-slate-400">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 dark:bg-slate-800 flex items-center justify-center text-emerald-500 mb-3">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path></svg>
                        </div>
                        <p class="font-semibold text-slate-700 dark:text-slate-300">Belum ada obrolan terpilih</p>
                        <p class="text-[11px] mt-1 text-slate-400 max-w-xs">Silakan klik salah satu nama member di panel sebelah kiri untuk membalas pertanyaan.</p>
                    </div>
                </div>

                <!-- Input Footer -->
                <form id="admin-chat-form" onsubmit="sendAdminMessage(event)" class="p-3 bg-white dark:bg-slate-900 border-t border-slate-200 dark:border-slate-800 flex items-end gap-2">
                    <div class="flex-grow relative">
                        <textarea id="admin-chat-input"
                                  rows="1"
                                  maxlength="3000"
                                  disabled
                                  placeholder="Pilih anggota terlebih dahulu..."
                                  onkeydown="handleAdminChatKeydown(event)"
                                  class="w-full resize-none max-h-24 px-3.5 py-2.5 rounded-2xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 text-xs focus:ring-2 focus:ring-emerald-500 focus:outline-none disabled:opacity-50 transition-all"></textarea>
                    </div>
                    <button id="admin-chat-send-btn" type="submit" disabled
                            class="p-2.5 rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white hover:from-emerald-500 hover:to-teal-500 shadow-md hover:shadow-lg active:scale-95 disabled:opacity-40 disabled:pointer-events-none transition-all flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5 transform rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    (function() {
        let isAdminChatOpen = false;
        let activeRoomId = null;
        let currentUnreadTotal = 0;
        let activeRoomMessagesCount = 0;
        let roomsPollingInterval = null;
        let messagesPollingInterval = null;
        let heartbeatInterval = null;
        let searchTimeout = null;

        const csrfToken = '{{ csrf_token() }}';
        const unreadCountUrl = '{{ route("admin.chat.unread_count") }}';
        const roomsUrl = '{{ route("admin.chat.rooms") }}';

        // Play gentle audio alert for new message
        function playAdminChime() {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if (!AudioContext) return;
                const ctx = new AudioContext();
                const osc = ctx.createOscillator();
                const gain = ctx.createGain();

                osc.type = 'triangle';
                osc.frequency.setValueAtTime(523.25, ctx.currentTime); // C5
                osc.frequency.setValueAtTime(659.25, ctx.currentTime + 0.1); // E5
                osc.frequency.setValueAtTime(783.99, ctx.currentTime + 0.2); // G5

                gain.gain.setValueAtTime(0.12, ctx.currentTime);
                gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.45);

                osc.connect(gain);
                gain.connect(ctx.destination);
                osc.start();
                osc.stop(ctx.currentTime + 0.45);
            } catch (e) {}
        }

        window.toggleAdminChat = function() {
            const win = document.getElementById('admin-chat-window');
            const iconMsg = document.getElementById('admin-chat-icon-msg');
            const iconClose = document.getElementById('admin-chat-icon-close');

            isAdminChatOpen = !isAdminChatOpen;

            if (isAdminChatOpen) {
                win.classList.remove('hidden');
                setTimeout(() => {
                    win.classList.remove('scale-95', 'opacity-0');
                    win.classList.add('scale-100', 'opacity-100');
                }, 10);
                iconMsg.classList.add('hidden');
                iconClose.classList.remove('hidden');

                loadAdminRooms();
                if (roomsPollingInterval) clearInterval(roomsPollingInterval);
                roomsPollingInterval = setInterval(loadAdminRooms, 4000);
            } else {
                win.classList.remove('scale-100', 'opacity-100');
                win.classList.add('scale-95', 'opacity-0');
                setTimeout(() => {
                    win.classList.add('hidden');
                }, 200);
                iconMsg.classList.remove('hidden');
                iconClose.classList.add('hidden');

                if (roomsPollingInterval) {
                    clearInterval(roomsPollingInterval);
                    roomsPollingInterval = null;
                }
                if (messagesPollingInterval) {
                    clearInterval(messagesPollingInterval);
                    messagesPollingInterval = null;
                }
            }
        };

        window.backToAdminRooms = function() {
            const sidebar = document.getElementById('admin-chat-sidebar');
            const thread = document.getElementById('admin-chat-thread');
            sidebar.classList.remove('hidden');
            thread.classList.add('hidden', 'sm:flex');
            activeRoomId = null;
            if (messagesPollingInterval) {
                clearInterval(messagesPollingInterval);
                messagesPollingInterval = null;
            }
        };

        // Heartbeat & Unread Counter Poll
        function pollAdminUnreadCount() {
            fetch(unreadCountUrl, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                const badge = document.getElementById('admin-chat-unread-badge');
                const newCount = parseInt(data.unread_total || 0);

                if (newCount > currentUnreadTotal) {
                    playAdminChime();
                }
                currentUnreadTotal = newCount;

                if (newCount > 0) {
                    badge.innerText = newCount;
                    badge.classList.remove('hidden');
                } else {
                    badge.innerText = '0';
                    badge.classList.add('hidden');
                }
            })
            .catch(() => {});
        }

        window.handleAdminChatSearch = function(val) {
            if (searchTimeout) clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                loadAdminRooms(val);
            }, 300);
        };

        function loadAdminRooms(query = null) {
            const q = query !== null ? query : (document.getElementById('admin-chat-search-input')?.value || '');
            const url = roomsUrl + (q ? ('?q=' + encodeURIComponent(q)) : '');

            fetch(url, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                renderRoomsList(data.rooms || []);
                currentUnreadTotal = parseInt(data.unread_total || 0);
                const badge = document.getElementById('admin-chat-unread-badge');
                if (currentUnreadTotal > 0) {
                    badge.innerText = currentUnreadTotal;
                    badge.classList.remove('hidden');
                } else {
                    badge.innerText = '0';
                    badge.classList.add('hidden');
                }
            })
            .catch(() => {});
        }

        function renderRoomsList(rooms) {
            const listEl = document.getElementById('admin-chat-rooms-list');
            if (!listEl) return;

            if (rooms.length === 0) {
                listEl.innerHTML = `
                    <div class="text-center py-10 px-3 text-slate-400">
                        <svg class="w-8 h-8 mx-auto mb-2 text-slate-300 dark:text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        <p class="text-xs font-semibold text-slate-600 dark:text-slate-300">Belum Ada Pesan</p>
                        <p class="text-[10px] mt-0.5">Pesan dari mahasiswa/dosen akan tampil di sini.</p>
                    </div>
                `;
                return;
            }

            let html = '';
            rooms.forEach(r => {
                const isActive = activeRoomId === r.id;
                const hasUnread = r.unread_admin_count > 0;

                html += `
                    <div onclick="selectAdminRoom(${r.id})"
                         class="p-3 cursor-pointer transition-colors flex items-start gap-3 ${isActive ? 'bg-emerald-50 dark:bg-emerald-950/40 border-l-4 border-emerald-600' : 'hover:bg-slate-100 dark:hover:bg-slate-800/60'}">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-sky-500 to-indigo-600 text-white font-bold flex items-center justify-center text-xs shrink-0 shadow-sm">
                            ${r.member_name ? escapeHtml(r.member_name.charAt(0).toUpperCase()) : 'M'}
                        </div>
                        <div class="flex-grow min-w-0">
                            <div class="flex items-center justify-between gap-1">
                                <h6 class="font-bold text-xs text-slate-900 dark:text-white truncate ${hasUnread ? 'text-emerald-700 dark:text-emerald-400 font-extrabold' : ''}">${escapeHtml(r.member_name)}</h6>
                                <span class="text-[9px] text-slate-400 shrink-0">${escapeHtml(r.last_message_at)}</span>
                            </div>
                            <p class="text-[10px] text-slate-400 truncate">${escapeHtml(r.member_id)} • ${escapeHtml(r.member_inst)}</p>
                            <div class="flex items-center justify-between mt-1">
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate max-w-[170px] ${hasUnread ? 'font-semibold text-slate-800 dark:text-slate-200' : ''}">${escapeHtml(r.last_message)}</p>
                                ${hasUnread ? `<span class="px-1.5 py-0.2 min-w-[18px] text-[10px] font-black bg-rose-500 text-white rounded-full text-center">${r.unread_admin_count}</span>` : ''}
                            </div>
                        </div>
                    </div>
                `;
            });

            listEl.innerHTML = html;
        }

        window.selectAdminRoom = function(roomId) {
            activeRoomId = roomId;

            // Responsive toggle on small screen
            const sidebar = document.getElementById('admin-chat-sidebar');
            const thread = document.getElementById('admin-chat-thread');
            if (window.innerWidth < 640) {
                sidebar.classList.add('hidden');
                thread.classList.remove('hidden');
            }

            const input = document.getElementById('admin-chat-input');
            const sendBtn = document.getElementById('admin-chat-send-btn');
            input.disabled = false;
            input.placeholder = "Ketik balasan Anda...";
            sendBtn.disabled = false;

            activeRoomMessagesCount = 0;
            loadAdminRoomMessages();

            if (messagesPollingInterval) clearInterval(messagesPollingInterval);
            messagesPollingInterval = setInterval(loadAdminRoomMessages, 3000);

            // Re-render rooms to show active state
            loadAdminRooms();
            setTimeout(() => input.focus(), 150);
        };

        function loadAdminRoomMessages() {
            if (!activeRoomId) return;

            const url = '{{ url("admin/chat/room") }}/' + activeRoomId + '/messages';

            fetch(url, {
                headers: { 'Accept': 'application/json' }
            })
            .then(res => res.json())
            .then(data => {
                const headerName = document.getElementById('admin-chat-active-name');
                const headerMeta = document.getElementById('admin-chat-active-meta');
                const headerTag = document.getElementById('admin-chat-active-tag');

                if (data.room) {
                    headerName.innerText = data.room.member_name;
                    headerMeta.innerText = data.room.member_id + ' • ' + data.room.member_inst;
                    headerTag.classList.remove('hidden');
                }

                if (data.messages && data.messages.length > activeRoomMessagesCount) {
                    const lastMsg = data.messages[data.messages.length - 1];
                    if (activeRoomMessagesCount > 0 && lastMsg.sender_type === 'member') {
                        playAdminChime();
                    }
                    activeRoomMessagesCount = data.messages.length;
                    renderAdminMessages(data.messages);
                } else if (activeRoomMessagesCount === 0 && data.messages) {
                    activeRoomMessagesCount = data.messages.length;
                    renderAdminMessages(data.messages);
                }
            })
            .catch(() => {});
        }

        function renderAdminMessages(messages) {
            const container = document.getElementById('admin-chat-messages-container');
            if (!container) return;

            if (messages.length === 0) {
                container.innerHTML = `
                    <div class="text-center py-12 px-4 text-slate-400">
                        <p class="font-semibold text-slate-600 dark:text-slate-300">Belum ada percakapan</p>
                        <p class="text-[11px] mt-1 text-slate-400">Tuliskan pesan pertama untuk menyapa mahasiswa/member ini.</p>
                    </div>
                `;
                return;
            }

            let html = '';
            messages.forEach(msg => {
                const isAdmin = msg.sender_type === 'admin';

                if (isAdmin) {
                    // Bubble Admin (Kanan)
                    html += `
                        <div class="flex flex-col items-end">
                            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 mb-0.5 mr-1">Anda (${escapeHtml(msg.sender_name)})</span>
                            <div class="max-w-[80%] rounded-2xl rounded-br-none px-4 py-2.5 bg-gradient-to-r from-emerald-600 to-teal-700 text-white shadow-sm break-words leading-relaxed text-xs">
                                ${escapeHtml(msg.message).replace(/\\n/g, '<br>')}
                            </div>
                            <div class="flex items-center gap-1 mt-1 text-[10px] text-slate-400 mr-1">
                                <span>${msg.time}</span>
                            </div>
                        </div>
                    `;
                } else {
                    // Bubble Member (Kiri)
                    html += `
                        <div class="flex flex-col items-start">
                            <span class="text-[10px] font-bold text-sky-600 dark:text-sky-400 mb-0.5 ml-1">${escapeHtml(msg.sender_name)}</span>
                            <div class="max-w-[80%] rounded-2xl rounded-bl-none px-4 py-2.5 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 border border-slate-200 dark:border-slate-700 shadow-sm break-words leading-relaxed text-xs">
                                ${escapeHtml(msg.message).replace(/\\n/g, '<br>')}
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

        window.handleAdminChatKeydown = function(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                document.getElementById('admin-chat-form').requestSubmit();
            }
        };

        window.sendAdminMessage = function(e) {
            e.preventDefault();
            if (!activeRoomId) return;

            const input = document.getElementById('admin-chat-input');
            const btn = document.getElementById('admin-chat-send-btn');
            const text = input.value.trim();

            if (!text) return;

            btn.disabled = true;
            const sendUrl = '{{ url("admin/chat/room") }}/' + activeRoomId + '/send';

            fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message: text })
            })
            .then(res => res.json())
            .then(data => {
                btn.disabled = false;
                if (data.success) {
                    input.value = '';
                    loadAdminRoomMessages();
                    loadAdminRooms();
                } else {
                    alert(data.error || 'Gagal mengirim pesan.');
                }
            })
            .catch(() => {
                btn.disabled = false;
                alert('Terjadi kesalahan pengiriman pesan.');
            });
        };

        function escapeHtml(text) {
            if (!text) return '';
            const div = document.createElement('div');
            div.innerText = text;
            return div.innerHTML;
        }

        // Initialize heartbeat & unread counter polling
        pollAdminUnreadCount();
        heartbeatInterval = setInterval(pollAdminUnreadCount, 8000);
    })();
</script>
@endif
