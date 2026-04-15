<template>
  <div class="bg-[#f3f6fb] min-h-screen pb-20">

    <!-- COVER -->
    <div class="h-56 bg-gradient-to-r from-[#213555] to-[#3E5879]"></div>

    <!-- PROFILE -->
    <div class="max-w-5xl mx-auto px-6">

      <div class="-mt-20 bg-white rounded-2xl shadow-xl p-6 md:p-8">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">

          <!-- LEFT -->
          <div class="flex items-center gap-5">

            <img
              :src="`https://ui-avatars.com/api/?name=${user.username}`"
              class="w-24 h-24 rounded-full border-4 border-white shadow"
            />

            <div>
              <h1 class="text-2xl md:text-3xl font-bold text-[#213555]">
                {{ user.username }}
              </h1>
              <p class="text-gray-500 text-sm">{{ user.email }}</p>

              <div class="flex gap-6 mt-3 text-sm text-gray-600">
                <p><span class="font-bold text-[#213555]">{{ folders.length }}</span> mapes</p>
                <p><span class="font-bold text-[#213555]">{{ books.length }}</span> grāmatas</p>
              </div>
            </div>

          </div>

          <!-- ACTIONS -->
          <div class="flex gap-3">
            <button class="px-4 py-2 bg-[#213555] text-white rounded-lg">
              ✏️ Rediģēt
            </button>
            <button class="px-4 py-2 bg-gray-200 rounded-lg">
              ⚙️ Iestatījumi
            </button>
          </div>

        </div>

      </div>

      <!-- TABS -->
      <div class="mt-8 flex gap-6 border-b text-sm">

        <button
          @click="activeTab = 'folders'"
          :class="tabClass('folders')"
        >
          📁 Mapes
        </button>

        <button
          @click="activeTab = 'books'"
          :class="tabClass('books')"
        >
          📚 Grāmatas
        </button>

        <button
          @click="activeTab = 'stats'"
          :class="tabClass('stats')"
        >
          📊 Statistika
        </button>

      </div>

      <!-- CONTENT -->

      <!-- FOLDERS -->
      <div v-if="activeTab === 'folders'" class="mt-6">

        <!-- ADD -->
        <div class="flex gap-2 mb-4">
          <input
            v-model="newFolderName"
            placeholder="Jauna mape..."
            class="flex-1 border px-3 py-2 rounded-lg"
          />
          <button
            @click="createFolder"
            class="bg-[#213555] text-white px-4 rounded-lg"
          >
            +
          </button>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">

          <div
            v-for="folder in folders"
            :key="folder.id"
            @click="selectFolder(folder.id)"
            class="bg-white p-4 rounded-xl shadow hover:shadow-lg transition cursor-pointer"
          >
            <p class="text-2xl">📁</p>
            <p class="mt-2 font-semibold text-[#213555]">
              {{ folder.name }}
            </p>
          </div>

        </div>

      </div>

      <!-- BOOKS -->
      <div v-if="activeTab === 'books'" class="mt-6">

        <div class="mb-4">
          <input
            placeholder="🔍 Meklē..."
            class="w-full border px-4 py-2 rounded-xl"
          />
        </div>

        <div v-if="books.length"
             class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

          <BookCard
            v-for="book in books"
            :key="book.id"
            :book="book"
          />

        </div>

        <div v-else class="text-center mt-16">
          <p class="text-5xl mb-3">📚</p>
          <p class="text-gray-500">Nav grāmatu</p>
        </div>

      </div>

      <!-- STATS -->
      <div v-if="activeTab === 'stats'" class="mt-6 grid grid-cols-3 gap-4">

        <div class="bg-white p-4 rounded-xl shadow text-center">
          <p class="text-2xl font-bold">{{ books.length }}</p>
          <p class="text-sm text-gray-500">Grāmatas</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow text-center">
          <p class="text-2xl font-bold">{{ folders.length }}</p>
          <p class="text-sm text-gray-500">Mapes</p>
        </div>

        <div class="bg-white p-4 rounded-xl shadow text-center">
          <p class="text-2xl font-bold">0</p>
          <p class="text-sm text-gray-500">Izlasītas</p>
        </div>

      </div>

    </div>

  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { usePage } from '@inertiajs/vue3'
import axios from 'axios'
import BookCard from '@/Components/BookCard.vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineOptions({ layout: AuthenticatedLayout })

const page = usePage()
const user = computed(() => page.props.auth.user)

const folders = ref([])
const books = ref([])
const activeTab = ref('folders')
const newFolderName = ref('')

const fetchFolders = async () => {
  const { data } = await axios.get('/folders')
  folders.value = data
}

watch(user, fetchFolders, { immediate: true })

const selectFolder = async (id) => {
  const { data } = await axios.get(`/folders/${id}/books`)
  books.value = data.books
  activeTab.value = 'books'
}

const createFolder = async () => {
  if (!newFolderName.value.trim()) return
  await axios.post('/folders', { name: newFolderName.value })
  newFolderName.value = ''
  fetchFolders()
}

const tabClass = (tab) => {
  return [
    'pb-2',
    activeTab.value === tab
      ? 'border-b-2 border-[#213555] text-[#213555] font-semibold'
      : 'text-gray-500 hover:text-[#213555]'
  ]
}
</script>