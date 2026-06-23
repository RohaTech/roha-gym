<script setup lang="ts">
defineProps<{
  memberName: string
  memberPhoto: string
  memberCode: string
  memberSlug: string
  memberPhone: string
  membershipType: string
  startDate: string
  gymLogo: string
  gymName: string
}>()
</script>

<template>
  <div
    class="flex flex-col overflow-hidden rounded-3xl shadow-2xl bg-white"
    style="width: 54mm; height: 85.6mm"
  >
    <!-- Header: orange band with circular logo + gym name -->
    <div
      class="bg-orange-500 flex items-center gap-2 px-3 rounded-t-3xl"
      style="padding-top: 3.5mm; padding-bottom: 3.5mm"
    >
      <div
        class="rounded-full overflow-hidden bg-white flex-shrink-0 border-2 border-white"
        style="width: 13mm; height: 13mm"
      >
        <img v-if="gymLogo" :src="gymLogo" alt="gym logo" class="w-full h-full object-cover" />
        <div v-else class="w-full h-full bg-orange-200"></div>
      </div>
      <p class="text-white font-black leading-tight" style="font-size: 9px">{{ gymName }}</p>
    </div>

    <!-- Body -->
    <div class="flex flex-col flex-1 px-3 pt-3 pb-2 bg-gray-50">
      <!-- Name + Photo row -->
      <div class="flex items-start justify-between">
        <div class="flex-1 pr-2">
          <p class="text-gray-400 font-medium" style="font-size: 5.5px">Members name</p>
          <p class="font-black text-gray-900 mt-0.5 leading-tight" style="font-size: 8.5px">
            {{ memberName }}
          </p>
          <p v-if="memberPhone" class="text-gray-500 mt-0.5" style="font-size: 6px">
            {{ memberPhone }}
          </p>
        </div>
        <div
          class="rounded-full overflow-hidden bg-gray-200 flex-shrink-0"
          style="width: 13mm; height: 13mm"
        >
          <img
            v-if="memberPhoto"
            :src="memberPhoto"
            alt="member photo"
            class="w-full h-full object-cover"
          />
          <div v-else class="w-full h-full bg-gray-300 flex items-center justify-center">
            <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
              <path
                d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"
              />
            </svg>
          </div>
        </div>
      </div>

      <!-- Divider -->
      <div class="h-px bg-gray-200 my-2"></div>

      <!-- Type + Code row -->
      <div class="flex justify-between">
        <div>
          <p class="text-gray-400 font-medium" style="font-size: 5px">Members Type</p>
          <p class="font-black text-gray-900 mt-0.5" style="font-size: 8px">{{ membershipType }}</p>
        </div>
        <div>
          <p class="text-gray-400 font-medium" style="font-size: 5px">Member Code</p>
          <p class="font-black text-gray-900 mt-0.5" style="font-size: 8px">{{ memberCode }}</p>
        </div>
      </div>

      <!-- Divider -->
      <div class="h-px bg-gray-200 my-2"></div>

      <!-- Member Since + Expiration Date -->
      <div class="flex justify-between">
        <div>
          <p class="text-gray-400 font-medium" style="font-size: 5px">Member Since</p>
          <p class="font-black text-gray-900 mt-0.5" style="font-size: 8px">{{ startDate }}</p>
        </div>
      </div>

      <!-- QR code in white rounded box -->
      <div class="flex justify-center mt-auto pt-2">
        <div class="bg-white rounded-2xl p-2 shadow-sm">
          <img
            :src="`https://api.qrserver.com/v1/create-qr-code/?data=${memberSlug}&size=300x300`"
            class="block"
            style="width: 20mm; height: 20mm"
            crossorigin="anonymous"
            alt="QR code"
          />
        </div>
      </div>
    </div>
  </div>
</template>
