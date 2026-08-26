<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Ad Details
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-6xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h1 class="h3 mb-1">{{ $ad->title }}</h1>
                            <p class="text-muted mb-0">{{ $ad->city }} | {{ ucfirst($ad->condition) }} | {{ ucfirst($ad->status) }}</p>
                        </div>
                        <div class="d-flex gap-2">
                            @auth
                                <form method="POST" action="{{ route('ads.favorite.toggle', $ad) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-outline-danger">
                                        {{ auth()->user()->favorites()->where('ad_id', $ad->id)->exists() ? 'Unfavorite' : 'Favorite' }}
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('ads.report.store', $ad) }}">
                                    @csrf
                                    <input type="hidden" name="reason" value="This ad looks suspicious and should be reviewed by the admin team.">
                                    <button type="submit" class="btn btn-outline-warning">Report</button>
                                </form>
                            @endauth
                            @if(auth()->user()?->is_admin)
                                <a href="{{ route('ads.edit', $ad) }}" class="btn btn-primary">Edit</a>
                            @endif
                            <a href="{{ route('ads.index') }}" class="btn btn-outline-secondary">Back</a>
                        </div>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-7">
                            <div class="card border-0 shadow-sm">
                                @if ($ad->images->count())
                                    <div id="adImagesCarousel" class="carousel slide" data-bs-ride="carousel">
                                        <div class="carousel-inner">
                                            @foreach ($ad->images as $image)
                                                <div class="carousel-item @if ($loop->first) active @endif">
                                                    <img src="{{ asset('storage/' . $image->image_path) }}" class="d-block w-100" alt="{{ $ad->title }}" style="height: 420px; object-fit: cover;">
                                                </div>
                                            @endforeach
                                        </div>
                                        @if ($ad->images->count() > 1)
                                            <button class="carousel-control-prev" type="button" data-bs-target="#adImagesCarousel" data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Previous</span>
                                            </button>
                                            <button class="carousel-control-next" type="button" data-bs-target="#adImagesCarousel" data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Next</span>
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="height: 420px;">
                                        No images available
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="col-lg-5">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h2 class="h4 text-success mb-3">Rs. {{ number_format($ad->price, 2) }}</h2>

                                    <p class="mb-3">{{ $ad->description }}</p>

                                    <ul class="list-group list-group-flush">
                                        <li class="list-group-item px-0 d-flex justify-content-between">
                                            <strong>Category</strong>
                                            <span>{{ $ad->category?->name ?? 'N/A' }}</span>
                                        </li>
                                        <li class="list-group-item px-0 d-flex justify-content-between">
                                            <strong>Seller</strong>
                                            <span>{{ $ad->user?->name ?? 'N/A' }}</span>
                                        </li>
                                        <li class="list-group-item px-0 d-flex justify-content-between">
                                            <strong>Owner Number</strong>
                                            <span>{{ $ad->user?->phone ?? 'N/A' }}</span>
                                        </li>
                                        <li class="list-group-item px-0 d-flex justify-content-between">
                                            <strong>Status</strong>
                                            <span>{{ ucfirst($ad->status) }}</span>
                                        </li>
                                        <li class="list-group-item px-0 d-flex justify-content-between">
                                            <strong>Posted</strong>
                                            <span>{{ $ad->created_at?->format('M d, Y') }}</span>
                                        </li>
                                    </ul>

                                    @auth
                                        <div class="mt-4 p-3 bg-light rounded">
                                            <form method="POST" action="{{ route('ads.report.store', $ad) }}" class="d-grid gap-2">
                                                @csrf
                                                <label class="form-label fw-semibold" for="reason">Report this ad</label>
                                                <textarea name="reason" id="reason" class="form-control" rows="4" placeholder="Describe the issue" required></textarea>
                                                @error('reason')
                                                    <div class="text-danger small">{{ $message }}</div>
                                                @enderror
                                                <button type="submit" class="btn btn-warning">Submit report</button>
                                            </form>
                                        </div>
                                    @endauth

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
