<template>
    <!-- Top grāmatu saraksts -->
    <div class="container mx-auto p-4">

        <!-- Lapas virsraksts -->
        <h1 class="text-2xl font-bold mb-4 text-center">Top Books</h1>

        <!-- Grāmatu kartīšu attēlošana -->
        <div v-if="books.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">

            <!-- Vienas grāmatas kartīte -->
            <div v-for="book in books" :key="book.id" class="p-4 bg-white rounded-lg shadow-lg">

                <!-- Grāmatas attēls -->
                <img
                    :src="book.image"
                    :alt="book.title"
                    class="w-full h-48 object-cover rounded-md mb-4"
                >

                <!-- Grāmatas pamatinformācija -->
                <h2 class="text-lg font-semibold">{{ book.title }}</h2>
                <p class="text-gray-600 text-sm">by {{ book.author }}</p>

                <!-- Grāmatas žanri -->
                <div v-if="book.genres && book.genres.length" class="mt-2">
                  <span
                      v-for="genre in book.genres"
                      :key="genre.id"
                      class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1 mb-1"
                  >
                    {{ genre.name }}
                  </span>
                </div>

                <!-- Grāmatas apraksts -->
                <p class="text-sm mt-2 text-gray-700">
                    {{ book.description }}
                </p>

                <!-- Poga grāmatas apskatei -->
                <button
                    class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                    @click="inspectBook(book.id)"
                >
                    Inspect
                </button>
            </div>
        </div>

        <!-- Ziņojums datu ielādes laikā -->
        <p v-else class="text-gray-500 text-center">
            Loading books...
        </p>

    </div>
</template>

<script>
import axios from 'axios';

export default {

    // Komponentes dati
    data() {
        return {
            books: [],
        };
    },

    // Datu ielāde pēc komponentes atvēršanas
    mounted() {
        axios.get('/get/all/books')
            .then(response => {
                this.books = response.data;
            })
            .catch(error => {
                console.error('Error fetching books:', error);
            });
    },

    // Komponentes funkcijas
    methods: {

        // Atver izvēlētās grāmatas detalizēto skatu
        inspectBook(id) {
            this.$inertia.visit(`/book/${id}`);
        }
    }
};
</script>

<style>

/* Lapas pamatstils */
body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
}
</style>