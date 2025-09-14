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
import Toggle from '@/components/ui/toggle/Toggle.vue'

const props = defineProps<{
  role: {
    id: number
    name: string
    guard_name: string
    permissions: Array<{ id: number; name: string }>
  }
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
    title: 'Edit Role',
    href: `/roles/${props.role.id}/edit`,
  },
]

const form = useForm({
  name: props.role.name,
  guard_name: props.role.guard_name,
  permissions: props.role.permissions.map((p) => p.id),
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
  form.put(`/roles/${props.role.id}`)
}
</script>

<template>
  <Head :title="`Edit Role - ${props.role.name}`" />

  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="flex h-full flex-1 flex-col gap-3 overflow-x-auto rounded-xl p-4">
      <div class="flex items-center">
        <Button variant="ghost" as-child>
          <Link href="/roles">&larr; Back to Roles</Link>
        </Button>
      </div>

      <div class="w-full">
        <Card>
          <CardHeader>
            <CardTitle class="flex items-center gap-3">
              <div class="w-8 h-8 rounded-lg bg-primary/5 flex items-center justify-center">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h-2A2 2 0 0113 18V7a2 2 0 012-2h2a2 2 0 012 2v11a2 2 0 01-2 2zM7 20H5a2 2 0 01-2-2V9a2 2 0 012-2h2a2 2 0 012 2v9a2 2 0 01-2 2z"></path></svg>
              </div>
              Edit Role
            </CardTitle>
            <CardDescription>
              Update the role details
            </CardDescription>
          </CardHeader>
          <CardContent>
            <form @submit.prevent="submit" class="space-y-6">
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

                <div class="mt-2">
                  <Toggle
                    id="select-all-permissions"
                    :model-value="isAllSelected === true"
                    :indeterminate="isAllSelected === null"
                    @update:model-value="toggleSelectAll"
                    label="Select All Permissions"
                  />
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <template v-for="(permissions, model) in props.permissions" :key="model">
                    <Card class="border-l-2 border-l-primary/20 shadow-sm hover:shadow-md hover:border-l-primary/50 transition-all duration-200">
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
                            <label :for="`permission-${permission.id}`" class="group flex items-center space-x-3 p-4 rounded-lg border border-border/50 hover:border-primary/30 hover:bg-primary/5 transition-all cursor-pointer">
                              <Toggle
                                :id="`permission-${permission.id}`"
                                :model-value="form.permissions.includes(permission.id)"
                                @update:model-value="(checked) => {
                                    if (checked) {
                                        form.permissions.push(permission.id)
                                    } else {
                                        form.permissions = form.permissions.filter(p => p !== permission.id)
                                    }
                                }"/>
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

              <div class="flex gap-4">
                <Button type="submit" :disabled="form.processing" class="transition-all duration-200 hover:shadow-md">
                  Update Role
                </Button>
                <Button variant="outline" as-child class="transition-all duration-200 hover:shadow-md">
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
