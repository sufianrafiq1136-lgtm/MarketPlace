
// ======================================
// Ads Module
// ======================================

document.addEventListener('DOMContentLoaded', () => {
    loadAds();
    bindAdActions();
});

async function loadAds() {
    try {
        const response = await fetch('/ads/data');
       
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

    if (!container) {
        return;
    }

    container.innerHTML = '';

    if (ads.length === 0) {
        container.innerHTML = `
            <div class="col-12 text-center">
                <p>No ads found.</p>
            </div>
        `;
        return;
    }

    ads.forEach(ad => {
        const firstImage = ad.images && ad.images.length ? ad.images[0].image_path : null;

        const imageMarkup = firstImage
            ? `
                <img
                    src="/storage/${firstImage}"
                    class="card-img-top"
                    alt="${ad.title}"
                    style="height: 180px; object-fit: cover;"
                >
            `
            : `
                <div
                    class="card-img-top d-flex align-items-center justify-content-center text-white fw-bold"
                    style="height: 180px; background: linear-gradient(135deg, #60a5fa, #8b5cf6, #f472b6); font-size: 2rem; letter-spacing: 1px;"
                >
                    ${ad.title ? ad.title.charAt(0).toUpperCase() : 'A'}
                </div>
            `;

        const card = `
            <div class="col-12 col-sm-6 col-lg-3">

                <div class="ad-card card h-100 shadow-sm border-0 bg-white">
                    ${imageMarkup}

                    <div class="card-body p-4">
                        <div class="d-flex flex-column gap-2">
                            <h5 class="card-title mb-0">
                                ${ad.title}
                            </h5>

                            <h6 class="text-success mb-0">
                                Rs. ${ad.price}
                            </h6>
                        </div>

                        <p class="card-text mb-0">
                            ${ad.description ?? ''}
                        </p>

                        <div class="d-flex flex-column gap-2">
                            <p class="mb-0">
                                <strong>Category:</strong>
                                ${ad.category ? ad.category.name : 'N/A'}
                            </p>

                            <p class="mb-0">
                                <strong>City:</strong>
                                ${ad.city}
                            </p>

                            <p class="mb-0">
                                <strong>Condition:</strong>
                                ${ad.condition}
                            </p>
                        </div>

                        <div class="ad-buttons">
                            <button
                                class="btn btn-sm btn-primary flex-fill"
                                data-id="${ad.id}"
                                data-action="edit">
                                Edit
                            </button>

                            <button
                                class="btn btn-sm btn-danger flex-fill"
                                data-id="${ad.id}"
                                data-action="delete">
                                Delete
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        `;

        container.insertAdjacentHTML('beforeend', card);
    });
}

function bindAdActions() {
    document.addEventListener('click', async (event) => {
        const button = event.target.closest('[data-action]');

        if (!button) {
            return;
        }

        const adId = button.dataset.id;

        if (button.dataset.action === 'edit') {
            window.location.href = `/ads/${adId}/edit`;
            return;
        }

        if (button.dataset.action === 'delete') {
            const confirmed = window.confirm('Delete this ad? This cannot be undone.');
            if (!confirmed) {
                return;
            }

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
