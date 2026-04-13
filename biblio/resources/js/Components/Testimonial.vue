<template>
    <section class="bg-gray-100 py-12">
        <div class="max-w-6xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-10">What Readers Say</h2>

            <div class="grid gap-8 grid-cols-1 md:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="(testimonial, index) in testimonials"
                    :key="index"
                    class="bg-white p-6 rounded-lg shadow hover:shadow-lg transition duration-300"
                >
                    <div class="mb-4">
                        <svg class="w-8 h-8 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 15l-5.878 3.09L5.5 12.18 1 8.91l6.09-.88L10 2.5l2.91 5.53 6.09.88-4.5 3.27 1.378 5.91z"/>
                        </svg>
                    </div>
                    <p class="text-gray-700 mb-4 italic">"{{ testimonial.quote }}"</p>
                    <div class="text-sm font-semibold text-gray-900">{{ testimonial.name }}</div>
                    <div class="text-sm text-gray-500">{{ testimonial.role }}</div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    data() {
        return {
            testimonials: [
                {
                    name: "Anna L.",
                    role: "Bookworm & Blogger",
                    quote: "Absolutely love the selection here! I always find something new and inspiring.",
                },
                {
                    name: "Jānis P.",
                    role: "High School Teacher",
                    quote: "This site makes finding great literature easy — my students and I use it often.",
                },
                {
                    name: "Elīna K.",
                    role: "Freelance Writer",
                    quote: "Beautifully designed and rich with content. It's my go-to book discovery platform.",
                },
            ],
        };
    },
};
</script>

<style scoped>
/* Add a subtle animation on hover */
.shadow:hover {
    transform: translateY(-2px);
}
</style>
