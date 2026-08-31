<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit Ad
        </h2>
    </x-slot>

    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Edit Ad</h2>

            <a href="{{ route('ads.index') }}" class="btn btn-secondary">
                Back to Ads
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">
                <form id="editAdForm" data-ad-id="{{ $ad->id }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="{{ $ad->title }}">
                        <div class="text-danger small" id="titleError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id">
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}" @selected($ad->category_id === $category->id)>{{ $category->name }}</option>
                            @endforeach
                        </select>
                        <div class="text-danger small" id="category_idError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="5">{{ $ad->description }}</textarea>
                        <div class="text-danger small" id="descriptionError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="price" class="form-label">Price</label>
                        <input type="number" class="form-control" id="price" name="price" min="0" step="0.01" value="{{ $ad->price }}">
                        <div class="text-danger small" id="priceError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="condition" class="form-label">Condition</label>
                        <select class="form-select" id="condition" name="condition">
                            <option value="new" @selected($ad->condition === 'new')>New</option>
                            <option value="used" @selected($ad->condition === 'used')>Used</option>
                        </select>
                        <div class="text-danger small" id="conditionError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="city" class="form-label">City</label>
                        <input type="text" class="form-control" id="city" name="city" value="{{ $ad->city }}">
                        <div class="text-danger small" id="cityError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="available" @selected($ad->status === 'available')>Available</option>
                            <option value="sold" @selected($ad->status === 'sold')>Sold</option>
                            <option value="pending" @selected($ad->status === 'pending')>Pending</option>
                        </select>
                        <div class="text-danger small" id="statusError"></div>
                    </div>

                    <div class="mb-3">
                        <label for="images" class="form-label">Replace Photos</label>
                        <input type="file" class="form-control" id="images" name="images[]" accept="image/*" multiple>

                        <div class="mt-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <strong>Current photos</strong>
                                <small class="text-muted">Remove any photo you do not want</small>
                            </div>
                            <div class="row g-2" id="existingImagesPreview">
                                @foreach ($ad->images->sortBy('sort_order') as $image)
                                    <div class="col-6 col-md-3 existing-image-item" data-image-id="{{ $image->id }}">
                                        <div class="border rounded overflow-hidden bg-light position-relative">
                                            <img src="{{ asset('storage/' . $image->image_path) }}" alt="Ad photo" class="w-100" style="height: 140px; object-fit: cover;">
                                            <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2 delete-existing-image" data-image-id="{{ $image->id }}" aria-label="Delete photo">×</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div id="deleteImageIdsContainer"></div>
                        </div>

                        <div class="row g-2 mt-3" id="imagesPreview"></div>
                        <div class="text-danger small" id="imagesError"></div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submitAdBtn">Update Ad</button>
                </form>
            </div>
        </div>
    </div>

    @vite(['resources/js/ads.js'])
</x-app-layout>
