<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3'
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

const page = usePage()

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
        // Show success toast notification
        toastMessage.value = 'Role deleted successfully!'
        toastType.value = 'success'
        showToast.value = true
      },
      onError: () => {
        // Show error toast notification
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
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900">Roles Management</h1>
          <p class="text-sm text-gray-600">Manage user roles and permissions</p>
        </div>
        <Button as-child>
          <Link href="/roles/create" class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create Role
          </Link>
        </Button>
      </div>

      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <Card v-for="role in roles.data" :key="role.id">
          <CardHeader>
            <div class="flex items-center justify-between">
              <CardTitle class="text-lg">{{ role.name }}</CardTitle>
              <span class="inline-flex items-center rounded-md bg-gray-100 px-2 py-1 text-xs font-medium text-gray-600">
                {{ role.guard_name }}
              </span>
            </div>
            <CardDescription>
              Created: {{ new Date(role.created_at).toLocaleDateString() }}
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div class="space-y-4">
              <!-- Permissions Section -->
              <div v-if="role.permission_counts && role.permission_counts.length > 0">
                <h4 class="text-sm font-semibold text-muted-foreground mb-2">PERMISSIONS</h4>
                <div class="flex flex-wrap gap-2">
                  <span
                    v-for="permissionGroup in role.permission_counts"
                    :key="permissionGroup.model"
                    class="inline-flex items-center gap-2 rounded-md bg-blue-50 px-2 py-1 text-xs font-medium text-blue-700 ring-1 ring-inset ring-blue-700/10"
                  >
                    <span class="capitalize">{{ permissionGroup.model }}</span>
                    <span class="bg-blue-200 text-blue-800 px-1.5 py-0.5 rounded text-xs font-semibold">
                      {{ permissionGroup.count }}
                    </span>
                  </span>
                </div>
              </div>

              <!-- Action Buttons -->
              <div class="flex gap-2 pt-2 border-t">
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
      </div>

      <div v-if="roles.data.length === 0" class="text-center py-12">
        <div class="max-w-md mx-auto">
          <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
          </svg>
          <h3 class="text-lg font-medium text-gray-900 mb-2">No roles found</h3>
          <p class="text-gray-500 mb-6">Get started by creating your first role to manage user permissions.</p>
          <Button as-child>
            <Link href="/roles/create">
              Create First Role
            </Link>
          </Button>
        </div>
      </div>

      <!-- Pagination -->
      <div v-if="roles.last_page > 1" class="flex justify-center">
        <div class="flex items-center gap-1">
          <template v-for="link in roles.links" :key="link.label">
            <Button
              v-if="link.url"
              :variant="link.active ? 'default' : 'outline'"
              as-child
              size="sm"
            >
              <Link :href="link.url">{{ link.label }}</Link>
            </Button>
            <span v-else class="px-3 py-2 text-sm text-gray-400">{{ link.label }}</span>
          </template>
        </div>
      </div>
    </div>
  </AppLayout>

  <!-- Confirmation Dialog -->
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

  <!-- Toast Notification -->
  <Toast
    :message="toastMessage"
    :type="toastType"
    :show="showToast"
    @close="handleToastClose"
  />
</template>

<style scoped>
/* Add subtle hover effect on cards */
.card:hover {
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
  transform: translateY(-4px);
  transition: all 0.3s ease;
}

/* Style the role title */
.card-title {
  font-weight: 700;
  font-size: 1.125rem;
  color: #1f2937; /* Tailwind gray-800 */
}

/* Style the guard badge */
.guard-badge {
  background-color: #e0e7ff; /* Tailwind indigo-100 */
  color: #4338ca; /* Tailwind indigo-700 */
  font-weight: 600;
  padding: 0.125rem 0.5rem;
  border-radius: 0.375rem;
  font-size: 0.75rem;
}

/* Style the created date */
.created-date {
  font-size: 0.875rem;
  color: #6b7280; /* Tailwind gray-500 */
}

/* Style buttons */
.button-group > button {
  transition: background-color 0.3s ease;
}

.button-group > button:hover {
  background-color: #4338ca; /* Tailwind indigo-700 */
  color: white;
}

/* Pagination buttons */
.pagination-button {
  border-radius: 0.375rem;
  padding: 0.5rem 0.75rem;
  font-weight: 600;
  cursor: pointer;
}

.pagination-button.active {
  background-color: #4338ca;
  color: white;
  border: none;
}

.pagination-button:not(.active):hover {
  background-color: #c7d2fe;
  color: #4338ca;
}
</style>
