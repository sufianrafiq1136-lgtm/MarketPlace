// ======================================
// Ads Module
// ======================================

document.addEventListener('DOMContentLoaded', () => {
    loadAds();
    bindCreateAdForm();
    bindEditAdForm();
    bindAdActions();
    bindImagePreview();
});

async function loadAds() {
    const container = document.getElementById('adsContainer');
    if (!container) {
        return;
    }

    try {
        const response = await fetch('/ads/data', {
            headers: { 'Accept': 'application/json' },
        });

        if (!response.ok) {
            throw new Error('Failed to load ads');
        }

        const result = await response.json();

        if (result.success) {
            displayAds(result.data);
        }
    } catch (error) {
        console.error('Error loading ads:', error);
    }
}

function displayAds(ads) {
    const container = document.getElementById('adsContainer');
    if (!container) return;

    container.innerHTML = '';

    if (!ads.length) {
        container.innerHTML = '<div class="col-12 text-center"><p>No ads found.</p></div>';
        return;
    }

    ads.forEach((ad) => {
        const firstImage = ad.images && ad.images.length ? ad.images[0].image_path : null;
        const imageMarkup = firstImage
            ? `<img src="/storage/${firstImage}" class="card-img-top" alt="${ad.title}" style="height: 180px; object-fit: cover;">`
            : `<div class="card-img-top d-flex align-items-center justify-content-center text-white fw-bold" style="height: 180px; background: linear-gradient(135deg, #60a5fa, #8b5cf6, #f472b6); font-size: 2rem; letter-spacing: 1px;">${ad.title ? ad.title.charAt(0).toUpperCase() : 'A'}</div>`;

        container.insertAdjacentHTML(
            'beforeend',
            `<div class="col-12 col-sm-6 col-lg-3">
                <div class="ad-card card h-100 shadow-sm border-0 bg-white">
                    ${imageMarkup}
                    <div class="card-body p-4">
                        <div class="d-flex flex-column gap-2">
                            <h5 class="card-title mb-0">${ad.title}</h5>
                            <h6 class="text-success mb-0">Rs. ${ad.price}</h6>
                        </div>
                        <p class="card-text mb-0">${ad.description ?? ''}</p>
                        <div class="d-flex flex-column gap-2">
                            <p class="mb-0"><strong>Category:</strong> ${ad.category ? ad.category.name : 'N/A'}</p>
                            <p class="mb-0"><strong>City:</strong> ${ad.city}</p>
                            <p class="mb-0"><strong>Condition:</strong> ${ad.condition}</p>
                        </div>
                        <div class="ad-buttons">
                            <a class="btn btn-sm btn-outline-secondary flex-fill" href="/ads/${ad.id}">View</a>
                            <button class="btn btn-sm btn-primary flex-fill" data-id="${ad.id}" data-action="edit">Edit</button>
                            <button class="btn btn-sm btn-danger flex-fill" data-id="${ad.id}" data-action="delete">Delete</button>
                        </div>
                    </div>
                </div>
            </div>`
        );
    });
}

function bindAdActions() {
    document.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-action]');
        if (!button) return;

        const adId = button.dataset.id;

        if (button.dataset.action === 'edit') {
            window.location.href = `/ads/${adId}/edit`;
            return;
        }

        if (button.dataset.action === 'delete') {
            if (!window.confirm('Delete this ad? This cannot be undone.')) return;

            const response = await fetch(`/ads/${adId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    'Accept': 'application/json',
                },
            });

            if (!response.ok) {
                console.error('Failed to delete ad');
                return;
            }

            loadAds();
        }
    });
}

function bindCreateAdForm() {
    const form = document.getElementById('createAdForm');
    if (!form) return;

    form.addEventListener('submit', submitAdForm);
}

function bindEditAdForm() {
    const form = document.getElementById('editAdForm');
    if (!form) return;

    form.addEventListener('submit', submitEditForm);
}

function bindImagePreview() {
    const input = document.getElementById('images');
    const preview = document.getElementById('imagesPreview');

    if (!input || !preview) {
        return;
    }

    input.addEventListener('change', () => {
        preview.innerHTML = '';

        const files = Array.from(input.files || []);
        if (!files.length) {
            return;
        }

        files.forEach((file) => {
            const reader = new FileReader();

            reader.onload = (event) => {
                preview.insertAdjacentHTML(
                    'beforeend',
                    `<div class="col-6 col-md-3">
                        <div class="border rounded overflow-hidden bg-light">
                            <img src="${event.target.result}" alt="${file.name}" class="w-100" style="height: 140px; object-fit: cover;">
                        </div>
                    </div>`
                );
            };

            reader.readAsDataURL(file);
        });
    });
}

async function submitAdForm(event) {
    event.preventDefault();
    await submitAdRequest('/ads', 'POST', 'Creating...', 'Create Ad', event.currentTarget);
}

async function submitEditForm(event) {
    event.preventDefault();
    const form = event.currentTarget;
    const adId = form.dataset.adId;
    await submitAdRequest(`/ads/${adId}`, 'POST', 'Updating...', 'Update Ad', form);
}

async function submitAdRequest(url, method, loadingLabel, idleLabel, form) {
    const formData = new FormData(form);
    const submitBtn = document.getElementById('submitAdBtn');

    clearErrors();
    setButtonLoading(submitBtn, true, loadingLabel);

    try {
        const response = await fetch(url, {
            method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json',
            },
            body: formData,
        });

        const payload = await response.json();

        if (!response.ok) {
            showErrors(payload.errors || {});
            throw new Error(payload.message || 'Request failed.');
        }

        window.location.href = '/ads';
    } catch (error) {
        console.error(error);
    } finally {
        setButtonLoading(submitBtn, false, idleLabel);
    }
}

function clearErrors() {
    document.querySelectorAll('[id$="Error"]').forEach((node) => {
        node.textContent = '';
    });
}

function showErrors(errors) {
    Object.entries(errors).forEach(([field, messages]) => {
        const node = document.getElementById(`${field}Error`);
        if (node) {
            node.textContent = messages[0];
        }
    });
}

function setButtonLoading(button, loading, label) {
    if (!button) return;
    if (!button.dataset.originalText) {
        button.dataset.originalText = button.textContent || label;
    }
    button.disabled = loading;
    button.textContent = loading ? label : button.dataset.originalText || label;
}
