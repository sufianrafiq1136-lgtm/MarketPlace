<x-app-layout>
    <div class="chat-page">
        <div class="chat-shell">
            <div class="chat-intro">
                <div>
                    <p class="chat-kicker">BazaarLink inbox</p>
                    <h1>My Chats</h1>
                    <p>Keep every buyer and seller conversation in one calm place.</p>
                </div>
                <a href="{{ route('home') }}" class="chat-back">Browse listings</a>
            </div>
            <div class="row g-4">
                <div class="col-12 col-lg-4">
                    <div class="chat-panel chat-list-panel">
                        <div class="chat-panel-header">
                            <div>
                                <h2>Conversations</h2>
                                <p>Messages about your listings</p>
                            </div>
                            <span class="unread-count">{{ $unreadMessages }} unread</span>
                        </div>

                        <div class="p-3 d-flex flex-column gap-2" style="max-height: 75vh; overflow-y: auto;">
                            @forelse ($conversations as $conversation)
                                @php
                                    $partnerId = $conversation->sender_id === auth()->id() ? $conversation->receiver_id : $conversation->sender_id;
                                    $partner = $conversation->sender_id === auth()->id() ? $conversation->receiver : $conversation->sender;
                                    $isActive = $selectedAd?->id === $conversation->ad_id && (int) $selectedUserId === (int) $partnerId;
                                @endphp
                                <a href="{{ route('messages.index', ['ad' => $conversation->ad_id, 'user' => $partnerId]) }}" class="text-decoration-none text-reset">
                                    <div class="conversation-item {{ $isActive ? 'active' : '' }}">
                                        <div class="d-flex justify-content-between gap-3">
                                            <div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <div class="fw-semibold">{{ $partner?->name ?? 'Unknown user' }}</div>
                                                    @if ($conversation->has_unread && ! $isActive)
                                                        <span class="unread-dot" aria-label="Unread chat"></span>
                                                    @endif
                                                </div>
                                                    <div class="text-muted small text-truncate">{{ $conversation->ad?->title }}</div>
                                            </div>
                                            <div class="text-muted small text-end">{{ $conversation->created_at?->diffForHumans() }}</div>
                                        </div>
                                        <div class="small mt-2 text-truncate conversation-preview">{{ $conversation->message }}</div>
                                    </div>
                                </a>
                            @empty
                                <div class="text-muted">No conversations yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-8">
                    <div class="chat-panel chat-thread-panel">
                        @if ($selectedAd && $selectedConversation)
                            @php
                                $selectedPartner = $selectedConversation->first()?->sender_id === auth()->id()
                                    ? $selectedConversation->first()?->receiver
                                    : ($selectedConversation->first()?->sender ?? $selectedAd->user);
                            @endphp
                            <div class="thread-header">
                                <div class="avatar">{{ strtoupper(substr($selectedPartner?->name ?? 'C', 0, 1)) }}</div>
                                <div>
                                    <h2>{{ $selectedPartner?->name ?? 'Chat' }}</h2>
                                    <div>{{ $selectedAd->title }}</div>
                                </div>
                            </div>

                            <div id="message-list" class="message-list">
                                @foreach ($selectedConversation as $message)
                                    @php $isMine = $message->sender_id === auth()->id(); @endphp
                                    <div class="message-bubble {{ $isMine ? 'mine' : 'theirs' }}">
                                        <div class="small fw-semibold mb-1">{{ $isMine ? 'You' : $message->sender?->name }}</div>
                                        <div>{{ $message->message }}</div>
                                        <div class="small opacity-75 mt-1">{{ $message->created_at?->format('M d, h:i A') }}</div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="message-composer">
                                <form id="message-form" class="composer-form" data-send-url="{{ route('messages.store') }}">
                                    @csrf
                                    <input type="hidden" name="receiver_id" value="{{ $selectedUserId }}">
                                    <input type="hidden" name="ad_id" value="{{ $selectedAd->id }}">
                                    <textarea name="message" class="form-control" rows="3" placeholder="Write a message..." required maxlength="5000"></textarea>
                                    <div class="composer-footer">
                                        <small>Both sides can reply here.</small>
                                        <button type="submit" class="send-button">Send message <span aria-hidden="true">&#8594;</span></button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="empty-chat">
                                <div class="empty-chat-icon">&#9993;</div>
                                <h3>Select a conversation</h3>
                                <p class="mb-0">Choose a thread on the left to view and send messages.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .chat-page { background: #f5f8f5; min-height: calc(100vh - 65px); padding: 3rem 0 4rem; color: #17221d; }
        .chat-shell { max-width: 1180px; margin: 0 auto; padding: 0 1.5rem; }
        .chat-intro { align-items: end; display: flex; justify-content: space-between; margin-bottom: 2rem; }
        .chat-kicker { color: #73907f; font-size: .72rem; font-weight: 700; letter-spacing: .13em; margin: 0 0 .6rem; text-transform: uppercase; }
        .chat-intro h1 { font-size: 2.6rem; letter-spacing: -.05em; margin: 0; }
        .chat-intro p:last-child { color: #6d7c73; margin: .5rem 0 0; }
        .chat-back { border: 1px solid #d6e2da; border-radius: 5px; color: #245947; font-size: .85rem; padding: .7rem 1rem; text-decoration: none; }
        .chat-panel { background: white; border: 1px solid #dce7df; border-radius: 7px; min-height: 620px; overflow: hidden; }
        .chat-panel-header, .thread-header { border-bottom: 1px solid #e3ebe5; padding: 1.25rem 1.35rem; }
        .chat-panel-header { align-items: center; display: flex; justify-content: space-between; }
        .chat-panel-header h2, .thread-header h2 { font-size: 1.05rem; font-weight: 700; margin: 0; }
        .chat-panel-header p { color: #849188; font-size: .78rem; margin: .3rem 0 0; }
        .unread-count { background: #eef7d5; border-radius: 999px; color: #456221; font-size: .72rem; padding: .35rem .6rem; }
        .unread-dot { background: #2563eb; border-radius: 999px; box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.15); display: inline-block; height: .7rem; width: .7rem; }
        .conversation-item { border: 1px solid transparent; border-radius: 5px; padding: .85rem; }
        .conversation-item.active { background: #eef7ff; border-color: #93c5fd; }
        .conversation-item:hover { background: #f1f8f2; border-color: #bcd8c5; }
        .conversation-preview { color: #78867d; }
        .thread-header { align-items: center; display: flex; gap: .8rem; }
        .thread-header > div:last-child > div { color: #7c8a81; font-size: .78rem; margin-top: .25rem; }
        .avatar { align-items: center; background: #d9f36a; border-radius: 50%; color: #24422f; display: flex; font-weight: 800; height: 2.6rem; justify-content: center; width: 2.6rem; }
        .message-list { background: #fbfdfb; display: flex; flex-direction: column; flex-grow: 1; gap: .75rem; min-height: 420px; max-height: 60vh; overflow-y: auto; padding: 1.5rem; }
        .message-bubble { border-radius: 14px; font-size: .9rem; line-height: 1.5; max-width: 78%; padding: .75rem 1rem; }
        .message-bubble.mine { background: #245947; color: white; border-bottom-right-radius: 4px; margin-left: auto; }
        .message-bubble.theirs { background: #eaf1ec; border-bottom-left-radius: 4px; margin-right: auto; }
        .message-composer { border-top: 1px solid #e3ebe5; padding: 1.2rem 1.35rem; }
        .composer-form textarea { border-color: #d6e2da; border-radius: 5px; resize: vertical; }
        .composer-footer { align-items: center; display: flex; justify-content: space-between; margin-top: .6rem; }
        .composer-footer small { color: #88958d; font-size: .75rem; }
        .send-button { background: #17221d; border: 0; border-radius: 5px; color: white; font-weight: 600; padding: .65rem 1rem; }
        .send-button span { color: #d9f36a; margin-left: .35rem; }
        .empty-chat { align-items: center; color: #7b8980; display: flex; flex-direction: column; justify-content: center; min-height: 620px; text-align: center; }
        .empty-chat-icon { color: #8eb27e; font-size: 2.5rem; margin-bottom: .8rem; }
        .empty-chat h3 { color: #26362c; font-size: 1.2rem; }
        @media (max-width: 767px) { .chat-page { padding-top: 2rem; } .chat-shell { padding: 0 1rem; } .chat-intro { align-items: start; flex-direction: column; gap: 1rem; } .chat-intro h1 { font-size: 2.1rem; } .chat-panel { min-height: 0; } .message-list { max-height: 55vh; min-height: 320px; } .empty-chat { min-height: 360px; } }
    </style>

    @if ($selectedAd && $selectedConversation)
        @push('scripts')
            <script>
                document.addEventListener('DOMContentLoaded', () => {
                    const form = document.getElementById('message-form');
                    const list = document.getElementById('message-list');
                    const textarea = form?.querySelector('textarea[name="message"]');
                    const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                    const sendUrl = form?.dataset.sendUrl;
                    const adId = Number(form?.querySelector('input[name="ad_id"]')?.value);
                    const receiverId = Number(form?.querySelector('input[name="receiver_id"]')?.value);
                    const authUserId = Number(@json(auth()->id()));
                    const channelName = `messages.ad.${adId}`;

                    const renderMessage = (message) => {
                        const isMine = Number(message.sender_id) === authUserId;
                        const wrapper = document.createElement('div');
                        wrapper.className = `p-3 rounded ${isMine ? 'bg-primary text-white ms-auto' : 'bg-light me-auto'}`;
                        wrapper.style.maxWidth = '80%';
                        wrapper.innerHTML = `
                            <div class="small fw-semibold mb-1">${isMine ? 'You' : (message.sender?.name ?? 'User')}</div>
                            <div></div>
                            <div class="small opacity-75 mt-1">${new Date(message.created_at).toLocaleString()}</div>
                        `;
                        wrapper.querySelector('div:nth-child(2)').textContent = message.message;
                        list?.appendChild(wrapper);
                        list?.scrollTo({ top: list.scrollHeight, behavior: 'smooth' });
                    };

                    list?.scrollTo({ top: list.scrollHeight });

                    if (window.Echo) {
                        window.Echo.private(channelName)
                            .listen('.message.sent', (event) => {
                                if (Number(event.message.ad_id) !== adId) {
                                    return;
                                }

                                if (
                                    Number(event.message.sender_id) !== authUserId &&
                                    Number(event.message.receiver_id) !== authUserId
                                ) {
                                    return;
                                }

                                renderMessage(event.message);
                            });
                    }

                    form?.addEventListener('submit', async (event) => {
                        event.preventDefault();

                        const message = textarea?.value.trim();
                        if (!message) {
                            return;
                        }

                        const response = await fetch(sendUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': csrf,
                                'X-Requested-With': 'XMLHttpRequest',
                            },
                            body: JSON.stringify({
                                receiver_id: receiverId,
                                ad_id: adId,
                                message,
                            }),
                        });

                        const payload = await response.json();
                        if (!response.ok) {
                            alert(payload?.message ?? 'Unable to send message.');
                            return;
                        }

                        textarea.value = '';
                        renderMessage(payload.data);
                    });
                });
            </script>
        @endpush
    @endif
</x-app-layout>
