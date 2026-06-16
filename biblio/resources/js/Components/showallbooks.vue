<template>

    <!-- galvenais konteiners -->
    <div class="container mx-auto p-4">

        <!-- lapas virsraksts -->
        <h1 class="text-2xl font-bold mb-4 text-center">
            Top Books
        </h1>

        <!-- grāmatu režģis -->
        <div
            v-if="books.length"
            class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6"
        >

            <!-- viena grāmatas kartiņa -->
            <div
                v-for="book in books"
                :key="book.id"
                class="p-4 bg-white rounded-lg shadow-lg"
            >

                <!-- grāmatas attēls -->
                <img
                    :src="book.image"
                    :alt="book.title"
                    class="w-full h-48 object-cover rounded-md mb-4"
                >

                <!-- grāmatas nosaukums -->
                <h2 class="text-lg font-semibold">
                    {{ book.title }}
                </h2>

                <!-- grāmatas autors -->
                <p class="text-gray-600 text-sm">
                    by {{ book.author }}
                </p>

                <!-- žanru saraksts -->
                <div
                    v-if="book.genres && book.genres.length"
                    class="mt-2"
                >

                  <!-- viens žanrs -->
                  <span
                      v-for="genre in book.genres"
                      :key="genre.id"
                      class="inline-block bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded-full mr-1 mb-1"
                  >
                    {{ genre.name }}
                  </span>
                </div>

                <!-- grāmatas apraksts -->
                <p class="text-sm mt-2 text-gray-700">
                    {{ book.description }}
                </p>

                <!-- poga grāmatas apskatei -->
                <button
                    class="mt-4 bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600"
                    @click="inspectBook(book.id)"
                >
                    Inspect
                </button>
            </div>
        </div>

        <!-- teksts, ja grāmatas vēl nav ielādētas -->
        <p
            v-else
            class="text-gray-500 text-center"
        >
            Loading books...
        </p>
    </div>
</template>

<script>

// axios bibliotēka pieprasījumiem uz backend
import axios from 'axios';

export default {

    // komponentes dati
    data() {
        return {

            // grāmatu saraksts
            books: [],
        };
    },

    // izsaucas pēc komponentes ielādes
    mounted() {

        // iegūst visas grāmatas no backend
        axios.get('/get/all/books')

            .then(response => {

                // saglabā saņemtās grāmatas
                this.books = response.data;
            })

            .catch(error => {

                // izvada kļūdu konsolē
                console.error('Error fetching books:', error);
            });
    },

    methods: {

        // atver konkrētās grāmatas lapu
        inspectBook(id) {

            // pāriet uz grāmatas detaļu lapu
            this.$inertia.visit(`/book/${id}`);
        }
    }
};
</script>

<style>

/* pamata stils visai lapai */
body {

    /* fonta stils */
    font-family: Arial, sans-serif;

    /* fona krāsa */
    background-color: #f9f9f9;
}
</style>