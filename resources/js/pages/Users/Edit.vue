<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
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

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
  {
    title: 'Users',
    href: '/users',
  },
  {
    title: 'Edit User',
    href: `/users/${props.user.id}/edit`,
  },
];

const form = useForm({
  name: props.user.name,
  email: props.user.email,
  password: '',
  password_confirmation: '',
  roles: [...props.userRoles],
});

// Drag and drop functionality
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
    // Adding role to user
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
    // Removing role from user
    const index = form.roles.indexOf(draggedRole.value);
    if (index > -1) {
      form.roles.splice(index, 1);
    }
  }

  draggedRole.value = null;
  draggedFromUser.value = false;
};

const submit = () => {
  form.put(`/users/${props.user.id}`, {
    onSuccess: () => {
      // Handle success
    },
    onError: () => {
      // Handle error
    },
  });
};
</script>

<template>
  <Head title="Edit User" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-3 overflow-x-auto rounded-xl p-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900">Edit User</h1>
          <p class="text-sm text-gray-600">Update user information and manage roles</p>
        </div>
        <Button variant="outline" as-child>
          <Link href="/users">Back to Users</Link>
        </Button>
      </div>

      <div class="grid gap-4 md:grid-cols-2">
        <!-- User Information -->
        <Card>
          <CardHeader>
            <CardTitle>User Information</CardTitle>
          </CardHeader>
          <CardContent class="space-y-3">
            <div>
              <Label for="name">Name</Label>
              <Input
                id="name"
                v-model="form.name"
                :error="form.errors.name"
                placeholder="Enter user name"
              />
              <p v-if="form.errors.name" class="text-sm text-red-600 mt-1">
                {{ form.errors.name }}
              </p>
            </div>

            <div>
              <Label for="email">Email</Label>
              <Input
                id="email"
                type="email"
                v-model="form.email"
                :error="form.errors.email"
                placeholder="Enter user email"
              />
              <p v-if="form.errors.email" class="text-sm text-red-600 mt-1">
                {{ form.errors.email }}
              </p>
            </div>

            <!-- Password Section -->
            <div class="border-t pt-4">
              <h3 class="text-sm font-medium text-gray-700 mb-3 flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                </svg>
                Change Password (Optional)
              </h3>
              <p class="text-xs text-gray-500 mb-4">Leave blank if you don't want to change the password</p>

              <div class="space-y-2">
                <div>
                  <Label for="password">New Password</Label>
                  <Input
                    id="password"
                    type="password"
                    v-model="form.password"
                    :error="form.errors.password"
                    placeholder="Enter new password"
                    autocomplete="new-password"
                  />
                  <p v-if="form.errors.password" class="text-sm text-red-600 mt-1">
                    {{ form.errors.password }}
                  </p>
                </div>

                <div>
                  <Label for="password_confirmation">Confirm New Password</Label>
                  <Input
                    id="password_confirmation"
                    type="password"
                    v-model="form.password_confirmation"
                    :error="form.errors.password_confirmation"
                    placeholder="Confirm new password"
                    autocomplete="new-password"
                  />
                  <p v-if="form.errors.password_confirmation" class="text-sm text-red-600 mt-1">
                    {{ form.errors.password_confirmation }}
                  </p>
                </div>
              </div>
            </div>

            <Button
              @click="submit"
              :disabled="form.processing"
              class="w-full"
            >
              {{ form.processing ? 'Updating...' : 'Update User' }}
            </Button>
          </CardContent>
        </Card>

        <!-- Role Management -->
        <Card>
          <CardHeader>
            <CardTitle>Role Management</CardTitle>
            <p class="text-sm text-gray-600">Drag roles between sections to assign or remove them</p>
          </CardHeader>
          <CardContent class="space-y-4">
            <!-- User's Current Roles -->
            <div>
              <h3 class="text-sm font-medium text-gray-700 mb-2">User's Roles</h3>
              <div
                class="min-h-[100px] border-2 border-dashed border-gray-300 rounded-lg p-4 transition-colors"
                :class="{ 'border-blue-400 bg-blue-50': draggedRole && !draggedFromUser }"
                @dragover="onDragOver"
                @drop="onDropToUser"
              >
                <div class="flex flex-wrap gap-1">
                  <span
                    v-for="roleId in form.roles"
                    :key="roleId"
                    class="inline-block bg-blue-100 text-blue-800 rounded px-3 py-1 text-sm cursor-move select-none"
                    draggable="true"
                    @dragstart="(event) => onDragStart(event, roleId, true)"
                  >
                    {{ allRoles.find(r => r.id === roleId)?.name }}
                  </span>
                  <span v-if="form.roles.length === 0" class="text-gray-500 text-sm">
                    No roles assigned. Drag roles here to assign them.
                  </span>
                </div>
              </div>
            </div>

            <!-- Available Roles -->
            <div>
              <h3 class="text-sm font-medium text-gray-700 mb-2">Available Roles</h3>
              <div
                class="min-h-[100px] border-2 border-dashed border-gray-300 rounded-lg p-4 transition-colors"
                :class="{ 'border-red-400 bg-red-50': draggedRole && draggedFromUser }"
                @dragover="onDragOver"
                @drop="onDropToAvailable"
              >
                <div class="flex flex-wrap gap-1">
                  <span
                    v-for="role in allRoles.filter(r => !form.roles.includes(r.id))"
                    :key="role.id"
                    class="inline-block bg-gray-100 text-gray-700 rounded px-3 py-1 text-sm cursor-move select-none"
                    draggable="true"
                    @dragstart="(event) => onDragStart(event, role.id, false)"
                  >
                    {{ role.name }}
                  </span>
                  <span v-if="allRoles.filter(r => !form.roles.includes(r.id)).length === 0" class="text-gray-500 text-sm">
                    All roles are assigned to this user.
                  </span>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
