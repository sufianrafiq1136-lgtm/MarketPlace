// ======================================
// Category Module
// ======================================

document.addEventListener('DOMContentLoaded', () => {

    init();

});

// ======================================
// Global Elements
// ======================================

let tableBody;
let categoryCount;
let searchInput;

let modal;
let form;

let nameInput;
let slugInput;

let formError;

let btnAddCategory;
let btnSaveCategory;

// Used later for Update
let editingCategoryId = null;
function init() {
    console.log('init called');
    tableBody = document.getElementById('categoryTableBody');

    if (!tableBody) return;

    categoryCount = document.getElementById('categoryCount');
    searchInput = document.getElementById('search');

    modal = document.getElementById('categoryModal');

    form = document.getElementById('categoryForm');

    nameInput = document.getElementById('categoryName');
    slugInput = document.getElementById('categorySlug');

    formError = document.getElementById('categoryFormError');

    btnAddCategory = document.getElementById('btnAddCategory');
    btnSaveCategory = document.getElementById('btnSaveCategory');

    attachEvents();

    loadCategories();

}
async function requestJson(url, options = {}) {

    const response = await fetch(url, {
        credentials: 'same-origin',

        headers: {
            Accept: 'application/json',
            ...(options.headers || {})
        },

        ...options
    });

    const result = await response.json();

    if (!response.ok || result.success === false) {

        throw result;

    }

    return result;

}
async function loadCategories() {

    try {

        const result = await requestJson('/categories/data');

        renderCategories(result.data);

    } catch (error) {

        showTableError(error.message || 'Unable to load categories.');

    }

}
function showTableError(message) {

    tableBody.innerHTML = `

        <tr>

            <td colspan="5"
                class="px-5 py-12 text-center text-red-600">

                ${message}

            </td>

        </tr>

    `;

}
function renderCategories(categories) {

    if (categoryCount) {
        categoryCount.textContent = categories.length;
    }

    if (!categories.length) {
        tableBody.innerHTML = `
            <tr>
                <td colspan="5"
                    class="px-5 py-12 text-center">
                    No categories found.
                </td>
            </tr>

        `;
        return;
    }

    let html = '';

    categories.forEach((category, index) => {
        html += `
            <tr
                data-category="${category.name.toLowerCase()} ${category.slug.toLowerCase()}">
                <td>${index + 1}</td>
                <td>${category.name}</td>
                <td>${category.slug}</td>
                <td>
                    ${new Date(category.created_at).toLocaleDateString()}
                </td>
                <td>
                    <button
                        class="edit-btn"
                        data-id="${category.id}">
                        Edit
                    </button>
                    <button
                        class="delete-btn"
                        data-id="${category.id}">
                        Delete
                    </button>
                </td>
            </tr>
        `;
    });
    tableBody.innerHTML = html;
}
function openModal() {

    modal.classList.remove('hidden');

    nameInput.focus();

}
function closeModal() {

    modal.classList.add('hidden');

}
function resetForm() {

    form.reset();

    formError.classList.add('hidden');

    formError.textContent = '';

    editingCategoryId = null;

    btnSaveCategory.textContent = 'Save Category';

}   

// ======================================
// Edit Category
// ======================================

async function editCategory(id) {

    try {

        const result = await requestJson(`/categories/${id}`);

        const category = result.data;

        editingCategoryId = category.id;

        nameInput.value = category.name;

        slugInput.value = category.slug;

        btnSaveCategory.textContent = 'Update Category';

        openModal();

    } catch (error) {

        alert(error.message || 'Unable to load category.');

    }

}
// ======================================
// Update Category
// ======================================

async function updateCategory() {

    const data = {
        name: nameInput.value.trim(),
        slug: slugInput.value.trim()
    };

    try {

        const result = await requestJson(
            `/categories/${editingCategoryId}`,
            {
                method: 'PUT',

                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': getCsrfToken()
                },

                body: JSON.stringify(data)
            }
        );

        closeModal();

        resetForm();

        await loadCategories();

        console.log(result.data);

    } catch (error) {

        showFormError(error);

    }

}

// ======================================
// Delete Category
// ======================================

async function deleteCategory(id) {

    const confirmed = confirm(
        'Are you sure you want to delete this category?'
    );

    if (!confirmed) {
        return;
    }

    try {

        await requestJson(`/categories/${id}`, {

            method: 'DELETE',

            headers: {
                'X-CSRF-TOKEN': getCsrfToken()
            }

        });

        // Refresh table without reloading the page
        await loadCategories();

    } catch (error) {

        alert(
            error.message || 'Unable to delete category.'
        );

    }

}
// ======================================
// Create Category
// ======================================

async function createCategory() {

    const data = {
        name: nameInput.value.trim(),
        slug: slugInput.value.trim()
    };
    try {
        const result = await requestJson('/categories', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': getCsrfToken()
            },

            body: JSON.stringify(data)
        });

        // Category created successfully
        closeModal();

        resetForm();

        // Reload table without refreshing the page
        await loadCategories();
        console.log(result.data);

    } catch (error) {

        showFormError(error);

    }

}
// ======================================
// CSRF Token
// ======================================

function getCsrfToken() {

    return document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute('content');

}
// ======================================
// Form Errors
// ====================================== 
function showFormError(error) {

    let message = 'Something went wrong.';

    if (error.errors) {

        const firstField = Object.keys(error.errors)[0];

        message = error.errors[firstField][0];

    } else if (error.message) {

        message = error.message;

    }

    formError.textContent = message;

    formError.classList.remove('hidden');

}
function searchCategories(query) {

    tableBody
        .querySelectorAll('tr[data-category]')
        .forEach(row => {

            row.classList.toggle(
                'hidden',
                !row.dataset.category.includes(query)
            );

        });

}
function attachEvents() {
    console.log('btnAddCategory', btnAddCategory);
    btnAddCategory.addEventListener('click', () => {
        resetForm();

        openModal();

    });

    document
        .getElementById('btnCancelCategory')
        ?.addEventListener('click', closeModal);

    document
        .getElementById('btnCloseCategoryModal')
        ?.addEventListener('click', closeModal);

    document
        .getElementById('categoryModalBackdrop')
        ?.addEventListener('click', closeModal);

    form?.addEventListener('submit', (event) => {
        event.preventDefault();

        if (editingCategoryId) {
            updateCategory();
            return;
        }

        createCategory();
    });

    tableBody.addEventListener('click', (event) => {
        const editButton = event.target.closest('.edit-btn');
        if (editButton) {
            const id = editButton.dataset.id;
            editCategory(id);
            return;
        }
    });
    tableBody.addEventListener('click', (event) => {
        const deleteButton = event.target.closest('.delete-btn');
        if (deleteButton) {
            const id = deleteButton.dataset.id;
            deleteCategory(id);
            return;
        }
    });

    searchInput?.addEventListener('input', e => {

        searchCategories(
            e.target.value
                .toLowerCase()
                .trim()
        );

    });

}
