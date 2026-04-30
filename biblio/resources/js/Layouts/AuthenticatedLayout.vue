<script setup>
import { usePage } from '@inertiajs/vue3'
import Navbar from '@/Components/Navbar.vue'
import Footer from '@/Components/footer.vue'

const user = usePage().props.auth?.user

const page = usePage()
console.log(page.props.latestNotifications)
console.log(page.props.notificationsCount)
</script>

<template>
  <div class="min-h-screen bg-gray-100">
    <Navbar />

    
    <div
      v-if="user?.restricted"
      class="bg-gradient-to-r from-red-500 to-red-600 text-white text-center py-3 px-4 font-medium shadow-md"
    >
      Tavs konts ir ierobežots līdz {{ user.restricted_until }}
      <span class="ml-2 opacity-90">| {{ user.restriction_reason }}</span>

      
    </div>

    <main>
      <slot />
    </main>

    <Footer />
  </div>
</template>