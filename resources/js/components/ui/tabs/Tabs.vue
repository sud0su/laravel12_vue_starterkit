<script lang="ts">
import { defineComponent, provide, ref } from 'vue'

export default defineComponent({
  props: {
    defaultValue: String,
  },
  setup(props) {
    const activeTab = ref(props.defaultValue || '')

    provide('activeTab', activeTab)

    function selectTab(tab: string) {
      activeTab.value = tab
    }

    provide('selectTab', selectTab)

    return {}
  },
})
</script>

<template>
  <div class="tabs">
    <slot />
  </div>
</template>