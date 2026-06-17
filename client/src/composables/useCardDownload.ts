import { ref } from 'vue'
import type { Ref } from 'vue'

export function useCardDownload(cardRef: Ref<HTMLElement | null>, memberName: Ref<string>) {
    const isDownloading = ref(false)

    async function download() {
        if (!cardRef.value) return
        isDownloading.value = true
        try {
            const html2canvas = (await import('html2canvas')).default
            const canvas = await html2canvas(cardRef.value, {
                useCORS: true,
                scale: 3,
                backgroundColor: null,
            })
            const link = document.createElement('a')
            link.download = `membership-card-${memberName.value}.png`
            link.href = canvas.toDataURL('image/png')
            link.click()
        } finally {
            isDownloading.value = false
        }
    }

    return { download, isDownloading }
}
