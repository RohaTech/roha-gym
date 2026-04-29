<script setup lang="ts">
const stack = [
  {
    name: 'Laravel',
    desc: 'PHP backend framework',
    version: 'v12',
    logo: 'https://laravel.com/img/logomark.min.svg',
  },
  {
    name: 'PostgreSQL',
    desc: 'Relational database',
    version: 'v17',
    logo: 'https://www.postgresql.org/media/img/about/press/elephant.png',
  },
  {
    name: 'Vue 3',
    desc: 'Frontend framework with Router & Pinia',
    version: 'v3.5',
    logo: 'https://vuejs.org/images/logo.png',
  },
  {
    name: 'Tailwind CSS',
    desc: 'Utility-first CSS framework',
    version: 'v4',
    logo: 'https://tailwindcss.com/favicons/favicon-32x32.png',
  },
  {
    name: 'Laravel Permission',
    desc: 'Roles & permissions by Spatie',
    version: 'v6',
    logo: 'https://spatie.be/images/spatie.svg',
  },
  {
    name: 'shadcn-vue',
    desc: 'Accessible UI component primitives',
    version: 'latest',
    logo: 'https://www.shadcn-vue.com/favicon.ico',
  },
  {
    name: 'TanStack Query',
    desc: 'Async state & data fetching',
    version: 'v5',
    logo: 'https://tanstack.com/favicon.ico',
  },
  {
    name: 'Zod',
    desc: 'TypeScript-first schema validation',
    version: 'v3',
    logo: 'https://zod.dev/_next/image?url=%2Flogo%2Flogo.png&w=48&q=100',
  },
]
</script>

<template>
  <main class="min-h-screen bg-white text-gray-900 flex flex-col items-center px-6 py-20">
    <!-- Hero -->
    <div class="text-center mb-14">
      <h1 class="text-4xl font-bold tracking-tight mb-3">Laravel · Vue Starter Kit</h1>
      <p class="text-gray-400 text-base max-w-md mx-auto">
        A full-stack starter kit built on a curated set of modern, production-ready tools.
      </p>
    </div>

    <!-- Stack Grid -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4 w-full max-w-4xl">
      <div
        v-for="item in stack"
        :key="item.name"
        class="bg-white border border-gray-200 rounded-2xl p-5 flex flex-col items-center text-center gap-3 hover:border-gray-400 transition-colors"
      >
        <img
          v-if="item.name !== 'Laravel Permission'"
          :src="item.logo"
          :alt="item.name + ' logo'"
          class="w-10 h-10 object-contain"
          loading="lazy"
        />

        <svg
          v-if="item.name === 'Laravel Permission'"
          width="50"
          height="50"
          viewBox="0 0 192 192"
          fill="none"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path
            d="M0 72C0 46.7976 0 34.1965 4.90471 24.5704C9.21901 16.1031 16.1031 9.21901 24.5704 4.90471C34.1965 0 46.7976 0 72 0H120C145.202 0 157.804 0 167.43 4.90471C175.897 9.21901 182.781 16.1031 187.095 24.5704C192 34.1965 192 46.7976 192 72V120C192 145.202 192 157.804 187.095 167.43C182.781 175.897 175.897 182.781 167.43 187.095C157.804 192 145.202 192 120 192H72C46.7976 192 34.1965 192 24.5704 187.095C16.1031 182.781 9.21901 175.897 4.90471 167.43C0 157.804 0 145.202 0 120V72Z"
            fill="#FF6E2B"
          ></path>
          <mask
            id="mask0_42_21141"
            style="mask-type: alpha"
            maskUnits="userSpaceOnUse"
            x="0"
            y="0"
            width="192"
            height="192"
          >
            <rect width="192" height="192" fill="url(#paint0_linear_42_21141)"></rect>
          </mask>
          <g mask="url(#mask0_42_21141)">
            <path
              d="M54.6052 65.7212C54.6052 129.032 95.8947 148.3 95.8947 148.3C95.8947 148.3 137.184 129.032 137.184 65.7212C115.163 65.7212 95.8947 43.7002 95.8947 43.7002C95.8947 43.7002 76.6263 65.7212 54.6052 65.7212Z"
              stroke="black"
              stroke-width="10"
              stroke-linecap="round"
            ></path>
            <path
              d="M80.7551 97.5852L92.1958 107.986L111.957 85.1045"
              stroke="black"
              stroke-width="10"
              stroke-linecap="round"
            ></path>
          </g>
          <defs>
            <linearGradient
              id="paint0_linear_42_21141"
              x1="96"
              y1="0"
              x2="96"
              y2="192"
              gradientUnits="userSpaceOnUse"
            >
              <stop stop-color="white"></stop>
              <stop offset="1" stop-color="white" stop-opacity="0.35"></stop>
            </linearGradient>
          </defs>
        </svg>
        <div>
          <div class="font-semibold text-gray-900 text-sm">{{ item.name }}</div>
          <div class="text-xs text-gray-400 mt-0.5">{{ item.version }}</div>
        </div>
        <p class="text-xs text-gray-500 leading-snug">{{ item.desc }}</p>
      </div>
    </div>

    <p class="text-gray-400 text-xs mt-16">
      Edit <code class="text-gray-500">HomeView.vue</code> to get started.
    </p>
  </main>
</template>
