<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

import { Link } from '@inertiajs/vue3'

defineProps({
  users: Object,
  search: String
})

defineOptions({
  layout: AuthenticatedLayout
})

</script>

<template>
  <div class="users-page">

    <div class="users-header">
      <h2 v-if="search">
        Meklēšanas rezultāti: 
        <span>"{{ search }}"</span>
      </h2>
    </div>

    <div v-if="users.data.length" class="users-grid">
      
      <Link 
        v-for="u in users.data"
        :key="u.id"
        :href="`/users/${u.id}`"
        class="user-card"
      >
        <div class="avatar">
          {{ u.name.charAt(0).toUpperCase() }}
        </div>

        <div class="user-info">
          <h3>{{ u.name }}</h3>
          <p>@{{ u.username }}</p>
        </div>
      </Link>

    </div>

    <div v-else class="no-results">
      Nav atrasts neviens lietotājs.
    </div>

  </div>
</template>

<style scoped>
.users-page {
  padding: 50px 8%;
  min-height: 80vh;
  background: #f4f6f9;
}

.users-header h2 {
  font-size: 1.4rem;
  margin-bottom: 30px;
  color: #213555;
}

.users-header span {
  color: #e6722a;
}

.users-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 20px;
}

.user-card {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 18px;
  background: white;
  border-radius: 14px;
  text-decoration: none;
  color: #213555;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  transition: 0.25s ease;
}

.user-card:hover {
  transform: translateY(-4px);
  box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  background: #213555;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  font-size: 1.2rem;
}

.user-info h3 {
  margin: 0;
  font-size: 1rem;
}

.user-info p {
  margin: 3px 0 0 0;
  font-size: 0.85rem;
  color: #777;
}

.no-results {
  margin-top: 40px;
  color: #999;
  font-size: 1rem;
}
</style>