<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
    data: { label: string; value: number }[]
}>()

const W = 500
const H = 200
const PAD = { top: 10, right: 8, bottom: 28, left: 30 }
const cw = W - PAD.left - PAD.right
const ch = H - PAD.top - PAD.bottom

const max = computed(() => Math.max(...props.data.map(d => d.value), 1))

const bars = computed(() => {
    const count = props.data.length
    if (!count) return []
    const gap = 4
    const bw = Math.max((cw - gap * (count + 1)) / count, 2)
    return props.data.map((d, i) => ({
        x: PAD.left + gap + i * (bw + gap),
        y: PAD.top + ch - (d.value / max.value) * ch,
        w: bw,
        h: Math.max((d.value / max.value) * ch, 0),
        label: d.label,
    }))
})

const yTicks = computed(() =>
    [0, 0.5, 1].map(t => ({
        y: PAD.top + ch - t * ch,
        label: Math.round(t * max.value),
    })),
)
</script>

<template>
    <div v-if="!data.length" class="flex items-center justify-center h-32 text-surface-400 text-sm">
        No data
    </div>
    <svg v-else :viewBox="`0 0 ${W} ${H}`" class="w-full h-auto" preserveAspectRatio="xMidYMid meet">
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

        <g v-for="bar in bars" :key="bar.label">
            <rect
                :x="bar.x" :y="bar.y"
                :width="bar.w" :height="bar.h"
                class="fill-brand-500"
                rx="2"
            />
            <text
                :x="bar.x + bar.w / 2"
                :y="H - PAD.bottom + 12"
                text-anchor="middle"
                fill="currentColor"
                class="text-surface-400"
                style="font-size: 8px"
            >{{ bar.label }}</text>
        </g>

        <line
            :x1="PAD.left" :y1="PAD.top + ch"
            :x2="W - PAD.right" :y2="PAD.top + ch"
            stroke="currentColor"
            class="text-surface-200 dark:text-surface-700"
            stroke-width="1"
        />
    </svg>
</template>
