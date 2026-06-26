<script setup lang="ts">
import { computed } from 'vue'
import { Building2, Activity, TrendingUp, Users, UserCheck, DoorOpen, ShieldOff, Clock } from 'lucide-vue-next'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import BarChart from '@/components/charts/BarChart.vue'
import AreaChart from '@/components/charts/AreaChart.vue'
import { useAdminDashboard } from '@/composables/useAdminDashboard'

const { data, isLoading, isError } = useAdminDashboard()

const snapshotCards = computed(() => {
    if (!data.value) return []
    return [
        { labelKey: 'adminDashTotalGyms',     icon: Building2,  value: data.value.snapshot.total_gyms,        color: '' },
        { labelKey: 'adminDashActiveGyms',     icon: Activity,   value: data.value.snapshot.active_gyms,       color: 'text-green-500' },
        { labelKey: 'adminDashSuspendedGyms',  icon: ShieldOff,  value: data.value.snapshot.suspended_gyms,    color: 'text-yellow-500' },
        { labelKey: 'adminDashPendingGyms',    icon: Clock,      value: data.value.snapshot.pending_gyms,      color: 'text-blue-500' },
        { labelKey: 'adminDashNewThisMonth',   icon: TrendingUp, value: data.value.snapshot.new_gyms_this_month, color: 'text-brand-500' },
        { labelKey: 'adminDashTotalMembers',   icon: Users,      value: data.value.snapshot.total_members,     color: '' },
        { labelKey: 'adminDashActiveMembers',  icon: UserCheck,  value: data.value.snapshot.active_members,    color: 'text-green-500' },
        { labelKey: 'adminDashCheckinsToday',  icon: DoorOpen,   value: data.value.snapshot.checkins_today,    color: '' },
    ]
})

const gymGrowthData = computed(() =>
    (data.value?.gyms_growth ?? []).map(d => ({ label: d.month, value: d.count })),
)

const memberGrowthData = computed(() =>
    (data.value?.members_growth ?? []).map(d => ({ label: d.month, value: d.count })),
)

const checkinsTrendData = computed(() =>
    (data.value?.checkins_trend ?? []).map(d => ({ label: d.date, value: d.count })),
)
</script>

<template>
    <section class="space-y-6">
        <div>
            <h2 class="text-xl font-semibold">{{ $lang.adminDashboardTitle }}</h2>
            <p class="text-sm text-surface-400">{{ $lang.adminDashboardSubtitle }}</p>
        </div>

        <div v-if="isLoading" class="text-surface-400 text-sm py-8 text-center">
            {{ $lang.adminDashLoading }}
        </div>
        <div v-else-if="isError" class="text-danger-500 text-sm py-8 text-center">
            {{ $lang.adminDashError }}
        </div>

        <template v-else-if="data">
            <!-- Snapshot Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-4">
                <Card
                    v-for="card in snapshotCards"
                    :key="card.labelKey"
                    class="border-surface-200 dark:border-surface-800"
                >
                    <CardHeader class="flex flex-row items-center justify-between pb-2">
                        <CardTitle class="text-xs text-surface-500 font-medium">{{ $lang[card.labelKey] }}</CardTitle>
                        <component :is="card.icon" class="size-4 text-surface-400" />
                    </CardHeader>
                    <CardContent>
                        <p class="text-2xl font-bold" :class="card.color">{{ card.value }}</p>
                    </CardContent>
                </Card>
            </div>

            <!-- Growth Charts -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader>
                        <CardTitle class="text-sm font-medium">{{ $lang.adminDashGymGrowth }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <BarChart :data="gymGrowthData" />
                    </CardContent>
                </Card>

                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader>
                        <CardTitle class="text-sm font-medium">{{ $lang.adminDashMemberGrowth }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <BarChart :data="memberGrowthData" />
                    </CardContent>
                </Card>
            </div>

            <!-- Check-ins Trend -->
            <Card class="border-surface-200 dark:border-surface-800">
                <CardHeader>
                    <CardTitle class="text-sm font-medium">{{ $lang.adminDashCheckinsTrend }}</CardTitle>
                </CardHeader>
                <CardContent>
                    <AreaChart :data="checkinsTrendData" />
                </CardContent>
            </Card>

            <!-- Top Gyms -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                <!-- Top by Members -->
                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader>
                        <CardTitle class="text-sm font-medium">{{ $lang.adminDashTopByMembers }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p v-if="!data.top_gyms_by_members.length" class="text-sm text-surface-400 py-4 text-center">
                            {{ $lang.analyticsNoData }}
                        </p>
                        <ul v-else class="space-y-3">
                            <li
                                v-for="(gym, i) in data.top_gyms_by_members"
                                :key="gym.id"
                                class="flex items-center gap-3"
                            >
                                <span class="w-5 text-xs font-bold text-surface-400 shrink-0 text-right">{{ i + 1 }}</span>
                                <img
                                    v-if="gym.logo"
                                    :src="gym.logo"
                                    :alt="gym.name"
                                    class="w-8 h-8 rounded-full object-cover shrink-0"
                                />
                                <div
                                    v-else
                                    class="w-8 h-8 rounded-full bg-surface-200 dark:bg-surface-700 shrink-0 flex items-center justify-center text-xs text-surface-500"
                                >
                                    {{ gym.name.charAt(0) }}
                                </div>
                                <span class="flex-1 text-sm font-medium truncate">{{ gym.name }}</span>
                                <span class="shrink-0 text-xs font-semibold bg-brand-500/10 text-brand-500 px-2 py-0.5 rounded-full">
                                    {{ gym.members_count }} members
                                </span>
                            </li>
                        </ul>
                    </CardContent>
                </Card>

                <!-- Top by Activity -->
                <Card class="border-surface-200 dark:border-surface-800">
                    <CardHeader>
                        <CardTitle class="text-sm font-medium">{{ $lang.adminDashTopByActivity }}</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <p v-if="!data.top_gyms_by_activity.length" class="text-sm text-surface-400 py-4 text-center">
                            {{ $lang.analyticsNoData }}
                        </p>
                        <ul v-else class="space-y-3">
                            <li
                                v-for="(gym, i) in data.top_gyms_by_activity"
                                :key="gym.id"
                                class="flex items-center gap-3"
                            >
                                <span class="w-5 text-xs font-bold text-surface-400 shrink-0 text-right">{{ i + 1 }}</span>
                                <img
                                    v-if="gym.logo"
                                    :src="gym.logo"
                                    :alt="gym.name ?? ''"
                                    class="w-8 h-8 rounded-full object-cover shrink-0"
                                />
                                <div
                                    v-else
                                    class="w-8 h-8 rounded-full bg-surface-200 dark:bg-surface-700 shrink-0 flex items-center justify-center text-xs text-surface-500"
                                >
                                    {{ (gym.name ?? '?').charAt(0) }}
                                </div>
                                <span class="flex-1 text-sm font-medium truncate">{{ gym.name ?? '—' }}</span>
                                <span class="shrink-0 text-xs font-semibold bg-green-500/10 text-green-500 px-2 py-0.5 rounded-full">
                                    {{ gym.checkin_count }} check-ins
                                </span>
                            </li>
                        </ul>
                    </CardContent>
                </Card>
            </div>
        </template>
    </section>
</template>
