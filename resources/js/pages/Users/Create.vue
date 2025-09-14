<script setup lang="ts">
import { Head, Link, useForm, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import InputError from '@/components/InputError.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'

const props = defineProps<{
  allRoles: Array<{
    id: number
    name: string
    guard_name: string
  }>
}>()

const page = usePage();
const authUser = computed(() => page.props.auth.user as { id: number; roles: Array<{name: string}> });

const isPrivilegedUser = computed(() => {
  return authUser.value?.roles?.some(role => ['admin', 'superadmin'].includes(role.name)) ?? false;
});

const availableRolesForAssignment = computed(() => {
  if (isPrivilegedUser.value) {
    return props.allRoles;
  }
  return props.allRoles.filter(role => !['admin', 'superadmin'].includes(role.name));
});

const breadcrumbs: BreadcrumbItem[] = [
  { title: 'Dashboard', href: '/dashboard' },
  { title: 'Users', href: '/users' },
  { title: 'Create User', href: '/users/create' },
]

const form = useForm({
  name: '',
  email: '',
  password: '',
  password_confirmation: '',
  roles: [] as number[],
})

function submit() {
  form.post('/users')
}
</script>

<template>
  <Head title="Create User" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-3 overflow-x-auto rounded-xl p-4">
      <div class="flex items-center">
        <Button variant="ghost" as-child>
          <Link href="/users">&larr; Back to Users</Link>
        </Button>
      </div>

      <div class="mx-auto max-w-4xl">
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-3">
              <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 dark:bg-primary/20">
                <svg class="h-4 w-4 text-primary dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
              </div>
              Create New User
            </CardTitle>
            <CardDescription>
              Add a new user to the system and assign roles
            </CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-6">
              <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="grid gap-2">
                  <Label for="name">Full Name</Label>
                  <Input id="name" v-model="form.name" type="text" placeholder="Enter full name" required autocomplete="name" />
                  <InputError :message="form.errors.name" />
                </div>
                <div class="grid gap-2">
                  <Label for="email">Email Address</Label>
                  <Input id="email" v-model="form.email" type="email" placeholder="Enter email address" required autocomplete="username" />
                  <InputError :message="form.errors.email" />
                </div>
              </div>
              <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                <div class="grid gap-2">
                  <Label for="password">Password</Label>
                  <Input id="password" v-model="form.password" type="password" placeholder="Enter password" required autocomplete="new-password" />
                  <InputError :message="form.errors.password" />
                </div>
                <div class="grid gap-2">
                  <Label for="password_confirmation">Confirm Password</Label>
                  <Input id="password_confirmation" v-model="form.password_confirmation" type="password" placeholder="Confirm password" required autocomplete="new-password" />
                  <InputError :message="form.errors.password_confirmation" />
                </div>
              </div>
              <div class="grid gap-4">
                <div>
                  <Label class="flex items-center gap-2 text-base font-semibold">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Roles
                  </Label>
                  <p class="text-sm text-muted-foreground">Select roles for this user (optional)</p>
                </div>
                <Card class="border-l-4 border-l-primary/20 shadow-sm transition-shadow hover:shadow-md">
                  <CardHeader class="pb-4">
                    <CardTitle class="flex items-center gap-3 text-lg capitalize">
                      <div class="flex h-4 w-4 items-center justify-center rounded-full bg-primary/20 dark:bg-primary/30"><div class="h-2 w-2 rounded-full bg-primary dark:bg-primary-400"></div></div>
                      Available Roles
                      <span class="text-sm font-normal text-muted-foreground">({{ availableRolesForAssignment.length }} roles)</span>
                    </CardTitle>
                    <CardDescription>Assign roles to manage user permissions</CardDescription>
                  </CardHeader>
                  <CardContent>
                    <div class="flex flex-wrap gap-3">
                      <template v-for="role in availableRolesForAssignment" :key="role.id">
                        <label :for="`role-${role.id}`" class="relative flex cursor-pointer items-center gap-3 rounded-lg border px-4 py-2 transition-all" :class="{ 'border-primary bg-primary/10 shadow-sm dark:border-primary/80 dark:bg-primary/20': form.roles.includes(role.id), 'border-border/50 hover:border-primary/30 hover:bg-primary/5 dark:hover:border-primary/40 dark:hover:bg-primary/10': !form.roles.includes(role.id) }">
                          <svg v-if="form.roles.includes(role.id)" class="h-4 w-4 text-primary dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                          <input :id="`role-${role.id}`" type="checkbox" :value="role.id" v-model="form.roles" class="sr-only">
                          <div class="text-sm font-medium capitalize">{{ role.name }}</div>
                        </label>
                      </template>
                    </div>
                  </CardContent>
                </Card>
                <InputError :message="form.errors.roles" />
              </div>
              <div class="flex gap-4 border-t pt-6">
                <Button type="submit" :disabled="form.processing" class="flex items-center gap-2">
                  <svg v-if="form.processing" class="h-4 w-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                  Create User
                </Button>
                <Button variant="outline" as-child><Link href="/users">Cancel</Link></Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
