<template>
  <div class="flex flex-col md:flex-row relative min-h-screen">
    <button class="md:hidden bg-blue-500 text-white px-4 py-2 m-4 rounded w-fit" @click="sidebarOpen = true">☰ Filtri</button>

    <!-- Sānu josla (lokālajām grāmatām) -->
    <transition name="slide">
      <aside v-show="sidebarOpen && mode==='local'" class="fixed md:sticky top-0 left-0 z-40 md:z-10 w-64 bg-gray-100 p-4 h-screen overflow-y-auto shadow-lg md:shadow-md rounded-r-xl">
        <div class="flex justify-between items-center mb-4 md:hidden">
          <h2 class="text-lg font-bold">Filtri</h2>
          <button @click="sidebarOpen = false" class="text-xl font-bold">&times;</button>
        </div>

        <div class="mb-6">
          <h2 class="text-md font-semibold mb-2 text-gray-700">Žanri</h2>
          <div class="flex flex-wrap gap-2">
            <button
              v-for="genre in genres"
              :key="genre.id"
              @click="toggleGenre(genre.id)"
              :class="[
                'px-4 py-2 rounded-full text-sm font-medium border transition',
                selectedGenres.includes(genre.id)
                  ? 'bg-blue-100 text-blue-600 border-blue-500'
                  : 'bg-white text-gray-700 border-gray-300 hover:bg-gray-100'
              ]"
            >
              {{ genre.name }}
            </button>
          </div>
        </div>

        <div class="mt-6">
          <h3 class="text-md font-semibold mb-2">Kārtot pēc:</h3>
          <select v-model="sortBy" @change="fetchLocalBooks" class="w-full border rounded px-2 py-1 mb-2">
            <option value="title">Nosaukuma</option>
            <option value="author">Autora</option>
            <option value="published_year">Iznākšanas gada</option>
          </select>

          <select v-model="direction" @change="fetchLocalBooks" class="w-full border rounded px-2 py-1">
            <option value="asc">Augošā secībā</option>
            <option value="desc">Dilstošā secībā</option>
          </select>
        </div>
      </aside>
    </transition>

    <div v-show="sidebarOpen && mode==='local'" class="fixed inset-0 bg-black bg-opacity-40 z-30 md:hidden" @click="sidebarOpen = false"></div>

    <main class="flex-1 p-4">
      <!-- Avota pārslēgs + meklēšana -->
      <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3 mb-4">
        <div class="inline-flex rounded overflow-hidden border">
          <button
            :class="['px-4 py-2', mode==='local' ? 'bg-[#213555] text-white' : 'bg-white text-gray-700']"
            @click="switchMode('local')"
          >Lokālās</button>
          <button
            :class="['px-4 py-2 border-l', mode==='google' ? 'bg-[#213555] text-white' : 'bg-white text-gray-700']"
            @click="switchMode('google')"
          >Google Books</button>
        </div>

        <div class="flex w-full md:w-auto gap-2">
          <input
            v-model="searchInput"
            @keyup.enter="search"
            class="flex-1 md:w-80 border rounded px-3 py-2"
            :placeholder="mode==='google' ? 'Meklēt Google Books (piem., Harijs Poters)' : 'Meklēt savā bibliotēkā'"
          />
          <button @click="search" class="bg-[#213555] hover:bg-[#3e5879] text-white px-4 py-2 rounded">Meklēt</button>
        </div>
      </div>

      <div v-if="effectiveQuery" class="mb-4 text-center">
        <p class="text-gray-600">Meklēšanas rezultāti: <strong>{{ effectiveQuery }}</strong></p>
      </div>

      <!-- Režģis (vienāds abiem avotiem) -->
      <div v-if="displayBooks.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-8">
        <div
          v-for="book in displayBooks"
          :key="book.key"
          class="relative w-full max-w-[390px] rounded-lg overflow-hidden shadow-lg cursor-pointer transition transform hover:scale-105 hover:shadow-2xl mx-auto"
          @click="cardClick(book)"
        >
          <img :src="resolveImg(book.image)" :alt="book.title" class="w-full h-[460px] object-cover" />
          <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent text-white px-6 py-6 pointer-events-none">
            <h3 class="text-xl font-bold leading-snug mb-1" style="text-shadow: 2px 2px 6px rgba(0,0,0,0.85);">
              {{ book.title }}
            </h3>
            <p class="text-sm text-gray-300 italic mb-1" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.85);">
              by {{ book.author || '—' }}
            </p>
            <div v-if="book.genres && book.genres.length" class="flex flex-wrap gap-1 mb-1">
              <span v-for="g in book.genres" :key="g.id || g.name" class="inline-block bg-white/30 text-white text-[10px] px-2 py-[2px] rounded-full border border-white/50" style="backdrop-filter: blur(4px); text-shadow: none;">
                {{ g.name }}
              </span>
            </div>
            <p v-if="book.description" class="text-xs text-gray-400 mt-1" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.85);">
              {{ book.description.slice(0, 60) }}...
            </p>
          </div>
        </div>
      </div>

      <p v-else class="text-gray-500 text-center">Nav atrastu grāmatu.</p>

      <!-- Google Books lappošanas pogas -->
      <div v-if="mode==='google' && total > perPage" class="flex items-center gap-2 mt-6 justify-center">
        <button :disabled="page===1" @click="page--, fetchGoogleBooks()" class="px-3 py-1 border rounded disabled:opacity-50">←</button>
        <span class="text-sm">Lapa {{ page }}</span>
        <button :disabled="page*perPage >= total" @click="page++, fetchGoogleBooks()" class="px-3 py-1 border rounded disabled:opacity-50">→</button>
      </div>
    </main>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      // koplietojamie
      booksLocal: [],
      booksGoogle: [],
      genres: [],
      selectedGenres: [],
      sortBy: 'title',
      direction: 'asc',
      sidebarOpen: true,
      mode: 'local',              // 'local' | 'google'
      searchInput: '',
      effectiveQuery: '',
      // google paging
      page: 1,
      perPage: 10,
      total: 0,
    }
  },
  computed: {
    displayBooks() {
      return this.mode === 'local' ? this.booksLocal : this.booksGoogle
    }
  },
  mounted() {
    // nolasām ?q= no URL
    const q = new URLSearchParams(window.location.search).get('q') || ''
    this.searchInput = q
    this.effectiveQuery = q

    // žanri lokālajam filtram
    axios.get('/api/genres').then(r => { this.genres = r.data }).catch(()=>{})

    // sākotnējā ielāde
    if (this.mode === 'local') this.fetchLocalBooks()
    if (window.innerWidth < 768) this.sidebarOpen = false
  },
  methods: {
    switchMode(m) {
      if (this.mode === m) return
      this.mode = m
      // ja pārslēdzamies uz Google, palaid meklēšanu Google
      if (m === 'google') {
        this.page = 1
        this.fetchGoogleBooks()
      } else {
        this.fetchLocalBooks()
      }
    },
    search() {
      this.effectiveQuery = this.searchInput.trim()
      if (this.mode === 'local') this.fetchLocalBooks()
      else { this.page = 1; this.fetchGoogleBooks() }
    },
    // Lokālās grāmatas (tava esošā loģika)
    fetchLocalBooks() {
      const query = (this.effectiveQuery || '').toLowerCase()
      axios.get('/get/all/books', {
        params: {
          sort: this.sortBy,
          direction: this.direction,
          genres: this.selectedGenres,
        }
      }).then(r => {
        let all = r.data
        if (query) {
          all = all.filter(b =>
            (b.title || '').toLowerCase().includes(query) ||
            (b.author || '').toLowerCase().includes(query)
          )
        }
        // normalizējam uz vienotu formātu
        this.booksLocal = all.map(b => ({
          key: `l-${b.id}`,
          id: b.id,
          title: b.title,
          author: b.author,
          description: b.description,
          image: b.image,       // var būt relatīvs ceļš
          genres: b.genres || [],
          isGoogle: false,
        }))
      }).catch(e => console.error('Kļūda ielādējot grāmatas:', e))
    },
    // Google Books meklēšana
    async fetchGoogleBooks() {
      const params = {
        q: this.effectiveQuery || 'latviešu grāmatas',
        page: this.page,
        perPage: this.perPage
      }
      try {
        const { data } = await axios.get('/google-books/search', { params })
        this.total = data.total || 0
        this.booksGoogle = (data.items || []).map(it => ({
          key: `g-${it.volumeId}`,
          volumeId: it.volumeId,
          title: it.title,
          author: it.author,
          description: it.description,
          image: it.image,      // pilns URL
          genres: [],           // Google kategorijas vari pievienot vēlāk
          previewLink: it.previewLink,
          isGoogle: true,
        }))
      } catch (e) {
        console.error('Google Books meklēšanas kļūda:', e)
        this.booksGoogle = []
      }
    },
    // uz bildes/ kartiņas klikšķa
    async cardClick(book) {
      if (!book.isGoogle) {
        // lokāla → ejam uz detaļām
        this.$inertia.visit(`/book/${book.id}`)
        return
      }
      // google → piedāvā importēt
      const ok = confirm('Importēt šo grāmatu tavā bibliotēkā?')
      if (!ok) {
        // ja neimportē, piedāvā priekšskatīt
        if (book.previewLink) window.open(book.previewLink, '_blank')
        return
      }
      try {
        const { data } = await axios.post('/google-books/import', { volumeId: book.volumeId })
        const newId = data?.book?.id
        if (newId) {
          alert('✔️ Grāmata saglabāta bibliotēkā!')
          this.$inertia.visit(`/book/${newId}`)
        } else {
          alert(data?.message || 'Saglabāšana izdevās.')
          this.fetchLocalBooks()
          this.switchMode('local')
        }
      } catch (e) {
        alert(e?.response?.data?.message || 'Neizdevās saglabāt grāmatu.')
      }
    },
    // attēla URL (gan relatīvs, gan pilns)
    resolveImg(img) {
      if (!img) return 'https://via.placeholder.com/300'
      return img.startsWith('http') ? img : `/${img}`
    },
    // žanru izvēle lokālajā režīmā
    toggleGenre(id) {
      this.selectedGenres = this.selectedGenres.includes(id)
        ? this.selectedGenres.filter(g => g !== id)
        : [...this.selectedGenres, id]
      this.fetchLocalBooks()
    },
  }
}
</script>

<style scoped>
.slide-enter-active,.slide-leave-active{transition:transform .3s ease}
.slide-enter-from,.slide-leave-to{transform:translateX(-100%)}
.fade-enter-active,.fade-leave-active{transition:all .3s ease}
.fade-enter-from,.fade-leave-to{opacity:0;max-height:0}
.fade-enter-to,.fade-leave-from{opacity:1;max-height:500px}
</style>
