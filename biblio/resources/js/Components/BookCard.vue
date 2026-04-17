<template>
  <div
    @click="!showModal && handleCardClick($event)"
    class="group relative w-full max-w-[400px] rounded-lg overflow-hidden shadow-lg transition transform hover:scale-105 hover:shadow-2xl cursor-pointer"
  >

    <!-- IMAGE -->
    <img
      :src="book.image ? `/${book.image}` : 'https://via.placeholder.com/300'"
      :alt="book.title"
      class="w-full h-[400px] object-cover"
    />

    <!-- OVERLAY -->
    <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent text-white px-4 py-4 pointer-events-none">
      
      <h3 class="text-lg font-semibold mb-1">
        {{ book.title }}
      </h3>

      <p class="text-xs text-gray-200 italic mb-1">
        by {{ book.author }}
      </p>

      <!-- GENRES -->
      <div v-if="book.genres?.length" class="flex flex-wrap gap-1">
        <span
          v-for="genre in book.genres"
          :key="genre.id"
          class="bg-white/30 text-white text-[10px] px-2 py-[2px] rounded-full border border-white/50"
        >
          {{ genre.name }}
        </span>
      </div>

      <!-- DESCRIPTION -->
      <p
        v-if="book.description"
        class="text-[10px] text-gray-300 mt-1 line-clamp-2"
      >
        {{ book.description.slice(0, 70) }}...
      </p>
    </div>

    <!-- DELETE BUTTON -->
    <button
        v-if="props.folderId"
        @click.stop="showModal = true"
        class="delete-button absolute top-3 right-3 bg-black/60 hover:bg-red-500 text-white w-8 h-8 flex items-center justify-center rounded-full z-20 opacity-0 group-hover:opacity-100 transition"
        title="Izņemt no mapes"
        >
        ✕
    </button>

    <!-- MODAL -->
    <div
    v-if="showModal"
    @click.stop
    class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
    >
    

    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">

        <!-- HEADER -->
        <div class="bg-[#213555] text-white px-6 py-4 flex items-center gap-3">
        <span class="material-icons">delete</span>
        <h2 class="text-lg font-semibold">Izņemt grāmatu</h2>
        </div>

        <!-- CONTENT -->
        <div class="p-6 text-gray-700 text-sm">
        Vai tiešām vēlies izņemt šo grāmatu no mapes?
        <br>
        <span class="text-gray-400 text-xs">Grāmata netiks dzēsta, tikai izņemta no mapes.</span>
        </div>

        <!-- ACTIONS -->
        <div class="flex justify-end gap-3 px-6 pb-6">

        <button
            @click.stop="showModal = false"
            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm"
        >
            Atcelt
        </button>

        <button
            @click.stop="confirmRemoveFromFolder"
            class="px-4 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white transition text-sm flex items-center gap-1"
        >
            <span class="material-icons text-[16px]">delete</span>
            Izņemt
        </button>

        </div>

    </div>
    </div>

  </div>
</template>

<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'
import axios from 'axios'

const emit = defineEmits(['removed'])

const props = defineProps({
  book: Object,
  folderId: Number
})

const showModal = ref(false)

function handleCardClick(event) {
  if (event.target.closest('.delete-button')) return
  router.visit(`/book/${props.book.id}`)
}

async function confirmRemoveFromFolder() {
  try {
    await axios.delete(`/folders/${props.folderId}/books/${props.book.id}`)

    emit('removed', props.book.id)
    showModal.value = false

  } catch (error) {
    console.error(error)
    showModal.value = false
  }
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>