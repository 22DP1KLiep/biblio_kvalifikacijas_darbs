<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
  users: Array,
  privateChats: Array,
})
</script>

<template>
  <GuestLayout>
    <div class="flex h-[calc(100vh-55px)] bg-gray-100">

      <!-- SIDEBAR -->
      <aside class="w-72 bg-white border-r p-4 flex flex-col">
        <h2 class="text-lg font-semibold mb-4">Chats</h2>

        <Link
          href="/chats/new"
          class="mb-4 px-3 py-2 bg-blue-500 text-white rounded text-center"
        >
          + Jauns čats
        </Link>

        <div>
          <p class="text-xs text-gray-500 uppercase mb-2">Privātais</p>
          <ul class="space-y-1">
            <li v-for="chat in privateChats" :key="chat.id">
              <Link
                :href="`/chats/${chat.id}`"
                class="block px-3 py-2 rounded hover:bg-gray-100"
              >
                {{ chat.username }}
              </Link>
            </li>
          </ul>
        </div>
      </aside>
      <main class="flex-1 p-6">

  <!-- HEADER -->
  <div class="flex items-center gap-3 mb-6">
    <Link
      href="/chats"
      class="text-blue-600 hover:text-blue-800 text-lg"
    >
      ←
    </Link>

    <h2 class="text-lg font-semibold text-[#213555]">
      Sākt jaunu čatu
    </h2>
  </div>

  <!-- USER LIST -->
  <div class="bg-white rounded shadow divide-y">
    <Link
      v-for="user in users"
      :key="user.id"
      :href="`/chats/start/${user.id}`"
      class="block px-4 py-3 hover:bg-gray-50"
    >
      {{ user.username }}
    </Link>
  </div>
  
</main>

    </div>
  </GuestLayout>
</template>
