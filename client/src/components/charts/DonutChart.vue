<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
    data: { label: string; value: number }[]
}>()

const COLORS = ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#06b6d4']
const CX = 90, CY = 90, R = 72, IR = 46

function polar(angle: number, r: number) {
    const rad = ((angle - 90) * Math.PI) / 180
    return { x: CX + r * Math.cos(rad), y: CY + r * Math.sin(rad) }
}

function arc(start: number, end: number): string {
    if (end - start >= 360) end = start + 359.9
    const o1 = polar(start, R), o2 = polar(end, R)
    const i1 = polar(end, IR), i2 = polar(start, IR)
    const large = end - start > 180 ? 1 : 0
    return [
        `M ${o1.x} ${o1.y}`,
        `A ${R} ${R} 0 ${large} 1 ${o2.x} ${o2.y}`,
        `L ${i1.x} ${i1.y}`,
        `A ${IR} ${IR} 0 ${large} 0 ${i2.x} ${i2.y}`,
        'Z',
    ].join(' ')
}

const total = computed(() => props.data.reduce((s, d) => s + d.value, 0))

const segments = computed(() => {
    if (!total.value) return []
    let angle = 0
    return props.data.map((d, i) => {
        const sweep = (d.value / total.value) * 360
        const path = arc(angle, angle + sweep)
        angle += sweep
        return { path, color: COLORS[i % COLORS.length], label: d.label, value: d.value }
    })
})
</script>

<template>
    <div v-if="!total" class="flex items-center justify-center h-32 text-surface-400 text-sm">
        No data
    </div>
    <div v-else class="flex flex-col items-center gap-3">
        <svg viewBox="0 0 180 180" class="w-40 h-40 shrink-0">
            <path
                v-for="seg in segments"
                :key="seg.label"
                :d="seg.path"
                :fill="seg.color"
            />
            <text x="90" y="87" text-anchor="middle" fill="currentColor" class="text-surface-500" style="font-size:9px">Total</text>
            <text x="90" y="101" text-anchor="middle" fill="currentColor" class="text-surface-900 dark:text-surface-50 font-semibold" style="font-size:14px;font-weight:700">{{ total }}</text>
        </svg>
        <div class="flex flex-wrap justify-center gap-x-4 gap-y-1">
            <div v-for="seg in segments" :key="seg.label" class="flex items-center gap-1.5">
                <span class="inline-block w-2.5 h-2.5 rounded-sm" :style="`background:${seg.color}`" />
                <span class="text-xs text-surface-500">{{ seg.label }}</span>
                <span class="text-xs font-medium">{{ seg.value }}</span>
            </div>
        </div>
    </div>
</template>
