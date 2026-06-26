<script setup lang="ts">
import { ref, reactive, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import { ArrowLeft, Upload, X, Receipt, Trash2, Infinity as InfinityIcon } from 'lucide-vue-next'
import { toast } from 'vue-sonner'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { useAdminGymForm } from '@/composables/useAdminGyms'
import { useSubscriptionDetail } from '@/composables/useAdminSubscriptions'
import RecordPaymentDialog from '@/components/admin/RecordPaymentDialog.vue'
import type { SubscriptionDetail, SubscriptionStatus } from '@/types/admin'

const router = useRouter()
const route = useRoute()

const gymId = computed(() => {
    const id = route.params.gymId
    return id ? Number(id) : null
})
const isEdit = computed(() => gymId.value !== null)

const { createGym, updateGym, isSaving, fetchGym } = useAdminGymForm()
const { fetchDetail, deletePayment, isDeletingPayment } = useSubscriptionDetail()

const subDetail = ref<SubscriptionDetail | null>(null)
const isLoadingSub = ref(false)
const subDialogOpen = ref(false)

const STATUS_STYLES: Record<SubscriptionStatus, string> = {
    lifetime: 'bg-brand-500/10 text-brand-600 dark:text-brand-400',
    active: 'bg-green-500/10 text-green-600 dark:text-green-400',
    expiring_soon: 'bg-yellow-500/10 text-yellow-600 dark:text-yellow-400',
    expired: 'bg-danger-500/10 text-danger-600 dark:text-danger-400',
    none: 'bg-surface-500/10 text-surface-500',
}

function subStatusLabel(status: SubscriptionStatus, lang: any): string {
    return {
        lifetime: lang.adminSubsStatusLifetime,
        active: lang.adminSubsStatusActive,
        expiring_soon: lang.adminSubsStatusExpiring,
        expired: lang.adminSubsStatusExpired,
        none: lang.adminSubsStatusNone,
    }[status]
}

async function loadSubscription() {
    if (!gymId.value) return
    isLoadingSub.value = true
    try {
        subDetail.value = await fetchDetail(gymId.value)
    } catch {
        // Non-fatal: the panel just won't render.
    } finally {
        isLoadingSub.value = false
    }
}

async function removePayment(id: number) {
    if (!window.confirm('Delete this payment record?')) return
    try {
        await deletePayment(id)
        toast.success('Payment record deleted')
        await loadSubscription()
    } catch (e: any) {
        toast.error(e?.response?.data?.message ?? 'Failed to delete payment')
    }
}

const form = reactive({
    name: '',
    phone: '',
    address: '',
    password: '',
})
const errors = reactive<Record<string, string>>({})
const logoFile = ref<File | null>(null)
const logoPreview = ref<string | null>(null)
const existingLogo = ref<string | null>(null)
const logoInput = ref<HTMLInputElement | null>(null)
const isLoadingGym = ref(false)
const loadError = ref(false)

onMounted(async () => {
    if (isEdit.value && gymId.value) {
        isLoadingGym.value = true
        try {
            const gym = await fetchGym(gymId.value)
            form.name = gym.name
            form.phone = gym.phone
            form.address = gym.address ?? ''
            existingLogo.value = gym.logo
            loadSubscription()
        } catch {
            loadError.value = true
        } finally {
            isLoadingGym.value = false
        }
    }
})

function clearError(field: string) {
    delete errors[field]
}

function handleLogoChange(e: Event) {
    const file = (e.target as HTMLInputElement).files?.[0]
    if (!file) return
    logoFile.value = file
    logoPreview.value = URL.createObjectURL(file)
}

function clearLogo() {
    logoFile.value = null
    logoPreview.value = null
    if (logoInput.value) logoInput.value.value = ''
}

function triggerLogoUpload() {
    logoInput.value?.click()
}

function goBack() {
    router.push({ name: 'admin-gyms' })
}

async function handleSubmit() {
    Object.keys(errors).forEach(k => delete errors[k])

    const payload = {
        name: form.name,
        phone: form.phone,
        address: form.address,
        password: form.password || undefined,
        logo: logoFile.value,
    }

    try {
        if (isEdit.value && gymId.value) {
            await updateGym({ id: gymId.value, payload })
        } else {
            await createGym(payload)
        }
        toast.success('Gym saved successfully')
        router.push({ name: 'admin-gyms' })
    } catch (e: any) {
        const serverErrors = e?.response?.data?.errors
        if (serverErrors) {
            Object.entries(serverErrors).forEach(([field, msgs]) => {
                errors[field] = (msgs as string[])[0] ?? ''
            })
        } else {
            toast.error(e?.response?.data?.message ?? 'Failed to save gym')
        }
    }
}
</script>

<template>
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex items-center gap-3">
            <Button variant="ghost" size="icon" @click="goBack">
                <ArrowLeft class="size-4" />
            </Button>
            <div>
                <h2 class="text-xl font-semibold">
                    {{ isEdit ? $lang.adminGymEditTitle : $lang.adminGymCreateTitle }}
                </h2>
                <p class="text-sm text-surface-400">
                    {{ isEdit ? $lang.adminGymEditSubtitle : $lang.adminGymCreateSubtitle }}
                </p>
            </div>
        </div>

        <div v-if="isLoadingGym" class="text-surface-400 text-sm py-8 text-center">
            {{ $lang.adminGymLoading }}
        </div>
        <div v-else-if="loadError" class="text-danger-500 text-sm py-8 text-center">
            {{ $lang.adminGymError }}
        </div>

        <form v-else @submit.prevent="handleSubmit">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Main Fields -->
                <div class="lg:col-span-2 space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ $lang.profileGymInfo }}</CardTitle>
                            <CardDescription>{{ $lang.profileGymInfoDesc }}</CardDescription>
                        </CardHeader>
                        <CardContent class="space-y-4">
                            <!-- Name -->
                            <div class="space-y-2">
                                <Label for="name">{{ $lang.adminGymFieldName }} *</Label>
                                <Input
                                    id="name"
                                    v-model="form.name"
                                    :placeholder="$lang.adminGymFieldNamePlaceholder"
                                    @input="clearError('name')"
                                />
                                <small v-if="errors.name" class="text-danger-500">{{ errors.name }}</small>
                            </div>

                            <!-- Phone -->
                            <div class="space-y-2">
                                <Label for="phone">{{ $lang.adminGymFieldPhone }} *</Label>
                                <Input
                                    id="phone"
                                    v-model="form.phone"
                                    type="tel"
                                    :placeholder="$lang.profilePhonePlaceholder"
                                    @input="clearError('phone')"
                                />
                                <small v-if="errors.phone" class="text-danger-500">{{ errors.phone }}</small>
                            </div>

                            <!-- Address -->
                            <div class="space-y-2">
                                <Label for="address">{{ $lang.adminGymFieldAddress }} *</Label>
                                <Input
                                    id="address"
                                    v-model="form.address"
                                    :placeholder="$lang.profileAddressPlaceholder"
                                    @input="clearError('address')"
                                />
                                <small v-if="errors.address" class="text-danger-500">{{ errors.address }}</small>
                            </div>

                            <!-- Password -->
                            <div class="space-y-2">
                                <Label for="password">
                                    {{ $lang.adminGymFieldPassword }}
                                    <span v-if="!isEdit" class="text-danger-500">*</span>
                                </Label>
                                <Input
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    :placeholder="isEdit ? $lang.adminGymFieldPasswordHint : '••••••••'"
                                    @input="clearError('password')"
                                />
                                <small v-if="errors.password" class="text-danger-500">{{ errors.password }}</small>
                                <small v-else-if="isEdit" class="text-surface-500">
                                    {{ $lang.adminGymFieldPasswordHint }}
                                </small>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <!-- Logo + Actions -->
                <div class="space-y-6">
                    <Card>
                        <CardHeader>
                            <CardTitle>{{ $lang.adminGymFieldLogo }}</CardTitle>
                        </CardHeader>
                        <CardContent>
                            <div class="space-y-3">
                                <!-- New logo preview -->
                                <div
                                    v-if="logoPreview"
                                    class="relative w-full aspect-square rounded-lg overflow-hidden bg-surface-100 dark:bg-surface-800"
                                >
                                    <img :src="logoPreview" alt="Logo preview" class="w-full h-full object-cover" />
                                    <button
                                        type="button"
                                        @click="clearLogo"
                                        class="absolute top-2 right-2 p-1 rounded-full bg-surface-900/80 hover:bg-surface-900 transition-colors"
                                    >
                                        <X class="size-4 text-white" />
                                    </button>
                                </div>

                                <!-- Existing logo -->
                                <div
                                    v-else-if="existingLogo"
                                    class="relative w-full aspect-square rounded-lg overflow-hidden bg-surface-100 dark:bg-surface-800 cursor-pointer"
                                    @click="triggerLogoUpload"
                                >
                                    <img :src="existingLogo" alt="Gym logo" class="w-full h-full object-contain p-2" />
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity">
                                        <Upload class="size-8 text-white" />
                                    </div>
                                </div>

                                <!-- Upload area -->
                                <div
                                    v-else
                                    class="w-full aspect-square rounded-lg border-2 border-dashed border-surface-300 dark:border-surface-700 flex flex-col items-center justify-center gap-2 hover:border-brand-500 transition-colors cursor-pointer"
                                    @click="triggerLogoUpload"
                                >
                                    <Upload class="size-8 text-surface-400" />
                                    <p class="text-sm text-surface-400">{{ $lang.adminGymFieldLogoClick }}</p>
                                    <p class="text-xs text-surface-500">{{ $lang.adminGymFieldLogoHint }}</p>
                                </div>

                                <input
                                    ref="logoInput"
                                    type="file"
                                    accept="image/jpeg,image/png,image/webp"
                                    class="hidden"
                                    @change="handleLogoChange"
                                />
                            </div>
                        </CardContent>
                    </Card>

                    <!-- Actions -->
                    <div class="flex flex-col gap-3">
                        <Button type="submit" :disabled="isSaving" class="w-full">
                            {{ isSaving ? $lang.adminGymSaving : $lang.adminGymSave }}
                        </Button>
                        <Button type="button" variant="outline" :disabled="isSaving" class="w-full" @click="goBack">
                            {{ $lang.adminGymCancel }}
                        </Button>
                    </div>
                </div>
            </div>
        </form>

        <!-- Subscription panel (edit mode only) -->
        <Card v-if="isEdit && !isLoadingGym && !loadError">
            <CardHeader class="flex flex-row items-start justify-between gap-4">
                <div>
                    <CardTitle>{{ $lang.adminSubsPanelTitle }}</CardTitle>
                    <CardDescription>{{ $lang.adminSubsPanelDesc }}</CardDescription>
                </div>
                <Button type="button" size="sm" class="gap-2 shrink-0" @click="subDialogOpen = true">
                    <Receipt class="size-4" />
                    {{ $lang.adminSubsRecordPayment }}
                </Button>
            </CardHeader>
            <CardContent class="space-y-5">
                <div v-if="isLoadingSub" class="text-surface-400 text-sm py-4 text-center">
                    {{ $lang.adminSubsLoading }}
                </div>

                <template v-else-if="subDetail">
                    <!-- Summary grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-surface-500 mb-1">{{ $lang.adminSubsPanelStatus }}</p>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium"
                                :class="STATUS_STYLES[subDetail.summary.subscription_status]"
                            >
                                {{ subStatusLabel(subDetail.summary.subscription_status, $lang) }}
                            </span>
                        </div>
                        <div>
                            <p class="text-xs text-surface-500 mb-1">{{ $lang.adminSubsPanelExpiry }}</p>
                            <p class="text-sm font-medium flex items-center gap-1">
                                <InfinityIcon v-if="subDetail.summary.subscription_status === 'lifetime'" class="size-4" />
                                <span>{{ subDetail.summary.subscription_status === 'lifetime' ? $lang.adminSubsStatusLifetime : (subDetail.summary.expiry_date ?? '—') }}</span>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-surface-500 mb-1">{{ $lang.adminSubsPanelDaysLeft }}</p>
                            <p class="text-sm font-medium tabular-nums">
                                <template v-if="subDetail.summary.subscription_status === 'lifetime' || subDetail.summary.days_until_expiry === null">—</template>
                                <span v-else-if="subDetail.summary.days_until_expiry < 0" class="text-danger-500">
                                    {{ Math.abs(subDetail.summary.days_until_expiry) }}d {{ $lang.adminSubsExpiredDays }}
                                </span>
                                <span v-else>{{ subDetail.summary.days_until_expiry }}d</span>
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-surface-500 mb-1">{{ $lang.adminSubsPanelTotalPaid }}</p>
                            <p class="text-sm font-semibold tabular-nums">
                                {{ subDetail.summary.total_paid.toLocaleString() }}
                                <span class="text-xs font-normal text-surface-400">{{ subDetail.summary.currency }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- History -->
                    <div>
                        <p class="text-xs uppercase tracking-wide text-surface-500 mb-2">{{ $lang.adminSubsPanelHistory }}</p>
                        <p v-if="!subDetail.payments.length" class="text-sm text-surface-400 py-2">
                            {{ $lang.adminSubsPanelNoHistory }}
                        </p>
                        <ul v-else class="divide-y divide-surface-100 dark:divide-surface-800 rounded-lg border border-surface-200 dark:border-surface-800">
                            <li
                                v-for="p in subDetail.payments"
                                :key="p.id"
                                class="flex items-center justify-between gap-3 px-3 py-2 text-sm"
                            >
                                <div class="min-w-0">
                                    <p class="font-medium">
                                        {{ p.plan_type === 'lifetime' ? $lang.adminSubsPlanLifetime : (p.months + ' × ' + $lang.adminSubsPlanMonthly) }}
                                        <span class="text-surface-400 font-normal">· {{ p.paid_at }}</span>
                                    </p>
                                    <p class="text-xs text-surface-500 truncate">
                                        {{ p.starts_at }}<span v-if="p.ends_at"> → {{ p.ends_at }}</span>
                                        <span v-if="p.note"> · {{ p.note }}</span>
                                    </p>
                                </div>
                                <div class="flex items-center gap-2 shrink-0">
                                    <span class="font-semibold tabular-nums">{{ p.amount.toLocaleString() }} {{ p.currency }}</span>
                                    <Button
                                        type="button"
                                        variant="ghost"
                                        size="icon"
                                        class="size-7 text-danger-500 hover:text-danger-400"
                                        :disabled="isDeletingPayment"
                                        :title="$lang.adminSubsPanelDelete"
                                        @click="removePayment(p.id)"
                                    >
                                        <Trash2 class="size-4" />
                                    </Button>
                                </div>
                            </li>
                        </ul>
                    </div>
                </template>
            </CardContent>
        </Card>

        <RecordPaymentDialog
            v-model:open="subDialogOpen"
            :gym-id="gymId"
            :gym-name="form.name"
            @saved="loadSubscription"
        />
    </div>
</template>
