<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { type BreadcrumbItem } from '@/types';
import { Head, Link, useForm, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardFooter, CardHeader, CardTitle } from '@/components/ui/card';
import InputError from '@/components/InputError.vue';
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
const authUser = computed(() => page.props.auth.user as { id: number; roles: Array<{name: string}> });

const isPrivilegedUser = computed(() => {
  return authUser.value?.roles?.some(role => ['admin', 'superadmin'].includes(role.name)) ?? false;
});

const targetUserIsPrivileged = computed(() => {
    return props.user.roles.some(role => ['admin', 'superadmin'].includes(role.name));
});

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
      <div class="flex items-center justify-end">
        <Button variant="ghost" as-child>
          <Link href="/users">Back to Users &rarr;</Link>
        </Button>
      </div>

      <div class="grid gap-4" :class="{ 'md:grid-cols-2': isPrivilegedUser }">
        <Card>
          <form @submit.prevent="submit">
            <CardHeader>
              <CardTitle>User Information</CardTitle>
              <CardDescription>Update the user's profile information.</CardDescription>
            </CardHeader>
            <CardContent class="space-y-6">
              <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input id="name" v-model="form.name" placeholder="e.g. John Doe" />
                <InputError :message="form.errors.name" />
              </div>
              <div class="grid gap-2">
                <Label for="email">Email</Label>
                <Input id="email" type="email" v-model="form.email" placeholder="e.g. john@example.com" />
                <InputError :message="form.errors.email" />
              </div>
              <div class="space-y-4 border-t pt-6">
                <h3 class="text-lg font-medium">Change Password</h3>
                <p class="text-sm text-muted-foreground">Leave blank if you don't want to change the password.</p>
                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                  <div class="grid gap-2">
                    <Label for="password">New Password</Label>
                    <Input id="password" type="password" v-model="form.password" placeholder="Enter new password" autocomplete="new-password" />
                    <InputError :message="form.errors.password" />
                  </div>
                  <div class="grid gap-2">
                    <Label for="password_confirmation">Confirm New Password</Label>
                    <Input id="password_confirmation" type="password" v-model="form.password_confirmation" placeholder="Confirm new password" autocomplete="new-password" />
                    <InputError :message="form.errors.password_confirmation" />
                  </div>
                </div>
              </div>
            </CardContent>
            <CardFooter class="flex justify-end border-t pt-6">
              <Button type="submit" :disabled="form.processing">
                {{ form.processing ? 'Updating...' : 'Update User' }}
              </Button>
            </CardFooter>
          </form>
        </Card>

        <Card v-if="isPrivilegedUser">
          <CardHeader>
            <CardTitle>Role Management</CardTitle>
            <p class="text-sm text-gray-600 dark:text-gray-400">Drag roles to assign or remove them.</p>
          </CardHeader>
          <CardContent class="grid grid-cols-1 gap-6 md:grid-cols-2">
            <div class="space-y-2">
              <h3 class="flex items-center gap-2 text-lg font-medium">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.653-.124-1.28-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.653.124-1.28.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                User's Roles
              </h3>
              <div class="min-h-[200px] rounded-lg border-2 border-dashed p-4 transition-colors" :class="{ 'border-primary bg-primary/10': draggedRole && !draggedFromUser }" @dragover="onDragOver" @drop="onDropToUser">
                <div class="flex flex-wrap gap-2">
                  <div v-for="roleId in form.roles" :key="roleId" class="flex cursor-move items-center gap-2 rounded-full bg-primary/10 px-3 py-1 text-sm font-medium text-primary" draggable="true" @dragstart="(event) => onDragStart(event, roleId, true)">
                    <span>{{ allRoles.find(r => r.id === roleId)?.name }}</span>
                    <button type="button" @click="form.roles = form.roles.filter(r => r !== roleId)" class="text-primary/50 hover:text-primary">&times;</button>
                  </div>
                  <p v-if="form.roles.length === 0" class="text-sm text-muted-foreground">Drag roles here to assign.</p>
                </div>
              </div>
            </div>
            <div class="space-y-2">
              <h3 class="flex items-center gap-2 text-lg font-medium">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                Available Roles
              </h3>
              <div class="min-h-[200px] rounded-lg border-2 border-dashed p-4 transition-colors" :class="{ 'border-destructive bg-destructive/10': draggedRole && draggedFromUser }" @dragover="onDragOver" @drop="onDropToAvailable">
                <div class="flex flex-wrap gap-2">
                  <div v-for="role in allRoles.filter(r => !form.roles.includes(r.id))" :key="role.id" class="cursor-move rounded-full bg-muted px-3 py-1 text-sm font-medium text-muted-foreground" draggable="true" @dragstart="(event) => onDragStart(event, role.id, false)">
                    {{ role.name }}
                  </div>
                  <p v-if="allRoles.filter(r => !form.roles.includes(r.id)).length === 0" class="text-sm text-muted-foreground">All roles have been assigned.</p>
                </div>
              </div>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>