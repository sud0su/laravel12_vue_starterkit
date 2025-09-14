<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import InputError from '@/components/InputError.vue'
import AppLayout from '@/layouts/AppLayout.vue'
import { type BreadcrumbItem } from '@/types'
import { ref, computed, watch } from 'vue'

const props = defineProps<{
  permissions: Record<string, Array<{
    id: number
    name: string
    model: string
    action: string
  }>>
}>()

const breadcrumbs: BreadcrumbItem[] = [
  {
    title: 'Roles',
    href: '/roles',
  },
  {
    title: 'Create Role',
    href: '/roles/create',
  },
]

const form = useForm({
  name: '',
  guard_name: 'web',
  permissions: [] as number[],
})

const selectAll = ref(false)

const allPermissionIds = computed(() => {
  return Object.values(props.permissions).flat().map(p => p.id)
})

const isAllSelected = computed(() => {
  if (form.permissions.length === allPermissionIds.value.length && allPermissionIds.value.length > 0) {
    return true
  }
  if (form.permissions.length === 0) {
    return false
  }
  return null // Indeterminate state
})

watch(isAllSelected, (newValue) => {
  if (newValue === true) {
    selectAll.value = true
  } else if (newValue === false) {
    selectAll.value = false
  }
})

function toggleSelectAll() {
  if (isAllSelected.value === true) {
    form.permissions = []
  } else {
    form.permissions = allPermissionIds.value
  }
}

function submit() {
  form.post('/roles')
}
</script>

<template>
  <Head title="Create Role" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-3 overflow-x-auto rounded-xl p-4">
      <div class="flex items-center">
        <Button variant="ghost" as-child>
          <Link href="/roles">&larr; Back to Roles</Link>
        </Button>
      </div>

      <div class="mx-auto max-w-4xl">
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                </svg>
              </div>
              Create New Role
            </CardTitle>
            <CardDescription>
              Add a new role to manage user permissions across different models
            </CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-6">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="grid gap-2">
                  <Label for="name">Role Name</Label>
                  <Input
                    id="name"
                    v-model="form.name"
                    type="text"
                    placeholder="Enter role name"
                    required
                  />
                  <InputError :message="form.errors.name" />
                </div>

                <div class="grid gap-2">
                  <Label for="guard_name">Guard Name</Label>
                  <Input
                    id="guard_name"
                    v-model="form.guard_name"
                    type="text"
                    placeholder="web"
                    required
                  />
                  <InputError :message="form.errors.guard_name" />
                </div>
              </div>

              <div class="grid gap-4">
                <div>
                  <Label class="text-base font-semibold flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Permissions
                  </Label>
                  <p class="text-sm text-muted-foreground">Select permissions for this role, grouped by model</p>
                </div>

                <div class="flex items-center gap-2 mt-2">
                  <input
                    type="checkbox"
                    :checked="isAllSelected === true"
                    :indeterminate="isAllSelected === null"
                    @change="toggleSelectAll"
                    class="w-4 h-4 text-primary border-border rounded focus:ring-primary focus:ring-2 transition-colors"
                  />
                  <Label for="select-all-permissions" class="text-sm font-medium">Select All Permissions</Label>
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <template v-for="(permissions, model) in props.permissions" :key="model">
                    <Card class="border-l-4 border-l-primary/20 shadow-sm hover:shadow-md transition-shadow">
                      <CardHeader class="pb-4">
                        <CardTitle class="text-lg capitalize flex items-center gap-3">
                          <div class="w-4 h-4 rounded-full bg-primary/20 flex items-center justify-center">
                            <div class="w-2 h-2 rounded-full bg-primary"></div>
                          </div>
                          {{ model }} Permissions
                          <span class="text-sm font-normal text-muted-foreground">({{ permissions.length }} permissions)</span>
                        </CardTitle>
                        <CardDescription>
                          Manage {{ model }} access and ownership permissions
                        </CardDescription>
                      </CardHeader>
                      <CardContent>
                        <div class="grid grid-cols-2 gap-2">
                          <template v-for="permission in permissions" :key="permission.id">
                            <label class="group flex items-center space-x-3 p-4 rounded-lg border border-border/50 hover:border-primary/30 hover:bg-primary/5 transition-all cursor-pointer">
                              <input
                                type="checkbox"
                                :value="permission.id"
                                v-model="form.permissions"
                                class="w-4 h-4 text-primary border-border rounded focus:ring-primary focus:ring-2 transition-colors"
                              />
                              <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2">
                                  <span class="font-medium capitalize text-sm">{{ permission.action }}</span>
                                  <span class="text-xs text-muted-foreground bg-muted px-2 py-1 rounded-full">{{ model }}</span>
                                </div>
                                <p class="text-xs text-muted-foreground mt-1">{{ permission.name }}</p>
                              </div>
                            </label>
                          </template>
                        </div>
                      </CardContent>
                    </Card>
                  </template>
                </div>

                <InputError :message="form.errors.permissions" />
              </div>

              <div class="flex gap-4 pt-6 border-t">
                <Button type="submit" :disabled="form.processing" class="flex items-center gap-2">
                  <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                  </svg>
                  Create Role
                </Button>
                <Button variant="outline" as-child>
                  <Link href="/roles">Cancel</Link>
                </Button>
              </div>
            </form>
          </CardContent>
        </Card>
      </div>
    </div>
  </AppLayout>
</template>
