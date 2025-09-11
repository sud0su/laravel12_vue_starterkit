<script setup lang="ts">
import { computed } from 'vue'
import NavFooter from '@/components/NavFooter.vue'
import NavUser from '@/components/NavUser.vue'
import {
  Sidebar,
  SidebarContent,
  SidebarFooter,
  SidebarHeader,
  SidebarMenu,
  SidebarMenuButton,
  SidebarMenuItem,
} from '@/components/ui/sidebar'
import { type NavItem } from '@/types'
import { Link, usePage } from '@inertiajs/vue3'
import {
  BookOpen,
  Folder,
  LayoutGrid,
  Users,
  Shield,
  Settings,
  BarChart3,
  User,
} from 'lucide-vue-next'
import AppLogo from './AppLogo.vue'

// --- TypeScript interface untuk menu ---
interface MenuItem {
  title: string
  href: string
  icon: string
  children?: MenuItem[]
}

// Inertia page
const page = usePage()

// Icon mapping
const iconMap: Record<string, any> = {
  LayoutGrid,
  Users,
  Shield,
  Settings,
  BarChart3,
  User,
  Folder,
  BookOpen,
}

const menuItems = computed(() => {
  const items = page.props.menuItems as MenuItem[] | undefined
  if (!Array.isArray(items)) return []
  return items.map((item) => ({
    title: item.title,
    href: item.href,
    icon: iconMap[item.icon] || LayoutGrid,
    children: item.children ?? [],
  }))
})

// Footer static menu
const footerNavItems: NavItem[] = [
  {
    title: 'Github Repo',
    href: 'https://github.com/laravel/vue-starter-kit',
    icon: Folder,
  },
  {
    title: 'Documentation',
    href: 'https://laravel.com/docs/starter-kits#vue',
    icon: BookOpen,
  },
]
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <!-- Header -->
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link href="/">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <!-- Content -->
    <SidebarContent>
      <SidebarMenu>
        <SidebarMenuItem v-for="item in menuItems" :key="item.href">
          <SidebarMenuButton as-child :tooltip="item.title">
            <Link :href="item.href">
              <component :is="item.icon" class="mr-2" />
              <span>{{ item.title }}</span>
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarContent>

    <!-- Footer -->
    <SidebarFooter>
      <NavFooter :items="footerNavItems" />
      <NavUser />
    </SidebarFooter>
  </Sidebar>

  <slot />
</template>
