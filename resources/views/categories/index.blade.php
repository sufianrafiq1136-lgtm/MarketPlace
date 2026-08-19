

<x-app-layout>
    {{-- The header slot is rendered by the shared authenticated layout. --}}
    <x-slot name="header">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <p class="text-sm font-medium text-indigo-600">Marketplace setup</p>
                <h2 class="mt-1 text-2xl font-bold tracking-tight text-gray-900">
                    Categories
                </h2>
            </div>

            @if(auth()->check() && auth()->user()?->is_admin)
                <button id="btnAddCategory" type="button"
                    class="inline-flex items-center justify-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10 4a.75.75 0 0 1 .75.75v4.5h4.5a.75.75 0 0 1 0 1.5h-4.5v4.5a.75.75 0 0 1-1.5 0v-4.5h-4.5a.75.75 0 0 1 0-1.5h4.5v-4.5A.75.75 0 0 1 10 4Z" />
                    </svg>
                    Add category
                </button>
            @endif
        </div>
    </x-slot>

    {{-- Main category-management content. --}}
    <div class="min-h-[calc(100vh-4rem)] bg-slate-50 py-8" data-can-manage="{{ auth()->check() && auth()->user()?->is_admin ? '1' : '0' }}">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            {{-- Summary card; JavaScript fills the count after loading the API data. --}}
            <div class="mb-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total categories</p>
                            <p id="categoryCount" class="mt-2 text-3xl font-bold text-gray-900">—</p>
                        </div>
                        <div class="rounded-lg bg-indigo-50 p-3 text-indigo-600">
                            <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4 6.75A2.75 2.75 0 0 1 6.75 4h10.5A2.75 2.75 0 0 1 20 6.75v10.5A2.75 2.75 0 0 1 17.25 20H6.75A2.75 2.75 0 0 1 4 17.25V6.75Z" />
                                <path stroke-linecap="round" d="M8 8h8M8 12h8M8 16h4" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Searchable category table. The tbody is filled by resources/js/app.js. --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
                <div class="flex flex-col gap-4 border-b border-gray-200 px-5 py-5 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">All categories</h3>
                        <p class="mt-1 text-sm text-gray-600">Manage the categories shown across your marketplace.</p>
                    </div>

                    <label class="relative block w-full sm:w-72">
                        <span class="sr-only">Search categories</span>
                            <svg class="pointer-events-none absolute left-3 top-1/2 h-5 w-5 -translate-y-1/2 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 1 0 3.465 9.77l2.632 2.633a.75.75 0 1 0 1.06-1.06l-2.632-2.633A5.5 5.5 0 0 0 9 3.5ZM5 9a4 4 0 1 1 8 0 4 4 0 0 1-8 0Z" clip-rule="evenodd" />
                            </svg>
                        <input id="search" type="search" placeholder="Search categories..."
                            class="w-full rounded-lg border-gray-300 bg-white py-2.5 pl-10 pr-3 text-sm text-gray-900 shadow-sm placeholder:text-gray-400 focus:border-indigo-500 focus:ring-indigo-500">
                    </label>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="w-16 px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">#</th>
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Category</th>
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Slug</th>
                                <th scope="col" class="px-5 py-3.5 text-left text-xs font-semibold uppercase tracking-wide text-gray-600">Created</th>
                                <th scope="col" class="px-5 py-3.5 text-right text-xs font-semibold uppercase tracking-wide text-gray-600">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categoryTableBody" class="divide-y divide-gray-100">
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-sm text-gray-600">Loading categories...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    {{-- Add category modal. It starts hidden and is opened by the Add category button. --}}
    @if(auth()->check() && auth()->user()?->is_admin)
    <div id="categoryModal" class="fixed inset-0 z-50 hidden" role="dialog" aria-modal="true" aria-labelledby="categoryModalTitle">
        <div id="categoryModalBackdrop" class="absolute inset-0 bg-gray-900/40"></div>
        <div class="relative flex min-h-full items-center justify-center p-4">
            <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl">
                <div class="flex items-start justify-between">
                    <div>
                        <h3 id="categoryModalTitle" class="text-lg font-semibold text-gray-900">Add category</h3>
                        <p class="mt-1 text-sm text-gray-500">Create a category for your marketplace listings.</p>
                    </div>
                    <button id="btnCloseCategoryModal" type="button" class="rounded-lg p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600" aria-label="Close modal">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path d="M4.22 4.22a.75.75 0 0 1 1.06 0L10 8.94l4.72-4.72a.75.75 0 1 1 1.06 1.06L11.06 10l4.72 4.72a.75.75 0 0 1-1.06 1.06L10 11.06l-4.72 4.72a.75.75 0 0 1-1.06-1.06L8.94 10 4.22 5.28a.75.75 0 0 1 0-1.06Z" /></svg>
                    </button>
                </div>

                <form id="categoryForm" class="mt-6 space-y-4">
                    <div>
                        <label for="categoryName" class="block text-sm font-medium text-gray-700">Category name</label>
                        <input id="categoryName" name="name" type="text" required autofocus placeholder="e.g. Electronics"
                            class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                         <label for="categorySlug" class="block text-sm font-medium text-gray-700">Category slug</label>
                        <input id="categorySlug" name="slug" type="text" required autofocus placeholder="e.g. electronics"
                            class="mt-1.5 block w-full rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <p id="categoryFormError" class="mt-2 hidden text-sm text-red-600"></p>
                    </div>
                    <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                        <button id="btnCancelCategory" type="button" class="rounded-lg border border-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Cancel</button>
                        <button id="btnSaveCategory" type="submit" class="rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Save category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

</x-app-layout>
