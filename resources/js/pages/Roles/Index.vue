<script setup lang="ts">
import { Head, Link, router } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import AppLayout from '@/layouts/AppLayout.vue'
import ConfirmationDialog from '@/components/ConfirmationDialog.vue'
import Toast from '@/components/ui/toast/Toast.vue'
import { type BreadcrumbItem } from '@/types'
import { ref } from 'vue'

interface Props {
  roles: {
    data: Array<{
      id: number
      name: string
      guard_name: string
      permissions: Array<{ id: number; name: string }>
      permission_counts: Array<{
        model: string
        count: number
        permissions: string[]
      }>
      created_at: string
      updated_at: string
    }>
    current_page: number
    last_page: number
    per_page: number
    total: number
    links: Array<{
      url: string | null
      label: string
      active: boolean
    }>
  }
  flash?: {
    success?: string
    error?: string
  }
}

defineProps<Props>()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
  {
    title: 'Roles',
    href: '/roles',
  },
]

// Confirmation dialog
const confirmDialog = ref<InstanceType<typeof ConfirmationDialog>>()
const roleToDelete = ref<number | null>(null)

// Toast notification
const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref<'success' | 'error' | 'info'>('success')

const confirmDelete = (roleId: number) => {
  roleToDelete.value = roleId
  confirmDialog.value?.show()
}

const handleDeleteConfirm = () => {
  if (roleToDelete.value) {
    router.delete(`/roles/${roleToDelete.value}`, {
      preserveScroll: true,
      onSuccess: () => {
        toastMessage.value = 'Role deleted successfully!'
        toastType.value = 'success'
        showToast.value = true
      },
      onError: () => {
        toastMessage.value = 'Failed to delete role. Please try again.'
        toastType.value = 'error'
        showToast.value = true
      }
    })
  }
}

const handleToastClose = () => {
  showToast.value = false
}
</script>

<template>
  <Head title="Roles Management" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
        <div>
          <!-- PERBAIKAN: Menambahkan class dark mode untuk judul dan deskripsi -->
          <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Roles Management</h1>
          <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Manage user roles and permissions</p>
        </div>
        <Button as-child class="transition-all duration-200 hover:shadow-md">
          <Link href="/roles/create" class="flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create Role
          </Link>
        </Button>
      </div>

      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <!-- PERBAIKAN: Mengganti hover effect dengan utility classes, menambahkan style dark mode -->
        <Card v-for="role in roles.data" :key="role.id" class="transition-all duration-200 hover:shadow-lg hover:scale-[1.02] dark:border-gray-700 dark:hover:border-primary/50">
          <CardHeader>
            <div class="flex items-center justify-between">
              <!-- PERBAIKAN: CardTitle sudah mendukung dark mode, tidak perlu diubah -->
              <CardTitle class="text-lg font-semibold capitalize">{{ role.name }}</CardTitle>
              <!-- PERBAIKAN: Menambahkan class dark mode untuk badge -->
              <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600 dark:bg-gray-700 dark:text-gray-300">
                {{ role.guard_name }}
              </span>
            </div>
            <!-- PERBAIKAN: CardDescription sudah mendukung dark mode, tidak perlu diubah -->
            <CardDescription class="text-xs">
              Created: {{ new Date(role.created_at).toLocaleDateString() }}
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div class="space-y-3">
              <div v-if="role.permission_counts && role.permission_counts.length > 0">
                <!-- PERBAIKAN: Menambahkan class dark mode untuk heading section -->
                <h4 class="mb-2 text-xs font-semibold uppercase text-gray-500 dark:text-gray-400">PERMISSIONS</h4>
                <div class="flex flex-wrap gap-1">
                  <!-- PERBAIKAN: Menambahkan class dark mode untuk badge permission -->
                  <span
                    v-for="permissionGroup in role.permission_counts"
                    :key="permissionGroup.model"
                    class="inline-flex items-center gap-1 rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10 dark:bg-blue-900/50 dark:text-blue-300 dark:ring-blue-400/20"
                  >
                    <span class="capitalize">{{ permissionGroup.model }}</span>
                    <!-- PERBAIKAN: Menambahkan class dark mode untuk count di dalam badge -->
                    <span class="rounded-full bg-blue-200 px-1 py-0.5 text-xs font-semibold text-blue-800 dark:bg-blue-500/50 dark:text-blue-100">
                      {{ permissionGroup.count }}
                    </span>
                  </span>
                </div>
              </div>

              <div class="flex gap-2 border-t pt-3 dark:border-gray-700">
                <Button variant="outline" size="sm" as-child class="flex-1">
                  <Link :href="`/roles/${role.id}/edit`">
                    Edit
                  </Link>
                </Button>
                <Button
                  variant="destructive"
                  size="sm"
                  @click="confirmDelete(role.id)"
                  class="flex-1"
                >
                  Delete
                </Button>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- PERBAIKAN: Menambahkan class dark mode untuk state kosong -->
        <div v-if="roles.data.length === 0" class="text-center rounded-lg border border-dashed border-gray-300 bg-gray-50 py-12 dark:border-gray-700 dark:bg-gray-800/50 md:col-span-2 lg:col-span-3">
          <div class="mx-auto max-w-md p-6">
            <svg class="mx-auto mb-4 h-16 w-16 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
            </svg>
            <h3 class="mb-2 text-xl font-semibold text-gray-800 dark:text-gray-200">No roles found</h3>
            <p class="mb-6 text-gray-600 dark:text-gray-400">Get started by creating your first role to manage user permissions.</p>
            <Button as-child>
              <Link href="/roles/create">
                Create First Role
              </Link>
            </Button>
          </div>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="roles.last_page > 1" class="flex justify-center">
        <div class="flex items-center gap-1">
          <template v-for="(link, index) in roles.links" :key="index">
            <Button
              v-if="link.url"
              :variant="link.active ? 'default' : 'outline'"
              as-child
              size="sm"
            >
              <!-- PERBAIKAN: Menggunakan v-html untuk render panah &raquo; dari Laravel -->
              <Link :href="link.url" v-html="link.label" />
            </Button>
            <!-- PERBAIKAN: Menambahkan class dark mode untuk label non-link -->
            <span v-else class="px-3 py-2 text-sm text-gray-400 dark:text-gray-500" v-html="link.label" />
          </template>
        </div>
      </div>
    </div>
  </AppLayout>

  <ConfirmationDialog
    ref="confirmDialog"
    title="Delete Role"
    description="Are you sure you want to delete this role? This action cannot be undone."
    confirm-text="Delete Role"
    cancel-text="Cancel"
    variant="destructive"
    icon="⚠️"
    @confirm="handleDeleteConfirm"
  />

  <Toast
    :message="toastMessage"
    :type="toastType"
    :show="showToast"
    @close="handleToastClose"
  />
</template>