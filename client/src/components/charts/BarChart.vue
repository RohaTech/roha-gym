<script setup lang="ts">
import { computed } from 'vue'
import { useThemeStore } from '@/store/themeStore'

const props = defineProps<{
    data: { label: string; value: number }[]
}>()

const themeStore = useThemeStore()

const isDark = computed(() => themeStore.theme === 'dark')

const series = computed(() => [{ name: 'Value', data: props.data.map(d => d.value) }])

const options = computed(() => ({
    chart: {
        type: 'bar',
        background: 'transparent',
        toolbar: { show: false },
        fontFamily: 'inherit',
        animations: { enabled: true, speed: 400 },
    },
    theme: { mode: isDark.value ? 'dark' : 'light' },
    plotOptions: {
        bar: {
            borderRadius: 4,
            columnWidth: '60%',
        },
    },
    dataLabels: { enabled: false },
    xaxis: {
        categories: props.data.map(d => d.label),
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: {
            style: {
                colors: isDark.value ? 'oklch(0.55 0.00 0)' : 'oklch(0.55 0.00 0)',
                fontSize: '13px',
            },
            rotate: -30,
            trim: true,
        },
    },
    yaxis: {
        labels: {
            style: {
                colors: isDark.value ? 'oklch(0.55 0.00 0)' : 'oklch(0.55 0.00 0)',
                fontSize: '13px',
            },
        },
    },
    grid: {
        borderColor: isDark.value ? 'oklch(0.23 0.01 260)' : 'oklch(0.88 0.00 0)',
        strokeDashArray: 4,
        xaxis: { lines: { show: false } },
    },
    colors: ['oklch(0.65 0.18 45)'],
    tooltip: {
        theme: isDark.value ? 'dark' : 'light',
    },
}))
</script>

<template>
    <div v-if="!data.length" class="flex items-center justify-center h-32 text-surface-400 text-sm">
        No data
    </div>
    <apexchart
        v-else
        type="bar"
        height="240"
        :options="options"
        :series="series"
    />
</template>
