<template>
  <Navbar />
  <main>
    <slot />
  </main>
  <Footer />
</template>

<script setup>
import Navbar from '@/Components/Navbar.vue'
import Footer from '@/Components/footer.vue'
</script>
  