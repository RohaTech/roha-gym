<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { useRegister } from '@/composables/useRegister'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Dumbbell,
  Lock,
  Eye,
  EyeOff,
  AlertCircle,
  Loader2,
  ArrowRight,
  User,
  Phone,
  MapPin,
} from 'lucide-vue-next'

const {
  form,
  errors,
  isSubmitting,
  showPassword,
  showConfirmPassword,
  logoPreview,
  logoFileName,
  handleSubmit,
  handleLogoChange,
  clearError,
  togglePassword,
  toggleConfirmPassword,
} = useRegister()
</script>

<template>
  <div class="min-h-screen flex bg-surface-950 text-white">
    <!-- ═══════════ Left Panel — Branding ═══════════ -->
    <div class="hidden lg:flex lg:w-[45%] relative overflow-hidden">
      <!-- Background image -->
      <img
        src="/gym-hero.png"
        alt="Gym interior"
        class="absolute inset-0 w-full h-full object-cover"
      />
      <div
        class="absolute inset-0 bg-gradient-to-br from-surface-950/90 via-surface-950/70 to-brand-900/50"
      ></div>

      <!-- Ambient orbs -->
      <div
        class="absolute top-1/3 left-1/4 w-72 h-72 bg-brand-500/20 rounded-full blur-[120px] animate-pulse-glow"
      ></div>
      <div
        class="absolute bottom-1/3 right-1/3 w-56 h-56 bg-energy-500/15 rounded-full blur-[100px] animate-pulse-glow stagger-3"
      ></div>

      <div class="relative z-10 flex flex-col justify-between p-12 w-full">
        <!-- Logo -->
        <RouterLink to="/" class="flex items-center gap-2.5 group">
          <div
            class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-400 to-energy-500 flex items-center justify-center shadow-lg shadow-brand-500/30"
          >
            <Dumbbell class="w-5 h-5 text-white" />
          </div>
          <span class="text-xl font-bold font-display tracking-tight">{{
            $lang.appName || 'RohaGym'
          }}</span>
        </RouterLink>

        <!-- Tagline -->
        <div class="max-w-md animate-slide-up">
          <h2
            class="text-3xl xl:text-4xl font-display font-black tracking-tight leading-tight mb-4"
          >
            {{ $lang.registerPanelTitle || 'Start managing your' }}
            <br />
            <span class="gradient-text">{{ $lang.registerPanelTitle2 || 'gym today' }}</span>
          </h2>
          <p class="text-surface-300 leading-relaxed mb-8">
            {{
              $lang.registerPanelDesc ||
              'Set up your gym in minutes. Add members, generate QR codes, and track everything from one place.'
            }}
          </p>

          <!-- Feature chips -->
          <div class="flex flex-wrap gap-2">
            <span
              v-for="chip in [
                $lang.chipFreeSetup || 'Free Setup',
                $lang.chipQrCards || 'QR ID Cards',
                $lang.chipAnalytics || 'Analytics',
                $lang.chipMultiGym || 'Multi-Gym',
              ]"
              :key="chip"
              class="glass rounded-full px-3 py-1.5 text-xs font-medium text-surface-200"
            >
              {{ chip }}
            </span>
          </div>
        </div>

        <!-- Footer text -->
        <p class="text-xs text-surface-500">
          {{ $lang.registerPanelFooter || 'Trusted by 500+ gyms across the country.' }}
        </p>
      </div>
    </div>

    <!-- ═══════════ Right Panel — Register Form ═══════════ -->
    <div class="flex-1 flex items-center justify-center px-6 py-10 overflow-y-auto">
      <div class="w-full max-w-lg">
        <!-- Mobile logo -->
        <div class="lg:hidden flex items-center gap-2.5 mb-8 justify-center">
          <RouterLink to="/" class="flex items-center gap-2.5">
            <div
              class="w-10 h-10 rounded-xl bg-gradient-to-br from-brand-400 to-energy-500 flex items-center justify-center shadow-lg shadow-brand-500/30"
            >
              <Dumbbell class="w-5 h-5 text-white" />
            </div>
            <span class="text-xl font-bold font-display tracking-tight">{{
              $lang.appName || 'RohaGym'
            }}</span>
          </RouterLink>
        </div>

        <!-- Header -->
        <div class="mb-8 text-center lg:text-left">
          <h1 class="text-2xl sm:text-3xl font-display font-black tracking-tight mb-2">
            {{ $lang.registerTitle || 'Create your account' }}
          </h1>
          <p class="text-surface-400 text-sm">
            {{ $lang.registerSubtitle || 'Register your gym and start managing members today' }}
          </p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="space-y-4" novalidate>
          <!-- Gym Name -->
          <div class="space-y-2">
            <Label for="register-name" class="text-sm font-medium text-surface-200">
              {{ $lang.gymName || 'Gym Name' }}
            </Label>
            <div class="relative">
              <User class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-500" />
              <Input
                id="register-name"
                v-model="form.name"
                type="text"
                :placeholder="$lang.gymNamePlaceholder || 'e.g. FitZone Gym'"
                class="pl-10 h-11 bg-surface-900 border-surface-700 text-white placeholder:text-surface-500 focus-visible:border-brand-500 focus-visible:ring-brand-500/30"
                :invalid="!!errors.name"
                :aria-invalid="!!errors.name"
                @input="clearError('name')"
              />
            </div>
            <small
              v-if="errors.name"
              class="text-xs text-danger-500 flex items-center gap-1 animate-scale-in"
            >
              <AlertCircle class="w-3 h-3" /> {{ errors.name }}
            </small>
          </div>

          <!-- Phone + Address Row -->
          <div class="grid sm:grid-cols-2 gap-4">
            <!-- Phone -->
            <div class="space-y-2">
              <Label for="register-phone" class="text-sm font-medium text-surface-200">
                {{ $lang.phone || 'Phone' }}
              </Label>
              <div class="relative">
                <Phone
                  class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-500"
                />
                <Input
                  id="register-phone"
                  v-model="form.phone"
                  type="tel"
                  :placeholder="$lang.phonePlaceholder || '+251 9XX XXX XXX'"
                  class="pl-10 h-11 bg-surface-900 border-surface-700 text-white placeholder:text-surface-500 focus-visible:border-brand-500 focus-visible:ring-brand-500/30"
                  :invalid="!!errors.phone"
                  :aria-invalid="!!errors.phone"
                  @input="clearError('phone')"
                />
              </div>
              <small
                v-if="errors.phone"
                class="text-xs text-danger-500 flex items-center gap-1 animate-scale-in"
              >
                <AlertCircle class="w-3 h-3" /> {{ errors.phone }}
              </small>
            </div>

            <!-- Address -->
            <div class="space-y-2">
              <Label for="register-address" class="text-sm font-medium text-surface-200">
                {{ $lang.address || 'Address' }}
              </Label>
              <div class="relative">
                <MapPin
                  class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-500"
                />
                <Input
                  id="register-address"
                  v-model="form.address"
                  type="text"
                  :placeholder="$lang.addressPlaceholder || 'City, Sub-city, Area'"
                  class="pl-10 h-11 bg-surface-900 border-surface-700 text-white placeholder:text-surface-500 focus-visible:border-brand-500 focus-visible:ring-brand-500/30"
                  :invalid="!!errors.address"
                  :aria-invalid="!!errors.address"
                  @input="clearError('address')"
                />
              </div>
              <small
                v-if="errors.address"
                class="text-xs text-danger-500 flex items-center gap-1 animate-scale-in"
              >
                <AlertCircle class="w-3 h-3" /> {{ errors.address }}
              </small>
            </div>
          </div>

          <!-- Logo Upload -->
          <div class="space-y-2">
            <Label for="register-logo" class="text-sm font-medium text-surface-200">
              {{ $lang.gymLogo || 'Gym Logo' }}
              <span class="text-surface-500">({{ $lang.optional || 'Optional' }})</span>
            </Label>
            <div class="flex items-center gap-4">
              <div class="flex-1">
                <Input
                  id="register-logo"
                  type="file"
                  accept="image/jpeg,image/png,image/webp"
                  class="h-11 bg-surface-900 border-surface-700 py-2 text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-brand-500 file:text-white hover:file:bg-brand-400 file:cursor-pointer"
                  :invalid="!!errors.logo"
                  :aria-invalid="!!errors.logo"
                  @change="
                    (event: any) => {
                      clearError('logo')
                      handleLogoChange(event)
                    }
                  "
                />
              </div>
              <div
                v-if="logoPreview"
                class="w-16 h-16 rounded-lg overflow-hidden border-2 border-surface-700 shrink-0"
              >
                <img :src="logoPreview" alt="Logo preview" class="w-full h-full object-cover" />
              </div>
            </div>
            <p v-if="logoFileName" class="text-xs text-surface-400">
              {{ logoFileName }}
            </p>
            <small
              v-if="errors.logo"
              class="text-xs text-danger-500 flex items-center gap-1 animate-scale-in"
            >
              <AlertCircle class="w-3 h-3" /> {{ errors.logo }}
            </small>
          </div>

          <!-- Password Row -->
          <div class="grid sm:grid-cols-2 gap-4">
            <!-- Password -->
            <div class="space-y-2">
              <Label for="register-password" class="text-sm font-medium text-surface-200">
                {{ $lang.password || 'Password' }}
              </Label>
              <div class="relative">
                <Lock class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-500" />
                <Input
                  id="register-password"
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  :placeholder="$lang.passwordPlaceholder || '••••••••'"
                  class="pl-10 pr-10 h-11 bg-surface-900 border-surface-700 text-white placeholder:text-surface-500 focus-visible:border-brand-500 focus-visible:ring-brand-500/30"
                  :invalid="!!errors.password"
                  :aria-invalid="!!errors.password"
                  @input="clearError('password')"
                />
                <button
                  type="button"
                  class="absolute right-3.5 top-1/2 -translate-y-1/2 text-surface-500 hover:text-surface-300 transition-colors"
                  @click="togglePassword"
                >
                  <EyeOff v-if="showPassword" class="w-4 h-4" />
                  <Eye v-else class="w-4 h-4" />
                </button>
              </div>
              <small
                v-if="errors.password"
                class="text-xs text-danger-500 flex items-center gap-1 animate-scale-in"
              >
                <AlertCircle class="w-3 h-3" /> {{ errors.password }}
              </small>
            </div>

            <!-- Confirm Password -->
            <div class="space-y-2">
              <Label for="register-password-confirm" class="text-sm font-medium text-surface-200">
                {{ $lang.confirmPassword || 'Confirm Password' }}
              </Label>
              <div class="relative">
                <Lock class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-500" />
                <Input
                  id="register-password-confirm"
                  v-model="form.password_confirmation"
                  :type="showConfirmPassword ? 'text' : 'password'"
                  :placeholder="$lang.passwordPlaceholder || '••••••••'"
                  class="pl-10 pr-10 h-11 bg-surface-900 border-surface-700 text-white placeholder:text-surface-500 focus-visible:border-brand-500 focus-visible:ring-brand-500/30"
                  :invalid="!!errors.password_confirmation"
                  :aria-invalid="!!errors.password_confirmation"
                  @input="clearError('password_confirmation')"
                />
                <button
                  type="button"
                  class="absolute right-3.5 top-1/2 -translate-y-1/2 text-surface-500 hover:text-surface-300 transition-colors"
                  @click="toggleConfirmPassword"
                >
                  <EyeOff v-if="showConfirmPassword" class="w-4 h-4" />
                  <Eye v-else class="w-4 h-4" />
                </button>
              </div>
              <small
                v-if="errors.password_confirmation"
                class="text-xs text-danger-500 flex items-center gap-1 animate-scale-in"
              >
                <AlertCircle class="w-3 h-3" /> {{ errors.password_confirmation }}
              </small>
            </div>
          </div>

          <!-- Submit -->
          <Button
            id="register-submit"
            type="submit"
            :disabled="isSubmitting"
            class="w-full h-11 bg-gradient-to-r from-brand-500 to-energy-500 hover:from-brand-400 hover:to-energy-400 text-white rounded-xl font-semibold shadow-lg shadow-brand-500/20 hover:shadow-brand-500/40 transition-all hover:-translate-y-0.5 border-0 mt-2"
          >
            <Loader2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
            <template v-else>
              {{ $lang.createAccount || 'Create Account' }}
              <ArrowRight class="w-4 h-4" />
            </template>
          </Button>
        </form>

        <!-- Divider -->
        <div class="flex items-center gap-4 my-6">
          <div class="flex-1 h-px bg-surface-800"></div>
          <span class="text-xs text-surface-500 uppercase tracking-wider">{{
            $lang.or || 'or'
          }}</span>
          <div class="flex-1 h-px bg-surface-800"></div>
        </div>

        <!-- Login link -->
        <p class="text-center text-sm text-surface-400">
          {{ $lang.alreadyHaveAccount || 'Already have an account?' }}
          <RouterLink
            to="/login"
            class="text-brand-400 hover:text-brand-300 font-semibold transition-colors ml-1"
          >
            {{ $lang.signIn || 'Sign In' }}
          </RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>
