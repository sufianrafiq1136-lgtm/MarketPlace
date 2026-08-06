// Category-page behavior is loaded after the document is ready.
document.addEventListener('DOMContentLoaded', () => {
    // Find the category-page elements. The JavaScript bundle is shared by all pages,
    // so we stop here when the current page does not contain the category table.
    const tableBody = document.getElementById('categoryTableBody');
    const categoryCount = document.getElementById('categoryCount');
    const searchInput = document.getElementById('search');

    if (!tableBody) return;

    // Ask Laravel for the category list. The browser automatically sends the current
    // login session because this is a same-origin request.
    fetch('/categories/data', {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
    })
        .then((response) => {
            // An unauthenticated request is redirected to the login page instead of returning JSON.
            if (!response.ok || response.redirected) {
                throw new Error('Please log in before viewing categories.');
            }
            return response.json();
        })
        .then((result) => {
            // The API returns its records inside the data property.
            const categories = result.data ?? [];
            if (categoryCount) categoryCount.textContent = categories.length;

            if (!categories.length) {
                tableBody.innerHTML = '<tr><td colspan="5" class="px-5 py-12 text-center text-sm text-gray-500">No categories found.</td></tr>';
                return;
            }
 
            tableBody.innerHTML = categories.map((category, index) => `
                <tr data-category="${`${category.name} ${category.slug}`.toLowerCase()}" class="transition hover:bg-gray-50">
                    <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-500">${index + 1}</td>
                    <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-gray-900">${category.name}</td>
                    <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-500"><span class="rounded-md bg-gray-100 px-2.5 py-1 font-mono text-xs">${category.slug}</span></td>
                    <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-500">${category.created_at ?? '—'}</td>
                    <td class="whitespace-nowrap px-5 py-4 text-right">
                        <button type="button" class="mr-2 rounded-md px-2.5 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-50"> Edit</button>
                        <button type="button" class="rounded-md px-2.5 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50"> Delete</button>
                    </td>
                </tr>`).join('');
        })
        .catch((error) => {
            tableBody.innerHTML = `<tr><td colspan="5" class="px-5 py-12 text-center text-sm text-red-600">${error.message}</td></tr>`;
        });

    // Filter already-rendered rows as the user types in the search box.
    searchInput?.addEventListener('input', (event) => {
        const query = event.target.value.toLowerCase().trim();
        tableBody.querySelectorAll('tr[data-category]').forEach((row) => {
            row.classList.toggle('hidden', !row.dataset.category.includes(query));
        });
    });

    const modal = document.getElementById('categoryModal');
    const form = document.getElementById('categoryForm');
    const nameInput = document.getElementById('categoryName');
    const slugInput = document.getElementById('categorySlug');
    const formError = document.getElementById('categoryFormError');
    // The modal is hidden by default. These handlers open and close it without a page reload.
    const closeModal = () => modal?.classList.add('hidden');

    document.getElementById('btnAddCategory')?.addEventListener('click', () => {
        modal?.classList.remove('hidden');
        nameInput?.focus();
    });
    document.getElementById('btnCloseCategoryModal')?.addEventListener('click', closeModal);
    document.getElementById('btnCancelCategory')?.addEventListener('click', closeModal);
    document.getElementById('categoryModalBackdrop')?.addEventListener('click', closeModal);

    // Submit the modal form as JSON to the Laravel store route.
    form?.addEventListener('submit', async (event) => {
        event.preventDefault();
        formError?.classList.add('hidden');

        try {
            const response = await fetch('/categories', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                },
                credentials: 'same-origin',
                body: JSON.stringify({ name: nameInput.value }),
            });

            if (!response.ok) {
                const result = await response.json();
                throw new Error(result.message || 'Unable to create category.');
            }

            // Reload the page so the newly saved category appears in the table and count.
            window.location.reload();
        } catch (error) {
            if (formError) {
                formError.textContent = error.message;
                formError.classList.remove('hidden');
            }
        }
    });
});
