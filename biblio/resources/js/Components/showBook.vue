<template>
    <div class="py-12 bg-[#F5EFE7]">
        <h2 class="text-3xl font-bold text-center text-[#213555] mb-10">
            TOP 10 grāmatas
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-6xl mx-auto px-[6px]">

        <div
                v-for="(book, index) in books.slice(0, 10)"
                :key="book.id"
                @click="inspectBook(book.id)"
                class="relative w-full max-w-[400px] rounded-lg overflow-hidden shadow-lg cursor-pointer transition transform hover:scale-105 hover:shadow-2xl mx-auto"
            >
                <!-- Karodziņš ar top vietu -->
                <div class="absolute top-0 left-0 z-10">
                    <div class="relative bg-[#E55050] text-white text-xs font-bold px-3 py-1">
                        {{ index + 1 }}.
                        <div class="absolute left-0 bottom-[-6px] w-0 h-0 border-l-[10px] border-r-[10px] border-t-[10px] border-l-transparent border-r-transparent border-t-[#E55050]"></div>
                    </div>
                </div>

                <!-- Fona attēls -->
                <img :src="book.image" alt="book cover" class="w-full h-[420px] object-cover" />

                <!-- Gradienta teksts -->
                <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent text-white px-6 py-6 pointer-events-none">
                    <h3
                        class="text-xl font-bold leading-snug mb-1"
                        style="text-shadow: 2px 2px 6px rgba(0,0,0,0.85);"
                    >
                        {{ book.title }}
                    </h3>

                    <p class="text-sm text-gray-300 italic mb-1" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.85);">
                        by {{ book.author }}
                    </p>

                    <!-- Žanri zem autora -->
                    <div v-if="book.genres && book.genres.length" class="flex flex-wrap gap-1 mb-1">
                        <span
                            v-for="genre in book.genres"
                            :key="genre.id"
                            class="inline-block bg-white/30 text-white text-[10px] px-2 py-[2px] rounded-full border border-white/50"
                            style="backdrop-filter: blur(4px); text-shadow: none;"
                        >
                            {{ genre.name }}
                        </span>
                    </div>

                    <p
                        v-if="book.description"
                        class="text-xs text-gray-400 mt-1"
                        style="text-shadow: 1px 1px 2px rgba(0,0,0,0.85);"
                    >
                        {{ book.description.slice(0, 60) }}...
                    </p>
                </div>
            </div>
        </div>
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
        axios.get('/books')
            .then(response => {
                this.books = response.data;
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
