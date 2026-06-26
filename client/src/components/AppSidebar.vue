<script setup lang="ts">
import { computed } from 'vue'
import { RouterLink, useRoute } from 'vue-router'
import type { LucideIcon } from 'lucide-vue-next'
import {
  Building2,
  CalendarCheck,
  ChevronRight,
  CreditCard,
  IdCard,
  LayoutDashboard,
  LogOut,
  User,
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
      { labelKey: 'sidebarDashboard', url: '/app/dashboard', icon: LayoutDashboard },
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
      { labelKey: 'sidebarCheckIn', url: '/app/check-in', icon: CalendarCheck },
    ],
  },
  {
    labelKey: 'sidebarAccount',
    items: [{ labelKey: 'sidebarProfile', url: '/app/profile', icon: User }],
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
      { labelKey: 'sidebarSubscriptions', url: '/admin/subscriptions', icon: CreditCard },
    ],
  },
]

const navGroups = computed(() => (props.role === 'admin' ? adminNav : ownerNav))

function isActive(item: NavItem) {
  if (item.items?.length) {
    // Parent item: active if any child matches (exact or sub-path)
    return item.items.some((child) =>
      route.path === child.url || route.path.startsWith(child.url + '/'),
    )
  }
  // Leaf item: only exact match
  return route.path === item.url
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
                <SidebarMenuButton :tooltip="$lang[item.labelKey]" :is-active="isActive(item)">
                  <component :is="item.icon" v-if="item.icon" />
                  <span>{{ $lang[item.labelKey] }}</span>
                  <ChevronRight
                    class="ml-auto size-4 transition-transform duration-200 group-data-[state=open]/collapsible:rotate-90"
                  />
                </SidebarMenuButton>
              </CollapsibleTrigger>
              <SidebarMenuButton v-else as-child :is-active="isActive(item)">
                <RouterLink :to="item.url" @click="handleLinkClick">
                  <component :is="item.icon" v-if="item.icon" />
                  <span>{{ $lang[item.labelKey] }}</span>
                </RouterLink>
              </SidebarMenuButton>
              <CollapsibleContent v-if="item.items">
                <SidebarMenuSub>
                  <SidebarMenuSubItem v-for="subItem in item.items" :key="subItem.labelKey">
                    <SidebarMenuSubButton as-child :is-active="route.path === subItem.url">
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
