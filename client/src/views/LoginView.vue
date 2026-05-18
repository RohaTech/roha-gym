<script setup lang="ts">
import { RouterLink } from 'vue-router'
import { useLogin } from '@/composables/useLogin'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import {
  Dumbbell,
  Phone,
  Lock,
  Eye,
  EyeOff,
  AlertCircle,
  Loader2,
  ArrowRight,
} from 'lucide-vue-next'

const { form, errors, isSubmitting, showPassword, handleSubmit, clearError, togglePassword } =
  useLogin()
</script>

<template>
  <div class="min-h-screen flex bg-surface-950 text-white">
    <!-- ═══════════ Left Panel — Branding ═══════════ -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
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
        class="absolute top-1/4 left-1/3 w-80 h-80 bg-brand-500/20 rounded-full blur-[120px] animate-pulse-glow"
      ></div>
      <div
        class="absolute bottom-1/4 right-1/4 w-64 h-64 bg-energy-500/15 rounded-full blur-[100px] animate-pulse-glow stagger-3"
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
            {{ $lang.loginPanelTitle || 'Welcome back to' }}
            <br />
            <span class="gradient-text">{{ $lang.loginPanelTitle2 || 'your gym dashboard' }}</span>
          </h2>
          <p class="text-surface-300 leading-relaxed">
            {{
              $lang.loginPanelDesc ||
              'Manage members, track attendance, and grow your gym — all from one powerful platform.'
            }}
          </p>
        </div>

        <!-- Testimonial / Trust -->
        <div class="glass rounded-2xl p-6 max-w-sm animate-slide-up stagger-4">
          <p class="text-sm text-surface-300 italic mb-3">
            "{{
              $lang.loginTestimonial ||
              'RohaGym transformed how we run our gym. Check-ins take seconds, not minutes.'
            }}"
          </p>
          <div class="flex items-center gap-3">
            <div
              class="w-8 h-8 rounded-full bg-gradient-to-br from-brand-400 to-energy-500 flex items-center justify-center text-xs font-bold"
            >
              A
            </div>
            <div>
              <div class="text-sm font-semibold">
                {{ $lang.loginTestimonialAuthor || 'Abebe K.' }}
              </div>
              <div class="text-xs text-surface-400">
                {{ $lang.loginTestimonialRole || 'Gym Owner, Addis Ababa' }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- ═══════════ Right Panel — Login Form ═══════════ -->
    <div class="flex-1 flex items-center justify-center px-6 py-12">
      <div class="w-full max-w-md">
        <!-- Mobile logo -->
        <div class="lg:hidden flex items-center gap-2.5 mb-10 justify-center">
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
            {{ $lang.loginTitle || 'Sign in to your account' }}
          </h1>
          <p class="text-surface-400 text-sm">
            {{ $lang.loginSubtitle || 'Enter your credentials below to access your dashboard' }}
          </p>
        </div>

        <!-- Form -->
        <form @submit.prevent="handleSubmit" class="space-y-5" novalidate>
          <!-- Phone -->
          <div class="space-y-2">
            <Label for="login-phone" class="text-sm font-medium text-surface-200">
              {{ $lang.phone || 'Phone' }}
            </Label>
            <div class="relative">
              <Phone class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-500" />
              <Input
                id="login-phone"
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

          <!-- Password -->
          <div class="space-y-2">
            <div class="flex items-center justify-between">
              <Label for="login-password" class="text-sm font-medium text-surface-200">
                {{ $lang.password || 'Password' }}
              </Label>
              <RouterLink
                to="/forgot-password"
                class="text-xs text-brand-400 hover:text-brand-300 transition-colors"
              >
                {{ $lang.forgotPassword || 'Forgot password?' }}
              </RouterLink>
            </div>
            <div class="relative">
              <Lock class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 text-surface-500" />
              <Input
                id="login-password"
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
                :aria-label="showPassword ? 'Hide password' : 'Show password'"
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

          <!-- Submit -->
          <Button
            id="login-submit"
            type="submit"
            :disabled="isSubmitting"
            class="w-full h-11 bg-linear-to-r from-brand-500 to-energy-500 hover:from-brand-400 hover:to-energy-400 text-white rounded-xl font-semibold shadow-lg shadow-brand-500/20 hover:shadow-brand-500/40 transition-all hover:-translate-y-0.5 border-0"
          >
            <Loader2 v-if="isSubmitting" class="w-4 h-4 animate-spin" />
            <template v-else>
              {{ $lang.signIn || 'Sign In' }}
              <ArrowRight class="w-4 h-4" />
            </template>
          </Button>
        </form>

        <!-- Divider -->
        <div class="flex items-center gap-4 my-8">
          <div class="flex-1 h-px bg-surface-800"></div>
          <span class="text-xs text-surface-500 uppercase tracking-wider">{{
            $lang.or || 'or'
          }}</span>
          <div class="flex-1 h-px bg-surface-800"></div>
        </div>

        <!-- Register link -->
        <p class="text-center text-sm text-surface-400">
          {{ $lang.dontHaveAccount || "Don't have an account?" }}
          <RouterLink
            to="/register"
            class="text-brand-400 hover:text-brand-300 font-semibold transition-colors ml-1"
          >
            {{ $lang.createAccount || 'Create one' }}
          </RouterLink>
        </p>
      </div>
    </div>
  </div>
</template>
