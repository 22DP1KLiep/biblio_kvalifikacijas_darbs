<script setup>
import { ref, computed, watch } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import axios from 'axios'

const page = usePage()

const user = computed(() => page.props?.auth?.user ?? null)
const notificationsCount = computed(() => page.props?.notificationsCount ?? 0)

const searchQuery = ref('')
const activeTab = ref('books')

const userResults = ref([])
const isLoadingUsers = ref(false)


watch(searchQuery, async (q) => {
  if (activeTab.value !== 'users') return

  if (!q.trim()) {
    userResults.value = []
    return
  }

  isLoadingUsers.value = true

  try {
    const res = await axios.get('/api/users/search', {
      params: { q }
    })
    userResults.value = res.data
  } catch {
    userResults.value = []
  }

  isLoadingUsers.value = false
})


const handleSearch = () => {
  const q = searchQuery.value.trim()
  if (!q) return

  if (activeTab.value === 'books') {
    router.get('/gramatas', { q })
  } else {
    router.get('/users', { q })
  }
}
</script>

<template>
  <nav class="navbar">

    <!-- LEFT -->
    <div class="nav-left">
      <h1 class="logo">Biblio</h1>

      <div class="nav-links">
        <Link href="/">Sākums</Link>
        <Link href="/gramatas">Grāmatas</Link>
        <Link href="/kabinets">Kabinets</Link>
      </div>
    </div>

    <!-- RIGHT SIDE -->
    <div class="nav-right">

      <!-- SEARCH -->
      <div class="search-wrapper">

        <!-- TABS -->
        <div class="search-tabs">
          <button
            :class="{ active: activeTab === 'books' }"
            @click="activeTab = 'books'"
          >
            Grāmatas
          </button>

          <button
            :class="{ active: activeTab === 'users' }"
            @click="activeTab = 'users'"
          >
            Lietotāji
          </button>
        </div>

        <!-- INPUT -->
        <div class="search-box">
          <span class="material-icons">search</span>

          <input
            v-model="searchQuery"
            @keyup.enter="handleSearch"
            placeholder="Meklēt..."
          />
        </div>

        <!-- DROPDOWN USERS -->
        <div v-if="activeTab === 'users' && userResults.length" class="search-dropdown">
          <div
            v-for="u in userResults"
            :key="u.id"
            class="search-item"
            @click="router.get(`/users/${u.id}`)"
          >
            <strong>{{ u.username }}</strong>
            <span>{{ u.email }}</span>
          </div>
        </div>

      </div>

      <!-- CHAT -->
      <Link v-if="user" href="/chats" class="icon-btn">
        <span class="material-icons">chat</span>
      </Link>

      <!-- NOTIFICATIONS -->
      <Link v-if="user" href="/notifications" class="icon-btn">
        <span class="material-icons">notifications</span>

        <span v-if="notificationsCount > 0" class="badge">
          {{ notificationsCount }}
        </span>
      </Link>

      <!-- PROFILE -->
      <Link v-if="user" :href="`/users/${user.id}`" class="avatar">
        {{ user.username.charAt(0).toUpperCase() }}
      </Link>

    </div>

  </nav>
</template>

<style scoped>
.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 3%;
  background: #213555;
  color: white;
}

/* LEFT */
.nav-left {
  display: flex;
  align-items: center;
  gap: 20px;
}

.logo {
  font-size: 20px;
  font-weight: bold;
}

.nav-links {
  display: flex;
  gap: 15px;
}

.nav-links a {
  color: white;
  text-decoration: none;
  font-size: 14px;
}

.nav-links a:hover {
  color: #e6722a;
}

/* RIGHT */
.nav-right {
  display: flex;
  align-items: center;
  gap: 15px;
}

/* SEARCH */
.search-wrapper {
  position: relative;
}

.search-tabs {
  display: flex;
  font-size: 11px;
}

.search-tabs button {
  background: transparent;
  border: none;
  color: #ccc;
  cursor: pointer;
  padding: 2px 6px;
}

.search-tabs button.active {
  color: #e6722a;
}

.search-box {
  display: flex;
  align-items: center;
  background: #3E5879;
  padding: 5px 10px;
  border-radius: 20px;
  width: 220px;
}

.search-box input {
  background: transparent;
  border: none;
  outline: none;
  color: white;
  margin-left: 6px;
  width: 100%;
}

.search-dropdown {
  position: absolute;
  top: 60px;
  width: 220px;
  background: white;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.search-item {
  padding: 8px;
  cursor: pointer;
}

.search-item:hover {
  background: #f3f3f3;
}

/* ICONS */
.icon-btn {
  position: relative;
  color: white;

  display: flex;          
  align-items: center;    
  height: 32px;           
}

.icon-btn:hover {
  color: #e6722a;
}

/* AVATAR */
.avatar {
  width: 32px;
  height: 32px;
  background: #e6722a;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* BADGE */
.badge {
  position: absolute;
  top: -5px;
  right: -6px;
  background: red;
  font-size: 10px;
  padding: 2px 5px;
  border-radius: 50%;
}

/* ICON */
.material-icons {
  font-size: 22px;

  display: flex;          
  align-items: center;    
}
</style>