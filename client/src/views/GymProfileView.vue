<script setup lang="ts">
import { Upload, X } from 'lucide-vue-next'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { useGymProfile } from '@/composables/useGymProfile'

const {
  form,
  errors,
  isLoading,
  isError,
  isSubmitting,
  logoPreview,
  logoInput,
  existingLogoUrl,
  clearError,
  handleSubmit,
  handleLogoChange,
  clearLogo,
  triggerLogoUpload,
} = useGymProfile()
</script>

<template>
  <div class="p-4 md:p-6 space-y-6">
    <!-- Header -->
    <div>
      <h1 class="text-2xl md:text-3xl font-display font-bold tracking-tight">
        {{ $lang.profileTitle }}
      </h1>
      <p class="text-surface-400 mt-1 text-sm">{{ $lang.profileSubtitle }}</p>
    </div>

    <div v-if="isLoading" class="text-surface-400 text-sm">{{ $lang.profileLoading }}</div>
    <div v-else-if="isError" class="text-danger-500 text-sm">{{ $lang.profileLoadError }}</div>

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
              <!-- Gym Name -->
              <div class="space-y-2">
                <Label for="name">{{ $lang.profileGymName }} *</Label>
                <Input
                  id="name"
                  v-model="form.name"
                  :placeholder="$lang.profileGymNamePlaceholder"
                  :invalid="!!errors.name"
                  @input="clearError('name')"
                />
                <small v-if="errors.name" class="text-danger-500">{{ errors.name }}</small>
              </div>

              <!-- Phone -->
              <div class="space-y-2">
                <Label for="phone">{{ $lang.profilePhone }} *</Label>
                <Input
                  id="phone"
                  v-model="form.phone"
                  type="tel"
                  :placeholder="$lang.profilePhonePlaceholder"
                  :invalid="!!errors.phone"
                  @input="clearError('phone')"
                />
                <small v-if="errors.phone" class="text-danger-500">{{ errors.phone }}</small>
              </div>

              <!-- Address -->
              <div class="space-y-2">
                <Label for="address">{{ $lang.profileAddress }} *</Label>
                <Input
                  id="address"
                  v-model="form.address"
                  :placeholder="$lang.profileAddressPlaceholder"
                  :invalid="!!errors.address"
                  @input="clearError('address')"
                />
                <small v-if="errors.address" class="text-danger-500">{{ errors.address }}</small>
              </div>
            </CardContent>
          </Card>
        </div>

        <!-- Logo + Actions -->
        <div class="space-y-6">
          <Card>
            <CardHeader>
              <CardTitle>{{ $lang.profileLogo }}</CardTitle>
              <CardDescription>{{ $lang.profileLogoDesc }}</CardDescription>
            </CardHeader>
            <CardContent>
              <div class="space-y-4">
                <!-- New logo preview -->
                <div
                  v-if="logoPreview"
                  class="relative w-full aspect-square rounded-lg overflow-hidden bg-surface-800"
                >
                  <img :src="logoPreview" alt="Logo preview" class="w-full h-full object-cover" />
                  <button
                    type="button"
                    @click="clearLogo"
                    class="absolute top-2 right-2 p-1 rounded-full bg-surface-900/80 hover:bg-surface-900 transition-colors"
                  >
                    <X class="w-4 h-4" />
                  </button>
                </div>

                <!-- Existing logo -->
                <div
                  v-else-if="existingLogoUrl"
                  class="relative w-full aspect-square rounded-lg overflow-hidden bg-surface-800 cursor-pointer"
                  @click="triggerLogoUpload"
                >
                  <img
                    :src="existingLogoUrl"
                    alt="Gym logo"
                    class="w-full h-full object-contain p-2"
                  />
                  <div
                    class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 hover:opacity-100 transition-opacity"
                  >
                    <Upload class="w-8 h-8 text-white" />
                  </div>
                </div>

                <!-- No logo upload area -->
                <div
                  v-else
                  class="w-full aspect-square rounded-lg border-2 border-dashed border-surface-700 flex flex-col items-center justify-center gap-2 hover:border-brand-500 transition-colors cursor-pointer"
                  @click="triggerLogoUpload"
                >
                  <Upload class="w-8 h-8 text-surface-400" />
                  <p class="text-sm text-surface-400">{{ $lang.profileClickToUpload }}</p>
                  <p class="text-xs text-surface-500">{{ $lang.profileLogoHint }}</p>
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
            <Button type="submit" :disabled="isSubmitting" class="w-full">
              {{ isSubmitting ? $lang.profileSaving : $lang.profileSaveChanges }}
            </Button>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>
