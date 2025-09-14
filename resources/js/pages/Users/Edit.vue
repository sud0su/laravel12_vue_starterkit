<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';

const props = defineProps<{
  user: {
    id: number;
    name: string;
    email: string;
    roles: Array<{
      id: number;
      name: string;
      guard_name: string;
    }>;
  };
  allRoles: Array<{
    id: number;
    name: string;
    guard_name: string;
  }>;
  userRoles: number[];
}>();

const page = usePage();
const authUser = computed(() => page.props.auth.user as { id: number; roles: string[] });

// PERBAIKAN: Menggunakan nama peran huruf kecil
const isPrivilegedUser = computed(() => {
  return authUser.value?.roles?.some(role => ['admin', 'superadmin'].includes(role)) ?? false;
});

// PERBAIKAN: Cek apakah user yang sedang diedit adalah privileged
const targetUserIsPrivileged = computed(() => {
    return props.user.roles.some(role => ['admin', 'superadmin'].includes(role.name));
});

// --- PERBAIKAN: Redirect Guard ---
// Jika user yang login bukan privileged, TAPI mencoba mengedit user privileged, redirect mereka.
if (targetUserIsPrivileged.value && !isPrivilegedUser.value) {
    router.replace('/users');
}

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Users', href: '/users' },
  { title: 'Edit User', href: `/users/${props.user.id}/edit` },
];

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  password: '',
  password_confirmation: '',
  roles: [...props.userRoles],
});

// ... (sisa script tidak berubah)
const draggedRole = ref<number | null>(null);
const draggedFromUser = ref<boolean>(false);
const onDragStart = (event: any, roleId: number, fromUser: boolean) => {
  draggedRole.value = roleId;
  draggedFromUser.value = fromUser;
  event.dataTransfer.effectAllowed = 'move';
};
const onDragOver = (event: any) => {
  event.preventDefault();
  event.dataTransfer.dropEffect = 'move';
};
const onDropToUser = (event: any) => {
  event.preventDefault();
  if (draggedRole.value && !draggedFromUser.value) {
    if (!form.roles.includes(draggedRole.value)) {
      form.roles.push(draggedRole.value);
    }
  }
  draggedRole.value = null;
  draggedFromUser.value = false;
};
const onDropToAvailable = (event: any) => {
  event.preventDefault();
  if (draggedRole.value && draggedFromUser.value) {
    const index = form.roles.indexOf(draggedRole.value);
    if (index > -1) {
      form.roles.splice(index, 1);
    }
  }
  draggedRole.value = null;
  draggedFromUser.value = false;
};
const submit = () => {
  form.put(`/users/${props.user.id}`);
};
</script>

<template>
  <Head title="Edit User" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="flex flex-col items-start justify-between gap-4 sm:flex-row sm:items-center">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900 dark:text-gray-100">Edit User</h1>
          <p class="text-sm text-gray-600 dark:text-gray-400">Update user information and manage roles.</p>
        </div>
        <Button variant="outline" as-child>
          <Link href="/users">Back to Users</Link>
        </Button>
      </div>

      <div class="grid gap-4" :class="{ 'md:grid-cols-2': isPrivilegedUser }">
        <Card>
          <CardHeader>
            <CardTitle>User Information</CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <Label for="name">Name</Label>
              <Input id="name" v-model="form.name" placeholder="Enter user name" />
              <p v-if="form.errors.name" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.name }}</p>
            </div>
            <div>
              <Label for="email">Email</Label>
              <Input id="email" type="email" v-model="form.email" placeholder="Enter user email" />
              <p v-if="form.errors.email" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.email }}</p>
            </div>
            <div class="border-t pt-4 dark:border-gray-700">
              <h3 class="mb-3 flex items-center gap-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Change Password (Optional)
              </h3>
              <p class="mb-4 text-xs text-gray-500 dark:text-gray-400">Leave blank if you don't want to change the password.</p>
              <div class="space-y-3">
                <div>
                  <Label for="password">New Password</Label>
                  <Input id="password" type="password" v-model="form.password" placeholder="Enter new password" autocomplete="new-password" />
                  <p v-if="form.errors.password" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.password }}</p>
                </div>
                <div>
                  <Label for="password_confirmation">Confirm New Password</Label>
                  <Input id="password_confirmation" type="password" v-model="form.password_confirmation" placeholder="Confirm new password" autocomplete="new-password" />
                  <p v-if="form.errors.password_confirmation" class="mt-1 text-sm text-red-600 dark:text-red-400">{{ form.errors.password_confirmation }}</p>
                </div>
              </div>
            </div>
            <Button @click="submit" :disabled="form.processing" class="w-full !mt-6">
              {{ form.processing ? 'Updating...' : 'Update User' }}
            </Button>
          </CardContent>
        </Card>

        <Card v-if="isPrivilegedUser">
          <CardHeader>
            <CardTitle>Role Management</CardTitle>
            <p class="text-sm text-gray-600 dark:text-gray-400">Drag roles to assign or remove them.</p>
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <h3 class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">User's Roles</h3>
              <div class="min-h-[100px] rounded-lg border-2 border-dashed border-gray-300 p-4 transition-colors dark:border-gray-600" :class="{ 'border-blue-400 bg-blue-50 dark:border-blue-600 dark:bg-blue-900/20': draggedRole && !draggedFromUser }" @dragover="onDragOver" @drop="onDropToUser">
                <div class="flex flex-wrap gap-2">
                  <span v-for="roleId in form.roles" :key="roleId" class="inline-block select-none cursor-move rounded bg-blue-100 px-3 py-1 text-sm text-blue-800 dark:bg-blue-900/50 dark:text-blue-200" draggable="true" @dragstart="(event) => onDragStart(event, roleId, true)">
                    {{ allRoles.find(r => r.id === roleId)?.name }}
                  </span>
                  <span v-if="form.roles.length === 0" class="text-sm text-gray-500 dark:text-gray-400">No roles assigned.</span>
                </div>
              </div>
            </div>
            <div>
              <h3 class="mb-2 text-sm font-medium text-gray-700 dark:text-gray-300">Available Roles</h3>
              <div class="min-h-[100px] rounded-lg border-2 border-dashed border-gray-300 p-4 transition-colors dark:border-gray-600" :class="{ 'border-red-400 bg-red-50 dark:border-red-600 dark:bg-red-900/20': draggedRole && draggedFromUser }" @dragover="onDragOver" @drop="onDropToAvailable">
                <div class="flex flex-wrap gap-2">
                  <span v-for="role in allRoles.filter(r => !form.roles.includes(r.id))" :key="role.id" class="inline-block select-none cursor-move rounded bg-gray-100 px-3 py-1 text-sm text-gray-700 dark:bg-gray-700 dark:text-gray-200" draggable="true" @dragstart="(event) => onDragStart(event, role.id, false)">
                    {{ role.name }}
                  </span>
                  <span v-if="allRoles.filter(r => !form.roles.includes(r.id)).length === 0" class="text-sm text-gray-500 dark:text-gray-400">All roles are assigned.</span>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>