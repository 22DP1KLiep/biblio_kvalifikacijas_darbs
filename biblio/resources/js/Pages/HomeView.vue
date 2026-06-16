<template>

  <!-- Sākuma lapas galvenā sadaļa -->
  <section
    class="relative bg-cover bg-center h-[90vh] overflow-hidden"
    style="background-image: url('/img/blue_wallpaper.jpg');"
  >

    <!-- Fona pārklājums labākai teksta salasāmībai -->
    <div class="absolute inset-0 bg-black/60 backdrop-blur-sm"></div>

    <!-- Galvenais ievada saturs -->
    <div class="relative z-10 flex flex-col justify-center items-center text-center h-full px-4">

      <h2 class="text-5xl md:text-6xl font-extrabold text-white mb-6 leading-tight">

        Atrodi grāmatas, kas

        <!-- izceltais teksts -->
        <span class="bg-gradient-to-r from-[#3E5879] to-[#f0f4f8] bg-clip-text text-transparent">
          Tevi aizrauj
        </span>
      </h2>

      <!-- apraksta teksts -->
      <p class="text-lg md:text-xl text-gray-200 max-w-2xl mb-8">
        Meklē, saglabā un dalies ar savām iecienītākajām grāmatām vienuviet.
      </p>

      <!-- Poga pārejai uz grāmatu katalogu -->
      <button
        @click="goToBooks"
        class="bg-gradient-to-r from-[#213555] to-[#3E5879] text-white font-semibold py-4 px-8 rounded-xl shadow-xl hover:scale-110 hover:shadow-2xl transition-all duration-300"
      >
        Sākt meklēt grāmatas
      </button>

    </div>
  </section>

  <!-- Sistēmas galveno funkciju sadaļa -->
  <section class="py-24 bg-white text-center">

    <h3 class="text-4xl md:text-5xl font-extrabold mb-16 text-[#213555]">
      Ko Tu vari darīt šeit?
    </h3>

    <!-- funkciju kartiņu režģis -->
    <div class="grid md:grid-cols-3 gap-10 max-w-6xl mx-auto px-6">

      <!-- Grāmatu meklēšana -->
      <div class="p-8 rounded-2xl bg-white shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">

        <h4 class="font-bold text-xl mb-2">Meklē grāmatas</h4>

        <p class="text-gray-500">
          Atrodi grāmatas pēc nosaukuma, autora vai žanra.
        </p>

      </div>

      <!-- Grāmatu vērtēšana un komentēšana -->
      <div class="p-8 rounded-2xl bg-white shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">

        <h4 class="font-bold text-xl mb-2">Vērtē un komentē</h4>

        <p class="text-gray-500">
          Dalies ar savu viedokli un redzi citu atsauksmes.
        </p>

      </div>

      <!-- Personīgo mapju veidošana -->
      <div class="p-8 rounded-2xl bg-white shadow-lg hover:shadow-2xl hover:-translate-y-2 transition-all duration-300">

        <h4 class="font-bold text-xl mb-2">Veido savas mapes</h4>

        <p class="text-gray-500">
          Saglabā grāmatas “Izlasīts”, “Vēlos lasīt” u.c.
        </p>

      </div>

    </div>
  </section>

  <!-- Informācijas sadaļa par sistēmu -->
  <section class="py-24 bg-gradient-to-br from-[#213555] to-[#3E5879] text-center text-white px-6">

    <h3 class="text-4xl md:text-5xl font-extrabold mb-8">
      Vairāk nekā tikai grāmatu lapa
    </h3>

    <!-- apraksts -->
    <p class="max-w-2xl mx-auto text-lg opacity-90">
      Šī ir vieta, kur Tu vari organizēt savu lasīšanas pasauli,
      sekot līdzi progresam un atrast jaunus iedvesmas avotus.
    </p>

  </section>

</template>

<script>

// importē usePage no inertia
import { usePage } from '@inertiajs/vue3'

// importē layout nepieslēgtiem lietotājiem
import GuestLayout from '@/Layouts/GuestLayout.vue'

// importē layout autorizētiem lietotājiem
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

export default {

  // Izvēlas atbilstošu izkārtojumu atkarībā no autorizācijas statusa
  layout: (h, page) => {

    // iegūst autorizēto lietotāju
    const user = page.props.auth?.user

    // ja lietotājs ir pieslēdzies → AuthenticatedLayout
    // ja nav → GuestLayout
    return user
      ? h(AuthenticatedLayout, page)
      : h(GuestLayout, page)
  },

  methods: {

    // Atver grāmatu kataloga lapu
    goToBooks() {

      // inertia navigācija
      this.$inertia.visit('/gramatas')
    },

  },
}
</script>