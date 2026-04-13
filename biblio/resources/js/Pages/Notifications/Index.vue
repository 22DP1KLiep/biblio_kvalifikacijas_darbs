<script setup>
import { router } from '@inertiajs/vue3'

defineProps({
  notifications: Array
})

const openNotification = (n) => {
  if (n.type === 'message') {
    router.get(`/chats/${n.data}`)
  }

  if (n.type === 'follow') {
    router.get(`/users/${n.from_user_id}`)
  }
}
</script>

<template>
  <div class="notifications-page">

    <div class="notifications-header">
      <h2>Notifikācijas</h2>
    </div>

    <div v-if="notifications.length" class="notifications-list">

      <div
        v-for="n in notifications"
        :key="n.id"
        class="notification-card"
        :class="{ unread: !n.is_read }"
        @click="openNotification(n)"
      >
        <div class="icon">
          <span v-if="n.type === 'message'">💬</span>
          <span v-if="n.type === 'follow'">👤</span>
        </div>

        <div class="content">
          <p v-if="n.type === 'message'">
            <strong>{{ n.from_user?.username }}</strong>
            uzrakstīja tev ziņu
          </p>

          <p v-if="n.type === 'follow'">
            <strong>{{ n.from_user?.username }}</strong>
            sāka tev sekot
          </p>

          <span class="time">
            {{ new Date(n.created_at).toLocaleString() }}
          </span>
        </div>
      </div>

    </div>

    <div v-else class="empty-state">
      <div class="empty-icon">🔔</div>
      <p>Tev vēl nav nevienas notifikācijas</p>
    </div>

  </div>
</template>

<style scoped>
.notifications-page {
  padding: 60px 10%;
  min-height: 80vh;
  background: #f4f6f9;
}

.notifications-header h2 {
  font-size: 1.8rem;
  color: #213555;
  margin-bottom: 30px;
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

.empty-icon {
  font-size: 40px;
  margin-bottom: 10px;
}
</style>