  <template>
    <div class="flex flex-col md:flex-row relative min-h-screen">
      <button class="md:hidden bg-blue-500 text-white px-4 py-2 m-4 rounded w-fit" @click="sidebarOpen = true">☰ Filtri</button>

      <transition name="slide">
        <aside v-show="sidebarOpen" class="fixed md:sticky top-0 left-0 z-40 md:z-10 w-64 bg-gray-100 p-4 h-screen overflow-y-auto shadow-lg md:shadow-md rounded-r-xl">
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
            <select v-model="sortBy" @change="fetchBooks" class="w-full border rounded px-2 py-1 mb-2">
              <option value="title">Nosaukuma</option>
              <option value="author">Autora</option>
              <option value="published_year">Iznākšanas gada</option>
            </select>

            <select v-model="direction" @change="fetchBooks" class="w-full border rounded px-2 py-1">
              <option value="asc">Augošā secībā</option>
              <option value="desc">Dilstošā secībā</option>
            </select>
          </div>
        </aside>
      </transition>

      <div v-show="sidebarOpen" class="fixed inset-0 bg-black bg-opacity-40 z-30 md:hidden" @click="sidebarOpen = false"></div>

      <main class="flex-1 p-4">
        <div v-if="searchQuery" class="mb-4 text-center">
          <p class="text-gray-600">Meklēšanas rezultāti: <strong>{{ searchQuery }}</strong></p>
        </div>

        <div v-if="books.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-8">
          <div v-for="book in books" :key="book.id" @click="inspectBook(book.id)" class="relative w-full max-w-[390px] rounded-lg overflow-hidden shadow-lg cursor-pointer transition transform hover:scale-105 hover:shadow-2xl mx-auto">
            <img :src="book.image" :alt="book.title" class="w-full h-[460px] object-cover" />
            <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent text-white px-6 py-6 pointer-events-none">
              <h3 class="text-xl font-bold leading-snug mb-1" style="text-shadow: 2px 2px 6px rgba(0,0,0,0.85);">
                {{ book.title }}
              </h3>
              <p class="text-sm text-gray-300 italic mb-1" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.85);">
                by {{ book.author }}
              </p>
              <div v-if="book.genres && book.genres.length" class="flex flex-wrap gap-1 mb-1">
                <span v-for="genre in book.genres" :key="genre.id" class="inline-block bg-white/30 text-white text-[10px] px-2 py-[2px] rounded-full border border-white/50" style="backdrop-filter: blur(4px); text-shadow: none;">
                  {{ genre.name }}
                </span>
              </div>
              <p v-if="book.description" class="text-xs text-gray-400 mt-1" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.85);">
                {{ book.description.slice(0, 60) }}...
              </p>
            </div>
          </div>
        </div>

        <p v-else class="text-gray-500 text-center">Nav atrastu grāmatu.</p>
      </main>
    </div>
  </template>

  <script>
  import axios from 'axios'
  export default {
    data() {
      return {
        books: [],
        genres: [],
        selectedGenres: [],
        sortBy: 'title',
        direction: 'asc',
        sidebarOpen: true,
        genresOpen: true,
        searchQuery: '',
      }
    },
    mounted() {
      axios.get('/api/genres').then(r => { this.genres = r.data })
      this.fetchBooks()
      if (window.innerWidth < 768) this.sidebarOpen = false
    },
    methods: {
      fetchBooks() {
        const query = new URLSearchParams(window.location.search).get('q') || ''
        this.searchQuery = query
        axios.get('/get/all/books', { params: {
          sort: this.sortBy,
          direction: this.direction,
          genres: this.selectedGenres,
        }}).then(r => {
          let all = r.data
          if (query) {
            const q = query.toLowerCase()
            all = all.filter(b =>
              b.title.toLowerCase().includes(q) || b.author.toLowerCase().includes(q)
            )
          }
          this.books = all
        }).catch(e => console.error('Kļūda ielādējot grāmatas:', e))
      },
      inspectBook(id) { this.$inertia.visit(`/book/${id}`) },
      toggleGenre(id) {
        this.selectedGenres = this.selectedGenres.includes(id)
          ? this.selectedGenres.filter(g => g !== id)
          : [...this.selectedGenres, id]
        this.fetchBooks()
      },
    },
  }
  </script>

  <style scoped>
  .slide-enter-active,.slide-leave-active{transition:transform .3s ease}
  .slide-enter-from,.slide-leave-to{transform:translateX(-100%)}
  .fade-enter-active,.fade-leave-active{transition:all .3s ease}
  .fade-enter-from,.fade-leave-to{opacity:0;max-height:0}
  .fade-enter-to,.fade-leave-from{opacity:1;max-height:500px}
  </style>
