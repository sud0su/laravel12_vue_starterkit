<script setup lang="ts">
import { ref } from 'vue'
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'

interface Props {
  title?: string
  description?: string
  confirmText?: string
  cancelText?: string
  variant?: 'default' | 'destructive'
  icon?: string
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Are you sure?',
  description: 'This action cannot be undone.',
  confirmText: 'Confirm',
  cancelText: 'Cancel',
  variant: 'destructive',
  icon: '⚠️',
})

const emit = defineEmits<{
  confirm: []
  cancel: []
}>()

const open = ref(false)

const show = () => {
  open.value = true
}

const hide = () => {
  open.value = false
}

const handleConfirm = () => {
  emit('confirm')
  hide()
}

const handleCancel = () => {
  emit('cancel')
  hide()
}

defineExpose({
  show,
  hide,
})
</script>

<template>
  <Dialog v-model:open="open">
    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle class="flex items-center gap-3">
          <span class="text-2xl">{{ icon }}</span>
          {{ title }}
        </DialogTitle>
        <DialogDescription class="text-left">
          {{ description }}
        </DialogDescription>
      </DialogHeader>

      <DialogFooter class="gap-2 sm:gap-0">
        <Button
          variant="outline"
          @click="handleCancel"
          class="transition-all duration-200 hover:bg-muted"
        >
          {{ cancelText }}
        </Button>
        <Button
          :variant="variant"
          @click="handleConfirm"
          class="transition-all duration-200"
        >
          {{ confirmText }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>

<style scoped>
/* Smooth animations for the dialog */
:deep(.dialog-content) {
  animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: translate(-50%, -48%) scale(0.95);
  }
  to {
    opacity: 1;
    transform: translate(-50%, -50%) scale(1);
  }
}

/* Enhanced button hover effects */
:deep(.button) {
  transition: all 0.2s ease;
}

:deep(.button:hover) {
  transform: translateY(-1px);
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
}
</style>
