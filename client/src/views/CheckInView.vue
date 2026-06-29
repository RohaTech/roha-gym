<script setup lang="ts">
import { ref, computed } from 'vue'
import { QrcodeStream } from 'vue-qrcode-reader'
import { useQrScanner } from '@/composables/useQrScanner'
import { useManualCheckIn } from '@/composables/useManualCheckIn'
import { useCheckInStore } from '@/store/checkIn'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/components/ui/card'
import {
  QrCode,
  Keyboard,
  CheckCircle2,
  XCircle,
  AlertCircle,
  Loader2,
  CameraOff,
  User,
  X,
  Clock,
  CalendarCheck,
  ShieldCheck,
} from 'lucide-vue-next'

const checkInStore = useCheckInStore()
const { onDecode, onError, isOnCooldown, cameraError, isPaused } = useQrScanner()
const { code, submit, validationError, clearError } = useManualCheckIn()

const activeTab = ref('qr')

const showModal = computed(
  () => checkInStore.status === 'success' || checkInStore.status === 'failed',
)

function formatDateTime(raw: string | null): string {
  if (!raw) return '—'
  const d = new Date(raw)
  return d.toLocaleString('en-US', {
    month: 'short',
    day: 'numeric',
    hour: 'numeric',
    minute: '2-digit',
    hour12: true,
  })
}

function daysColor(days: number): string {
  if (days > 14) return 'text-success-500'
  if (days > 7) return 'text-yellow-400'
  return 'text-danger-500'
}

</script>

<template>
  <div class="min-h-screen bg-surface-50 dark:bg-surface-950 text-surface-900 dark:text-white p-6 md:p-12">
    <div class="max-w-2xl mx-auto">
      
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-display font-black tracking-tight mb-2">
          {{ $lang.checkInTitle || 'Member Check-In' }}
        </h1>
        <p class="text-surface-500 dark:text-surface-400">
          {{ $lang.checkInSubtitle || 'Scan a QR code or manually enter a member ID to record attendance.' }}
        </p>
      </div>

      <!-- Main Interface -->
      <Tabs v-model="activeTab" class="w-full">
        <TabsList class="w-full grid grid-cols-2 mb-6 h-12 bg-surface-100 dark:bg-surface-900 rounded-xl p-1">
          <TabsTrigger value="qr" class="rounded-lg data-[state=active]:bg-brand-500 data-[state=active]:text-white transition-all text-surface-500 dark:text-surface-400">
            <QrCode class="w-4 h-4 mr-2" />
            {{ $lang.qrScanner || 'QR Scanner' }}
          </TabsTrigger>
          <TabsTrigger value="manual" class="rounded-lg data-[state=active]:bg-brand-500 data-[state=active]:text-white transition-all text-surface-500 dark:text-surface-400">
            <Keyboard class="w-4 h-4 mr-2" />
            {{ $lang.manualEntry || 'Manual Entry' }}
          </TabsTrigger>
        </TabsList>

        <!-- QR Tab -->
        <TabsContent value="qr" class="space-y-4">
          <Card class="border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900/50 glass">
            <CardHeader>
              <CardTitle class="text-lg text-surface-900 dark:text-white flex items-center gap-2">
                <QrCode class="w-5 h-5 text-brand-400" />
                {{ $lang.scanQrCode || 'Scan QR Code' }}
              </CardTitle>
              <CardDescription class="text-surface-500 dark:text-surface-400">
                {{ $lang.scanQrDesc || 'Hold the member\'s ID card or mobile screen up to the camera.' }}
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div class="relative rounded-2xl overflow-hidden aspect-square sm:aspect-video bg-surface-100 dark:bg-surface-950 border border-surface-200 dark:border-surface-800 flex items-center justify-center">
                
                <div v-if="cameraError" class="text-center p-6 text-danger-500">
                  <CameraOff class="w-10 h-10 mx-auto mb-3 opacity-80" />
                  <p class="font-medium mb-1">{{ $lang.cameraError || 'Camera Error' }}</p>
                  <p class="text-sm opacity-80">{{ cameraError }}</p>
                </div>
                
                <QrcodeStream
                  v-else
                  :paused="isPaused"
                  @detect="onDecode($event[0]?.rawValue || '')"
                  @error="onError"
                  class="w-full h-full object-cover"
                />

                <div v-if="isOnCooldown" class="absolute inset-0 bg-surface-100/80 dark:bg-surface-950/80 backdrop-blur-sm flex items-center justify-center z-10">
                  <div class="flex flex-col items-center gap-3">
                    <Loader2 class="w-8 h-8 text-brand-500 animate-spin" />
                    <span class="text-sm font-medium">{{ $lang.processing || 'Processing...' }}</span>
                  </div>
                </div>

                <!-- Scanner overlay frame -->
                <div class="absolute inset-0 border-4 border-surface-900/40 z-0 pointer-events-none">
                  <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-48 h-48 sm:w-64 sm:h-64 border-2 border-dashed border-brand-500/50 rounded-xl"></div>
                </div>

              </div>
            </CardContent>
          </Card>
        </TabsContent>

        <!-- Manual Tab -->
        <TabsContent value="manual" class="space-y-4">
          <Card class="border-surface-200 dark:border-surface-800 bg-white dark:bg-surface-900/50 glass">
            <CardHeader>
              <CardTitle class="text-lg text-surface-900 dark:text-white flex items-center gap-2">
                <Keyboard class="w-5 h-5 text-brand-400" />
                {{ $lang.enterCode || 'Enter Member ID' }}
              </CardTitle>
              <CardDescription class="text-surface-500 dark:text-surface-400">
                {{ $lang.enterCodeDesc || 'Type the 5-digit unique code shown on the member\'s profile.' }}
              </CardDescription>
            </CardHeader>
            <CardContent>
              <form @submit.prevent="submit" class="space-y-4">
                <div class="space-y-2">
                  <Input
                    v-model="code"
                    type="text"
                    maxlength="5"
                    :placeholder="$lang.codePlaceholder || 'e.g. 12345'"
                    class="h-14 text-center text-2xl tracking-widest font-mono uppercase bg-surface-50 dark:bg-surface-950 border-surface-300 dark:border-surface-700 text-surface-900 dark:text-white placeholder:text-surface-400 dark:placeholder:text-surface-600 focus-visible:border-brand-500 focus-visible:ring-brand-500/30"
                    @input="clearError"
                  />
                  <p v-if="validationError" class="text-xs text-danger-500 flex items-center gap-1 justify-center mt-2 animate-scale-in">
                    <AlertCircle class="w-3 h-3" /> {{ validationError }}
                  </p>
                </div>
                <Button
                  type="submit"
                  class="w-full h-12 bg-linear-to-r from-brand-500 to-energy-500 hover:from-brand-400 hover:to-energy-400 text-white rounded-xl font-semibold shadow-lg shadow-brand-500/20 hover:shadow-brand-500/40 transition-all border-0"
                >
                  {{ $lang.submitCheckIn || 'Record Check-In' }}
                </Button>
              </form>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>

    </div>

    <!-- Check-In Result Modal -->
    <Transition name="modal">
      <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <!-- Backdrop — clicking it closes the modal -->
        <div class="absolute inset-0 bg-black/60 backdrop-blur-sm" @click="checkInStore.reset()" />

        <!-- SUCCESS CARD -->
        <div
          v-if="checkInStore.status === 'success' && checkInStore.lastResult?.success"
          class="relative z-10 w-full max-w-xs sm:max-w-sm bg-white dark:bg-surface-900 rounded-2xl shadow-2xl overflow-hidden border border-surface-200 dark:border-surface-700"
        >
          <div class="h-1 w-full bg-linear-to-r from-success-500 to-emerald-400" />

          <!-- Close -->
          <button
            class="absolute top-2 right-2 w-7 h-7 rounded-full bg-surface-100 dark:bg-surface-800 hover:bg-surface-200 dark:hover:bg-surface-700 flex items-center justify-center text-surface-500 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white transition-colors"
            @click="checkInStore.reset()"
          >
            <X class="w-3.5 h-3.5" />
          </button>

          <div class="flex flex-col items-center pt-5 pb-4 px-4">
            <!-- Photo + badge -->
            <div class="relative mb-3">
              <img
                v-if="checkInStore.lastResult.member.photo"
                :src="checkInStore.lastResult.member.photo"
                class="w-16 h-16 rounded-full object-cover border-4 border-surface-800"
              />
              <div
                v-else
                class="w-16 h-16 rounded-full bg-surface-800 border-4 border-surface-700 flex items-center justify-center"
              >
                <User class="w-7 h-7 text-surface-500" />
              </div>
              <div class="absolute -bottom-2 left-1/2 -translate-x-1/2 bg-success-500 rounded-full px-2 py-0.5 flex items-center gap-1 shadow">
                <ShieldCheck class="w-2.5 h-2.5 text-white" />
                <span class="text-white text-[9px] font-bold uppercase tracking-wide whitespace-nowrap">Checked In</span>
              </div>
            </div>

            <h2 class="text-surface-900 dark:text-white font-bold text-base mt-1 text-center leading-tight">
              {{ checkInStore.lastResult.member.name }}
            </h2>

            <div class="w-full h-px bg-surface-200 dark:bg-surface-700 my-3" />

            <!-- Stats: 2 small tiles + last visit row -->
            <div class="w-full space-y-2">
              <div class="grid grid-cols-2 gap-2">
                <div class="bg-surface-100 dark:bg-surface-800 rounded-xl p-2.5 text-center">
                  <CalendarCheck class="w-3.5 h-3.5 mx-auto mb-1 text-surface-500 dark:text-surface-400" />
                  <p class="text-lg font-black leading-none" :class="daysColor(checkInStore.lastResult.member.remaining_days)">
                    {{ checkInStore.lastResult.member.remaining_days }}
                  </p>
                  <p class="text-[10px] text-surface-500 dark:text-surface-400 mt-0.5">Days Left</p>
                </div>
                <div class="bg-surface-100 dark:bg-surface-800 rounded-xl p-2.5 text-center">
                  <CheckCircle2 class="w-3.5 h-3.5 mx-auto mb-1 text-surface-500 dark:text-surface-400" />
                  <p class="text-lg font-black text-surface-900 dark:text-white leading-none">
                    {{ checkInStore.lastResult.member.today_count }}
                  </p>
                  <p class="text-[10px] text-surface-500 dark:text-surface-400 mt-0.5">Today</p>
                </div>
              </div>

              <div class="bg-surface-100 dark:bg-surface-800 rounded-xl px-3 py-2 flex items-center gap-2">
                <Clock class="w-3.5 h-3.5 text-surface-500 dark:text-surface-400 shrink-0" />
                <div class="min-w-0">
                  <p class="text-[10px] text-surface-500 dark:text-surface-400 uppercase tracking-wide">Last Visit</p>
                  <p class="text-xs font-semibold text-surface-900 dark:text-white truncate">
                    {{ checkInStore.lastResult.member.last_check_in
                      ? formatDateTime(checkInStore.lastResult.member.last_check_in)
                      : 'No previous visits' }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- FAILURE CARD -->
        <div
          v-else-if="checkInStore.status === 'failed'"
          class="relative z-10 w-full max-w-xs sm:max-w-sm bg-white dark:bg-surface-900 rounded-2xl shadow-2xl overflow-hidden border border-surface-200 dark:border-surface-700"
        >
          <div class="h-1 w-full bg-linear-to-r from-danger-500 to-red-400" />

          <button
            class="absolute top-2 right-2 w-7 h-7 rounded-full bg-surface-100 dark:bg-surface-800 hover:bg-surface-200 dark:hover:bg-surface-700 flex items-center justify-center text-surface-500 dark:text-surface-400 hover:text-surface-900 dark:hover:text-white transition-colors"
            @click="checkInStore.reset()"
          >
            <X class="w-3.5 h-3.5" />
          </button>

          <div class="flex flex-col items-center py-6 px-4 text-center">
            <div class="w-14 h-14 rounded-full bg-danger-500/15 border-2 border-danger-500/30 flex items-center justify-center mb-3">
              <XCircle class="w-7 h-7 text-danger-500" />
            </div>
            <h2 class="text-danger-500 font-bold text-base mb-1">Check-In Failed</h2>
            <p class="text-surface-600 dark:text-surface-300 text-sm leading-relaxed">
              <template v-if="checkInStore.failureReason === 'not_found'">
                {{ $lang.reasonNotFound || 'Member not found. Please verify the ID or QR code.' }}
              </template>
              <template v-else-if="checkInStore.failureReason === 'expired'">
                {{ $lang.reasonExpired || 'Membership has expired. Renewal required.' }}
              </template>
              <template v-else-if="checkInStore.failureReason === 'limit_reached'">
                {{ $lang.reasonLimit || 'Daily check-in limit reached for this membership.' }}
              </template>
              <template v-else>
                {{ $lang.reasonUnknown || 'An unknown error occurred.' }}
              </template>
            </p>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.2s ease;
}
.modal-enter-active .relative,
.modal-leave-active .relative {
  transition: transform 0.25s cubic-bezier(0.34, 1.56, 0.64, 1), opacity 0.2s ease;
}
.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}
.modal-enter-from .relative {
  transform: scale(0.88) translateY(16px);
  opacity: 0;
}
.modal-leave-to .relative {
  transform: scale(0.92);
  opacity: 0;
}
</style>
