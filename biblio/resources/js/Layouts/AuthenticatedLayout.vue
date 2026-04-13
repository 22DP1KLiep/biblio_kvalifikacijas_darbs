<template>
  <div class="min-h-screen bg-gray-100">
    <Navbar />

    <main>
      <slot />
    </main>
  </div>
</template>

<script setup>
import Navbar from '@/Components/Navbar.vue'
import Footer from '@/Components/footer.vue'
</script>
