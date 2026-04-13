<template>
    <div class="container mx-auto p-4">
        <h1 class="text-2xl font-bold mb-4 text-center">Top Books</h1>

        <div v-if="books.length" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <div v-for="book in books" :key="book.id" class="p-4 bg-white rounded-lg shadow-lg">
                <img
                    :src="book.image"
                    :alt="book.title"
                    class="w-full h-48 object-cover rounded-md mb-4"
                >
                <h2 class="text-lg font-semibold">{{ book.title }}</h2>
                <p class="text-gray-600 text-sm">by {{ book.author }}</p>
                <!-- Žanri -->
                <div v-if="book.genres && book.genres.length" class="mt-2">
                  <span
                      v-for="genre in book.genres"
                      :key="genre.id"
                      class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1 mb-1"
                  >
                    {{ genre.name }}
                  </span>
                </div>
                <p class="text-sm mt-2 text-gray-700">{{ book.description }}</p>

                <button
                    class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                    @click="inspectBook(book.id)"
                >
                    Inspect
                </button>
            </div>
        </div>

        <p v-else class="text-gray-500 text-center">Loading books...</p>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    data() {
        return {
            books: [],
        };
    },
    mounted() {
        axios.get('/get/all/books')
            .then(response => {
                this.books = response.data; // Get only top 10 books
            })
            .catch(error => {
                console.error('Error fetching books:', error);
            });
    },

    methods: {
        inspectBook(id) {
            this.$inertia.visit(`/book/${id}`);
        }
    }
};
</script>

<style>
body {
    font-family: Arial, sans-serif;
    background-color: #f9f9f9;
}
</style>
