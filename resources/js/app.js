import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

const tableBody = document.getElementById('categoryTableBody');
const categoryCount = document.getElementById('categoryCount');
const searchInput = document.getElementById('search');

async function loadCategories() {
    if (!tableBody) return;

    const response = await fetch('/categories/data', {
        headers: { Accept: 'application/json' },
        credentials: 'same-origin',
    });
    const result = await response.json();

    if (!result.success) {
        return ;
    }

    const categories = result.data;
    if (categoryCount) categoryCount.textContent = categories.length;

    if (categories.length === 0) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5" class="text-center">
                    No Categories Found
                </td>
            </tr>
        `;
        return;
    }

    let rows = '';

    categories.forEach((category, index) => {

        rows += `
            <tr data-category="${`${category.name} ${category.slug}`.toLowerCase()}" class="transition hover:bg-gray-50 dark:hover:bg-gray-700/30">
                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-500 dark:text-gray-400">${index + 1}</td>
                <td class="whitespace-nowrap px-5 py-4 text-sm font-semibold text-gray-900 dark:text-white">${category.name}</td>
                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-500 dark:text-gray-400"><span class="rounded-md bg-gray-100 px-2.5 py-1 font-mono text-xs dark:bg-gray-700">${category.slug}</span></td>
                <td class="whitespace-nowrap px-5 py-4 text-sm text-gray-500 dark:text-gray-400">${category.created_at ?? '—'}</td>
                <td class="whitespace-nowrap px-5 py-4 text-right">
                    <button class="mr-2 rounded-md px-2.5 py-1.5 text-xs font-semibold text-indigo-600 hover:bg-indigo-50 dark:text-indigo-400 dark:hover:bg-indigo-900/30">Edit</button>
                    <button class="rounded-md px-2.5 py-1.5 text-xs font-semibold text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/30">Delete</button>
                </td>
            </tr>
        `;

    });

    tableBody.innerHTML = rows;
}

// loadCategories();
document.addEventListener('DOMContentLoaded', ()=>{
loadCategories();
});

if (searchInput) {
    searchInput.addEventListener('input', (event) => {
        const query = event.target.value.toLowerCase().trim();
        tableBody?.querySelectorAll('tr[data-category]').forEach((row) => {
            row.classList.toggle('hidden', !row.dataset.category.includes(query));
        });
    });
}
