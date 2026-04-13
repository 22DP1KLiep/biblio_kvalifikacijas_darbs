<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Link } from '@inertiajs/vue3'

defineProps({
  privateChats: Array,
  channels: Array,
  activeConversationId: Number,
})
</script>

<template>
  <GuestLayout>
    <div class="flex h-[calc(100vh-55px)] bg-gray-100">

      <!-- SIDEBAR -->
<aside class="w-72 bg-white border-r p-4 flex flex-col">

  <h2 class="text-lg font-semibold mb-4">Chats</h2>

  <!-- NEW CHAT BUTTON -->
  <Link
    href="/chats/new"
    class="mb-4 inline-flex items-center justify-center px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition"
  >
    + New Chat
  </Link>

        <!-- Private chats -->
        <div class="mb-6">
          <p class="text-xs text-gray-500 uppercase mb-2">Private</p>
          <ul class="space-y-1">
            <li v-for="chat in privateChats" :key="chat.id">
              <Link
                :href="`/chats/${chat.id}`"
                class="flex justify-between items-center px-3 py-2 rounded"
                :class="chat.id === activeConversationId
                  ? 'bg-blue-100 text-blue-700 font-semibold'
                  : 'hover:bg-gray-100'"
              >
                <span>{{ chat.username }}</span>

                <span
                  v-if="chat.unread > 0"
                  class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full"
                >
                  {{ chat.unread }}
                </span>
              </Link>

            </li>

            <li
              v-if="privateChats.length === 0"
              class="text-sm text-gray-400 px-3"
            >
              No private chats
            </li>
          </ul>
        </div>

        <!-- Channels -->
        <div>
          <p class="text-xs text-gray-500 uppercase mb-2">Channels</p>
          <ul class="space-y-1">
            <li v-for="channel in channels" :key="channel.id">
              <Link
                :href="`/chats/${channel.id}`"
                class="flex justify-between items-center px-3 py-2 rounded"
                :class="channel.id === activeConversationId
                  ? 'bg-blue-100 text-blue-700 font-semibold'
                  : 'hover:bg-gray-100'"
              >
                <span># {{ channel.title }}</span>

                <span
                  v-if="channel.unread > 0"
                  class="bg-red-500 text-white text-xs px-2 py-0.5 rounded-full"
                >
                  {{ channel.unread }}
                </span>
              </Link>

            </li>

            <li
              v-if="channels.length === 0"
              class="text-sm text-gray-400 px-3"
            >
              No channels
            </li>
          </ul>
        </div>
      </aside>

      <!-- EMPTY RIGHT SIDE -->
      <main class="flex-1 flex items-center justify-center text-gray-400">
        Select a chat
      </main>

    </div>
  </GuestLayout>
</template>
