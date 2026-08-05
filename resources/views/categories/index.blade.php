<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-indigo-600 dark:text-indigo-400">Marketplace setup</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-gray-900 dark:text-white">
                    Categories
                </h2>
            </div>

            <button id="btnAddCategory" type="button"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800">
                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10 4a.75.75 0 0 1 .75.75v4.5h4.5a.75.75 0 0 1 0 1.5h-4.5v4.5a.75.75 0 0 1-1.5 0v-4.5h-4.5a.75.75 0 0 1 0-1.5h4.5v-4.5A.75.75 0 0 1 10 4Z" />
                </svg>
                Add category
            </button>
        </div>
    </x-slot>

    <div class="min-h-[calc(100vh-4rem)] bg-slate-50 py-8 dark:bg-gray-950">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm dark:border-gray-700 dark:bg-gray-800">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Total categories</p>
                            <p id="categoryCount" class="mt-2 text-3xl font-bold text-gray-900 dark:text-white">—</p>
                        </div>
                        <div class="rounded-lg bg-indigo-50 p-3 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6.75A2.75 2.75 0 0 1 6.75 4h10.5A2.75 2.75 0 0 1 20 6.75v10.5A2.75 2.75 0 0 1 17.25 20H6.75A2.75 2.75 0 0 1 4 17.25V6.75Z" />
                                <path stroke-linecap="round" d="M8 8h8M8 12h8M8 16h4" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm dark:border-gray-700 dark:bg-gray-800">
                <div class="flex flex-col gap-4 border-b border-gray-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between dark:border-gray-700">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">All categories</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage the categories shown across your marketplace.</p>
                    </div>

                    <label class="relative block w-full sm:w-72">
                        <span class="sr-only">Search categories</span>
                        <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.465 9.77l2.632 2.633a.75.75 0 1 0 1.06-1.06l-2.632-2.633A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                        </svg>
                        <input id="search" type="search" placeholder="Search categories..."
                            class="w-full rounded-lg border-gray-300 py-2.5 pl-10 pr-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white">
                    </label>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700/40">
                            <tr>
                                <th scope="col" class="w-16 px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">#</th>
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Category</th>
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Slug</th>
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Created</th>
                                <th scope="col" class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categoryTableBody" class="divide-y divide-gray-100 dark:divide-gray-700">
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-sm text-gray-500 dark:text-gray-400">Loading categories...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
