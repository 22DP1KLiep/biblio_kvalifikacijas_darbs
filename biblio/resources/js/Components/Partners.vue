<template>
    <section class="bg-white py-12">
        <div class="max-w-6xl mx-auto px-4">
            <h2 class="text-3xl font-bold text-center mb-10">Our Trusted Partners</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-8">
                <div
                    v-for="(partner, index) in partners"
                    :key="index"
                    class="bg-gray-50 border border-gray-200 p-6 rounded-lg shadow hover:shadow-md transition duration-300 flex flex-col items-center text-center"
                >
                    <img
                        :src="partner.logo"
                        :alt="partner.name"
                        class="w-24 h-24 object-contain mb-4"
                    />
                    <h3 class="text-lg font-semibold mb-2">{{ partner.name }}</h3>
                    <p class="text-sm text-gray-600">{{ partner.description }}</p>
                    <a
                        v-if="partner.link"
                        :href="partner.link"
                        target="_blank"
                        class="mt-3 inline-block text-blue-500 hover:underline text-sm"
                    >
                        Visit Website
                    </a>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
export default {
    data() {
        return {
            partners: [
                {
                    name: "Latvian Book Club",
                    logo: "/images/partner1.png",
                    description: "A community of passionate readers and authors.",
                    link: "https://example.com/latvian-book-club"
                },
                {
                    name: "ReadMore Publishing",
                    logo: "/images/partner2.png",
                    description: "Helping authors publish and share their stories.",
                    link: "https://example.com/readmore"
                },
                {
                    name: "Paper & Ink",
                    logo: "/imgages/partner3.png",
                    description: "Premium stationery and bookbinding.",
                    link: "https://example.com/paper-ink"
                },
                {
                    name: "eBookify",
                    logo: "/imgages/partner4.png",
                    description: "Convert and read books digitally, anywhere.",
                    link: "https://example.com/ebookify"
                },
            ]
        };
    }
};
</script>

<style scoped>
/* Optional hover animation */
.shadow:hover {
    transform: translateY(-2px);
}
</style>
