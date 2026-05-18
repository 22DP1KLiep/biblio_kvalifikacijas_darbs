<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { router } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted } from 'vue'
import axios from 'axios'

// Saņem paziņojumus no backend
const props = defineProps({
  notifications: Array
})

// Padara paziņojumus reactive
const notifications = ref(props.notifications)

// Filtrē nevajadzīgos paziņojumu tipus
const filteredNotifications = computed(() => {
  return notifications.value.filter(
    n => n.type !== 'admin_restriction'
  )
})

// Automātiski atjauno paziņojumus
let interval = null

const loadNotifications = async () => {
  try {
    const res = await axios.get('/api/notifications')
    notifications.value = res.data
  } catch (e) {
    console.error(e)
  }
}

onMounted(() => {
  interval = setInterval(loadNotifications, 5000)
})

onUnmounted(() => {
  clearInterval(interval)
})

// Atver konkrēto paziņojumu
const openNotification = async (n) => {

  // Atzīmē kā izlasītu
  if (!n.is_read) {
    await axios.post(`/notifications/${n.id}/read`)
  }

  switch (n.type) {

    case 'follow':
      router.get(`/users/${n.from_user_id}`)
      break

    case 'message':
      const chatId = typeof n.data === 'object'
        ? n.data.chat_id
        : n.data

      router.get(`/chats/${chatId}`)
      break

    case 'comment_like':
      router.get(`/book/${n.data.book_id}#comment-${n.data.comment_id}`)
      break

    case 'comment':
      router.get(`/book/${n.data.book_id}`)
      break

    case 'admin_deleted_comment':
      router.get('/kabinets')
      break
  }
}

// Atgriežas iepriekšējā lapā
const goBack = () => {
  window.history.back()
}

// Dzēš vienu paziņojumu
const deleteNotification = async (id) => {
  await axios.delete(`/notifications/${id}`)
  notifications.value = notifications.value.filter(n => n.id !== id)
}

// Dzēš visus paziņojumus
const clearAll = async () => {
  await axios.delete('/notifications')
  notifications.value = []
}
</script>

<template>
  <AuthenticatedLayout>

    <div class="notifications-page">

      <!-- Augšējā daļa -->
      <div class="notifications-header">
        
        <button @click="goBack" class="back-btn">
          ← Atpakaļ
        </button>

        <h2>Paziņojumi</h2>
        
        <button @click="clearAll" class="clear-btn">
          Notīrīt visus paziņojumus
        </button>
        
      </div>

      <!-- Paziņojumu saraksts -->
      <div
        v-if="filteredNotifications.length"
        class="notifications-list"
      >

        <div
          v-for="n in filteredNotifications.filter(n =>
            ['message', 'follow', 'comment_like', 'comment', 'admin_deleted_comment'].includes(n.type)
          )"
          :key="n.id"
          class="notification-card"
          :class="{ unread: !n.is_read }"
          @click="openNotification(n)"
        >

          <!-- Ikona -->
          <div class="icon">
            <span v-if="n.type === 'message'" class="material-icons">chat</span>
            <span v-if="n.type === 'follow'" class="material-icons">person_add</span>
            <span v-if="n.type === 'comment_like'" class="material-icons">favorite</span>
            <span v-if="n.type === 'comment'" class="material-icons">comment</span>
            <span v-if="n.type === 'admin_deleted_comment'" class="material-icons">warning</span>
          </div>

          <!-- Teksts -->
          <div class="content">

            <p v-if="n.type === 'message'">
              <strong>{{ n.from_user?.username }}</strong>
              uzrakstīja tev ziņu
            </p>

            <p v-if="n.type === 'follow'">
              <strong>{{ n.from_user?.username }}</strong>
              sāka tev sekot
            </p>

            <p v-if="n.type === 'comment_like'">
              <strong>{{ n.from_user?.username }}</strong>
              patika tavs komentārs
            </p>

            <p v-if="n.type === 'comment'">
              <strong>{{ n.from_user?.username }}</strong>
              komentēja grāmatu
            </p>

            <p v-if="n.type === 'admin_deleted_comment'">
               Administrators izdzēsa tavu komentāru
            </p>

            <!-- Datums -->
            <span class="time">
              {{ new Date(n.created_at).toLocaleString() }}
            </span>

          </div>

          <!-- Dzēšanas poga -->
          <button
            @click.stop="deleteNotification(n.id)"
            class="delete-btn"
          >
            <span class="material-icons">delete</span>
          </button>

        </div>

      </div>

      <!-- Ja nav paziņojumu -->
      <div v-else class="empty-state">
        
        <p>Tev vēl nav neviena paziņojuma.</p>

      </div>

    </div>

  </AuthenticatedLayout>
</template>
<style scoped>
.notifications-page {
  padding: 60px 10%;
  min-height: 80vh;
  background: #f4f6f9;
}

.notifications-header {
  display: flex;
  align-items: center;
  justify-content: flex-start;
  gap: 15px;
  margin-bottom: 25px;
}

.notifications-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.notification-card {
  display: flex;
  gap: 16px;
  align-items: center;
  background: white;
  padding: 18px 20px;
  border-radius: 14px;
  box-shadow: 0 6px 18px rgba(0,0,0,0.08);
  cursor: pointer;
  transition: 0.25s ease;
}

.notification-card:hover {
  transform: translateY(-3px);
  box-shadow: 0 10px 25px rgba(0,0,0,0.12);
}

.notification-card.unread {
  border-left: 5px solid #e6722a;
  background: #fff8f2;
}

.icon {
  font-size: 24px;
}

.content p {
  margin: 0;
  font-size: 15px;
  color: #213555;
}

.time {
  font-size: 12px;
  color: #888;
  margin-top: 4px;
  display: block;
}

.empty-state {
  text-align: center;
  margin-top: 80px;
  color: #999;
}

.notifications-header {
  display: flex;
  align-items: center;
  gap: 15px;
  margin-bottom: 25px;
}

.back-btn {
  background: #213555;
  color: white;
  border: none;
  padding: 6px 14px;
  border-radius: 8px;
  cursor: pointer;
  font-size: 13px;
}

.back-btn:hover {
  background: #3E5879;
}

.material-icons {
  font-size: 24px;
  color: #213555;
}

.notification-card.unread .material-icons {
  color: #e6722a;
}

.delete-btn {
  margin-left: auto;
  background: transparent;
  border: none;
  cursor: pointer;
  display: flex;
  align-items: center;
  padding: 4px;
}

.delete-btn .material-icons {
  font-size: 20px;
  color: #999;
  transition: 0.2s ease;
}

.delete-btn:hover .material-icons {
  color: #e53935;
}

.clear-btn {
  margin-left: auto;
  background: #e6722a;
  color: white;
  border: none;
  padding: 6px 12px;
  border-radius: 8px;
  cursor: pointer;
}

.clear-btn:hover {
  background: #c85f20;
}
</style>