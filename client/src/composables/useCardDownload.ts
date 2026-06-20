import { ref } from 'vue'
import type { Ref } from 'vue'

export function useCardDownload(cardRef: Ref<HTMLElement | null>, memberName: Ref<string>) {
    const isDownloading = ref(false)

    async function download() {
        if (!cardRef.value) return
        isDownloading.value = true
        try {
            // Pre-load all images as data-URIs so html2canvas can render them
            const images = cardRef.value.querySelectorAll<HTMLImageElement>('img')
            await Promise.all(
                Array.from(images).map(
                    (img) =>
                        new Promise<void>((resolve) => {
                            if (img.complete && img.naturalWidth > 0) { resolve(); return }
                            img.onload = () => resolve()
                            img.onerror = () => resolve()
                        })
                )
            )

            const html2canvas = (await import('html2canvas')).default
            const canvas = await html2canvas(cardRef.value, {
                useCORS: true,
                allowTaint: true,
                scale: 3,
                backgroundColor: '#ffffff',
                logging: false,
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
