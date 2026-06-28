<script setup lang="ts">
import { ref, computed } from 'vue'
import html2canvas from 'html2canvas'
import { toast } from 'vue-sonner'
import { useMemberCard } from '@/composables/useMemberCard'
import MembershipCard from '@/components/membership/MembershipCard.vue'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'

const props = defineProps<{
  open: boolean
  memberId: number | null
}>()

const emit = defineEmits<{
  (e: 'update:open', value: boolean): void
}>()

const memberId = computed(() => props.memberId ?? 0)
const { data, isLoading, isError } = useMemberCard(memberId)

const downloading = ref(false)
const downloadRef = ref<HTMLElement | null>(null)

// Helper to convert OKLCH color to RGB
function oklchToRgb(l: number, c: number, h: number) {
  const hRad = (h * Math.PI) / 180
  const a = c * Math.cos(hRad)
  const b = c * Math.sin(hRad)

  const l_ = l + 0.3963377774 * a + 0.2158037573 * b
  const m_ = l - 0.1055613458 * a - 0.0638541728 * b
  const s_ = l - 0.0894841775 * a - 1.2914855480 * b

  const l_3 = l_ * l_ * l_
  const m_3 = m_ * m_ * m_
  const s_3 = s_ * s_ * s_

  const rLinear = +4.0767416621 * l_3 - 3.3077115913 * m_3 + 0.2309699292 * s_3
  const gLinear = -1.2684380046 * l_3 + 2.6097574011 * m_3 - 0.3413193965 * s_3
  const bLinear = -0.0041960863 * l_3 - 0.7034186147 * m_3 + 1.7068271700 * s_3

  const toSrgb = (val: number) => {
    const clamped = Math.max(0, Math.min(1, val))
    const srgbVal = clamped <= 0.0031308
      ? 12.92 * clamped
      : 1.055 * Math.pow(clamped, 1 / 2.4) - 0.055
    return Math.round(srgbVal * 255)
  }

  return {
    r: toSrgb(rLinear),
    g: toSrgb(gLinear),
    b: toSrgb(bLinear),
  }
}

function convertOklchToRgbStr(oklchStr: string): string {
  // Matches oklch(L C H) or oklch(L C H / A)
  const regex = /oklch\(\s*([0-9.]+)\s+([0-9.]+)\s+([0-9.]+)(?:\s*\/\s*([0-9.%]+))?\s*\)/g
  return oklchStr.replace(regex, (match, lStr, cStr, hStr, aStr) => {
    const l = parseFloat(lStr)
    const c = parseFloat(cStr)
    const h = parseFloat(hStr)
    let alpha = 1

    if (aStr) {
      if (aStr.endsWith('%')) {
        alpha = parseFloat(aStr) / 100
      } else {
        alpha = parseFloat(aStr)
      }
    }

    const rgb = oklchToRgb(l, c, h)
    if (alpha === 1) {
      return `rgb(${rgb.r}, ${rgb.g}, ${rgb.b})`
    } else {
      return `rgba(${rgb.r}, ${rgb.g}, ${rgb.b}, ${alpha})`
    }
  })
}

const patchGetComputedStyle = () => {
  const originalGetComputedStyle = window.getComputedStyle

  window.getComputedStyle = function (elt, pseudoElt) {
    const style = originalGetComputedStyle.call(this, elt, pseudoElt)

    return new Proxy(style, {
      get(target, prop) {
        const value = Reflect.get(target, prop)
        
        if (typeof value === 'function') {
          return function (...args: any[]) {
            const result = value.apply(target, args)
            if (typeof result === 'string' && result.includes('oklch')) {
              return convertOklchToRgbStr(result)
            }
            return result
          }
        }

        if (typeof value === 'string' && value.includes('oklch')) {
          return convertOklchToRgbStr(value)
        }
        return value
      },
    }) as any
  }

  return () => {
    window.getComputedStyle = originalGetComputedStyle
  }
}

const triggerDownload = async () => {
  console.log('MemberCardModal: triggerDownload called!')
  if (downloading.value || !data.value) {
    console.log('MemberCardModal: download blocked (already downloading or data empty)', { downloading: downloading.value, hasData: !!data.value })
    return
  }
  downloading.value = true
  const toastId = toast.loading('Generating card image...')
  const restoreStylePatch = patchGetComputedStyle()

  try {
    console.log('MemberCardModal: download target DOM element:', downloadRef.value)
    if (!downloadRef.value) {
      throw new Error('Render reference (downloadRef) not found!')
    }

    toast.info('Rendering card graphics...')
    console.log('MemberCardModal: running html2canvas conversion...')
    const canvas = await html2canvas(downloadRef.value, {
      useCORS: true,
      allowTaint: false,
      scale: 3.5, // Enhances quality for a sharp 300 DPI-like card print
      backgroundColor: null,
      logging: true // Enabled logging so warnings appear in browser devtools
    })

    console.log('MemberCardModal: html2canvas finished successfully, generating image URL')
    const dataUrl = canvas.toDataURL('image/png')
    const fileName = `${data.value.member.name.toLowerCase().replace(/[^a-z0-9]/g, '_')}_card.png`

    console.log('MemberCardModal: simulating file download click', { fileName, dataUrlLength: dataUrl.length })
    const link = document.createElement('a')
    link.download = fileName
    link.href = dataUrl
    document.body.appendChild(link)
    link.click()
    document.body.removeChild(link)

    toast.success('Card downloaded successfully', { id: toastId })
    console.log('MemberCardModal: download process completed successfully')
  } catch (err: any) {
    console.error('Failed to generate membership card PNG:', err)
    toast.error('Failed to download card image', { id: toastId })
    alert('Oops! Card download failed.\nError details: ' + (err?.message || err))
  } finally {
    restoreStylePatch()
    downloading.value = false
  }
}
</script>

<template>
  <Dialog :open="open" @update:open="(val) => emit('update:open', val)">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>Membership Card</DialogTitle>
      </DialogHeader>

      <div class="flex flex-col items-center gap-6 py-2">
        <div v-if="isLoading" class="text-muted-foreground text-sm">Loading card...</div>
        <div v-else-if="isError" class="text-danger-500 text-sm">
          Failed to load membership card.
        </div>

        <template v-else-if="data">
          <!-- Card Dialog Preview -->
          <div class="relative" style="width: calc(54mm * 1.6); height: calc(85.6mm * 1.6)">
            <div class="absolute top-0 left-0 origin-top-left" style="transform: scale(1.6)">
              <MembershipCard
                :member-name="data.member.name"
                :member-photo="data.member.photo_url ?? ''"
                :member-code="data.member.code"
                :member-slug="data.member.slug"
                :member-phone="data.member.phone ?? ''"
                :membership-type="data.member.membership_type"
                :start-date="data.member.start_date"
                :expiry-date="data.member.expiry_date"
                :gym-logo="data.gym.logo_url ?? ''"
                :gym-name="data.gym.name"
                @download="triggerDownload"
              />
            </div>
          </div>

          <!-- Hidden off-screen renderer for physical 2.125in x 4in PNG download -->
          <div
            class="fixed pointer-events-none select-none"
            style="left: -9999px; top: -9999px; width: 2.125in; height: 4in"
          >
            <div ref="downloadRef">
              <MembershipCard
                :member-name="data.member.name"
                :member-photo="data.member.photo_url ?? ''"
                :member-code="data.member.code"
                :member-slug="data.member.slug"
                :member-phone="data.member.phone ?? ''"
                :membership-type="data.member.membership_type"
                :start-date="data.member.start_date"
                :expiry-date="data.member.expiry_date"
                :gym-logo="data.gym.logo_url ?? ''"
                :gym-name="data.gym.name"
                :for-download="true"
              />
            </div>
          </div>
        </template>
      </div>
    </DialogContent>
  </Dialog>
</template>
