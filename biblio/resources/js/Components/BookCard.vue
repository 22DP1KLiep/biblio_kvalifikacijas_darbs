<template>
    <div
        @click="!showModal && handleCardClick($event)"
        class="relative w-full max-w-[400px] rounded-lg overflow-hidden shadow-lg transition transform hover:scale-105 hover:shadow-2xl cursor-pointer"
    >

    <!-- Attēls -->
        <img
            :src="props.book.image ? `/${props.book.image}` : 'https://via.placeholder.com/300'"
            :alt="props.book.title"
            class="w-full h-[400px] object-cover"
        />

        <!-- Gradients un teksts -->
        <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent text-white px-4 py-4 pointer-events-none">
            <h3 class="text-lg font-semibold leading-tight mb-1" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.8)">
                {{ props.book.title }}
            </h3>
            <p class="text-xs text-gray-200 italic mb-1" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.85)">
                by {{ props.book.author }}
            </p>

            <!-- Žanri -->
            <div v-if="props.book.genres?.length" class="flex flex-wrap gap-1">
        <span
            v-for="genre in props.book.genres"
            :key="genre.id"
            class="inline-block bg-white/30 text-white text-[10px] px-2 py-[2px] rounded-full border border-white/50"
            style="backdrop-filter: blur(4px); text-shadow: none;"
        >
          {{ genre.name }}
        </span>
            </div>

            <!-- Apraksts -->
            <p
                v-if="props.book.description"
                class="text-[10px] text-gray-300 mt-1 line-clamp-2"
                style="text-shadow: 1px 1px 2px rgba(0,0,0,0.8);"
            >
                {{ props.book.description.slice(0, 70) }}...
            </p>
        </div>

        <!-- Miskastes ikona (dzēšana no mapes) -->
        <button
            v-if="props.folderId"
            @click.stop="showModal = true"
            class="delete-button absolute top-2 right-2 bg-black/60 hover:bg-red-600 text-white p-1 rounded-full z-20"
            title="Izņemt no mapes"
        >
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24"
                 stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M1 7h22M10 3h4a1 1 0 011 1v1H9V4a1 1 0 011-1z"/>
            </svg>
        </button>

        <!-- MODĀLIS -->
        <div
            v-if="showModal"
            @click.stop
            class="fixed inset-0 flex items-center justify-center bg-black/50 z-50"
        >            <div class="bg-white p-6 rounded-lg shadow-xl w-80 text-center">
                <h3 class="text-lg font-semibold text-gray-800 mb-3">Apstiprinājums</h3>
                <p class="text-gray-600 mb-5">Vai tiešām vēlies izņemt šo grāmatu no mapes?</p>
                <div class="flex justify-center gap-4">
                    <button
                        @click="showModal = false"
                        class="px-4 py-2 bg-gray-200 hover:bg-gray-300 rounded text-sm"
                    >
                        Atcelt
                    </button>
                    <button
                        @click="confirmRemoveFromFolder"
                        class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded text-sm"
                    >
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
import { defineProps, defineEmits } from 'vue'
import axios from 'axios'

const emit = defineEmits(['removed'])

const props = defineProps({
    book: Object,
    folderId: Number
})

const showModal = ref(false)

function handleCardClick(event) {
    // Ja klikšķina uz miskastes pogas, neko nedari
    if (event.target.closest('.delete-button')) return
    inspectBook(props.book.id)
}

function inspectBook(id) {
    router.visit(`/book/${id}`)
}

function confirmRemoveFromFolder() {
    axios.delete(`/folders/${props.folderId}/books/${props.book.id}`)
        .then(() => {
            emit('removed', props.book.id)
            showModal.value = false
        })
        .catch(error => {
            console.error("Kļūda izņemot grāmatu no mapes:", error)
            showModal.value = false
        })
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
