<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  modelValue: boolean
  id?: string
  label?: string
  disabled?: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void
}>()

const internalValue = computed({
  get: () => props.modelValue,
  set: (value) => emit('update:modelValue', value),
})
</script>

<template>
  <label :for="id" class="flex items-center cursor-pointer" :class="{ 'opacity-50 cursor-not-allowed': disabled }">
    <div class="relative">
      <input
        :id="id"
        type="checkbox"
        class="sr-only"
        :disabled="disabled"
        v-model="internalValue"
      />
      <div
        class="block w-10 h-6 rounded-full transition-colors"
        :class="{
          'bg-primary': internalValue,
          'bg-gray-300': !internalValue,
          'group-hover:bg-primary/80': !disabled && internalValue,
          'group-hover:bg-gray-400': !disabled && !internalValue,
        }"
      ></div>
      <div
        class="dot absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform"
        :class="{ 'translate-x-full': internalValue }"
      ></div>
    </div>
    <span v-if="label" class="ml-3 text-sm font-medium text-gray-900">{{ label }}</span>
  </label>
</template>

<style scoped>
.dot {
  box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
}
</style>
