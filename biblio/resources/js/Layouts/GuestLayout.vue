<script setup>
import { usePage } from '@inertiajs/vue3'
import Navbar from '@/Components/Navbar.vue'
import Footer from '@/Components/footer.vue'

</script>
<template>
  <Navbar />
  <main>
    <slot />
  </main>
  <Footer />
</template>


  