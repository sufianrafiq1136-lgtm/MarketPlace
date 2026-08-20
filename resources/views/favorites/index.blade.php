<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Favorites
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('status'))
                        <div class="mb-4 rounded-md bg-green-50 px-4 py-3 text-green-700">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if ($favorites->isEmpty())
                        <p class="text-gray-500">You have not saved any ads yet.</p>
                    @else
                        <div class="row g-4">
                            @foreach ($favorites as $favorite)
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="card h-100 shadow-sm border-0">
                                        @php $image = $favorite->ad?->images?->first(); @endphp
                                        @if ($image)
                                            <img src="{{ asset('storage/' . $image->image_path) }}" class="card-img-top" alt="{{ $favorite->ad?->title }}" style="height: 200px; object-fit: cover;">
                                        @else
                                            <div class="d-flex align-items-center justify-content-center text-white fw-semibold" style="height: 200px; background: linear-gradient(135deg, #0f172a, #2563eb);">
                                                {{ $favorite->ad?->title ? strtoupper(substr($favorite->ad->title, 0, 1)) : 'A' }}
                                            </div>
                                        @endif
                                        <div class="card-body d-flex flex-column gap-2">
                                            <h5 class="card-title mb-0">{{ $favorite->ad?->title }}</h5>
                                            <p class="text-success fw-semibold mb-0">Rs. {{ number_format($favorite->ad?->price ?? 0, 2) }}</p>
                                            <p class="text-muted mb-0">{{ $favorite->ad?->city }}</p>
                                            <div class="mt-auto d-flex gap-2">
                                                <a href="{{ route('ads.show', $favorite->ad) }}" class="btn btn-primary btn-sm">View</a>
                                                <form method="POST" action="{{ route('ads.favorite.toggle', $favorite->ad) }}">
                                                    @csrf
                                                    <button type="submit" class="btn btn-outline-danger btn-sm">Remove</button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
