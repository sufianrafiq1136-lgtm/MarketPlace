<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            My Ads
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 bg-white">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <div>
                            <h3 class="h4 mb-1">My Published Ads</h3>
                            <p class="text-muted mb-0">Only the ads created by your account are shown here.</p>
                        </div>
                        <a href="{{ route('ads.create') }}" class="btn btn-primary">Add New Ad</a>
                    </div>

                    <div class="row g-4" id="adsContainer" data-endpoint="{{ route('ads.my.data') }}" data-can-manage="{{ auth()->user()?->is_admin ? '1' : '0' }}">
                        <div class="col-12 text-sm text-gray-500">
                            Loading ads...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        #adsContainer .ad-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-radius: 18px;
        }

        #adsContainer .ad-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 1rem 2rem rgba(15, 23, 42, 0.12) !important;
        }

        #adsContainer .ad-card .card-body {
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
        }

        #adsContainer .ad-card .ad-buttons {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 0.25rem;
        }
    </style>

    @vite(['resources/js/ads.js'])
</x-app-layout>
