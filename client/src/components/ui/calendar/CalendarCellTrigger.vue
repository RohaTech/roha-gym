<script lang="ts" setup>
import type { CalendarCellTriggerProps } from "reka-ui"
import type { HTMLAttributes } from "vue"
import { reactiveOmit } from "@vueuse/core"
import { CalendarCellTrigger, useForwardProps } from "reka-ui"
import { cn } from "@/lib/utils"
import { buttonVariants } from '@/components/ui/button'

const props = withDefaults(defineProps<CalendarCellTriggerProps & { class?: HTMLAttributes["class"] }>(), {
  as: "button",
})

const delegatedProps = reactiveOmit(props, "class")

const forwardedProps = useForwardProps(delegatedProps)
</script>

<template>
  <CalendarCellTrigger
    data-slot="calendar-cell-trigger"
    :class="cn(
      buttonVariants({ variant: 'ghost' }),
      'size-8 p-0 font-normal aria-selected:opacity-100 cursor-default',
      '[&[data-today]:not([data-selected])]:bg-gold-500/20 [&[data-today]:not([data-selected])]:text-gold-400 [&[data-today]:not([data-selected])]:font-semibold',
      // Selected - Enhanced highlighting
      'data-selected:bg-brand-500 data-selected:text-white data-selected:font-bold data-selected:opacity-100 data-selected:shadow-md',
      '[&[data-selected]:hover]:bg-brand-600 data-selected:hover:text-white',
      'data-selected:focus:bg-brand-600 data-selected:focus:text-white data-selected:focus:ring-2 data-selected:focus:ring-brand-500 data-selected:focus:ring-offset-2',
      // Disabled
      'data-disabled:text-muted-foreground data-disabled:opacity-50',
      // Unavailable
      'data-unavailable:text-destructive-foreground data-unavailable:line-through',
      // Outside months
      'data-outside-view:text-muted-foreground data-outside-view:opacity-50',
      props.class,
    )"
    v-bind="forwardedProps"
  >
    <slot />
  </CalendarCellTrigger>
</template>
