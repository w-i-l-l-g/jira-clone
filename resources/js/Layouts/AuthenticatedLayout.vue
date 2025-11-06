<script setup>
import AppLayout from '@/Layouts/AppLayout.vue'
</script>

<template>
  <AppLayout>
    <slot name="header" />
    <slot />
  </AppLayout>
</template>