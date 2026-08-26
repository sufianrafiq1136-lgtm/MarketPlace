<x-app-layout>
    <div class="marketplace-page">
        <section class="marketplace-hero">
            <div class="marketplace-shell">
                <div class="hero-copy">
                    <p class="eyebrow">A better way to buy and sell locally</p>
                    <h1>Find your next <span>good thing.</span></h1>
                    <p class="hero-subtitle">Browse trusted listings from people in your community, all in one place.</p>
                </div>
                <div class="search-panel">
                    <label class="search-field" for="adSearch">
                        <span aria-hidden="true">⌕</span>
                        <input id="adSearch" type="search" placeholder="What are you looking for?" autocomplete="off">
                    </label>
                    <label class="location-field" for="adLocation">
                        <span aria-hidden="true">⌖</span>
                        <input id="adLocation" type="search" placeholder="Your city" autocomplete="off">
                    </label>
                    <button class="search-button" type="button" id="searchAds">Search</button>
                </div>
            </div>
        </section>

        <main class="marketplace-shell marketplace-content">
            <div class="category-strip" aria-label="Browse categories">
                <button class="category-pill active" type="button" data-category="all">All listings</button>
                <button class="category-pill" type="button" data-category="vehicles">Vehicles</button>
                <button class="category-pill" type="button" data-category="property">Property</button>
                <button class="category-pill" type="button" data-category="electronics">Electronics</button>
                <button class="category-pill" type="button" data-category="home">Home & garden</button>
                <button class="category-pill" type="button" data-category="fashion">Fashion</button>
            </div>

            <div class="listing-heading">
                <div>
                    <p class="eyebrow">Fresh from the community</p>
                    <h2>Explore all listings</h2>
                </div>
                <div class="listing-actions">
                    <select id="adSort" aria-label="Sort listings">
                        <option value="newest">Newest first</option>
                        <option value="price-low">Price: low to high</option>
                        <option value="price-high">Price: high to low</option>
                    </select>
                    <a href="{{ route('ads.create') }}" class="sell-button"><span aria-hidden="true">+</span> Sell an item</a>
                </div>
            </div>

            <div class="listing-layout">
                <aside class="filter-panel">
                    <div class="filter-title"><span>Refine results</span><button type="button" id="clearFilters">Clear</button></div>
                    <label for="conditionFilter">Condition</label>
                    <select id="conditionFilter">
                        <option value="all">Any condition</option>
                        <option value="new">Brand new</option>
                        <option value="used">Used</option>
                    </select>
                    <label for="priceFilter">Price range</label>
                    <select id="priceFilter">
                        <option value="all">Any price</option>
                        <option value="0-10000">Under Rs. 10,000</option>
                        <option value="10000-50000">Rs. 10,000 - 50,000</option>
                        <option value="50000-999999999">Over Rs. 50,000</option>
                    </select>
                    <p class="filter-note">Every listing is posted by a real local seller.</p>
                </aside>
                <div class="listing-results">
                    <div class="results-meta" id="resultsMeta">Loading listings...</div>
                    <div class="row g-4" id="adsContainer" data-endpoint="{{ route('ads.data') }}" data-can-manage="0">
                        <div class="col-12 text-sm text-gray-500">Loading listings...</div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <style>
        :root { --ink: #17221d; --muted: #68736d; --line: #dce5df; --mint: #cdebdc; --lime: #d9f36a; --paper: #f7faf6; }
        .marketplace-page { background: var(--paper); color: var(--ink); min-height: calc(100vh - 65px); font-family: 'Figtree', sans-serif; }
        .marketplace-shell { max-width: 1240px; margin: 0 auto; padding-left: 1.5rem; padding-right: 1.5rem; }
        .marketplace-hero { background: linear-gradient(115deg, #173d31 0%, #245947 57%, #77a15d 100%); color: white; padding: 4.75rem 0 5rem; position: relative; overflow: hidden; }
        .marketplace-hero:after { content: ''; position: absolute; width: 32rem; height: 32rem; border: 1px solid rgba(217,243,106,.28); border-radius: 50%; right: -8rem; top: -15rem; box-shadow: 0 0 0 3rem rgba(217,243,106,.04), 0 0 0 7rem rgba(217,243,106,.03); }
        .hero-copy { max-width: 620px; position: relative; z-index: 1; }
        .eyebrow { color: #7a9487; font-size: .72rem; font-weight: 700; letter-spacing: .12em; text-transform: uppercase; margin: 0 0 .8rem; }
        .marketplace-hero .eyebrow { color: var(--lime); }
        .hero-copy h1 { font-size: clamp(2.6rem, 5vw, 4.8rem); line-height: .98; letter-spacing: -.04em; margin: 0; font-weight: 700; }
        .hero-copy h1 span { color: var(--lime); }
        .hero-subtitle { color: #d3e3d9; font-size: 1.05rem; margin: 1.25rem 0 0; max-width: 450px; }
        .search-panel { display: flex; gap: .65rem; margin-top: 2.35rem; padding: .65rem; background: white; border-radius: 8px; max-width: 850px; position: relative; z-index: 1; box-shadow: 0 18px 45px rgba(10, 35, 24, .2); }
        .search-field, .location-field { display: flex; align-items: center; gap: .6rem; color: #799184; padding: 0 .85rem; flex: 1; border-right: 1px solid var(--line); }
        .search-field span, .location-field span { font-size: 1.45rem; }
        .search-field input, .location-field input { border: 0; outline: 0; width: 100%; color: var(--ink); padding: .75rem 0; }
        .search-button, .sell-button { border: 0; border-radius: 5px; background: var(--lime); color: var(--ink); font-weight: 700; padding: .75rem 1.5rem; text-decoration: none; white-space: nowrap; }
        .marketplace-content { padding-top: 2rem; padding-bottom: 4rem; }
        .category-strip { display: flex; gap: .6rem; overflow-x: auto; padding-bottom: 2.2rem; }
        .category-pill { border: 1px solid var(--line); border-radius: 999px; padding: .6rem 1rem; background: white; color: var(--muted); white-space: nowrap; font-size: .86rem; }
        .category-pill.active, .category-pill:hover { background: var(--ink); border-color: var(--ink); color: white; }
        .listing-heading { align-items: end; display: flex; justify-content: space-between; gap: 1rem; margin-bottom: 1.5rem; }
        .listing-heading h2 { font-size: 2rem; letter-spacing: -.03em; margin: 0; }
        .listing-actions { align-items: center; display: flex; gap: .75rem; }
        .listing-actions select, .filter-panel select { border: 1px solid var(--line); border-radius: 5px; padding: .7rem .85rem; background: white; color: var(--ink); }
        .sell-button { background: var(--ink); color: white; }
        .sell-button span { color: var(--lime); font-size: 1.2rem; margin-right: .3rem; }
        .listing-layout { display: grid; grid-template-columns: 210px 1fr; gap: 2rem; }
        .filter-panel { align-self: start; background: white; border: 1px solid var(--line); border-radius: 6px; padding: 1.2rem; }
        .filter-title { display: flex; justify-content: space-between; font-weight: 700; margin-bottom: 1.5rem; }
        .filter-title button { border: 0; background: none; color: #648c73; font-size: .76rem; }
        .filter-panel label { display: block; color: var(--muted); font-size: .78rem; font-weight: 600; margin: 1rem 0 .4rem; }
        .filter-panel select { width: 100%; font-size: .8rem; }
        .filter-note { border-top: 1px solid var(--line); color: var(--muted); font-size: .75rem; line-height: 1.5; margin: 1.4rem 0 0; padding-top: 1rem; }
        .results-meta { color: var(--muted); font-size: .8rem; margin-bottom: .8rem; }
        #adsContainer .ad-card { border: 1px solid var(--line) !important; border-radius: 6px; overflow: hidden; transition: transform .2s ease, box-shadow .2s ease; }
        #adsContainer .ad-card:hover { box-shadow: 0 14px 28px rgba(23,34,29,.12) !important; transform: translateY(-4px); }
        #adsContainer .ad-card .card-body { display: flex; flex-direction: column; gap: .65rem; }
        #adsContainer .ad-card .ad-buttons { display: flex; gap: .5rem; flex-wrap: wrap; margin-top: auto; padding-top: .5rem; }
        #adsContainer .ad-card .card-img-top { height: 190px !important; }
        #adsContainer .ad-card .card-title { font-size: 1rem; font-weight: 700; line-height: 1.25; }
        @media (max-width: 767px) { .marketplace-shell { padding-left: 1rem; padding-right: 1rem; } .marketplace-hero { padding: 3.2rem 0 3.5rem; } .search-panel { flex-direction: column; } .search-field, .location-field { border-right: 0; border-bottom: 1px solid var(--line); } .listing-heading { align-items: start; flex-direction: column; } .listing-actions { width: 100%; } .listing-actions select, .sell-button { flex: 1; } .listing-layout { display: block; } .filter-panel { margin-bottom: 1.5rem; } }
    </style>
        </div>
    </div>

    <style>
        #adsContainer .ad-card {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            border-radius: 18px;
        }

        #adsContainer .ad-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 1rem 2rem rgba(15, 23, 42, 0.12) !important;
        }

        #adsContainer .ad-card .card-body {
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
        }

        #adsContainer .ad-card .ad-buttons {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            margin-top: 0.25rem;
        }
    </style>

    @vite(['resources/js/ads.js'])
</x-app-layout>
