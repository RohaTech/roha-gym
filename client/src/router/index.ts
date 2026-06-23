import { createRouter, createWebHistory } from 'vue-router'
import { useAuthStore } from '@/store/authStore'
import type { UserRole } from '@/types/roleTypes'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      component: () => import('@/layouts/PublicLayout.vue'),
      children: [
        {
          path: '',
          name: 'home',
          component: () => import('@/views/HomeView.vue'),
          meta: { requiresAuth: false, roles: ['guest'] },
        },
        {
          path: 'login',
          name: 'login',
          component: () => import('@/views/LoginView.vue'),
          meta: { requiresAuth: false, roles: ['guest'] },
        },
        {
          path: 'register',
          name: 'register',
          component: () => import('@/views/RegisterView.vue'),
          meta: { requiresAuth: false, roles: ['guest'] },
        },
        {
          path: 'forbidden',
          name: 'forbidden',
          component: () => import('@/views/ForbiddenView.vue'),
          meta: { requiresAuth: false, roles: ['guest'] },
        },
      ],
    },
    {
      path: '/admin',
      component: () => import('@/layouts/AdminLayout.vue'),
      meta: { requiresAuth: true, roles: ['admin'] },
      children: [
        {
          path: '',
          name: 'admin-dashboard',
          component: () => import('@/views/AdminDashboardView.vue'),
          meta: { requiresAuth: true, roles: ['admin'] },
        },
      ],
    },
    {
      path: '/app',
      component: () => import('@/layouts/UserLayout.vue'),
      meta: { requiresAuth: true, roles: ['user'] },
      children: [
        {
          path: '',
          name: 'user-dashboard',
          component: () => import('@/views/UserDashboardView.vue'),
          meta: { requiresAuth: true, roles: ['user'] },
        },
        {
          path: 'memberships',
          name: 'membership-types',
          component: () => import('@/views/MembershipTypesView.vue'),
          meta: { requiresAuth: true, roles: ['user'] },
        },
        {
          path: 'members',
          name: 'members-list',
          component: () => import('@/views/MembersListView.vue'),
          meta: { requiresAuth: true, roles: ['user'] },
        },
        {
          path: 'members/new',
          name: 'member-create',
          component: () => import('@/views/MemberCreateView.vue'),
          meta: { requiresAuth: true, roles: ['user'] },
        },
        {
          path: 'members/:memberId/edit',
          name: 'member-edit',
          component: () => import('@/views/members/MemberEditView.vue'),
          meta: { requiresAuth: true, roles: ['user'] },
        },
        {
          path: 'members/expiring',
          name: 'members-expiring',
          component: () => import('@/views/MembersExpiringView.vue'),
          meta: { requiresAuth: true, roles: ['user'] },
        },
        {
          path: 'check-in',
          name: 'check-in',
          component: () => import('@/views/CheckInView.vue'),
          meta: { requiresAuth: true, roles: ['user'] },
        },
        {
          path: 'analytics',
          name: 'user-analytics',
          component: () => import('@/views/UserAnalyticsView.vue'),
          meta: { requiresAuth: true, roles: ['user'] },
        },
      ],
    },
    {
      path: '/members/:memberId/card',
      name: 'member-card',
      component: () => import('@/views/members/MemberCardView.vue'),
      meta: { requiresAuth: true, roles: ['user'] },
    },
    {
      path: '/:pathMatch(.*)*',
      name: 'not-found',
      component: () => import('@/views/NotFoundView.vue'),
      meta: { requiresAuth: false, roles: ['guest'] },
    },
  ],
  scrollBehavior(to) {
    if (to.hash) {
      return { el: to.hash, behavior: 'smooth' }
    }
    return { top: 0 }
  },
})

router.beforeEach(async (to) => {
  const authStore = useAuthStore()
  await authStore.initialize()

  const requiresAuth = to.matched.some((record) => record.meta.requiresAuth)
  const requiredRoles = to.matched.flatMap((record) => record.meta.roles as UserRole[] | undefined)

  if (!requiresAuth && requiredRoles.includes('guest') && authStore.isAuthenticated) {
    if (authStore.userRole === 'admin') {
      return { name: 'admin-dashboard' }
    }

    return { name: 'user-dashboard' }
  }

  if (requiresAuth && !authStore.isAuthenticated) {
    return { name: 'login' }
  }

  const authRoles = requiredRoles.filter(Boolean) as UserRole[]
  if (requiresAuth && authRoles.length > 0 && !authRoles.includes(authStore.userRole)) {
    return { name: 'forbidden' }
  }
})

export default router
