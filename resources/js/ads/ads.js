// ======================================
// Ads Module
// ======================================

document.addEventListener('DOMContentLoaded', () => {
    loadAds();
    bindBrowseControls();
    bindCreateAdForm();
    bindEditAdForm();
    bindAdActions();
    bindImagePreview();
});

let allAds = [];

function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>'"]/g, (character) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', "'": '&#039;', '"': '&quot;' })[character]);
}

async function loadAds() {
    const container = document.getElementById('adsContainer');
    if (!container) {
        return;
    }

    const endpoint = container.dataset.endpoint || '/ads/data';

    try {
        const response = await fetch(endpoint, {
            headers: { 'Accept': 'application/json' },
        });

        if (!response.ok) {
            throw new Error('Failed to load ads');
        }

        const result = await response.json();

        if (result.success) {
            allAds = result.data;
            displayAds(allAds);
        }
    } catch (error) {
        console.error('Error loading ads:', error);
    }
}

function bindBrowseControls() {
    ['adSearch', 'adLocation', 'conditionFilter', 'priceFilter', 'adSort'].forEach((id) => {
        document.getElementById(id)?.addEventListener('input', filterAds);
        document.getElementById(id)?.addEventListener('change', filterAds);
    });

    document.getElementById('searchAds')?.addEventListener('click', filterAds);
    document.getElementById('clearFilters')?.addEventListener('click', () => {
        ['adSearch', 'adLocation'].forEach((id) => {
            const field = document.getElementById(id);
            if (field) field.value = '';
        });
        document.getElementById('conditionFilter').value = 'all';
        document.getElementById('priceFilter').value = 'all';
        displayAds(allAds);
    });

    document.querySelectorAll('[data-category]').forEach((button) => button.addEventListener('click', () => {
        document.querySelectorAll('[data-category]').forEach((item) => item.classList.remove('active'));
        button.classList.add('active');
        filterAds();
    }));
}

function filterAds() {
    const search = document.getElementById('adSearch')?.value.toLowerCase() || '';
    const location = document.getElementById('adLocation')?.value.toLowerCase() || '';
    const condition = document.getElementById('conditionFilter')?.value || 'all';
    const priceRange = document.getElementById('priceFilter')?.value || 'all';
    const category = document.querySelector('[data-category].active')?.dataset.category || 'all';

    const filteredAds = allAds.filter((ad) => {
        const haystack = `${ad.title} ${ad.description} ${ad.category?.name || ''}`.toLowerCase();
        const price = Number(ad.price);
        const [minimum, maximum] = priceRange === 'all' ? [0, Infinity] : priceRange.split('-').map(Number);

        return haystack.includes(search)
            && String(ad.city || '').toLowerCase().includes(location)
            && (condition === 'all' || ad.condition === condition)
            && price >= minimum
            && price <= maximum
            && (category === 'all' || String(ad.category?.name || '').toLowerCase().includes(category));
    });

    const sort = document.getElementById('adSort')?.value;
    filteredAds.sort((first, second) => (
        sort === 'price-low'
            ? Number(first.price) - Number(second.price)
            : sort === 'price-high'
                ? Number(second.price) - Number(first.price)
                : new Date(second.created_at) - new Date(first.created_at)
    ));

    displayAds(filteredAds);
}

function displayAds(ads) {
    const container = document.getElementById('adsContainer');
    if (!container) return;

    const canManage = container.dataset.canManage === '1';

    const resultsMeta = document.getElementById('resultsMeta');
    if (resultsMeta) resultsMeta.textContent = `${ads.length} ${ads.length === 1 ? 'listing' : 'listings'} available`;

    container.innerHTML = '';

    if (!ads.length) {
        container.innerHTML = '<div class="col-12 text-center py-5"><p class="text-muted">No listings match your search.</p></div>';
        return;
    }

    ads.forEach((ad) => {
        const firstImage = ad.images && ad.images.length ? ad.images[0].image_path : null;
        const title = escapeHtml(ad.title);
        const adUrl = `/ads/${ad.id}`;

        const imageMarkup = firstImage
            ? `<img src="/storage/${encodeURI(firstImage)}" class="card-img-top" alt="${title}" style="height: 180px; object-fit: cover;">`
            : `<div class="card-img-top d-flex align-items-center justify-content-center text-white fw-bold" style="height: 180px; background: linear-gradient(135deg, #356a57, #9cbf55); font-size: 2rem; letter-spacing: 1px;">${title ? title.charAt(0).toUpperCase() : 'A'}</div>`;

        container.insertAdjacentHTML(
            'beforeend',
            `<div class="col-12 col-sm-6 col-lg-3">
                <div class="ad-card card h-100 shadow-sm border-0 bg-white" role="link" tabindex="0" data-href="${adUrl}" aria-label="View ${title}">
                    ${imageMarkup}
                    <div class="card-body p-4">
                        <div class="d-flex flex-column gap-2">
                            <h5 class="card-title mb-0">${title}</h5>
                            <h6 class="text-success mb-0">Rs. ${Number(ad.price).toLocaleString()}</h6>
                        </div>
                        <p class="card-text mb-0 text-muted">${escapeHtml(ad.description).slice(0, 90)}${String(ad.description || '').length > 90 ? '...' : ''}</p>
                        <div class="d-flex flex-column gap-2">
                            <p class="mb-0 small text-muted">${escapeHtml(ad.category ? ad.category.name : 'Other')} · ${escapeHtml(ad.city)} · ${escapeHtml(ad.condition)}</p>
                        </div>
                        <div class="ad-buttons">
                            ${canManage ? `
                                <button class="btn btn-sm btn-primary flex-fill" data-id="${ad.id}" data-action="edit">Edit</button>
                                <button class="btn btn-sm btn-danger flex-fill" data-id="${ad.id}" data-action="delete">Delete</button>
                            ` : ''}
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
        if (button) {
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

            return;
        }

        const card = event.target.closest('.ad-card[data-href]');
        if (!card || event.target.closest('a, button')) {
            return;
        }

        window.location.href = card.dataset.href;
    });

    document.addEventListener('keydown', (event) => {
        const card = event.target.closest('.ad-card[data-href]');
        if (!card) return;

        if (event.key === 'Enter' || event.key === ' ') {
            event.preventDefault();
            window.location.href = card.dataset.href;
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
