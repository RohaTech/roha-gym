<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import type { LucideIcon } from 'lucide-vue-next'
import {
  BarChart3,
  Building2,
  CalendarCheck,
  ChevronRight,
  CreditCard,
  Crown,
  IdCard,
  LayoutDashboard,
  LogOut,
  Settings,
  ShieldCheck,
  UserCog,
  Users,
} from 'lucide-vue-next'
import type { UserRole } from '@/types/roleTypes'
import { Collapsible, CollapsibleContent, CollapsibleTrigger } from '@/components/ui/collapsible'
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarGroup,
  SidebarGroupLabel,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
  SidebarMenuSub,
  SidebarMenuSubButton,
  SidebarMenuSubItem,
  SidebarRail,
  useSidebar,
} from '@/components/ui/sidebar'
import { useAuthStore } from '@/store/authStore'

type NavItem = {
  labelKey: string
  url: string
  icon?: LucideIcon
  isPrimary?: boolean
  items?: NavItem[]
}

type NavGroup = {
  labelKey: string
  items: NavItem[]
}

const props = defineProps<{
  role: UserRole
}>()

const route = useRoute()
const authStore = useAuthStore()
const { isMobile, setOpenMobile } = useSidebar()

const ownerNav: NavGroup[] = [
  {
    labelKey: 'sidebarMain',
    items: [
      { labelKey: 'sidebarDashboard', url: '/app', icon: LayoutDashboard },
      {
        labelKey: 'sidebarMembers',
        url: '/app/members',
        icon: Users,
        items: [
          { labelKey: 'sidebarAllMembers', url: '/app/members' },
          { labelKey: 'sidebarAddMember', url: '/app/members/new' },
          { labelKey: 'sidebarExpiringSoon', url: '/app/members/expiring' },
        ],
      },
      { labelKey: 'sidebarMembershipTypes', url: '/app/memberships', icon: IdCard },
      {
        labelKey: 'sidebarCheckIn',
        url: '/app/check-in',
        icon: CalendarCheck,
        isPrimary: true,
      },
      { labelKey: 'sidebarAnalytics', url: '/app/analytics', icon: BarChart3 },
      { labelKey: 'sidebarMembershipCards', url: '/app/members', icon: CreditCard },
      { labelKey: 'sidebarSettings', url: '/app/settings', icon: Settings },
    ],
  },
  {
    labelKey: 'sidebarAccount',
    items: [
      { labelKey: 'sidebarProfile', url: '/app/account/profile', icon: UserCog },
      { labelKey: 'sidebarPassword', url: '/app/account/password', icon: ShieldCheck },
    ],
  },
]

const adminNav: NavGroup[] = [
  {
    labelKey: 'sidebarMain',
    items: [
      { labelKey: 'sidebarDashboard', url: '/admin', icon: LayoutDashboard },
      {
        labelKey: 'sidebarGyms',
        url: '/admin/gyms',
        icon: Building2,
        items: [
          { labelKey: 'sidebarAllGyms', url: '/admin/gyms' },
          { labelKey: 'sidebarAddGym', url: '/admin/gyms/new' },
        ],
      },
      { labelKey: 'sidebarAnalytics', url: '/admin/analytics', icon: BarChart3 },
      { labelKey: 'sidebarSubscriptions', url: '/admin/subscriptions', icon: CreditCard },
      { labelKey: 'sidebarSystemSettings', url: '/admin/settings', icon: Settings },
    ],
  },
  {
    labelKey: 'sidebarAccount',
    items: [{ labelKey: 'sidebarAdminAccount', url: '/admin/account', icon: Crown }],
  },
]

const navGroups = computed(() => (props.role === 'admin' ? adminNav : ownerNav))
const accountUrl = computed(() =>
  props.role === 'admin' ? '/admin/account' : '/app/account/profile',
)

function isActive(item: NavItem) {
  if (item.items?.length) {
    return item.items.some((child) => route.path.startsWith(child.url))
  }
  return route.path.startsWith(item.url)
}

function handleLinkClick() {
  // Close mobile sidebar when a link is clicked
  if (isMobile.value) {
    setOpenMobile(false)
  }
}
</script>

<template>
  <Sidebar collapsible="icon">
    <SidebarHeader>
      <div class="px-3 py-2">
        <div
          class="flex items-center gap-2 rounded-xl border border-sidebar-border bg-sidebar/60 px-3 py-2"
        >
          <div
            class="flex size-8 items-center justify-center rounded-lg bg-sidebar-primary text-sidebar-primary-foreground"
          >
            <LayoutDashboard class="size-4" />
          </div>
          <div class="flex-1 text-left text-sm">
            <p class="font-semibold text-sidebar-foreground">{{ $lang.sidebarBrand }}</p>
            <p class="text-xs text-sidebar-foreground/70">{{ $lang.sidebarWorkspace }}</p>
          </div>
        </div>
      </div>
    </SidebarHeader>

    <SidebarContent>
      <SidebarGroup v-for="group in navGroups" :key="group.labelKey">
        <SidebarGroupLabel>{{ $lang[group.labelKey] }}</SidebarGroupLabel>
        <SidebarMenu>
          <Collapsible
            v-for="item in group.items"
            :key="item.labelKey"
            as-child
            :default-open="isActive(item)"
            class="group/collapsible"
          >
            <SidebarMenuItem>
              <CollapsibleTrigger v-if="item.items" as-child>
                <SidebarMenuButton :tooltip="$lang[item.labelKey]">
                  <component :is="item.icon" v-if="item.icon" />
                  <span>{{ $lang[item.labelKey] }}</span>
                  <ChevronRight
                    class="ml-auto size-4 transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                  />
                </SidebarMenuButton>
              </CollapsibleTrigger>
              <SidebarMenuButton
                v-else
                as-child
                :class="
                  item.isPrimary
                    ? 'bg-sidebar-primary/10 text-sidebar-primary hover:bg-sidebar-primary/20'
                    : ''
                "
              >
                <RouterLink :to="item.url" @click="handleLinkClick">
                  <component :is="item.icon" v-if="item.icon" />
                  <span>{{ $lang[item.labelKey] }}</span>
                </RouterLink>
              </SidebarMenuButton>
              <CollapsibleContent v-if="item.items">
                <SidebarMenuSub>
                  <SidebarMenuSubItem v-for="subItem in item.items" :key="subItem.labelKey">
                    <SidebarMenuSubButton as-child>
                      <RouterLink :to="subItem.url" @click="handleLinkClick">
                        <span>{{ $lang[subItem.labelKey] }}</span>
                      </RouterLink>
                    </SidebarMenuSubButton>
                  </SidebarMenuSubItem>
                </SidebarMenuSub>
              </CollapsibleContent>
            </SidebarMenuItem>
          </Collapsible>
        </SidebarMenu>
      </SidebarGroup>
    </SidebarContent>

    <SidebarFooter>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton as-child>
            <RouterLink :to="accountUrl" @click="handleLinkClick">
              <UserCog class="size-4" />
              <span>{{ $lang.sidebarAccount }}</span>
            </RouterLink>
          </SidebarMenuButton>
        </SidebarMenuItem>
        <SidebarMenuItem>
          <SidebarMenuButton @click="authStore.logout">
            <LogOut class="size-4" />
            <span>{{ $lang.sidebarLogout }}</span>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarFooter>

    <SidebarRail />
  </Sidebar>
</template>
