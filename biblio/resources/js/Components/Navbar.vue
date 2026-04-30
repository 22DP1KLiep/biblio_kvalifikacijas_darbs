<script setup>
import { ref, computed, watch } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'
import axios from 'axios'
import { onMounted, onUnmounted } from 'vue'

const handleClickOutside = (e) => {
  if (!e.target.closest('.search-wrapper')) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})

const page = usePage()

const user = computed(() => page.props?.auth?.user ?? null)
const isAdmin = computed(() => user.value?.role === 'admin')
const notificationsCount = computed(() => page.props?.notificationsCount ?? 0)

const searchQuery = ref('')
const activeTab = ref('books')

const userResults = ref([])
const isLoadingUsers = ref(false)

const isMenuOpen = ref(false)

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}

const closeMenu = () => {
  isMenuOpen.value = false
}

const closeDropdown = () => {
  searchQuery.value = ''
  userResults.value = []
}


watch(searchQuery, async (q) => {

  // 🔒 JA RESTRICTED UN USERS TAB → STOP
  if (user.value?.restricted && activeTab.value === 'users') return

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

// SUBMIT SEARCH
const handleSearch = () => {
  const q = searchQuery.value.trim()
  if (!q) return

  
  if (user.value?.restricted && activeTab.value === 'users') {
    alert('Lietotāju meklēšana nav pieejama')
    return
  }

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
        <Link v-if="user" href="/kabinets">Kabinets</Link>
        <Link v-if="isAdmin" href="/admin" class="admin-link">
          Admin Panelis
        </Link>
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
            @click="!user?.restricted && (activeTab = 'users')"
            :disabled="user?.restricted"
          >
            Lietotāji
          </button>
        </div>

        <!-- INPUT -->
        <div class="search-box">
          <span class="material-icons">search</span>

          <input
            v-model="searchQuery"
            :disabled="user?.restricted && activeTab === 'users'"
            @keyup.enter="handleSearch(); closeDropdown()"
            placeholder="Meklēt..."
          />
        </div>

        <!-- DROPDOWN USERS -->
        <div
  v-if="activeTab === 'users' && (userResults.length || isLoadingUsers)"
  class="search-dropdown"
>
  <!-- LOADING -->
  <div v-if="isLoadingUsers" class="search-item">
    Meklē...
  </div>

  <!-- USERS -->
  <div
    v-for="u in userResults"
    :key="u.id"
    class="search-item user-row"
    @click="router.get(`/users/${u.id}`); closeDropdown()"
  >
    <!-- AVATAR -->
    <img
      :src="u.avatar ? `/storage/${u.avatar}` : `https://ui-avatars.com/api/?name=${u.username}`"
      class="user-avatar"
    />

    <!-- INFO -->
    <div class="user-info">
      <span class="username">{{ u.username }}</span>
      <span class="email">{{ u.email }}</span>
    </div>
  </div>

  <!-- EMPTY -->
  <div v-if="!isLoadingUsers && !userResults.length" class="search-item">
    Nav rezultātu
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
        <img
          :src="user.avatar 
            ? `/storage/${user.avatar}` 
            : `https://ui-avatars.com/api/?name=${user.username}`"
          class="avatar-img"
        />
      </Link>
      <!-- LOGOUT -->
      <Link
        v-if="user"
        href="/logout"
        method="post"
        as="button"
        class="icon-btn"
      >
        <span class="material-icons">logout</span>
      </Link>

      <!-- LOGIN (ja nav lietotājs) -->
      <Link v-else href="/auth" class="icon-btn">
        <span class="material-icons">login</span>
      </Link>

    </div>

    <!-- HAMBURGER -->
    <div class="hamburger" @click="toggleMenu">
      <span></span>
      <span></span>
      <span></span>
    </div>

  </nav>
  <!-- MOBILE MENU -->
<div class="mobile-menu" :class="{ open: isMenuOpen }">

  <!-- CLOSE -->
  <button class="close-btn" @click="closeMenu">
    <span class="material-icons">close</span>
  </button>

  <!-- SEARCH -->
  <!-- MOBILE SEARCH -->
<div class="mobile-search">

  <!-- TABS -->
  <div class="mobile-tabs">
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
  <input
    v-model="searchQuery"
    :disabled="user?.restricted && activeTab === 'users'"
    @keyup.enter="handleSearch(); closeMenu()"
    placeholder="Meklēt..."
  />

</div>

  <Link href="/" @click="closeMenu">Sākums</Link>
  <Link href="/gramatas" @click="closeMenu">Grāmatas</Link>
  <Link href="/kabinets" @click="closeMenu">Kabinets</Link>
  <Link v-if="isAdmin" href="/admin" @click="closeMenu">
    Admin Panelis
  </Link>

  <Link v-if="user" href="/chats" @click="closeMenu">Čati</Link>
  <Link v-if="user" href="/notifications" @click="closeMenu">Notifikācijas</Link>

  <Link v-if="user" :href="`/users/${user.id}`" @click="closeMenu">
    Mans profils
  </Link>

  <Link v-if="user" href="/logout" method="post" as="button" @click="closeMenu">
    Iziet
  </Link>

  <Link v-else href="/auth" @click="closeMenu">
    Ienākt
  </Link>

</div>
</template>

<style scoped>

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 3%;
  background: #213555;
  color: white;
  position: relative;
  z-index: 100;
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
  z-index: 50; 
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
  z-index: 999; 
}

.search-item {
  padding: 8px;
  cursor: pointer;
  color: black;
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
  border-radius: 50%;
  overflow: hidden; /* 🔥 svarīgi */
  display: flex;
  align-items: center;
  justify-content: center;
  background: #e6722a;
}

.avatar-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
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

.icon-btn {
  transition: 0.2s ease;
}

.icon-btn:hover {
  color: #e6722a;
  transform: scale(1.1);
}

/* HAMBURGER */
.hamburger {
  display: none;
  flex-direction: column;
  cursor: pointer;
}

.hamburger span {
  width: 22px;
  height: 2px;
  background: white;
  margin: 3px 0;
}

/* MOBILE MENU */
.mobile-menu {
  position: fixed;
  top: 0;
  right: -100%;
  width: 75%;
  height: 100vh;
  background: #213555;
  display: flex;
  flex-direction: column;
  padding: 70px 20px;
  gap: 18px;
  transition: 0.3s ease;
  z-index: 100;
}

.mobile-menu.open {
  right: 0;
}

/* CLOSE BUTTON */
.close-btn {
  position: absolute;
  top: 15px;
  right: 15px;
  background: transparent;
  border: none;
  color: white;
  cursor: pointer;
}

/* MOBILE SEARCH */
.mobile-search input {
  width: 100%;
  padding: 10px;
  border-radius: 10px;
  border: none;
  background: #3E5879;
  color: white;
}

/* LINKS */
.mobile-menu a,
.mobile-menu button {
  color: white;
  font-size: 16px;
  text-decoration: none;
  background: none;
  border: none;
  text-align: left;
}

.mobile-menu a:hover,
.mobile-menu button:hover {
  color: #e6722a;
}

/* RESPONSIVE */
@media (max-width: 768px) {

  .nav-links,
  .nav-right,
  .search-wrapper {
    display: none;
  }

  .hamburger {
    display: flex;
  }
}

.mobile-tabs {
  display: flex;
  gap: 12px; /* 🔥 palielinām atstarpi */
  margin-bottom: 10px;
}

.mobile-tabs button {
  background: #3E5879; /* 🔥 dod fonu, lai redz atstarpi */
  border: none;
  color: #ccc;
  font-size: 13px;
  cursor: pointer;

  padding: 6px 12px; 
  border-radius: 12px;
}

/* ACTIVE */
.mobile-tabs button.active {
  color: white;
  background: #e6722a; 
}

/* USER ROW */
.user-row {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px;
  transition: 0.2s;
  color: black;
}

.user-row:hover {
  background: #f5f5f5;
}

/* AVATAR */
.user-avatar {
  width: 36px;
  height: 36px;
  border-radius: 50%;
  object-fit: cover;
}

/* INFO */
.user-info {
  display: flex;
  flex-direction: column;
}

.username {
  font-weight: 600;
  font-size: 14px;
}

.email {
  font-size: 12px;
  color: gray;
}
</style>