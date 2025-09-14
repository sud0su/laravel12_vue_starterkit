<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
// Removed import { users } from '@/routes'; because it does not exist
import { type BreadcrumbItem } from '@/types';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import ConfirmationDialog from '@/components/ConfirmationDialog.vue';
import Toast from '@/components/ui/toast/Toast.vue';
// Removed Badge import because it does not exist, replaced with span with styling

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
        // Show success toast notification
        toastMessage.value = 'User deleted successfully!'
        toastType.value = 'success'
        showToast.value = true
      },
      onError: () => {
        // Show error toast notification
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

// Drag and drop functionality
const draggedRole = ref<number | null>(null);
const draggedUser = ref<number | null>(null);

const onDragStart = (event: DragEvent, roleId: number, userId: number) => {
  draggedRole.value = roleId;
  draggedUser.value = userId;
  if (event.dataTransfer) {
    event.dataTransfer.effectAllowed = 'move';
  }
};

const onDragOver = (event: DragEvent) => {
  event.preventDefault();
  if (event.dataTransfer) {
    event.dataTransfer.dropEffect = 'move';
  }
};

const onDrop = (event: DragEvent, targetUserId: number) => {
  event.preventDefault();

  if (draggedRole.value && draggedUser.value !== targetUserId) {
    // Here you would typically make an API call to update the user's roles
    // For now, we'll just show an alert
    alert(`Role assignment would be updated: Role ${draggedRole.value} to User ${targetUserId}`);
  }

  draggedRole.value = null;
  draggedUser.value = null;
};
</script>

<template>
  <Head title="Users Management" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900">Users Management</h1>
          <p class="text-sm text-gray-600">Manage users and their roles with drag and drop</p>
        </div>
        <Button v-if="userPermissions && userPermissions.includes('create users')" as-child>
          <Link href="/users/create" class="flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
            </svg>
            Create User
          </Link>
        </Button>
      </div>

      <div class="grid gap-3 md:grid-cols-2 lg:grid-cols-3">
        <Card
          v-for="user in users.data"
          :key="user.id"
          class="transition-all duration-200 hover:shadow-md"
          @dragover="onDragOver"
          @drop="(event: DragEvent) => onDrop(event, user.id)"
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
            <p class="text-sm text-gray-600">{{ user.email }}</p>
          </CardHeader>
          <CardContent>
            <div class="space-y-1">
              <p class="text-sm font-medium text-gray-700">Roles:</p>
              <div class="flex flex-wrap gap-1">
                <span
                  v-for="role in user.roles"
                  :key="role.id"
                  class="inline-block bg-gray-200 text-gray-700 rounded px-2 py-1 text-xs cursor-move select-none"
                  draggable="true"
                  @dragstart="(event: DragEvent) => onDragStart(event, role.id, user.id)"
                >
                  {{ role.name }}
                </span>
                <span v-if="user.roles.length === 0" class="inline-block border border-gray-300 rounded px-2 py-1 text-xs text-gray-500">
                  No roles assigned
                </span>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>

      <div v-if="users.data.length === 0" class="text-center py-12">
        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No users found</h3>
        <p class="text-gray-500">Users will appear here once they register.</p>
      </div>

      <!-- Pagination -->
      <div v-if="users.last_page > 1" class="flex justify-center">
        <div class="flex items-center gap-1">
          <Button
            v-for="link in users.links"
            :key="link.label"
            :variant="link.active ? 'default' : 'outline'"
            as-child
            size="sm"
          >
            <Link v-if="link.url" :href="link.url">{{ link.label }}</Link>
            <span v-else>{{ link.label }}</span>
          </Button>
        </div>
      </div>

      <!-- Available Roles for Reference -->
      <Card class="mt-6">
        <CardHeader>
          <CardTitle class="text-lg">Available Roles</CardTitle>
          <p class="text-sm text-gray-600">Drag roles from user cards to assign them to other users</p>
        </CardHeader>
        <CardContent>
          <div class="flex flex-wrap gap-1">
            <span
              v-for="role in roles"
              :key="role.id"
              class="inline-block border border-gray-300 rounded px-2 py-1 text-xs text-gray-500 cursor-not-allowed opacity-50"
            >
              {{ role.name }}
            </span>
          </div>
        </CardContent>
      </Card>
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
