import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

/**
 * Load categories from the server
 */
async function loadCategories() {

    try {

        const response = await fetch('/categories/data');
        const result = await response.json();

        if (!result.success) {
            console.error(result.errors);
            return;
        }

        renderCategories(result.data);

    } catch (error) {

        console.error('Error loading categories:', error);

    }

}

/**
 * Render categories in the table
 */
function renderCategories(categories) {

    const tableBody = document.getElementById('categoryTableBody');

    // If this page doesn't contain the table, do nothing.
    if (!tableBody) {
        return;
    }

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
            <tr>
                <td>${index + 1}</td>
                <td>${category.name}</td>
                <td>${category.slug}</td>
                <td>${new Date(category.created_at).toLocaleDateString()}</td>
                <td>
                    <button class="btn btn-warning btn-sm">Edit</button>
                    <button class="btn btn-danger btn-sm">Delete</button>
                </td>
            </tr>
        `;

    });

    tableBody.innerHTML = rows;

}

/**
 * Run after the page loads
 */
document.addEventListener('DOMContentLoaded', () => {

    loadCategories();

});