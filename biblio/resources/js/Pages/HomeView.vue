<template>
  <!-- HERO / INTRO SECTION -->
  <section
    class="relative bg-cover bg-center h-[80vh] shadow-xl overflow-hidden"
    style="background-image: url('/img/blue_wallpaper.jpg');"
  >
    <div
      class="absolute inset-0 bg-black bg-opacity-60 flex flex-col justify-center items-center text-center px-4"
    >
      <h2
        class="text-4xl md:text-5xl font-bold text-white mb-4 leading-tight drop-shadow-lg"
      >
        Atrodi savu nākamo <br />
        iecienīto grāmatu
      </h2>

      <p class="text-lg text-gray-200 max-w-xl mb-6">
        Lasīšana nav tikai hobijs — tā ir ceļošana laikā, idejās un sajūtās.
      </p>

      <button
        @click="goToBooks"
        class="bg-gradient-to-r from-[#213555] to-[#3E5879] text-white font-semibold py-3 px-6 rounded-md shadow-lg hover:scale-105 transition-all duration-300"
      >
        Skatīt visas grāmatas
      </button>
    </div>
  </section>

  <!-- BOOK SECTION -->
  <ShowBook />
</template>

<script>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import ShowBook from '@/Components/showBook.vue'

export default {
  layout: GuestLayout,

  components: {
    ShowBook,
  },

  methods: {
    goToBooks() {
      this.$inertia.visit('/gramatas')
    },
  },
}
</script>
