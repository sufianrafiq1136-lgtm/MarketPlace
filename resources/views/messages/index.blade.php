<x-app-layout>
    <x-slot name="header">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Chats
            </h2>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">Back to dashboard</a>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="row g-4">
                <div class="col-12 col-lg-4">
                    <div class="bg-white shadow-sm sm:rounded-lg h-100">
                        <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                            <div>
                                <h3 class="h5 mb-1">Conversations</h3>
                                <p class="text-muted mb-0">All chat threads grouped by ad.</p>
                            </div>
                            <span class="badge text-bg-warning">{{ $unreadMessages }} unread</span>
                        </div>

                        <div class="p-3 d-flex flex-column gap-2" style="max-height: 75vh; overflow-y: auto;">
                            @forelse ($conversations as $conversation)
                                @php
                                    $partnerId = $conversation->sender_id === auth()->id() ? $conversation->receiver_id : $conversation->sender_id;
                                    $partner = $conversation->sender_id === auth()->id() ? $conversation->receiver : $conversation->sender;
                                    $isActive = $selectedAd?->id === $conversation->ad_id && (int) $selectedUserId === (int) $partnerId;
                                @endphp
                                <a href="{{ route('messages.index', ['ad' => $conversation->ad_id, 'user' => $partnerId]) }}" class="text-decoration-none text-reset">
                                    <div class="p-3 rounded border {{ $isActive ? 'border-primary bg-light' : 'bg-white' }}">
                                        <div class="d-flex justify-content-between gap-3">
                                            <div>
                                                <div class="fw-semibold">{{ $partner?->name ?? 'Unknown user' }}</div>
                                                <div class="text-muted small">{{ $conversation->ad?->title }}</div>
                                            </div>
                                            <div class="text-muted small text-end">{{ $conversation->created_at?->diffForHumans() }}</div>
                                        </div>
                                        <div class="small mt-2 text-truncate">{{ $conversation->message }}</div>
                                    </div>
                                </a>
                            @empty
                                <div class="text-muted">No conversations yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-8">
                    <div class="bg-white shadow-sm sm:rounded-lg h-100 d-flex flex-column">
                        @if ($selectedAd && $selectedConversation)
                            @php
                                $selectedPartner = $selectedConversation->first()?->sender_id === auth()->id()
                                    ? $selectedConversation->first()?->receiver
                                    : $selectedConversation->first()?->sender;
                            @endphp
                            <div class="p-4 border-bottom">
                                <h3 class="h5 mb-1">{{ $selectedPartner?->name ?? 'Chat' }}</h3>
                                <div class="text-muted small">{{ $selectedAd->title }}</div>
                            </div>

                            <div id="message-list" class="p-4 d-flex flex-column gap-3 flex-grow-1" style="min-height: 420px; max-height: 75vh; overflow-y: auto;">
                                @foreach ($selectedConversation as $message)
                                    @php $isMine = $message->sender_id === auth()->id(); @endphp
                                    <div class="p-3 rounded {{ $isMine ? 'bg-primary text-white ms-auto' : 'bg-light me-auto' }}" style="max-width: 80%;">
                                        <div class="small fw-semibold mb-1">{{ $isMine ? 'You' : $message->sender?->name }}</div>
                                        <div>{{ $message->message }}</div>
                                        <div class="small opacity-75 mt-1">{{ $message->created_at?->format('M d, h:i A') }}</div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="p-4 border-top">
                                <form id="message-form" class="d-grid gap-2" data-send-url="{{ route('messages.store') }}">
                                    @csrf
                                    <input type="hidden" name="receiver_id" value="{{ $selectedUserId }}">
                                    <input type="hidden" name="ad_id" value="{{ $selectedAd->id }}">
                                    <textarea name="message" class="form-control" rows="3" placeholder="Write a message..." required maxlength="5000"></textarea>
                                    <div class="d-flex justify-content-between align-items-center gap-3">
                                        <small class="text-muted">Both sides can reply here.</small>
                                        <button type="submit" class="btn btn-primary">Send</button>
                                    </div>
                                </form>
                            </div>
                        @else
                            <div class="p-5 text-center text-muted d-flex flex-column align-items-center justify-content-center h-100" style="min-height: 70vh;">
                                <h3 class="h4 text-gray-800">Select a conversation</h3>
                                <p class="mb-0">Choose a thread on the left to view and send messages.</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

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
