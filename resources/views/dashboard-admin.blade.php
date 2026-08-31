<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Admin Dashboard
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="grid gap-4 md:grid-cols-4 mb-6">
                <div class="rounded-2xl bg-gradient-to-br from-slate-900 to-slate-700 p-6 text-white shadow-lg">
                    <div class="text-sm text-slate-200">Users</div>
                    <div class="mt-2 text-3xl font-bold">{{ $stats['users'] }}</div>
                </div>
                <div class="rounded-2xl bg-gradient-to-br from-emerald-600 to-emerald-500 p-6 text-white shadow-lg">
                    <div class="text-sm text-emerald-50">Ads</div>
                    <div class="mt-2 text-3xl font-bold">{{ $stats['ads'] }}</div>
                </div>
                <div class="rounded-2xl bg-gradient-to-br from-amber-500 to-orange-500 p-6 text-white shadow-lg">
                    <div class="text-sm text-amber-50">Favorites</div>
                    <div class="mt-2 text-3xl font-bold">{{ $stats['favorites'] }}</div>
                </div>
                <div class="rounded-2xl bg-gradient-to-br from-rose-600 to-pink-500 p-6 text-white shadow-lg">
                    <div class="text-sm text-rose-50">Reports</div>
                    <div class="mt-2 text-3xl font-bold">{{ $stats['reports'] }}</div>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-slate-200">
                <div class="p-6 text-gray-900">
                    <div class="grid gap-6 lg:grid-cols-2">
                        <div>
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="h4 mb-0">Recent Reports</h3>
                                <a href="{{ route('categories.index') }}" class="btn btn-success btn-sm">Manage Categories</a>
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
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($recentReports as $report)
                                                <tr>
                                                    <td>{{ $report->ad?->title }}</td>
                                                    <td>{{ $report->user?->name }}</td>
                                                    <td><span class="badge bg-secondary">{{ $report->status }}</span></td>
                                                    <td>{{ \Illuminate\Support\Str::limit($report->reason, 90) }}</td>
                                                    <td>
                                                        @if ($report->ad)
                                                            <a href="{{ route('ads.show', $report->ad) }}" class="btn btn-outline-primary btn-sm">
                                                                Show Ad
                                                            </a>
                                                        @else
                                                            <span class="text-gray-400">Unavailable</span>
                                                        @endif
                                                    </td>
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
