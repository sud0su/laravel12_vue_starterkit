<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import ConfirmationDialog from '@/components/ConfirmationDialog.vue';
import Toast from '@/components/ui/toast/Toast.vue';

defineProps<{
  users: {
    data: Array<{
      id: number;
      name: string;
      email: string;
      roles: Array<{
        id: number;
        name: string;
        guard_name: string;
      }>;
      created_at: string;
    }>;
    current_page: number;
    last_page: number;
    links: Array<{
      url: string | null;
      label: string;
      active: boolean;
    }>;
  };
  roles: Array<{
    id: number;
    name: string;
    guard_name: string;
  }>;
}>();

const page = usePage()
const { userPermissions } = page.props as { userPermissions?: string[] };

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
  {
    title: 'Users',
    href: '/users',
  },
];

// Confirmation dialog
const confirmDialog = ref<InstanceType<typeof ConfirmationDialog>>()
const userToDelete = ref<number | null>(null)

const confirmDelete = (userId: number) => {
  userToDelete.value = userId
  confirmDialog.value?.show()
}

const showToast = ref(false)
const toastMessage = ref('')
const toastType = ref<'success' | 'error' | 'info'>('success')

const handleDeleteConfirm = () => {
  if (userToDelete.value) {
    router.delete(`/users/${userToDelete.value}`, {
      preserveScroll: true,
      onSuccess: () => {
        userToDelete.value = null;
        confirmDialog.value?.hide();
        toastMessage.value = 'User deleted successfully!'
        toastType.value = 'success'
        showToast.value = true
      },
      onError: () => {
        toastMessage.value = 'Failed to delete user. Please try again.'
        toastType.value = 'error'
        showToast.value = true
      }
    });
  }
}

const handleToastClose = () => {
  showToast.value = false
}

// Drag and drop functionality (simplified for this example)
const onDragOver = (event: DragEvent) => {
  event.preventDefault();
  if (event.dataTransfer) {
    event.dataTransfer.dropEffect = 'move';
  }
};
</script>

<template>
  <Head title="Users Management" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Users Management</h1>
          <p class="text-sm text-gray-600 dark:text-gray-400">A list of all the users in your account including their name, and email.</p>
        </div>
        <Button v-if="userPermissions && userPermissions.includes('create users')" as-child>
          <Link href="/users/create" class="flex items-center gap-2">
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create User
          </Link>
        </Button>
      </div>

      <div class="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
        <Card
          v-for="user in users.data"
          :key="user.id"
          class="transition-all duration-200 hover:shadow-md"
          @dragover="onDragOver"
        >
          <CardHeader>
            <div class="flex items-center justify-between">
              <CardTitle class="text-lg">{{ user.name }}</CardTitle>
              <div class="flex gap-2">
                <Button v-if="userPermissions && userPermissions.includes('edit users')" variant="outline" size="sm" as-child>
                  <Link :href="`/users/${user.id}/edit`">
                    Edit
                  </Link>
                </Button>
                <Button
                  v-if="userPermissions && userPermissions.includes('delete users')"
                  variant="destructive"
                  size="sm"
                  @click="confirmDelete(user.id)"
                >
                  Delete
                </Button>
              </div>
            </div>
            <p class="text-sm text-gray-600 dark:text-gray-400">{{ user.email }}</p>
          </CardHeader>
          <CardContent>
            <div class="space-y-2">
              <p class="text-sm font-medium text-gray-700 dark:text-gray-300">Roles:</p>
              <div class="flex min-h-[30px] flex-wrap gap-2 rounded-md border bg-slate-50 p-2 dark:border-slate-700 dark:bg-slate-800/50">
                <span
                  v-for="role in user.roles"
                  :key="role.id"
                  class="inline-block select-none rounded bg-gray-200 px-2 py-1 text-xs text-gray-700 dark:bg-gray-700 dark:text-gray-200"
                >
                  {{ role.name }}
                </span>
                <span v-if="user.roles.length === 0" class="inline-block rounded px-2 py-1 text-xs text-gray-500 dark:text-gray-400">
                  No roles assigned
                </span>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <div v-if="users.data.length === 0" class="rounded-lg border border-dashed bg-gray-50/50 py-12 text-center dark:border-gray-700 dark:bg-gray-800/20 md:col-span-2 lg:col-span-3">
        <svg class="mx-auto mb-4 h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
        </svg>
        <h3 class="mb-2 text-lg font-medium text-gray-900 dark:text-gray-200">No users found</h3>
        <p class="text-gray-500 dark:text-gray-400">Get started by creating a new user.</p>
      </div>

      <!-- Pagination -->
      <div v-if="users.last_page > 1" class="flex justify-center">
        <div class="flex items-center gap-1">
          <template v-for="(link, index) in users.links" :key="index">
            <Button
              v-if="link.url"
              :variant="link.active ? 'default' : 'outline'"
              as-child
              size="sm"
            >
              <Link :href="link.url" v-html="link.label" />
            </Button>
            <span v-else class="px-3 py-2 text-sm text-gray-400 dark:text-gray-500" v-html="link.label" />
          </template>
        </div>
      </div>
    </div>
  </AppLayout>

  <!-- Confirmation Dialog -->
  <ConfirmationDialog
    ref="confirmDialog"
    title="Delete User"
    description="Are you sure you want to delete this user? This action cannot be undone."
    confirm-text="Delete User"
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