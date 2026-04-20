<script setup>
import { router, usePage } from '@inertiajs/vue3'
import { computed, ref } from 'vue'
import axios from 'axios'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import BookCard from '@/Components/BookCard.vue'

defineOptions({ layout: AuthenticatedLayout })

const props = defineProps({
  profileUser: Object,
  isFollowing: Boolean,
  publicFolders: Array,
})

const page = usePage()
const authUser = computed(() => page.props.auth.user)

const isOwnProfile = computed(() =>
  authUser.value && authUser.value.id === props.profileUser.id
)

const startChat = () => {
  router.get(`/chats/start/${props.profileUser.id}`)
}

// FOLLOW
const isFollowingState = ref(props.isFollowing)

const toggleFollow = () => {
  router.post(`/users/${props.profileUser.id}/follow`, {}, {
    onSuccess: () => {
      isFollowingState.value = !isFollowingState.value
    }
  })
}

const books = ref([])
const openedFolderId = ref(null)
const openedFolderName = ref(null)

const openPublicFolder = async (id) => {
  const { data } = await axios.get(`/folders/${id}/books`)

  books.value = data.books
  openedFolderId.value = id
  openedFolderName.value = data.folder.name
}

const closeFolder = () => {
  openedFolderId.value = null
  books.value = []
}
</script>

<template>
  <div v-if="profileUser" class="bg-[#f3f6fb] min-h-screen pb-20">

    <!-- COVER -->
    <div class="h-64 bg-gradient-to-r from-[#213555] to-[#3E5879]"></div>

    <!-- PROFILE CONTENT -->
    <div class="max-w-4xl mx-auto px-6">

      <!-- CARD -->
      <div class="-mt-20 bg-white rounded-2xl shadow-xl p-8">

        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-8">

          <!-- LEFT SIDE -->
          <div class="flex items-center gap-6">

            <!-- AVATAR -->
            <div class="w-32 h-32 rounded-full bg-[#213555] text-white flex items-center justify-center text-5xl font-bold shadow-lg border-4 border-white">
              {{ profileUser.username.charAt(0).toUpperCase() }}
            </div>

            <div>
              <h1 class="text-3xl font-bold text-[#213555]">
                {{ profileUser.username }}
              </h1>

              <div class="flex gap-10 mt-4 text-gray-600 text-sm">

                <div class="text-center">
                  <p class="text-xl font-bold text-[#213555]">
                    {{ profileUser.followers_count }}
                  </p>
                  <p>Sekotāji</p>
                </div>

                <div class="text-center">
                  <p class="text-xl font-bold text-[#213555]">
                    {{ profileUser.following_count }}
                  </p>
                  <p>Seko</p>
                </div>

              </div>
            </div>
          </div>

          <!-- ACTION BUTTONS -->
          <div v-if="!isOwnProfile" class="flex gap-4">

            <button
              @click="toggleFollow"
              class="px-6 py-2 rounded-full font-medium transition shadow-sm"
              :class="isFollowing
                ? 'bg-gray-200 text-gray-700 hover:bg-gray-300'
                : 'bg-[#213555] text-white hover:bg-[#3E5879]'"
            >
              {{ isFollowing ? 'Nesekot' : 'Sekot' }}
            </button>

            <button
              @click="startChat"
              class="px-6 py-2 rounded-full bg-[#3E5879] text-white hover:bg-[#213555] transition shadow-sm"
            >
              Rakstīt ziņu
            </button>

          </div>

        </div>

      </div>

      <!-- EMPTY STATE -->
      <!-- PUBLIC FOLDERS -->
<div class="mt-16">

  <h2 class="text-2xl font-bold text-[#213555] mb-6">
    Publiskās kolekcijas
  </h2>

  <!-- JA NAV MAPES -->
  <div v-if="!publicFolders || publicFolders.length === 0"
       class="text-center text-gray-500">
    <p>Šis lietotājs vēl nav publicējis kolekcijas.</p>
  </div>

  <!-- MAPES -->
<div v-if="!openedFolderId" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6">
  <div
    v-for="folder in publicFolders"
    :key="folder.id"
    class="relative group text-center cursor-pointer flex flex-col items-center"
    @click="openPublicFolder(folder.id)"
  >

    <!-- IKONA (TĀDA PAT KĀ MAPĒS) -->
    <svg
      xmlns="http://www.w3.org/2000/svg"
      viewBox="0 0 24 24"
      class="w-28 h-28 text-[#213555] fill-current group-hover:scale-105 transition"
    >
      <path d="M10 4H4a2 2 0 00-2 2v2h20V8a2 2 0 00-2-2h-8l-2-2z"/>
      <path d="M22 10H2v8a2 2 0 002 2h16a2 2 0 002-2v-8z"/>
    </svg>

    <!-- NOSAUKUMS -->
    <p class="mt-2 text-sm font-medium text-[#213555] truncate max-w-[120px]">
      {{ folder.name }}
    </p>

  </div>

</div>
<!-- ATVĒRTA MAPE -->
<div v-if="openedFolderId" class="mt-10">

  <!-- HEADER -->
  <div class="mb-6 border-b pb-4">

    <button
      @click="closeFolder"
      class="text-sm text-[#213555] hover:underline mb-2"
    >
      ← Atpakaļ
    </button>

    <h2 class="text-2xl font-bold text-[#213555]">
      {{ openedFolderName }}
    </h2>

    <p class="text-gray-500 text-sm">
      Šī lietotāja kolekcija
    </p>

  </div>

  <!-- BOOKS -->
  <div v-if="books.length"
       class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">

    <BookCard
      v-for="book in books"
      :key="book.id"
      :book="book"
    />

  </div>

  <div v-else class="text-center text-gray-400 mt-10">
    Nav grāmatu
  </div>

</div>

</div>

    </div>

  </div>
</template>