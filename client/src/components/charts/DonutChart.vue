<script setup lang="ts">
import { computed } from 'vue'
import { useThemeStore } from '@/store/themeStore'

const props = defineProps<{
    data: { label: string; value: number }[]
}>()

const themeStore = useThemeStore()

const isDark = computed(() => themeStore.theme === 'dark')

const total = computed(() => props.data.reduce((s, d) => s + d.value, 0))

const series = computed(() => props.data.map(d => d.value))

const COLORS = [
    '#3b82f6',
    '#10b981',
    '#f59e0b',
    '#ef4444',
    '#8b5cf6',
    '#06b6d4',
]

const options = computed(() => ({
    chart: {
        type: 'donut',
        background: 'transparent',
        fontFamily: 'inherit',
        animations: { enabled: true, speed: 400 },
    },
    theme: { mode: isDark.value ? 'dark' : 'light' },
    labels: props.data.map(d => d.label),
    colors: COLORS,
    dataLabels: { enabled: false },
    legend: {
        position: 'bottom',
        fontSize: '14px',
        labels: {
            colors: isDark.value ? 'oklch(0.55 0.00 0)' : 'oklch(0.40 0.00 0)',
        },
        markers: { size: 7, shape: 'square' },
        itemMargin: { horizontal: 10, vertical: 5 },
    },
    plotOptions: {
        pie: {
            donut: {
                size: '60%',
                labels: {
                    show: true,
                    total: {
                        show: true,
                        label: 'Total',
                        fontSize: '14px',
                        color: isDark.value ? 'oklch(0.55 0.00 0)' : 'oklch(0.40 0.00 0)',
                        formatter: () => String(total.value),
                    },
                    value: {
                        fontSize: '22px',
                        fontWeight: 700,
                        color: isDark.value ? 'oklch(0.97 0.00 0)' : 'oklch(0.13 0.01 260)',
                    },
                },
            },
        },
    },
    stroke: { width: 0 },
    tooltip: {
        theme: isDark.value ? 'dark' : 'light',
    },
}))
</script>

<template>
    <div v-if="!total" class="flex items-center justify-center h-32 text-surface-400 text-sm">
        No data
    </div>
    <apexchart
        v-else
        type="donut"
        height="320"
        :options="options"
        :series="series"
    />
</template>
