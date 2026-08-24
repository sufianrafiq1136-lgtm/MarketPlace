<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Customer Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid gap-4 md:grid-cols-3 mb-6">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">My Ads</div>
                    <div class="mt-2 text-3xl font-bold">{{ $myAdsCount }}</div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Favorites</div>
                    <div class="mt-2 text-3xl font-bold">{{ $favoriteCount }}</div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Reports Sent</div>
                    <div class="mt-2 text-3xl font-bold">{{ $myReportsCount }}</div>
                </div>
            </div>

            <div class="mb-6">
                <a href="{{ route('messages.index') }}" class="btn btn-dark">
                    Open Chats
                </a>
            </div>

            <div class="grid gap-6 lg:grid-cols-2">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h3 class="h4 mb-0">Recent Favorites</h3>
                        <a href="{{ route('ads.create') }}" class="btn btn-primary btn-sm">Post an Ad</a>
                    </div>

                    @if ($recentFavorites->isEmpty())
                        <p class="text-gray-500 mb-0">Start saving ads you like. They will appear here.</p>
                    @else
                        <div class="row g-4">
                            @foreach ($recentFavorites as $favorite)
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="card h-100 shadow-sm border-0">
                                        @php $image = $favorite->ad?->images?->first(); @endphp
                                        @if ($image)
                                            <img src="{{ asset('storage/' . $image->image_path) }}" class="card-img-top" alt="{{ $favorite->ad?->title }}" style="height: 200px; object-fit: cover;">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center text-white fw-semibold" style="height: 200px; background: linear-gradient(135deg, #1d4ed8, #14b8a6);">
                                                {{ $favorite->ad?->title ? strtoupper(substr($favorite->ad->title, 0, 1)) : 'A' }}
                                            </div>
                                        @endif
                                        <div class="card-body d-flex flex-column gap-2">
                                            <h5 class="card-title mb-0">{{ $favorite->ad?->title }}</h5>
                                            <p class="text-success fw-semibold mb-0">Rs. {{ number_format($favorite->ad?->price ?? 0, 2) }}</p>
                                            <p class="text-muted mb-0">{{ $favorite->ad?->city }}</p>
                                            <a href="{{ route('ads.show', $favorite->ad) }}" class="btn btn-outline-primary btn-sm mt-auto">View Ad</a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    </div>
                </div>

                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div>
                                <h3 class="h4 mb-0">Chats</h3>
                                <p class="text-muted mb-0">Messages related to your ads and conversations.</p>
                            </div>
                            <span class="badge text-bg-warning">{{ $unreadMessages }} unread</span>
                        </div>

                        @if ($recentChats->isEmpty())
                            <p class="text-gray-500 mb-0">No chats yet. Buyer messages will show up here.</p>
                        @else
                            <div class="d-flex flex-column gap-3">
                                @foreach ($recentChats as $message)
                                    @php
                                        $isMine = $message->sender_id === auth()->id();
                                    @endphp
                                    <a href="{{ route('ads.show', $message->ad) }}" class="text-decoration-none text-reset">
                                        <div class="p-3 rounded border {{ $isMine ? 'bg-light' : 'bg-white' }}">
                                            <div class="d-flex justify-content-between align-items-start gap-3">
                                                <div>
                                                    <div class="fw-semibold">{{ $isMine ? 'You' : $message->sender?->name }}</div>
                                                    <div class="text-muted small">{{ $message->ad?->title }}</div>
                                                </div>
                                                <div class="text-muted small text-end">{{ $message->created_at?->diffForHumans() }}</div>
                                            </div>
                                            <div class="mt-2">{{ \Illuminate\Support\Str::limit($message->message, 120) }}</div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
