<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Link } from '@inertiajs/vue3'

// Komponentes saņemtie dati
defineProps({
  users: Array,
  privateChats: Array,
})
</script>

<template>
  <GuestLayout>

    <!-- Čatu lapas galvenais konteiners -->
    <div class="flex h-[calc(100vh-55px)] bg-gray-100">

      <!-- Sānu panelis ar esošajiem čatiem -->
      <aside class="w-72 bg-white border-r p-4 flex flex-col">

        <!-- Sadaļas virsraksts -->
        <h2 class="text-lg font-semibold mb-4">Chats</h2>

        <!-- Jauna čata izveides poga -->
        <Link
          href="/chats/new"
          class="mb-4 px-3 py-2 bg-blue-500 text-white rounded text-center"
        >
          + Jauns čats
        </Link>

        <!-- Privāto čatu saraksts -->
        <div>

          <p class="text-xs text-gray-500 uppercase mb-2">
            Privātais
          </p>

          <ul class="space-y-1">

            <!-- Viena privātā čata ieraksts -->
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

      <!-- Galvenā satura daļa -->
      <main class="flex-1 p-6">

        <!-- Lapas galvene -->
        <div class="flex items-center gap-3 mb-6">

          <!-- Atgriešanās poga -->
          <Link
            href="/chats"
            class="text-blue-600 hover:text-blue-800 text-lg"
          >
            ←
          </Link>

          <!-- Lapas nosaukums -->
          <h2 class="text-lg font-semibold text-[#213555]">
            Sākt jaunu čatu
          </h2>

        </div>

        <!-- Lietotāju saraksts -->
        <div class="bg-white rounded shadow divide-y">

          <!-- Lietotājs, ar kuru var sākt sarunu -->
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