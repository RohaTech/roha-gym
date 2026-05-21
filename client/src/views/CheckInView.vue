<script setup lang="ts">
import { ref } from 'vue'
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
  CameraOff
} from 'lucide-vue-next'

const checkInStore = useCheckInStore()
const { onDecode, onError, isOnCooldown, cameraError } = useQrScanner()
const { code, submit, validationError } = useManualCheckIn()

// Add basic active tab state
const activeTab = ref('qr')

</script>

<template>
  <div class="min-h-screen bg-surface-950 text-white p-6 md:p-12">
    <div class="max-w-2xl mx-auto">
      
      <!-- Header -->
      <div class="mb-8">
        <h1 class="text-3xl font-display font-black tracking-tight mb-2">
          {{ $lang.checkInTitle || 'Member Check-In' }}
        </h1>
        <p class="text-surface-400">
          {{ $lang.checkInSubtitle || 'Scan a QR code or manually enter a member ID to record attendance.' }}
        </p>
      </div>

      <!-- Main Interface -->
      <Tabs v-model="activeTab" class="w-full">
        <TabsList class="w-full grid grid-cols-2 mb-6 h-12 bg-surface-900 rounded-xl p-1">
          <TabsTrigger value="qr" class="rounded-lg data-[state=active]:bg-brand-500 data-[state=active]:text-white transition-all text-surface-400">
            <QrCode class="w-4 h-4 mr-2" />
            {{ $lang.qrScanner || 'QR Scanner' }}
          </TabsTrigger>
          <TabsTrigger value="manual" class="rounded-lg data-[state=active]:bg-brand-500 data-[state=active]:text-white transition-all text-surface-400">
            <Keyboard class="w-4 h-4 mr-2" />
            {{ $lang.manualEntry || 'Manual Entry' }}
          </TabsTrigger>
        </TabsList>

        <!-- QR Tab -->
        <TabsContent value="qr" class="space-y-4">
          <Card class="border-surface-800 bg-surface-900/50 glass">
            <CardHeader>
              <CardTitle class="text-lg text-white flex items-center gap-2">
                <QrCode class="w-5 h-5 text-brand-400" />
                {{ $lang.scanQrCode || 'Scan QR Code' }}
              </CardTitle>
              <CardDescription class="text-surface-400">
                {{ $lang.scanQrDesc || 'Hold the member\'s ID card or mobile screen up to the camera.' }}
              </CardDescription>
            </CardHeader>
            <CardContent>
              <div class="relative rounded-2xl overflow-hidden aspect-video bg-surface-950 border border-surface-800 flex items-center justify-center">
                
                <div v-if="cameraError" class="text-center p-6 text-danger-500">
                  <CameraOff class="w-10 h-10 mx-auto mb-3 opacity-80" />
                  <p class="font-medium mb-1">{{ $lang.cameraError || 'Camera Error' }}</p>
                  <p class="text-sm opacity-80">{{ cameraError }}</p>
                </div>
                
                <QrcodeStream
                  v-else
                  @detect="onDecode($event[0]?.rawValue || '')"
                  @error="onError"
                  class="w-full h-full object-cover"
                />

                <div v-if="isOnCooldown" class="absolute inset-0 bg-surface-950/80 backdrop-blur-sm flex items-center justify-center z-10">
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
          <Card class="border-surface-800 bg-surface-900/50 glass">
            <CardHeader>
              <CardTitle class="text-lg text-white flex items-center gap-2">
                <Keyboard class="w-5 h-5 text-brand-400" />
                {{ $lang.enterCode || 'Enter Member ID' }}
              </CardTitle>
              <CardDescription class="text-surface-400">
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
                    class="h-14 text-center text-2xl tracking-widest font-mono bg-surface-950 border-surface-700 text-white placeholder:text-surface-600 focus-visible:border-brand-500 focus-visible:ring-brand-500/30"
                  />
                  <p v-if="validationError" class="text-xs text-danger-500 flex items-center gap-1 justify-center mt-2 animate-scale-in">
                    <AlertCircle class="w-3 h-3" /> {{ validationError }}
                  </p>
                </div>
                <Button
                  type="submit"
                  class="w-full h-12 bg-gradient-to-r from-brand-500 to-energy-500 hover:from-brand-400 hover:to-energy-400 text-white rounded-xl font-semibold shadow-lg shadow-brand-500/20 hover:shadow-brand-500/40 transition-all border-0"
                >
                  {{ $lang.submitCheckIn || 'Record Check-In' }}
                </Button>
              </form>
            </CardContent>
          </Card>
        </TabsContent>
      </Tabs>

      <!-- Status Indicator Overlay -->
      <div v-if="checkInStore.status === 'success' || checkInStore.status === 'failed'" class="fixed bottom-6 right-6 left-6 md:left-auto z-50 animate-slide-up">
        
        <!-- Success Alert -->
        <div v-if="checkInStore.status === 'success' && checkInStore.lastResult?.success" class="bg-surface-900 border border-success-500/30 rounded-2xl p-5 shadow-2xl flex items-start gap-4 max-w-sm w-full relative overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-32 bg-success-500/10 blur-[40px] rounded-full pointer-events-none"></div>
          
          <div class="w-12 h-12 rounded-full bg-success-500/20 text-success-500 flex items-center justify-center shrink-0">
            <CheckCircle2 class="w-6 h-6" />
          </div>
          
          <div class="flex-1 min-w-0">
            <h3 class="text-success-500 font-bold mb-1">{{ $lang.checkInSuccess || 'Check-In Successful' }}</h3>
            <div class="flex items-center gap-3 mt-3">
               <img v-if="checkInStore.lastResult.member.photo" :src="checkInStore.lastResult.member.photo" class="w-10 h-10 rounded-full object-cover border border-surface-700" />
               <div v-else class="w-10 h-10 rounded-full bg-surface-800 flex items-center justify-center border border-surface-700">
                  <User class="w-5 h-5 text-surface-500" />
               </div>
               <div>
                  <p class="font-medium text-white truncate">{{ checkInStore.lastResult.member.name }}</p>
                  <p class="text-xs text-surface-400">{{ checkInStore.lastResult.member.remaining_days }} {{ $lang.daysRemaining || 'days remaining' }}</p>
               </div>
            </div>
          </div>
        </div>

        <!-- Failure Alert -->
        <div v-if="checkInStore.status === 'failed'" class="bg-surface-900 border border-danger-500/30 rounded-2xl p-5 shadow-2xl flex items-start gap-4 max-w-sm w-full relative overflow-hidden">
          <div class="absolute top-0 right-0 w-32 h-32 bg-danger-500/10 blur-[40px] rounded-full pointer-events-none"></div>
          
          <div class="w-10 h-10 rounded-full bg-danger-500/20 text-danger-500 flex items-center justify-center shrink-0 mt-1">
            <XCircle class="w-5 h-5" />
          </div>
          
          <div>
            <h3 class="text-danger-500 font-bold mb-1">{{ $lang.checkInFailed || 'Check-In Failed' }}</h3>
            <p class="text-sm text-surface-300">
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
    </div>
  </div>
</template>
