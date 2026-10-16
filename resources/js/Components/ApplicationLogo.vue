<template>
    <svg
        viewBox="0 0 48 48"
        xmlns="http://www.w3.org/2000/svg"
        role="img"
        aria-label="SaúdeVue"
    >
        <defs>
            <linearGradient id="saudevue-brand" x1="0" y1="0" x2="48" y2="48" gradientUnits="userSpaceOnUse">
                <stop stop-color="#34d399" />
                <stop offset="1" stop-color="#0d9488" />
            </linearGradient>
        </defs>

        <rect x="2" y="2" width="44" height="44" rx="13" fill="url(#saudevue-brand)" />

        <!-- Batimento cardíaco: identidade de saúde, legível de 16px a 96px. -->
        <path
            d="M9 24.5h6.2l2.6-6.4 4.6 13.2 3.1-6.8H39"
            fill="none"
            stroke="#ffffff"
            stroke-width="3"
            stroke-linecap="round"
            stroke-linejoin="round"
            opacity="0.96"
        />
    </svg>
</template>
