<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import { ref, onMounted, onUnmounted, nextTick, watch } from 'vue'

const props = defineProps({
  conversationId: Number,
  title: String,
  type: String,
  messages: Array,
  privateChats: Array,
})

const form = useForm({ body: '' })
const showSidebar = ref(false)
let interval = null

onMounted(() => {
  setTimeout(scrollToBottom, 100)

  interval = setInterval(() => {
    router.reload({
      only: ['messages'],
      preserveScroll: true,
    })
  }, 2000)
})

onUnmounted(() => {
  clearInterval(interval)
})

const scrollToBottom = async () => {
  const container = document.querySelector('.messages-container')
  if (container) {
    container.scrollTop = container.scrollHeight
  }
}

watch(
  () => props.messages,
  async () => {
    await nextTick()
    scrollToBottom()
  }
)

const sendMessage = () => {
  form.post(`/chats/${props.conversationId}/messages`, {
    preserveScroll: true,
    onSuccess: () => form.reset('body'),
  })
}
</script>

<template>
  <GuestLayout>
    <div class="flex h-[calc(100vh-55px)] bg-gray-100 relative">

      <!-- BACKDROP (mobile only) -->
      <div
        v-if="showSidebar"
        @click="showSidebar = false"
        class="fixed inset-0 bg-black/40 md:hidden z-40"
      ></div>

      <!-- SIDEBAR -->
      <aside
        class="fixed md:static top-0 left-0 h-full w-72 bg-white border-r p-4 flex flex-col z-50 transition-transform duration-300"
        :class="showSidebar ? 'translate-x-0' : '-translate-x-full md:translate-x-0'"
      >
        <h2 class="text-lg font-semibold mb-4">Čati</h2>

        <!-- NEW CHAT -->
        <Link
          href="/chats/new"
          class="mb-4 inline-flex items-center justify-center px-3 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 transition"
        >
          + Jauns čats
        </Link>

        <!-- PRIVATE CHATS -->
        <div class="mb-6">
          <p class="text-xs text-gray-500 uppercase mb-2">Privātie čati</p>

          <ul class="space-y-1">
            <li v-for="chat in privateChats" :key="chat.id">
              <Link
                :href="`/chats/${chat.id}`"
                @click="showSidebar = false"
                class="flex justify-between items-center px-3 py-2 rounded"
                :class="chat.id === conversationId
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

            <li v-if="privateChats.length === 0" class="text-sm text-gray-400 px-3">
              Nav privāto čatu
            </li>
          </ul>
        </div>
      </aside>

      <!-- CHAT CONTENT -->
      <div class="flex flex-col flex-1 bg-gray-100">

        <!-- HEADER -->
        <header class="bg-white border-b px-4 py-3 flex items-center gap-3">
          <!-- MOBILE MENU -->
          <button
            @click="showSidebar = true"
            class="md:hidden text-xl"
          >
            ☰
          </button>

          <div>
            <h2 class="font-semibold">{{ title }}</h2>
            <p class="text-xs text-gray-500">
              {{ type === 'private' ? 'Privāts čats' : '' }}
            </p>
          </div>
        </header>

        <!-- MESSAGES -->
        <div class="messages-container flex-1 overflow-y-auto px-4 py-4 space-y-3">

          <div
            v-for="msg in messages"
            :key="msg.id"
            class="max-w-[80%]"
            :class="msg.isMine ? 'ml-auto text-right' : ''"
          >
            <div
              class="inline-block px-4 py-2 rounded-2xl"
              :class="msg.isMine
                ? 'bg-blue-500 text-white rounded-br-none'
                : 'bg-white text-gray-800 rounded-bl-none border'"
            >
              <p v-if="!msg.isMine" class="text-xs font-semibold mb-1">
                {{ msg.username }}
              </p>

              <p>{{ msg.body }}</p>

              <span class="text-[10px] opacity-70 block mt-1">
                {{ msg.created_at }}
              </span>
            </div>
          </div>
        </div>

        <!-- INPUT -->
        <form
          @submit.prevent="sendMessage"
          class="bg-white border-t px-4 py-3 flex gap-2"
        >
          <input
            v-model="form.body"
            placeholder="Rakstīt ziņu..."
            class="flex-1 border rounded-full px-4 py-2 text-sm focus:outline-none focus:ring"
          />

          <button
            type="submit"
            class="bg-blue-500 text-white px-4 py-2 rounded-full disabled:opacity-50"
            :disabled="!form.body"
          >
            ➤
          </button>
        </form>

      </div>
    </div>
  </GuestLayout>
</template>