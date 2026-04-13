<template>
  <GoogleBookSearch />
</template>

<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import GoogleBookSearch from '@/Components/GoogleBookSearch.vue'

defineOptions({
  layout: GuestLayout,
})
</script>
