<script setup lang="ts">
import { computed } from 'vue'
import {
    Users, UserCheck, UserX, UserPlus, DoorOpen, TrendingUp,
} from 'lucide-vue-next'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import BarChart from '@/components/charts/BarChart.vue'
import AreaChart from '@/components/charts/AreaChart.vue'
import DonutChart from '@/components/charts/DonutChart.vue'
import { useAnalytics } from '@/composables/useAnalytics'
import type { RangeOption } from '@/types/analytics'

const { data, isLoading, isError, range } = useAnalytics()

const RANGES: RangeOption[] = ['7d', '30d', '3m', '12m']

function formatHour(h: number): string {
    if (h === 0) return '12AM'
    if (h < 12) return `${h}AM`
    if (h === 12) return '12PM'
    return `${h - 12}PM`
}

const dailyCheckinsData = computed(() =>
    (data.value?.daily_checkins ?? []).map(d => ({ label: d.date.slice(5), value: Number(d.count) })),
)
const byDayData = computed(() =>
    (data.value?.checkins_by_day_of_week ?? []).map(d => ({ label: d.day, value: Number(d.count) })),
)
const byHourData = computed(() =>
    (data.value?.checkins_by_hour ?? []).map(d => ({ label: formatHour(Number(d.hour)), value: Number(d.count) })),
)
const methodData = computed(() => {
    const r = data.value?.checkin_method_ratio
    if (!r) return []
    return [
        { label: 'QR', value: r.qr },
        { label: 'Manual', value: r.manual },
    ]
})
const monthlyRegData = computed(() =>
    (data.value?.monthly_registrations ?? []).map(d => ({ label: d.month, value: Number(d.count) })),
)
const cumulativeData = computed(() =>
    (data.value?.cumulative_members ?? []).map(d => ({ label: d.month, value: Number(d.count) })),
)
const membershipDistData = computed(() =>
    (data.value?.membership_distribution ?? []).map(d => ({ label: d.type, value: d.count })),
)
</script>

<template>
    <section class="space-y-8">
        <!-- Header + Range Filter -->
        <div class="flex flex-wrap items-start justify-between gap-3">
            <div>
                <h2 class="text-xl font-semibold">{{ $lang.analyticsTitle }}</h2>
                <p class="text-sm text-surface-400">{{ $lang.analyticsSubtitle }}</p>
            </div>
            <div class="flex gap-1 rounded-lg border border-surface-200 dark:border-surface-700 p-1">
                <button
                    v-for="r in RANGES"
                    :key="r"
                    class="px-3 py-1 rounded-md text-xs font-medium transition-colors"
                    :class="range === r
                        ? 'bg-brand-500 text-white'
                        : 'text-surface-500 hover:text-surface-900 dark:hover:text-surface-100'"
                    @click="range = r"
                >{{ r }}</button>
            </div>
        </div>

        <div v-if="isLoading" class="text-surface-400 text-sm py-12 text-center">
            {{ $lang.analyticsLoading }}
        </div>
        <div v-else-if="isError" class="text-danger-500 text-sm py-12 text-center">
            {{ $lang.analyticsError }}
        </div>

        <template v-else-if="data">
            <!-- Snapshot Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-3 gap-4">
                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs text-surface-500 font-medium">{{ $lang.dashTotalMembers }}</CardTitle>
                        <Users class="size-4 text-surface-400" />
                    </CardHeader>
                    <CardContent><p class="text-2xl font-bold">{{ data.snapshot.total_members }}</p></CardContent>
                </Card>
                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs text-surface-500 font-medium">{{ $lang.dashActiveMembers }}</CardTitle>
                        <UserCheck class="size-4 text-surface-400" />
                    </CardHeader>
                    <CardContent><p class="text-2xl font-bold text-green-500">{{ data.snapshot.active_members }}</p></CardContent>
                </Card>
                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs text-surface-500 font-medium">{{ $lang.dashExpiredMembers }}</CardTitle>
                        <UserX class="size-4 text-surface-400" />
                    </CardHeader>
                    <CardContent><p class="text-2xl font-bold text-danger-500">{{ data.snapshot.expired_members }}</p></CardContent>
                </Card>
                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs text-surface-500 font-medium">{{ $lang.analyticsNewThisMonth }}</CardTitle>
                        <UserPlus class="size-4 text-surface-400" />
                    </CardHeader>
                    <CardContent><p class="text-2xl font-bold">{{ data.snapshot.new_this_month }}</p></CardContent>
                </Card>
                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs text-surface-500 font-medium">{{ $lang.dashCheckinsToday }}</CardTitle>
                        <DoorOpen class="size-4 text-surface-400" />
                    </CardHeader>
                    <CardContent><p class="text-2xl font-bold">{{ data.snapshot.checkins_today }}</p></CardContent>
                </Card>
                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs text-surface-500 font-medium">{{ $lang.analyticsAvgDailyCheckins }}</CardTitle>
                        <TrendingUp class="size-4 text-surface-400" />
                    </CardHeader>
                    <CardContent><p class="text-2xl font-bold">{{ data.snapshot.avg_daily_checkins }}</p></CardContent>
                </Card>
            </div>

            <!-- Attendance Section -->
            <div class="space-y-4">
                <h3 class="text-base font-semibold">{{ $lang.analyticsAttendance }}</h3>

                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader><CardTitle class="text-sm font-medium">{{ $lang.analyticsDailyCheckins }}</CardTitle></CardHeader>
                    <CardContent><AreaChart :data="dailyCheckinsData" /></CardContent>
                </Card>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <Card class="border-surface-200 dark:border-surface-800">
                        <CardHeader><CardTitle class="text-sm font-medium">{{ $lang.analyticsCheckinsByDay }}</CardTitle></CardHeader>
                        <CardContent><BarChart :data="byDayData" /></CardContent>
                    </Card>
                    <Card class="border-surface-200 dark:border-surface-800">
                        <CardHeader><CardTitle class="text-sm font-medium">{{ $lang.analyticsCheckinsByHour }}</CardTitle></CardHeader>
                        <CardContent><BarChart :data="byHourData" /></CardContent>
                    </Card>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <Card class="border-surface-200 dark:border-surface-800">
                        <CardHeader><CardTitle class="text-sm font-medium">{{ $lang.analyticsQrVsManual }}</CardTitle></CardHeader>
                        <CardContent class="flex justify-center"><DonutChart :data="methodData" /></CardContent>
                    </Card>
                </div>
            </div>

            <!-- Member Growth Section -->
            <div class="space-y-4">
                <h3 class="text-base font-semibold">{{ $lang.analyticsMemberGrowth }}</h3>

                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader><CardTitle class="text-sm font-medium">{{ $lang.analyticsMonthlyReg }}</CardTitle></CardHeader>
                    <CardContent><BarChart :data="monthlyRegData" /></CardContent>
                </Card>
                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader><CardTitle class="text-sm font-medium">{{ $lang.analyticsCumulative }}</CardTitle></CardHeader>
                    <CardContent><AreaChart :data="cumulativeData" /></CardContent>
                </Card>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <Card class="border-surface-200 dark:border-surface-800">
                        <CardHeader><CardTitle class="text-sm font-medium">{{ $lang.analyticsRetentionRate }}</CardTitle></CardHeader>
                        <CardContent class="flex flex-col items-center py-6 gap-1">
                            <p class="text-5xl font-bold text-brand-500">{{ data.retention_rate }}%</p>
                            <p class="text-sm text-surface-400">{{ $lang.analyticsMembersRenewed }}</p>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Membership Section -->
            <div class="space-y-4">
                <h3 class="text-base font-semibold">{{ $lang.analyticsMembership }}</h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <Card class="border-surface-200 dark:border-surface-800">
                        <CardHeader><CardTitle class="text-sm font-medium">{{ $lang.analyticsMembershipDist }}</CardTitle></CardHeader>
                        <CardContent class="flex justify-center"><DonutChart :data="membershipDistData" /></CardContent>
                    </Card>

                    <Card class="border-surface-200 dark:border-surface-800">
                        <CardHeader><CardTitle class="text-sm font-medium">{{ $lang.analyticsExpiringSoon }}</CardTitle></CardHeader>
                        <CardContent>
                            <p v-if="!data.expiring_soon.length" class="text-sm text-surface-400 py-4 text-center">
                                {{ $lang.dashNoExpiring }}
                            </p>
                            <ul v-else class="space-y-3">
                                <li
                                    v-for="m in data.expiring_soon"
                                    :key="m.name + m.days_left"
                                    class="flex items-center gap-3"
                                >
                                    <img
                                        v-if="m.photo"
                                        :src="m.photo"
                                        :alt="m.name"
                                        class="w-8 h-8 rounded-full object-cover shrink-0"
                                    />
                                    <div
                                        v-else
                                        class="w-8 h-8 rounded-full bg-surface-200 dark:bg-surface-700 shrink-0 flex items-center justify-center text-xs text-surface-500"
                                    >
                                        {{ m.name.charAt(0) }}
                                    </div>
                                    <span class="flex-1 text-sm truncate">{{ m.name }}</span>
                                    <span
                                        class="shrink-0 text-xs font-semibold px-2 py-0.5 rounded-full"
                                        :class="m.days_left <= 3 ? 'bg-danger-500/10 text-danger-500' : 'bg-yellow-500/10 text-yellow-500'"
                                    >{{ m.days_left }}d</span>
                                </li>
                            </ul>
                        </CardContent>
                    </Card>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <Card class="border-surface-200 dark:border-surface-800">
                        <CardHeader><CardTitle class="text-sm font-medium">{{ $lang.analyticsExpiredMonth }}</CardTitle></CardHeader>
                        <CardContent class="flex flex-col items-center py-6 gap-1">
                            <p class="text-5xl font-bold text-danger-500">{{ data.expired_this_month }}</p>
                            <p class="text-sm text-surface-400">{{ $lang.analyticsExpiredMonth }}</p>
                        </CardContent>
                    </Card>
                </div>
            </div>

            <!-- Member Activity Section -->
            <div class="space-y-4">
                <h3 class="text-base font-semibold">{{ $lang.analyticsMemberActivity }}</h3>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    <Card class="border-surface-200 dark:border-surface-800">
                        <CardHeader><CardTitle class="text-sm font-medium">{{ $lang.analyticsAvgCheckinsWeek }}</CardTitle></CardHeader>
                        <CardContent class="flex flex-col items-center py-6 gap-1">
                            <p class="text-5xl font-bold text-brand-500">{{ data.avg_checkins_per_member_per_week }}</p>
                            <p class="text-sm text-surface-400">{{ $lang.analyticsCheckins }} / member / week</p>
                        </CardContent>
                    </Card>
                </div>

                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader><CardTitle class="text-sm font-medium">{{ $lang.analyticsMostActive }}</CardTitle></CardHeader>
                    <CardContent>
                        <p v-if="!data.most_active_members.length" class="text-sm text-surface-400 py-4 text-center">
                            {{ $lang.analyticsNoData }}
                        </p>
                        <ul v-else class="space-y-3">
                            <li
                                v-for="(m, i) in data.most_active_members"
                                :key="m.name + i"
                                class="flex items-center gap-3"
                            >
                                <span class="w-5 text-xs font-bold text-surface-400 shrink-0 text-right">{{ i + 1 }}</span>
                                <img
                                    v-if="m.photo"
                                    :src="m.photo"
                                    :alt="m.name"
                                    class="w-8 h-8 rounded-full object-cover shrink-0"
                                />
                                <div
                                    v-else
                                    class="w-8 h-8 rounded-full bg-surface-200 dark:bg-surface-700 shrink-0 flex items-center justify-center text-xs text-surface-500"
                                >
                                    {{ m.name.charAt(0) }}
                                </div>
                                <span class="flex-1 text-sm font-medium truncate">{{ m.name }}</span>
                                <span class="shrink-0 text-xs font-semibold bg-brand-500/10 text-brand-500 px-2 py-0.5 rounded-full">
                                    {{ m.checkin_count }} {{ $lang.analyticsCheckins }}
                                </span>
                            </li>
                        </ul>
                    </CardContent>
                </Card>

                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader><CardTitle class="text-sm font-medium">{{ $lang.analyticsInactive }}</CardTitle></CardHeader>
                    <CardContent>
                        <p v-if="!data.inactive_members.length" class="text-sm text-surface-400 py-4 text-center">
                            {{ $lang.analyticsNoData }}
                        </p>
                        <ul v-else class="space-y-3">
                            <li
                                v-for="m in data.inactive_members"
                                :key="m.name"
                                class="flex items-center gap-3"
                            >
                                <img
                                    v-if="m.photo"
                                    :src="m.photo"
                                    :alt="m.name"
                                    class="w-8 h-8 rounded-full object-cover shrink-0"
                                />
                                <div
                                    v-else
                                    class="w-8 h-8 rounded-full bg-surface-200 dark:bg-surface-700 shrink-0 flex items-center justify-center text-xs text-surface-500"
                                >
                                    {{ m.name.charAt(0) }}
                                </div>
                                <span class="flex-1 text-sm truncate">{{ m.name }}</span>
                                <span class="shrink-0 text-xs font-medium bg-orange-500/10 text-orange-500 px-2 py-0.5 rounded-full">
                                    {{ $lang.analyticsNoCheckin14Days }}
                                </span>
                            </li>
                        </ul>
                    </CardContent>
                </Card>
            </div>
        </template>
    </section>
</template>
