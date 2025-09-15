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
    const selectTab = inject('selectTab')

    const isActive = computed(() => (activeTab as any).value === props.value)

    function handleClick() {
      if (selectTab) {
        (selectTab as any)(props.value)
      }
    }

    return {
      isActive,
      handleClick,
    }
  },
})
</script>

<template>
  <button
    type="button"
    role="tab"
    :aria-selected="isActive"
    :data-state="isActive ? 'active' : 'inactive'"
    @click="handleClick"
    class="inline-flex items-center justify-center whitespace-nowrap rounded-sm px-3 py-1.5 text-sm font-medium ring-offset-background transition-all focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:pointer-events-none disabled:opacity-50 data-[state=active]:bg-background data-[state=active]:text-foreground data-[state=active]:shadow-sm"
  >
    <slot />
  </button>
</template>