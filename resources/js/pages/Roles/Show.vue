<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { Head, Link } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Badge } from '@/components/ui/badge'
import { Label } from '@/components/ui/label'

const props = defineProps<{
  role: {
    id: number
    name: string
    guard_name: string
    permissions: Array<{
      id: number
      name: string
      guard_name: string
    }>
    created_at: string
    updated_at: string
  }
}>()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Dashboard',
    href: '/dashboard',
  },
  {
    title: 'Roles',
    href: '/roles',
  },
  {
    title: 'Role Details',
    href: `/roles/${props.role.id}`,
  },
]
</script>

<template>
  <Head :title="`Role: ${props.role.name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto rounded-xl p-4">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-semibold text-gray-900">Role Details</h1>
          <p class="text-sm text-gray-600">View role information and permissions</p>
        </div>
        <div class="flex gap-2">
          <Button variant="outline" as-child>
            <Link :href="`/roles/${props.role.id}/edit`">
              Edit Role
            </Link>
          </Button>
          <Button variant="outline" as-child>
            <Link href="/roles">
              Back to Roles
            </Link>
          </Button>
        </div>
      </div>

      <div class="grid gap-6 md:grid-cols-2">
        <!-- Role Information -->
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                </svg>
              </div>
              Role Information
            </CardTitle>
          </CardHeader>
          <CardContent class="space-y-4">
            <div>
              <Label class="text-sm font-medium text-gray-700">Role Name</Label>
              <p class="text-lg font-semibold">{{ props.role.name }}</p>
            </div>
            <div>
              <Label class="text-sm font-medium text-gray-700">Guard Name</Label>
              <Badge variant="secondary">{{ props.role.guard_name }}</Badge>
            </div>
            <div class="grid grid-cols-2 gap-4">
              <div>
                <Label class="text-sm font-medium text-gray-700">Created</Label>
                <p class="text-sm text-gray-600">{{ new Date(props.role.created_at).toLocaleDateString() }}</p>
              </div>
              <div>
                <Label class="text-sm font-medium text-gray-700">Updated</Label>
                <p class="text-sm text-gray-600">{{ new Date(props.role.updated_at).toLocaleDateString() }}</p>
              </div>
            </div>
          </CardContent>
        </Card>

        <!-- Permissions -->
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              Permissions ({{ props.role.permissions.length }})
            </CardTitle>
            <CardDescription>
              Permissions assigned to this role
            </CardDescription>
          </CardHeader>
          <CardContent>
            <div v-if="props.role.permissions.length > 0" class="space-y-2">
              <div v-for="permission in props.role.permissions" :key="permission.id" class="flex items-center justify-between p-3 border rounded-lg">
                <div>
                  <p class="font-medium">{{ permission.name }}</p>
                  <p class="text-sm text-gray-600">{{ permission.guard_name }}</p>
                </div>
                <Badge variant="outline" class="text-xs">
                  Active
                </Badge>
              </div>
            </div>
            <div v-else class="text-center py-8">
              <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              <h3 class="text-lg font-medium text-gray-900 mb-2">No permissions assigned</h3>
              <p class="text-gray-500">This role doesn't have any permissions yet.</p>
              <Button as-child class="mt-4">
                <Link :href="`/roles/${props.role.id}/edit`">
                  Assign Permissions
                </Link>
              </Button>
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
