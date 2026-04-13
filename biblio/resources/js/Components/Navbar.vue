<script setup>
import { ref, computed, watch } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import axios from 'axios'

/* =========================
   GLOBAL PAGE DATA
========================= */

const page = usePage()

const user = computed(() => page.props?.auth?.user ?? null)

const notificationsCount = computed(() =>
  page.props?.notificationsCount ?? 0
)

/* =========================
   NAVBAR STATE
========================= */

const isMenuActive = ref(false)
const isSearchOpen = ref(false)
const searchQuery = ref('')
const activeTab = ref('books')

/* =========================
   SEARCH STATE
========================= */

const userResults = ref([])
const isLoadingUsers = ref(false)

/* =========================
   WATCH USER SEARCH
========================= */

watch(searchQuery, async (newValue) => {
  if (activeTab.value !== 'users') return

  if (!newValue.trim()) {
    userResults.value = []
    return
  }

  isLoadingUsers.value = true

  try {
    const response = await axios.get('/api/users/search', {
      params: { q: newValue }
    })

    userResults.value = response.data
  } catch (e) {
    userResults.value = []
  }

  isLoadingUsers.value = false
})

/* =========================
   NAVIGATION FUNCTIONS
========================= */

const toggleNav = () => {
  isMenuActive.value = !isMenuActive.value
}

const toggleSearch = () => {
  isSearchOpen.value = !isSearchOpen.value
  searchQuery.value = ''
  userResults.value = []
}

const handleSearch = () => {
  const q = searchQuery.value.trim()
  if (!q) return

  if (activeTab.value === 'books') {
    router.get('/gramatas', { q })
  } else {
    router.get('/users', { q })
  }

  isSearchOpen.value = false
}
</script>

<template>
  <nav>

    <!-- SEARCH MODE -->
    <template v-if="isSearchOpen">

      <div class="logo-search">
        <h1><a href="/">Biblio</a></h1>

        <div class="search-tabs">
          <button
            type="button"
            :class="{ active: activeTab === 'books' }"
            @click="activeTab = 'books'"
          >
            Grāmatas
          </button>

          <button
            type="button"
            :class="{ active: activeTab === 'users' }"
            @click="activeTab = 'users'"
          >
            Lietotāji
          </button>
        </div>

        <form @submit.prevent="handleSearch" class="search-inline">
          <input
            type="text"
            v-model="searchQuery"
            placeholder="Meklēt..."
            class="search-input"
            autofocus
          />
          <div 
            v-if="activeTab === 'users' && userResults.length"
            class="search-dropdown"
          >
            <div
              v-for="u in userResults"
              :key="u.id"
              class="search-item"
              @click="router.get(`/users/${u.id}`); isSearchOpen = false"
            >
              <strong>{{ u.name }}</strong>
              <span>@{{ u.username }}</span>
            </div>
          </div>
        </form>

        <button class="search-toggle" @click="toggleSearch">
          ✕
        </button>
      </div>

    </template>

    <!-- NORMAL MODE -->
    <template v-else>

      <div class="logo-search">
  <h1><a href="/">Biblio</a></h1>

  <button class="search-toggle" @click="toggleSearch">
    🔍
  </button>
</div>

      <ul>
        <li><a href="/gramatas">Grāmatas</a></li>
        <li><a href="/kabinets">Mans kabinets</a></li>
        <li v-if="user">
    <Link href="/chats">
      Čati
    </Link>
  </li>
  <li v-if="user" class="notification-item">
    <Link href="/notifications">
      🔔
      <span v-if="notificationsCount > 0" class="badge">
        {{ notificationsCount }}
      </span>
    </Link>
  </li>

        <li v-if="user">
          <Link :href="`/users/${user.id}`">
            Mans profils
          </Link>
        </li>

        <li v-if="user && user.role === 'admin'">
          <Link href="/admin" class="admin-link">
            Admin
          </Link>
        </li>

        <li v-if="!user">
          <a href="/auth">Ienākt</a>
        </li>

        <li v-else>
          <Link
            href="/logout"
            method="post"
            as="button"
            class="logout-button"
          >
            Iziet
          </Link>
        </li>
      </ul>

      <div class="hamburger" @click="toggleNav">
        <span class="line"></span>
        <span class="line"></span>
        <span class="line"></span>
      </div>

    </template>

  </nav>

  <!-- Mobile menu -->
  <div class="menubar" :class="{ active: isMenuActive }">
    <ul>
      <li><a href="/gramatas">Grāmatas</a></li>
      <li><a href="/kabinets">Mans kabinets</a></li>
      <li v-if="user">
      <Link href="/chats">
        Čati
      </Link>
    </li>

      <li v-if="user">
        <Link :href="`/users/${user.id}`">
          Mans profils
        </Link>
      </li>

      <li v-if="user && user.role === 'admin'">
        <Link href="/admin">Admin panelis</Link>
      </li>

      <li v-if="!user">
        <a href="/auth">Ienākt</a>
      </li>

      <li v-else>
        <Link
          href="/logout"
          method="post"
          as="button"
          class="logout-button"
        >
          Iziet
        </Link>
      </li>
    </ul>
  </div>
</template>

<style scoped>
nav {
  background-color: rgb(33, 53, 85);
  padding: 5px 2%;
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 55px;
  box-shadow: rgba(50, 50, 93, 0.25) 0px 2px 5px -1px,
              rgba(0, 0, 0, 0.3) 0px 1px 3px -1px;
}

.logo-search {
  display: flex;
  align-items: center;
  gap: 12px;
}

nav h1 {
  font-size: 1.5rem;
  background: white;
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
}

nav ul {
  display: flex;
  list-style: none;
}

nav ul li {
  margin-left: 0.5rem;
}

nav ul li a {
  text-decoration: none;
  color: #fff;
  font-size: 95%;
  padding: 4px 8px;
  border-radius: 5px;
}

nav ul li a:hover {
  color: #e6722a;
}

.search-toggle {
  background: transparent;
  border: none;
  color: white;
  font-size: 18px;
  cursor: pointer;
}

.search-inline {
  flex: 1;
}

.search-input {
  padding: 6px 10px;
  border-radius: 6px;
  border: none;
  outline: none;
}

.search-tabs {
  display: flex;
}

.search-tabs button {
  background: transparent;
  border: none;
  color: white;
  padding: 4px 10px;
  cursor: pointer;
  border-bottom: 2px solid transparent;
}

.search-tabs button.active {
  border-bottom: 2px solid #e6722a;
  color: #e6722a;
}

.logout-button {
  background: transparent;
  border: none;
  color: white;
  cursor: pointer;
}

.logout-button:hover {
  color: #e6722a;
}

.admin-link:hover {
  color: #e6722a;
}

.hamburger {
  display: none;
  cursor: pointer;
}

.hamburger .line {
  width: 25px;
  height: 2px;
  background: #fff;
  display: block;
  margin: 7px auto;
}

.menubar {
  position: absolute;
  top: 0;
  left: -60%;
  width: 60%;
  height: 100vh;
  padding: 20% 0;
  background: #fff;
  transition: all .5s ease-in;
  z-index: 2;
}

.menubar.active {
  left: 0;
}

.menubar ul {
  list-style: none;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.menubar li {
  margin: 10px 0;
}

@media screen and (max-width: 790px) {
  .hamburger {
    display: block;
  }
  nav ul {
    display: none;
  }
}

.search-dropdown {
  position: absolute;
  background: white;
  width: 300px;
  margin-top: 4px;
  border-radius: 8px;
  overflow: hidden;
  box-shadow: 0 5px 15px rgba(0,0,0,0.2);
  z-index: 10;
}

.search-item {
  padding: 8px 12px;
  cursor: pointer;
  display: flex;
  flex-direction: column;
}

.search-item:hover {
  background: #f3f3f3;
}

.search-item span {
  font-size: 12px;
  color: #666;
}
</style>