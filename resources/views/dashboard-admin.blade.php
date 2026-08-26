<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid gap-4 md:grid-cols-4 mb-6">
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Users</div>
                    <div class="mt-2 text-3xl font-bold">{{ $stats['users'] }}</div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Ads</div>
                    <div class="mt-2 text-3xl font-bold">{{ $stats['ads'] }}</div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Favorites</div>
                    <div class="mt-2 text-3xl font-bold">{{ $stats['favorites'] }}</div>
                </div>
                <div class="rounded-lg bg-white p-6 shadow-sm">
                    <div class="text-sm text-gray-500">Reports</div>
                    <div class="mt-2 text-3xl font-bold">{{ $stats['reports'] }}</div>
                </div>
            </div>

            <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="h4 mb-0">Recent Reports</h3>
                                <a href="{{ route('categories.index') }}" class="btn btn-outline-primary btn-sm">Manage Categories</a>
                            </div>

                            @if ($recentReports->isEmpty())
                                <p class="text-gray-500 mb-0">No reports yet.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table align-middle">
                                        <thead>
                                            <tr>
                                                <th>Ad</th>
                                                <th>Reporter</th>
                                                <th>Status</th>
                                                <th>Reason</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recentReports as $report)
                                                <tr>
                                                    <td>{{ $report->ad?->title }}</td>
                                                    <td>{{ $report->user?->name }}</td>
                                                    <td><span class="badge bg-secondary">{{ $report->status }}</span></td>
                                                    <td>{{ \Illuminate\Support\Str::limit($report->reason, 90) }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
