<script lang="ts">
import { defineComponent, inject, computed } from 'vue'

export default defineComponent({
  props: {
    value: {
      type: String,
      required: true,
    },
  },
  setup(props) {
    const activeTab = inject('activeTab')

    const isActive = computed(() => (activeTab as any).value === props.value)

    return {
      isActive,
    }
  },
})
</script>

<template>
  <div
    v-if="isActive"
    role="tabpanel"
    :aria-labelledby="`tab-${value}`"
    class="mt-2 ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2"
  >
    <slot />
  </div>
</template>