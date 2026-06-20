<script setup lang="ts">
defineProps<{
    memberName: string
    memberPhoto: string
    memberCode: string
    memberSlug: string
    membershipType: string
    startDate: string
    expiryDate: string
    gymLogo: string
    gymName: string
}>()
</script>

<template>
    <div
        class="flex flex-col overflow-hidden rounded-2xl shadow-2xl bg-white"
        style="width: 54mm; height: 85.6mm;"
    >
        <!-- Header: dark band with logo + gym name -->
        <div class="bg-[#0f172a] flex flex-col items-center justify-center py-2 px-4 relative">
            <!-- Decorative accent stripe -->
            <div
                class="absolute bottom-0 left-0 right-0 h-[2px]"
                style="background: linear-gradient(90deg, #f59e0b, #ea580c, #f59e0b);"
            ></div>
            <img
                v-if="gymLogo"
                :src="gymLogo"
                alt="gym logo"
                class="h-7 w-auto object-contain"
            />
            <div v-else class="h-7 w-16 rounded bg-white/10"></div>
            <p class="text-white text-[7px] font-bold uppercase tracking-widest mt-1 opacity-90">{{ gymName }}</p>
        </div>

        <!-- Photo -->
        <div class="flex justify-center mt-2">
            <div
                class="rounded-full overflow-hidden"
                style="width: 52px; height: 52px; padding: 2px; background: linear-gradient(135deg, #f59e0b, #ea580c);"
            >
                <div class="w-full h-full rounded-full overflow-hidden bg-gray-200">
                    <img
                        v-if="memberPhoto"
                        :src="memberPhoto"
                        alt="member photo"
                        class="w-full h-full object-cover"
                    />
                    <div v-else class="w-full h-full bg-[#0f172a]/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-[#0f172a]/40" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M12 12c2.7 0 4.8-2.1 4.8-4.8S14.7 2.4 12 2.4 7.2 4.5 7.2 7.2 9.3 12 12 12zm0 2.4c-3.2 0-9.6 1.6-9.6 4.8v2.4h19.2v-2.4c0-3.2-6.4-4.8-9.6-4.8z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Member name -->
        <p class="text-center text-[9px] font-black text-[#0f172a] mt-2 px-2 leading-tight tracking-wide uppercase">
            {{ memberName }}
        </p>

        <!-- Membership type badge -->
        <div class="flex justify-center mt-1">
            <span
                class="text-[5.5px] font-bold uppercase tracking-widest text-white px-2 py-0.5 rounded-full"
                style="background: linear-gradient(90deg, #f59e0b, #ea580c);"
            >
                {{ membershipType }}
            </span>
        </div>

        <!-- Accent divider -->
        <div class="mx-4 mt-2 mb-1.5 h-[1px] bg-gray-100"></div>

        <!-- Dates row -->
        <div class="flex justify-between px-3">
            <div>
                <p class="text-[5px] uppercase tracking-widest text-gray-400 font-semibold">Start Date</p>
                <p class="text-[6.5px] font-bold text-[#0f172a] mt-0.5">{{ startDate }}</p>
            </div>
            <div class="text-right">
                <p class="text-[5px] uppercase tracking-widest text-gray-400 font-semibold">Expires</p>
                <p class="text-[6.5px] font-bold text-[#0f172a] mt-0.5">{{ expiryDate }}</p>
            </div>
        </div>

        <!-- Member code -->
        <div class="mx-3 mt-2 rounded-lg bg-[#0f172a] py-1.5 px-2">
            <p class="text-center text-[11px] font-black text-white tracking-[0.2em]">{{ memberCode }}</p>
            <p class="text-center text-[4.5px] text-white/50 uppercase tracking-widest mt-0.5">Member Code</p>
        </div>

        <!-- QR code -->
        <div class="flex justify-center mt-1.5">
            <img
                :src="`https://api.qrserver.com/v1/create-qr-code/?data=${memberSlug}&size=300x300`"
                class="w-20 h-20"
                crossorigin="anonymous"
                alt="QR code"
            />
        </div>

        <!-- Footer -->
        <div
            class="mt-auto py-1.5"
            style="background: linear-gradient(90deg, #0f172a, #1e293b);"
        >
            <p class="text-center text-white text-[5px] uppercase tracking-widest font-semibold">
                Scan to Check In
            </p>
        </div>
    </div>
</template>
