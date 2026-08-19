<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Ad
        </h2>
    </x-slot>

    <div class="container py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Create New Ad</h2>

            <a href="{{ route('ads.index') }}" class="btn btn-secondary">
                Back to Ads
            </a>
        </div>

        <div class="card shadow-sm">
            <div class="card-body">

                <form id="createAdForm" enctype="multipart/form-data">

                    @csrf

                    <!-- Title -->
                    <div class="mb-3">
                        <label for="title" class="form-label">
                            Title
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="title"
                            name="title"
                            placeholder="e.g. iPhone 15 Pro"
                        >

                        <div class="text-danger small" id="titleError"></div>
                    </div>


                    <!-- Category -->
                    <div class="mb-3">
                        <label for="category_id" class="form-label">
                            Category
                        </label>

                        <select
                            class="form-select"
                            id="category_id"
                            name="category_id"
                        >
                            <option value="">Select Category</option>
                            @foreach ($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>

                        <div class="text-danger small" id="category_idError"></div>
                    </div>


                    <!-- Description -->
                    <div class="mb-3">
                        <label for="description" class="form-label">
                            Description
                        </label>

                        <textarea
                            class="form-control"
                            id="description"
                            name="description"
                            rows="5"
                            placeholder="Describe your item..."
                        ></textarea>

                        <div class="text-danger small" id="descriptionError"></div>
                    </div>


                    <!-- Price -->
                    <div class="mb-3">
                        <label for="price" class="form-label">
                            Price
                        </label>

                        <input
                            type="number"
                            class="form-control"
                            id="price"
                            name="price"
                            min="0"
                            step="0.01"
                            placeholder="Enter price"
                        >

                        <div class="text-danger small" id="priceError"></div>
                    </div>


                    <!-- Condition -->
                    <div class="mb-3">
                        <label for="condition" class="form-label">
                            Condition
                        </label>

                        <select
                            class="form-select"
                            id="condition"
                            name="condition"
                        >
                            <option value="">Select Condition</option>
                            <option value="new">New</option>
                            <option value="used">Used</option>
                        </select>

                        <div class="text-danger small" id="conditionError"></div>
                    </div>


                    <!-- City -->
                    <div class="mb-3">
                        <label for="city" class="form-label">
                            City
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            id="city"
                            name="city"
                            placeholder="e.g. Lahore"
                        >

                        <div class="text-danger small" id="cityError"></div>
                    </div>


                    <!-- Status -->
                    <div class="mb-3">
                        <label for="status" class="form-label">
                            Status
                        </label>

                        <select
                            class="form-select"
                            id="status"
                            name="status"
                        >
                            <option value="available">Available</option>
                            <option value="sold">Sold</option>
                            <option value="pending">Pending</option>
                        </select>

                        <div class="text-danger small" id="statusError"></div>
                    </div>


                    <!-- Images -->
                    <div class="mb-4">
                        <label for="images" class="form-label">
                            Product Photos
                        </label>

                        <input
                            type="file"
                            class="form-control"
                            id="images"
                            name="images[]"
                            accept="image/*"
                            multiple
                        >

                        <small class="text-muted">
                            You can select multiple photos.
                        </small>

                        <div class="row g-2 mt-3" id="imagesPreview"></div>

                        <div class="text-danger small" id="imagesError"></div>
                    </div>


                    <!-- Submit -->
                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="submitAdBtn"
                    >
                        Create Ad
                    </button>

                </form>

            </div>
        </div>

    </div>

    @vite(['resources/js/ads.js'])

</x-app-layout>
