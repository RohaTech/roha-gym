<script setup lang="ts">
import { RouterLink } from 'vue-router'
import {
  Dumbbell,
  QrCode,
  Users,
  BarChart3,
  ShieldCheck,
  Zap,
  ChevronRight,
  Star,
  ArrowRight,
} from 'lucide-vue-next'

const features = [
  {
    icon: Users,
    title: 'memberManagement',
    desc: 'memberManagementDesc',
    color: 'from-brand-400 to-brand-600',
  },
  {
    icon: QrCode,
    title: 'qrCheckin',
    desc: 'qrCheckinDesc',
    color: 'from-energy-400 to-energy-600',
  },
  {
    icon: BarChart3,
    title: 'analytics',
    desc: 'analyticsDesc',
    color: 'from-success-500 to-brand-500',
  },
  {
    icon: ShieldCheck,
    title: 'checkinValidation',
    desc: 'checkinValidationDesc',
    color: 'from-brand-500 to-energy-500',
  },
  {
    icon: Dumbbell,
    title: 'membershipPlans',
    desc: 'membershipPlansDesc',
    color: 'from-energy-500 to-brand-400',
  },
  {
    icon: Zap,
    title: 'multiGym',
    desc: 'multiGymDesc',
    color: 'from-brand-300 to-energy-400',
  },
]

const stats = [
  { value: '500+', label: 'activeGyms' },
  { value: '50K+', label: 'membersManaged' },
  { value: '1M+', label: 'checkinsRecorded' },
  { value: '99.9%', label: 'uptime' },
]
</script>

<template>
  <div class="min-h-screen bg-surface-950 text-white overflow-x-hidden">

    <!-- ═══════════ Navigation ═══════════ -->
    <nav class="fixed top-0 inset-x-0 z-50 glass">
      <div class="max-w-7xl mx-auto flex items-center justify-between px-6 py-4">
        <RouterLink to="/" class="flex items-center gap-2.5 group">
          <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-brand-400 to-energy-500 flex items-center justify-center shadow-lg shadow-brand-500/25 group-hover:shadow-brand-500/40 transition-shadow">
            <Dumbbell class="w-5 h-5 text-white" />
          </div>
          <span class="text-lg font-bold font-display tracking-tight">{{ $lang.appName || 'RohaGym' }}</span>
        </RouterLink>

        <div class="hidden md:flex items-center gap-8">
          <a href="#features" class="text-sm text-surface-300 hover:text-white transition-colors">{{ $lang.features || 'Features' }}</a>
          <a href="#stats" class="text-sm text-surface-300 hover:text-white transition-colors">{{ $lang.stats || 'Stats' }}</a>
          <a href="#cta" class="text-sm text-surface-300 hover:text-white transition-colors">{{ $lang.pricing || 'Pricing' }}</a>
        </div>

        <div class="flex items-center gap-3">
          <RouterLink
            to="/login"
            class="text-sm text-surface-300 hover:text-white transition-colors px-4 py-2"
          >
            {{ $lang.login || 'Log in' }}
          </RouterLink>
          <RouterLink
            to="/register"
            class="text-sm bg-gradient-to-r from-brand-500 to-energy-500 text-white px-5 py-2.5 rounded-xl font-medium hover:shadow-lg hover:shadow-brand-500/30 transition-all hover:-translate-y-0.5"
          >
            {{ $lang.getStarted || 'Get Started' }}
          </RouterLink>
        </div>
      </div>
    </nav>

    <!-- ═══════════ Hero Section ═══════════ -->
    <section class="relative min-h-screen flex items-center justify-center pt-20 noise-overlay">
      <!-- Background image -->
      <div class="absolute inset-0">
        <img
          src="/gym-hero.png"
          alt="Modern gym interior"
          class="w-full h-full object-cover opacity-25"
        />
        <div class="absolute inset-0 bg-gradient-to-b from-surface-950/60 via-surface-950/80 to-surface-950"></div>
      </div>

      <!-- Ambient glow orbs -->
      <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-brand-500/15 rounded-full blur-[128px] animate-pulse-glow"></div>
      <div class="absolute bottom-1/3 right-1/4 w-80 h-80 bg-energy-500/10 rounded-full blur-[100px] animate-pulse-glow stagger-3"></div>

      <div class="relative z-10 max-w-5xl mx-auto text-center px-6">
        <!-- Badge -->
        <div class="inline-flex items-center gap-2 glass rounded-full px-4 py-1.5 mb-8 animate-slide-up">
          <Star class="w-3.5 h-3.5 text-energy-500" />
          <span class="text-xs text-surface-300 font-medium">{{ $lang.heroBadge || 'The #1 Gym Management Platform' }}</span>
        </div>

        <h1 class="text-4xl sm:text-5xl md:text-7xl font-display font-black tracking-tight leading-[1.1] mb-6 animate-slide-up stagger-1">
          {{ $lang.heroTitlePart1 || 'Manage Your Gym' }}
          <br />
          <span class="gradient-text">{{ $lang.heroTitlePart2 || 'Like Never Before' }}</span>
        </h1>

        <p class="text-lg md:text-xl text-surface-300 max-w-2xl mx-auto mb-10 leading-relaxed animate-slide-up stagger-2">
          {{ $lang.heroSubtitle || 'Digitally manage members, streamline check-ins with QR codes, and gain powerful insights into your gym operations — all in one platform.' }}
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 animate-slide-up stagger-3">
          <RouterLink
            to="/register"
            id="hero-cta-primary"
            class="group flex items-center gap-2 bg-gradient-to-r from-brand-500 to-energy-500 text-white px-8 py-4 rounded-2xl font-semibold text-base hover:shadow-2xl hover:shadow-brand-500/30 transition-all hover:-translate-y-1"
          >
            {{ $lang.startFreeTrial || 'Start Free Trial' }}
            <ArrowRight class="w-4 h-4 group-hover:translate-x-1 transition-transform" />
          </RouterLink>
          <a
            href="#features"
            id="hero-cta-secondary"
            class="flex items-center gap-2 glass text-surface-200 px-8 py-4 rounded-2xl font-medium text-base hover:bg-white/10 transition-all"
          >
            {{ $lang.seeFeatures || 'See Features' }}
            <ChevronRight class="w-4 h-4" />
          </a>
        </div>
      </div>

      <!-- Scroll indicator -->
      <div class="absolute bottom-8 left-1/2 -translate-x-1/2 animate-float-slow">
        <div class="w-6 h-10 rounded-full border-2 border-surface-500 flex items-start justify-center p-1.5">
          <div class="w-1.5 h-3 rounded-full bg-brand-400 animate-pulse-glow"></div>
        </div>
      </div>
    </section>

    <!-- ═══════════ Stats Section ═══════════ -->
    <section id="stats" class="relative py-20 border-y border-surface-800/50">
      <div class="max-w-6xl mx-auto px-6">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
          <div
            v-for="(stat, i) in stats"
            :key="stat.label"
            class="text-center animate-count-up"
            :class="`stagger-${i + 1}`"
          >
            <div class="text-3xl md:text-4xl font-display font-black gradient-text mb-2">{{ stat.value }}</div>
            <div class="text-sm text-surface-400 font-medium">{{ $lang[stat.label] || stat.label }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════ Features Section ═══════════ -->
    <section id="features" class="relative py-24 md:py-32">
      <div class="absolute top-0 right-0 w-[500px] h-[500px] bg-brand-500/5 rounded-full blur-[160px]"></div>

      <div class="max-w-7xl mx-auto px-6 relative z-10">
        <div class="text-center mb-16">
          <span class="text-sm font-semibold text-brand-400 uppercase tracking-widest mb-3 block">{{ $lang.whyChooseUs || 'Why Choose Us' }}</span>
          <h2 class="text-3xl md:text-5xl font-display font-black tracking-tight mb-4">
            {{ $lang.featuresTitle || 'Everything You Need to' }}
            <span class="gradient-text"> {{ $lang.featuresTitle2 || 'Run Your Gym' }}</span>
          </h2>
          <p class="text-surface-400 max-w-xl mx-auto">
            {{ $lang.featuresSubtitle || 'From member registration to attendance analytics, we\'ve got every aspect of gym management covered.' }}
          </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="(feat, i) in features"
            :key="feat.title"
            class="group relative glass rounded-2xl p-6 hover:bg-white/[0.08] transition-all duration-300 hover:-translate-y-1 cursor-default animate-slide-up"
            :class="`stagger-${i + 1}`"
          >
            <!-- Icon -->
            <div
              class="w-12 h-12 rounded-xl bg-gradient-to-br flex items-center justify-center mb-5 shadow-lg group-hover:scale-110 transition-transform"
              :class="feat.color"
            >
              <component :is="feat.icon" class="w-6 h-6 text-white" />
            </div>

            <h3 class="text-lg font-bold mb-2 text-white group-hover:text-brand-300 transition-colors">
              {{ $lang[feat.title] || feat.title }}
            </h3>
            <p class="text-sm text-surface-400 leading-relaxed">
              {{ $lang[feat.desc] || feat.desc }}
            </p>

            <!-- Hover glow -->
            <div class="absolute inset-0 rounded-2xl bg-gradient-to-br opacity-0 group-hover:opacity-5 transition-opacity" :class="feat.color"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════ How it Works ═══════════ -->
    <section class="relative py-24 md:py-32 bg-surface-900/50">
      <div class="max-w-6xl mx-auto px-6">
        <div class="text-center mb-16">
          <span class="text-sm font-semibold text-energy-500 uppercase tracking-widest mb-3 block">{{ $lang.howItWorks || 'How It Works' }}</span>
          <h2 class="text-3xl md:text-5xl font-display font-black tracking-tight">
            {{ $lang.howItWorksTitle || 'Get Started in' }}
            <span class="gradient-text"> {{ $lang.howItWorksTitle2 || '3 Simple Steps' }}</span>
          </h2>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <div
            v-for="(step, i) in [
              { num: '01', title: 'step1Title', desc: 'step1Desc', defaultTitle: 'Register Your Gym', defaultDesc: 'Create your account in seconds. Set up your gym profile, branding, and preferences.' },
              { num: '02', title: 'step2Title', desc: 'step2Desc', defaultTitle: 'Add Members', defaultDesc: 'Register members, assign plans, and generate QR-coded ID cards instantly.' },
              { num: '03', title: 'step3Title', desc: 'step3Desc', defaultTitle: 'Start Managing', defaultDesc: 'Scan QR codes for check-ins, track attendance, and view real-time analytics.' },
            ]"
            :key="step.num"
            class="relative text-center animate-slide-up"
            :class="`stagger-${i + 2}`"
          >
            <div class="text-6xl font-display font-black text-surface-800 mb-4">{{ step.num }}</div>
            <h3 class="text-xl font-bold mb-2">{{ $lang[step.title] || step.defaultTitle }}</h3>
            <p class="text-sm text-surface-400 leading-relaxed">{{ $lang[step.desc] || step.defaultDesc }}</p>

            <!-- Connector line -->
            <div v-if="i < 2" class="hidden md:block absolute top-8 -right-4 w-8 border-t border-dashed border-surface-700"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- ═══════════ CTA Section ═══════════ -->
    <section id="cta" class="relative py-24 md:py-32">
      <div class="absolute inset-0">
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[400px] bg-brand-500/10 rounded-full blur-[120px]"></div>
      </div>

      <div class="relative z-10 max-w-3xl mx-auto text-center px-6">
        <h2 class="text-3xl md:text-5xl font-display font-black tracking-tight mb-6">
          {{ $lang.ctaTitle || 'Ready to Transform' }}
          <br />
          <span class="gradient-text">{{ $lang.ctaTitle2 || 'Your Gym Operations?' }}</span>
        </h2>
        <p class="text-surface-400 mb-10 text-lg">
          {{ $lang.ctaSubtitle || 'Join hundreds of gyms already using our platform to manage members, automate check-ins, and grow their business.' }}
        </p>
        <RouterLink
          to="/register"
          id="cta-register"
          class="inline-flex items-center gap-2 bg-gradient-to-r from-brand-500 to-energy-500 text-white px-10 py-4 rounded-2xl font-semibold text-lg hover:shadow-2xl hover:shadow-brand-500/30 transition-all hover:-translate-y-1"
        >
          {{ $lang.getStartedFree || 'Get Started — It\'s Free' }}
          <ArrowRight class="w-5 h-5" />
        </RouterLink>
      </div>
    </section>

    <!-- ═══════════ Footer ═══════════ -->
    <footer class="border-t border-surface-800/50 py-12">
      <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-2">
          <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-brand-400 to-energy-500 flex items-center justify-center">
            <Dumbbell class="w-4 h-4 text-white" />
          </div>
          <span class="text-sm font-bold font-display">{{ $lang.appName || 'RohaGym' }}</span>
        </div>
        <p class="text-xs text-surface-500">&copy; 2026 {{ $lang.appName || 'RohaGym' }}. {{ $lang.allRightsReserved || 'All rights reserved.' }}</p>
      </div>
    </footer>
  </div>
</template>
