<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
    data: { label: string; value: number }[]
}>()

const W = 500
const H = 200
const PAD = { top: 10, right: 8, bottom: 28, left: 34 }
const cw = W - PAD.left - PAD.right
const ch = H - PAD.top - PAD.bottom

const max = computed(() => Math.max(...props.data.map(d => d.value), 1))

const pts = computed(() => {
    const n = props.data.length
    if (n === 0) return []
    return props.data.map((d, i) => ({
        x: PAD.left + (n === 1 ? cw / 2 : (i / (n - 1)) * cw),
        y: PAD.top + ch - (d.value / max.value) * ch,
        label: d.label,
    }))
})

function smooth(points: { x: number; y: number }[]): string {
    if (points.length < 2) return points.length ? `M ${points[0].x} ${points[0].y}` : ''
    let d = `M ${points[0].x} ${points[0].y}`
    for (let i = 1; i < points.length; i++) {
        const mx = (points[i - 1].x + points[i].x) / 2
        d += ` C ${mx} ${points[i - 1].y} ${mx} ${points[i].y} ${points[i].x} ${points[i].y}`
    }
    return d
}

const linePath = computed(() => smooth(pts.value))

const areaPath = computed(() => {
    if (!pts.value.length) return ''
    const base = PAD.top + ch
    const first = pts.value[0]
    const last = pts.value[pts.value.length - 1]
    return `M ${first.x} ${base} L ${first.x} ${first.y} ${smooth(pts.value).slice(1)} L ${last.x} ${base} Z`
})

const yTicks = computed(() =>
    [0, 0.5, 1].map(t => ({
        y: PAD.top + ch - t * ch,
        label: Math.round(t * max.value),
    })),
)

// Show at most 7 x-axis labels evenly spaced
const xLabels = computed(() => {
    const n = pts.value.length
    if (n <= 7) return pts.value
    const step = Math.floor(n / 6)
    return pts.value.filter((_, i) => i % step === 0 || i === n - 1)
})
</script>

<template>
    <div v-if="!data.length" class="flex items-center justify-center h-32 text-surface-400 text-sm">
        No data
    </div>
    <svg v-else :viewBox="`0 0 ${W} ${H}`" class="w-full h-auto" preserveAspectRatio="xMidYMid meet">
        <defs>
            <linearGradient id="areaFill" x1="0" y1="0" x2="0" y2="1">
                <stop offset="0%" stop-color="var(--color-brand-500)" stop-opacity="0.25" />
                <stop offset="100%" stop-color="var(--color-brand-500)" stop-opacity="0" />
            </linearGradient>
        </defs>

        <g v-for="tick in yTicks" :key="tick.y">
            <line
                :x1="PAD.left" :y1="tick.y"
                :x2="W - PAD.right" :y2="tick.y"
                stroke="currentColor"
                class="text-surface-200 dark:text-surface-700"
                stroke-width="0.5"
            />
            <text
                :x="PAD.left - 4" :y="tick.y + 3"
                text-anchor="end"
                fill="currentColor"
                class="text-surface-400"
                style="font-size: 8px"
            >{{ tick.label }}</text>
        </g>

        <path :d="areaPath" fill="url(#areaFill)" />
        <path :d="linePath" fill="none" stroke="var(--color-brand-500)" stroke-width="1.5" stroke-linejoin="round" />

        <g v-for="pt in xLabels" :key="pt.x">
            <text
                :x="pt.x"
                :y="H - PAD.bottom + 12"
                text-anchor="middle"
                fill="currentColor"
                class="text-surface-400"
                style="font-size: 8px"
            >{{ pt.label }}</text>
        </g>
    </svg>
</template>
