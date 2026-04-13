<script setup>
import { router, usePage } from '@inertiajs/vue3'
import { computed } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

defineOptions({ layout: AuthenticatedLayout })

const props = defineProps({
  profileUser: Object,
  isFollowing: Boolean,
})

const page = usePage()
const authUser = computed(() => page.props.auth.user)

const isOwnProfile = computed(() =>
  authUser.value && authUser.value.id === props.profileUser.id
)

const toggleFollow = () => {
  router.post(`/users/${props.profileUser.id}/follow`)
}

const startChat = () => {
  router.get(`/chats/start/${props.profileUser.id}`)
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
              {{ isFollowing ? 'Unfollow' : 'Follow' }}
            </button>

            <button
              @click="startChat"
              class="px-6 py-2 rounded-full bg-[#3E5879] text-white hover:bg-[#213555] transition shadow-sm"
            >
              Message
            </button>

          </div>

        </div>

      </div>

      <!-- EMPTY STATE -->
      <div class="mt-16 text-center text-gray-500">
        <p>Šis lietotājs vēl nav publicējis kolekcijas.</p>
      </div>

    </div>

  </div>
</template>