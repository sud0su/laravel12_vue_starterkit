<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
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
    title: 'Create User',
    href: '/users/create',
  },
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
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="flex items-center">
        <Button variant="ghost" as-child>
          <Link href="/users">&larr; Back to Users</Link>
        </Button>
      </div>

      <div class="mx-auto max-w-4xl">
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
              </div>
              Create New User
            </CardTitle>
            <CardDescription>
              Add a new user to the system and assign roles
            </CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-8">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="grid gap-2">
                  <Label for="name">Full Name</Label>
                  <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    placeholder="Enter full name"
                    required
                    autocomplete="name"
                  />
                  <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                  <Label for="email">Email Address</Label>
                  <Input
                    id="email"
                    v-model="form.email"
                    type="email"
                    placeholder="Enter email address"
                    required
                    autocomplete="username"
                  />
                  <InputError :message="form.errors.email" />
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="grid gap-2">
                  <Label for="password">Password</Label>
                  <Input
                    id="password"
                    v-model="form.password"
                    type="password"
                    placeholder="Enter password"
                    required
                    autocomplete="new-password"
                  />
                  <InputError :message="form.errors.password" />
                </div>

                <div class="grid gap-2">
                  <Label for="password_confirmation">Confirm Password</Label>
                  <Input
                    id="password_confirmation"
                    v-model="form.password_confirmation"
                    type="password"
                    placeholder="Confirm password"
                    required
                    autocomplete="new-password"
                  />
                  <InputError :message="form.errors.password_confirmation" />
                </div>
              </div>

              <div class="grid gap-6">
                <div>
                  <Label class="text-base font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Roles
                  </Label>
                  <p class="text-sm text-muted-foreground">Select roles for this user (optional)</p>
                </div>

                <div class="grid grid-cols-2 gap-6">
                  <Card class="border-l-4 border-l-primary/20 shadow-sm hover:shadow-md transition-shadow">
                    <CardHeader class="pb-4">
                      <CardTitle class="text-lg capitalize flex items-center gap-3">
                        <div class="w-4 h-4 rounded-full bg-primary/20 flex items-center justify-center">
                          <div class="w-2 h-2 rounded-full bg-primary"></div>
                        </div>
                        Available Roles
                        <span class="text-sm font-normal text-muted-foreground">({{ props.allRoles.length }} roles)</span>
                      </CardTitle>
                      <CardDescription>
                        Assign roles to manage user permissions
                      </CardDescription>
                    </CardHeader>
                    <CardContent>
                      <div class="grid grid-cols-1 gap-3">
                        <template v-for="role in props.allRoles" :key="role.id">
                          <label class="group flex items-center space-x-3 p-4 rounded-lg border border-border/50 hover:border-primary/30 hover:bg-primary/5 transition-all cursor-pointer">
                            <input
                              type="checkbox"
                              :value="role.id"
                              v-model="form.roles"
                              class="w-4 h-4 text-primary border-border rounded focus:ring-primary focus:ring-2 transition-colors"
                            />
                            <div class="flex-1 min-w-0">
                              <div class="flex items-center gap-2">
                                <span class="font-medium capitalize text-sm">{{ role.name }}</span>
                                <span class="text-xs text-muted-foreground bg-muted px-2 py-1 rounded-full">{{ role.guard_name }}</span>
                              </div>
                              <p class="text-xs text-muted-foreground mt-1">Role permissions and access control</p>
                            </div>
                          </label>
                        </template>
                      </div>
                    </CardContent>
                  </Card>
                </div>

                <InputError :message="form.errors.roles" />
              </div>

              <div class="flex gap-4 pt-6 border-t">
                <Button type="submit" :disabled="form.processing" class="flex items-center gap-2">
                  <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                  </svg>
                  Create User
                </Button>
                <Button variant="outline" as-child>
                  <Link href="/users">Cancel</Link>
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
