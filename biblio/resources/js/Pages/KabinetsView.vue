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

              <div v-if="activeTab === 'profile'" class="flex items-center gap-6 mt-2">

                <h1 class="text-2xl md:text-3xl font-bold text-[#213555]">
                  {{ user.username }}
                </h1>

                <div class="flex gap-6 text-sm text-gray-600">

                  <p class="text-center">
                    <span class="font-bold text-[#213555] text-lg">
                      {{ user.followers_count }}
                    </span>
                    <span class="text-gray-500 text-xs ml-1">Sekotāji</span>
                  </p>

                  <p class="text-center">
                    <span class="font-bold text-[#213555] text-lg">
                      {{ user.following_count }}
                    </span>
                    <span class="text-gray-500 text-xs ml-1">Seko</span>
                  </p>

                </div>

              </div>

              <div v-else>
                <!-- USERNAME -->
                <p class="text-2xl md:text-3xl font-bold text-[#213555]">
                  {{ user.username }}
                </p>
                <p class="text-gray-500 text-sm">{{ user.email }}</p>

                <div class="flex gap-6 mt-3 text-sm text-gray-600">
                  <p><span class="font-bold text-[#213555]">{{ folders.length }}</span> mapes</p>
                  <p><span class="font-bold text-[#213555]">{{ books.length }}</span> grāmatas</p>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- TABS -->
      <div class="mt-8 flex gap-6 border-b text-sm">
        <button @click="goFolders" :class="tabClass('folders')">
          Mapes
        </button>

        <button @click="loadAllBooks" :class="tabClass('books')">
          Bibliotēka
        </button>

        <button @click="goProfile" :class="tabClass('profile')">
          Profils
        </button>

        <button @click="goEdit" :class="tabClass('edit')">
          Rediģēt informāciju
        </button>
      </div>
      
      <div v-if="activeTab === 'profile'" class="mt-10 text-center">

        <div v-if="publicFolders.length">

          <h3 class="text-xl font-semibold text-[#213555] mb-6">
            Publiskās kolekcijas
          </h3>

          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-6 max-w-4xl mx-auto">

            <div
              v-for="folder in publicFolders"
              :key="folder.id"
              @click="openPublicFolder(folder.id)"
              class="relative group text-center cursor-pointer flex flex-col items-center"
            >

              <!-- IKONA -->
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

        </div>

      <div v-else class="text-gray-400 mt-10">
        Šis lietotājs vēl nav publicējis kolekcijas.
      </div>

    </div>

      <!-- 📁 MAPES -->
      <div v-if="activeTab === 'folders'" class="mt-6">

        <!-- NAV ATVERTE MAPE -->
        <div v-if="!openedFolderId">

          <!-- ADD -->
          <div class="mb-4">

            <div class="flex items-center gap-3">
              <!-- INPUT -->
              <input
                v-model="newFolderName"
                @input="folderError = ''"
                placeholder="Jauna mape..."
                class="flex-1 border px-3 py-2 rounded-lg"
              />

              <!-- TOGGLE -->
              <div
                v-if="newFolderName.trim() !== ''"
                class="flex items-center gap-2"
              >

                <span
                  class="text-xs"
                  :class="!newFolderPublic ? 'text-[#213555] font-semibold' : 'text-gray-400'"
                >
                  Privāts
                </span>

                <button
                  @click="newFolderPublic = !newFolderPublic"
                  class="relative w-10 h-5 rounded-full transition"
                  :class="newFolderPublic ? 'bg-green-500' : 'bg-gray-300'"
                >
                  <span
                    class="absolute top-[2px] left-[2px] w-4 h-4 bg-white rounded-full transition"
                    :class="newFolderPublic ? 'translate-x-5' : ''"
                  ></span>
                </button>

                <span
                  class="text-xs"
                  :class="newFolderPublic ? 'text-green-600 font-semibold' : 'text-gray-400'"
                >
                  Publisks
                </span>

              </div>

              <!-- + POGA -->
              <button
                @click="createFolder"
                class="bg-[#213555] text-white px-4 rounded-lg"
              >
                +
              </button>

            </div>

            <p v-if="folderError" class="text-red-500 text-sm mt-2">
              {{ folderError }}
            </p>

          </div>

          <!-- MAPES -->
          <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-6">

            <div
              v-for="folder in folders"
              :key="folder.id"
              class="relative group text-center cursor-pointer flex flex-col items-center"
              @click="selectFolder(folder.id)"
            >

              <!-- IKONA -->
              <svg
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                class="w-28 h-28 text-[#213555] fill-current group-hover:scale-105 transition"
              >
                <path d="M10 4H4a2 2 0 00-2 2v2h20V8a2 2 0 00-2-2h-8l-2-2z"/>
                <path d="M22 10H2v8a2 2 0 002 2h16a2 2 0 002-2v-8z"/>
              </svg>

              <!-- DELETE -->
              <button
                @click.stop="openDeleteFolderModal(folder.id)"
                class="absolute -top-2 -right-2 opacity-0 group-hover:opacity-100 bg-white rounded-full px-1 text-red-500 shadow"
              >
                ✕
              </button>

              <!-- NOSAUKUMS -->
              <p class="mt-2 text-sm font-medium text-[#213555] truncate max-w-[120px]">
                {{ folder.name }}
              </p>

            </div>

          </div>

        </div>

        <!-- ATVĒRTA MAPE -->
        <div v-else>

          <!-- HEADER -->
          <div class="relative mb-10 border-b pb-6">

            <!-- 🔥 TOGGLE -->
            <div class="absolute top-2 right-2 flex items-center gap-2">

              <!-- PRIVĀTA -->
              <span
                class="text-xs"
                :class="!isPublic ? 'text-[#213555] font-semibold' : 'text-gray-400'"
              >
                Privāts
              </span>

              <!-- SWITCH -->
              <button
                @click="toggleFolderVisibility"
                class="relative w-12 h-6 rounded-full transition"
                :class="isPublic ? 'bg-green-500' : 'bg-gray-300'"
              >
                <span
                  class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full transition"
                  :class="isPublic ? 'translate-x-6' : ''"
                ></span>
              </button>

              <!-- PUBLISKA -->
              <span
                class="text-xs"
                :class="isPublic ? 'text-green-600 font-semibold' : 'text-gray-400'"
              >
                Publisks
              </span>

            </div>

            <!-- BACK -->
            <button
              @click="closeFolder"
              class="flex items-center gap-2 text-[#213555] hover:text-[#3E5879] mb-4"
            >
              ← <span class="text-sm">Atpakaļ</span>
            </button>

            <!-- TITLE -->
            <h1 class="text-3xl md:text-4xl font-bold text-[#213555]">
              {{ openedFolderName }}
            </h1>

            <p class="text-gray-500 mt-2 text-sm">
              Tavas grāmatas šajā mapē
            </p>

          </div>

          <!-- BOOKS -->
          <div v-if="books.length"
               class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6">

            <BookCard
              v-for="book in books"
              :key="book.id"
              :book="book"
              :folder-id="openedFolderId"
              @removed="removeBookFromList"
            />

          </div>

          <div v-else class="text-center mt-16">
            <p class="text-5xl mb-3">📚</p>
            <p class="text-gray-500">Šajā mapē nav grāmatu</p>
          </div>

        </div>

      </div>

      <!-- BIBLIOTĒKA -->
      <div v-if="activeTab === 'books'" class="mt-6">

        <h2 class="text-2xl font-bold text-[#213555] mb-4">
          Mana bibliotēka
        </h2>

        <div v-if="books.length"
             class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6">

          <BookCard
            v-for="book in books"
            :key="book.id"
            :book="book"
            :folder-id="null"
          />

        </div>

        <div v-else class="text-center mt-16">
          <p class="text-gray-500">Nav grāmatu</p>
        </div>

      </div>

      <!-- ✏️ EDIT -->
<div v-if="activeTab === 'edit'" class="mt-6 max-w-md">

  <h2 class="text-xl font-semibold text-[#213555] mb-6">
    Rediģēt profilu
  </h2>

  <div class="space-y-4">

    <!-- USERNAME -->
    <input
      v-model="form.username"
      :placeholder="form.username ? 'Username' : 'Ievadi lietotājvārdu...'"
      class="w-full border px-3 py-2 rounded-lg"
    />
    <p v-if="errors.username" class="text-red-500 text-sm">
      {{ errors.username }}
    </p>

    <!-- EMAIL -->
    <input
      v-model="form.email"
      :placeholder="form.email ? 'Email' : 'Ievadi e-pastu...'"
      class="w-full border px-3 py-2 rounded-lg"
    />
    <p v-if="errors.email" class="text-red-500 text-sm">
      {{ errors.email }}
    </p>

    <!-- PASSWORD -->
    <input
      type="password"
      v-model="form.password"
      placeholder="Nomainīt paroli..."
      class="w-full border px-3 py-2 rounded-lg"
    />
    <p v-if="errors.password" class="text-red-500 text-sm">
      {{ errors.password }}
    </p>

    <!-- CONFIRM PASSWORD -->
    <input
      v-if="form.password"
      type="password"
      v-model="form.password_confirmation"
      placeholder="Apstiprini jauno paroli..."
      class="w-full border px-3 py-2 rounded-lg"
    />
    <p v-if="errors.password_confirmation" class="text-red-500 text-sm">
      {{ errors.password_confirmation }}
    </p>

    <button
      @click="saveProfile"
      class="bg-[#213555] text-white px-6 py-2 rounded-lg"
    >
      Saglabāt
    </button>

  </div>

</div>
    
    </div>

    <!-- DELETE MODAL -->
    <div
      v-if="showDeleteFolderModal"
      class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 backdrop-blur-sm"
    >

      <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden">

        <!-- HEADER -->
        <div class="bg-[#213555] text-white px-6 py-4 flex items-center gap-3">
          <span class="material-icons">delete</span>
          <h2 class="text-lg font-semibold">Dzēst mapi</h2>
        </div>

        <!-- CONTENT -->
        <div class="p-6 text-gray-700 text-sm">
          Vai tiešām vēlies dzēst šo mapi?
          <br>
          <span class="text-gray-400 text-xs">Šo darbību nevar atsaukt.</span>
        </div>

        <!-- ACTIONS -->
        <div class="flex justify-end gap-3 px-6 pb-6">

          <!-- CANCEL -->
          <button
            @click="cancelDeleteFolder"
            class="px-4 py-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-100 transition text-sm"
          >
            Atcelt
          </button>

          <button
            @click="saveProfile"
            class="px-4 py-2 bg-[#213555] text-white rounded-lg"
          >
            Saglabāt
          </button>

          <!-- DELETE -->
          <button
            @click="deleteFolderConfirmed"
            class="px-4 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white transition text-sm flex items-center gap-1"
          >
            <span class="material-icons text-[16px]">delete</span>
            Dzēst
          </button>

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

const openedFolderId = ref(null)
const openedFolderName = ref(null)

const showDeleteFolderModal = ref(false)
const folderToDelete = ref(null)

const folderError = ref('')

const isPublic = ref(false)

const newFolderPublic = ref(false)

const publicFolders = ref([])

const form = ref({
  username: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const errors = ref({
  username: '',
  email: '',
  password: '',
  password_confirmation: ''
})

const resetErrors = () => {
  errors.value = {
    username: '',
    email: '',
    password: '',
    password_confirmation: ''
  }
}

const fetchFolders = async () => {
  const { data } = await axios.get('/folders')
  folders.value = data
}

watch(user, fetchFolders, { immediate: true })

const selectFolder = async (id) => {
  const { data } = await axios.get(`/folders/${id}/books`)

  books.value = data.books
  openedFolderId.value = id
  openedFolderName.value = data.folder.name
  isPublic.value = data.folder.is_public
}

const createFolder = async () => {
  if (!newFolderName.value.trim()) return

  folderError.value = '' // reset

  try {
    await axios.post('/folders', {
      name: newFolderName.value,
      is_public: newFolderPublic.value
    })

    newFolderName.value = ''
    newFolderPublic.value = false
    fetchFolders()
    

  } catch (e) {
    if (e.response?.status === 422) {
      folderError.value = 'Šāda mape jau eksistē'
    } else {
      console.error(e)
    }
  }
}

const loadAllBooks = async () => {
  resetErrors()

  const { data } = await axios.get('/books/user')
  books.value = data
  openedFolderId.value = null
  openedFolderName.value = null
  activeTab.value = 'books'
}

const goFolders = () => {
  resetErrors()
  activeTab.value = 'folders'
  books.value = []
  openedFolderId.value = null
  openedFolderName.value = null
}

const closeFolder = () => {
  openedFolderId.value = null
  openedFolderName.value = null
  books.value = []
}

const tabClass = (tab) => [
  'pb-2',
  activeTab.value === tab
    ? 'border-b-2 border-[#213555] text-[#213555] font-semibold'
    : 'text-gray-500 hover:text-[#213555]'
]

const removeBookFromList = (id) => {
  books.value = books.value.filter(b => b.id !== id)
}

/* DELETE FOLDER */
const openDeleteFolderModal = (id) => {
  folderToDelete.value = id
  showDeleteFolderModal.value = true
}

const cancelDeleteFolder = () => {
  showDeleteFolderModal.value = false
  folderToDelete.value = null
}

const deleteFolderConfirmed = async () => {
  await axios.delete(`/folders/${folderToDelete.value}`)
  folders.value = folders.value.filter(f => f.id !== folderToDelete.value)
  cancelDeleteFolder()
}
const toggleFolderVisibility = async () => {
  try {
    const res = await axios.patch(`/folders/${openedFolderId.value}/visibility`)
    isPublic.value = res.data.is_public
  } catch (e) {
    console.error(e)
  }
}

const loadProfile = async () => {
  activeTab.value = 'profile'

  const { data } = await axios.get('/folders')
  publicFolders.value = data.filter(f => f.is_public)
}
const goProfile = () => {
  loadProfile()
  resetErrors()
}

const openPublicFolder = async (id) => {
  const { data } = await axios.get(`/folders/${id}/books`)

  books.value = data.books
  openedFolderId.value = id
  openedFolderName.value = data.folder.name
  isPublic.value = data.folder.is_public

  activeTab.value = 'folders' // pārslēdz uz mapes view
}

const saveProfile = async () => {

  // reset errors
  errors.value = {
    username: '',
    email: '',
    password: '',
    password_confirmation: ''
  }

  let hasError = false

  // USERNAME
  if (!isUsernameValid(form.value.username)) {
    errors.value.username = 'Username: vismaz 4 simboli, tikai burti/cipari'
    hasError = true
  }

  // EMAIL
  if (!isEmailValid(form.value.email)) {
    errors.value.email = 'Nepareizs e-pasts'
    hasError = true
  }

  // PASSWORD
  if (form.value.password) {
    const hasUppercase = /[A-Z]/.test(form.value.password)
    const hasNumber = /\d/.test(form.value.password)

    if (form.value.password.length < 6 || !hasUppercase || !hasNumber) {
      errors.value.password = 'Min 6 simboli, 1 lielais burts un 1 cipars'
      hasError = true
    }

    if (form.value.password !== form.value.password_confirmation) {
      errors.value.password_confirmation = 'Paroles nesakrīt'
      hasError = true
    }
  }

  if (hasError) return   // ✅ tagad IR funkcijā

  try {
    const res = await axios.put('/user/update', form.value)

    user.value.username = res.data.user.username
    user.value.email = res.data.user.email

    form.value.password = ''
    form.value.password_confirmation = ''

    activeTab.value = 'profile'

  } catch (e) {
    if (e.response?.status === 422) {
      const backendErrors = e.response.data.errors

      errors.value.username = backendErrors.username?.[0] || ''
      errors.value.email = backendErrors.email?.[0] || ''
      errors.value.password = backendErrors.password?.[0] || ''
    }
  }
}


const goEdit = () => {
  resetErrors()

  form.value.username = user.value.username
  form.value.email = user.value.email
  form.value.password = ''
  form.value.password_confirmation = ''

  activeTab.value = 'edit'
}

const isEmailValid = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)

const isPasswordStrong = (password) =>
  /[A-Za-z]/.test(password) && /\d/.test(password)

const isUsernameValid = (username) =>
  /^[A-Za-z0-9]+$/.test(username) &&
  !/^\d+$/.test(username) &&
  username.length >= 4
</script>