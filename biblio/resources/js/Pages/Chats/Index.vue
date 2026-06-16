<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Link } from '@inertiajs/vue3'

// Komponentes saņemtie dati
defineProps({
  privateChats: Array,
  activeConversationId: Number,
})
</script>

<template>
  <GuestLayout>

    <!-- Čatu lapas galvenais konteiners -->
    <div class="flex h-[calc(100vh-55px)] bg-gray-100">

      <!-- Sānu panelis ar čatu sarakstu -->
      <aside class="w-72 bg-white border-r p-4 flex flex-col">

        <!-- Sadaļas virsraksts -->
        <h2 class="text-lg font-semibold mb-4">Čati</h2>

        <!-- Jauna čata izveides poga -->
        <Link
          href="/chats/new"
          class="mb-4 inline-flex items-center justify-center px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition"
        >
          + Jauns čats
        </Link>

        <!-- Privāto čatu saraksts -->
        <div class="mb-6">

          <p class="text-xs text-gray-500 uppercase mb-2">
            Privātais
          </p>

          <ul class="space-y-1">

            <!-- Viena privātā čata ieraksts -->
            <li v-for="chat in privateChats" :key="chat.id">

              <Link
                :href="`/chats/${chat.id}`"
                class="flex justify-between items-center px-3 py-2 rounded"
                :class="chat.id === activeConversationId
                  ? 'bg-blue-100 text-blue-700 font-semibold'
                  : 'hover:bg-gray-100'"
              >

                <!-- Sarunas nosaukums -->
                <span>{{ chat.username }}</span>

                <!-- Neizlasīto ziņojumu skaits -->
                <span
                  v-if="chat.unread > 0"
                  class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full"
                >
                  {{ chat.unread }}
                </span>

              </Link>

            </li>

            <!-- Ziņojums, ja nav neviena privātā čata -->
            <li
              v-if="privateChats.length === 0"
              class="text-sm text-gray-400 px-3"
            >
              Nav privāto čatu
            </li>

          </ul>
        </div>

      </aside>

      <!-- Sākuma skats pirms čata izvēles -->
      <main class="flex-1 flex items-center justify-center text-gray-400">
        Izvēlies čatu
      </main>

    </div>

  </GuestLayout>
</template>