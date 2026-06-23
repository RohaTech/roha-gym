<script setup lang="ts">
import { computed } from 'vue'
import { Users, UserCheck, UserX, DoorOpen } from 'lucide-vue-next'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import BarChart from '@/components/charts/BarChart.vue'
import { useDashboard } from '@/composables/useDashboard'

const { data, isLoading, isError } = useDashboard()

const snapshotCards = computed(() => {
    if (!data.value) return []
    return [
        {
            key: 'total',
            icon: Users,
            value: data.value.snapshot.total_members,
            colorClass: '',
        },
        {
            key: 'active',
            icon: UserCheck,
            value: data.value.snapshot.active_members,
            colorClass: 'text-green-500',
        },
        {
            key: 'expired',
            icon: UserX,
            value: data.value.snapshot.expired_members,
            colorClass: 'text-danger-500',
        },
        {
            key: 'checkins',
            icon: DoorOpen,
            value: data.value.snapshot.checkins_today,
            colorClass: '',
        },
    ]
})

const weeklyChartData = computed(() =>
    (data.value?.weekly_checkins ?? []).map(d => ({ label: d.date, value: d.count })),
)
</script>

<template>
    <section class="space-y-6">
        <div>
            <h2 class="text-xl font-semibold">{{ $lang.userDashboardTitle }}</h2>
            <p class="text-sm text-surface-400">{{ $lang.userDashboardSubtitle }}</p>
        </div>

        <div v-if="isLoading" class="text-surface-400 text-sm py-8 text-center">
            {{ $lang.analyticsLoading }}
        </div>
        <div v-else-if="isError" class="text-danger-500 text-sm py-8 text-center">
            {{ $lang.analyticsError }}
        </div>

        <template v-else-if="data">
            <!-- Snapshot Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs text-surface-500 font-medium">{{ $lang.dashTotalMembers }}</CardTitle>
                        <Users class="size-4 text-surface-400" />
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-bold">{{ data.snapshot.total_members }}</p>
                    </CardContent>
                </Card>
                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs text-surface-500 font-medium">{{ $lang.dashActiveMembers }}</CardTitle>
                        <UserCheck class="size-4 text-surface-400" />
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-bold text-green-500">{{ data.snapshot.active_members }}</p>
                    </CardContent>
                </Card>
                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs text-surface-500 font-medium">{{ $lang.dashExpiredMembers }}</CardTitle>
                        <UserX class="size-4 text-surface-400" />
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-bold text-danger-500">{{ data.snapshot.expired_members }}</p>
                    </CardContent>
                </Card>
                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs text-surface-500 font-medium">{{ $lang.dashCheckinsToday }}</CardTitle>
                        <DoorOpen class="size-4 text-surface-400" />
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-bold">{{ data.snapshot.checkins_today }}</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Weekly Check-ins Bar Chart -->
            <Card class="border-surface-200 dark:border-surface-800">
                <CardHeader>
                    <CardTitle class="text-sm font-medium">{{ $lang.dashWeeklyCheckins }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <BarChart :data="weeklyChartData" />
                </CardContent>
            </Card>

            <!-- Today's Check-ins + Expiring Soon -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Today's Check-ins -->
                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader>
                        <CardTitle class="text-sm font-medium">{{ $lang.dashTodaysCheckins }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p v-if="!data.recent_checkins.length" class="text-sm text-surface-400 py-4 text-center">
                            {{ $lang.dashNoCheckins }}
                        </p>
                        <ul v-else class="space-y-3">
                            <li
                                v-for="ci in data.recent_checkins"
                                :key="ci.member_name + ci.checked_in_at"
                                class="flex items-center gap-3"
                            >
                                <img
                                    v-if="ci.member_photo"
                                    :src="ci.member_photo"
                                    :alt="ci.member_name"
                                    class="w-8 h-8 rounded-full object-cover shrink-0"
                                />
                                <div
                                    v-else
                                    class="w-8 h-8 rounded-full bg-surface-200 dark:bg-surface-700 shrink-0 flex items-center justify-center text-xs text-surface-500"
                                >
                                    {{ ci.member_name.charAt(0) }}
                                </div>
                                <span class="flex-1 text-sm font-medium truncate">{{ ci.member_name }}</span>
                                <span class="text-xs text-surface-400 shrink-0">{{ ci.checked_in_at }}</span>
                            </li>
                        </ul>
                    </CardContent>
                </Card>

                <!-- Expiring Soon -->
                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader>
                        <CardTitle class="text-sm font-medium">{{ $lang.dashExpiringSoon }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p v-if="!data.expiring_soon.length" class="text-sm text-surface-400 py-4 text-center">
                            {{ $lang.dashNoExpiring }}
                        </p>
                        <ul v-else class="space-y-3">
                            <li
                                v-for="m in data.expiring_soon"
                                :key="m.name + m.expiry_date"
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
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium truncate">{{ m.name }}</p>
                                    <p class="text-xs text-surface-400">{{ m.expiry_date }}</p>
                                </div>
                                <span
                                    class="shrink-0 text-xs font-semibold px-2 py-0.5 rounded-full"
                                    :class="m.days_left <= 3 ? 'bg-danger-500/10 text-danger-500' : 'bg-yellow-500/10 text-yellow-500'"
                                >
                                    {{ m.days_left }}d
                                </span>
                            </li>
                        </ul>
                    </CardContent>
                </Card>
            </div>
        </template>
    </section>
</template>
