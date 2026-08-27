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
                                <button type="button" class="btn btn-outline-warning" data-bs-toggle="modal" data-bs-target="#reportAdModal">
                                    Report
                                </button>
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

                                    @auth
                                        @if (auth()->id() !== $ad->user_id)
                                            <a href="{{ route('messages.index', ['ad' => $ad->id, 'user' => $ad->user_id]) }}" class="btn btn-primary w-100 mb-3">
                                                Message seller
                                            </a>
                                        @endif
                                    @endauth

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
                                        @if(auth()->id() !== $ad->user_id)
                                            <div class="mt-4">
                                                <a href="{{ route('messages.index', ['ad' => $ad->id, 'user' => $ad->user_id]) }}" class="btn btn-primary w-100">
                                                    Start chat about this ad
                                                </a>
                                            </div>
                                        @endif
                                    @endauth

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @auth
        <div class="modal fade" id="reportAdModal" tabindex="-1" aria-labelledby="reportAdModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <form method="POST" action="{{ route('ads.report.store', $ad) }}">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="reportAdModalLabel">Report this ad</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <p class="text-muted mb-3">Tell us what is wrong with this listing. Your report will be reviewed by the admin team.</p>
                            <label class="form-label fw-semibold" for="reportReason">Reason</label>
                            <textarea name="reason" id="reportReason" class="form-control" rows="4" placeholder="Describe the issue" required></textarea>
                            @error('reason')
                                <div class="text-danger small mt-2">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                            <button type="submit" class="btn btn-warning">Submit report</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endauth

</x-app-layout>
