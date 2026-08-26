<span {{ $attributes->merge(['class' => 'bazaar-brand']) }} aria-label="BazaarLink">
    <svg class="bazaar-brand-mark" viewBox="0 0 48 48" role="img" aria-hidden="true">
        <defs>
            <linearGradient id="bazaar-brand-gradient" x1="8" y1="4" x2="39" y2="45" gradientUnits="userSpaceOnUse">
                <stop stop-color="#11c6b3" />
                <stop offset="1" stop-color="#0a3380" />
            </linearGradient>
        </defs>
        <path d="M12 13h24l3 24c.2 3.4-2.4 6-5.8 6H14.8C11.4 43 8.8 40.4 9 37l3-24Z" fill="url(#bazaar-brand-gradient)" />
        <path d="M16 14V11a8 8 0 0 1 16 0v3" fill="none" stroke="#102b60" stroke-width="2.8" stroke-linecap="round" />
        <path d="m16 27 8-7 8 7v10H16V27Z" fill="none" stroke="white" stroke-width="2.3" stroke-linejoin="round" />
        <path d="M20 37v-5h8v5M21 28h.01M27 28h.01" stroke="white" stroke-width="2.3" stroke-linecap="round" />
    </svg>
    <span class="bazaar-brand-name"><strong>Bazaar</strong><em>Link</em></span>
</span>

<style>
    .bazaar-brand { align-items: center; display: inline-flex; gap: .45rem; text-decoration: none; }
    .bazaar-brand-mark { height: 2.15rem; width: 2.15rem; }
    .bazaar-brand-name { color: #102b60; font-size: 1.3rem; letter-spacing: -.04em; line-height: 1; }
    .bazaar-brand-name strong { font-weight: 800; }
    .bazaar-brand-name em { color: #08b8aa; font-style: normal; font-weight: 800; }
</style>
