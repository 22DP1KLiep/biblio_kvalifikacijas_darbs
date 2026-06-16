<template>

    <!-- parāda saturu tikai tad, ja lietotājs nav autorizējies -->
    <div
        v-if="!$page.props.auth.user"
        class="flex items-center justify-center h-screen bg-gray-100"
    >

        <!-- galvenais paziņojuma logs -->
        <div class="bg-white shadow-lg rounded-lg p-8 text-center">

            <!-- virsraksts -->
            <h2 class="text-2xl font-semibold mb-4 text-gray-700">
                Jūs esat viesis
            </h2>

            <!-- paskaidrojuma teksts -->
            <p class="mb-6 text-gray-500">
                Lūdzu, autorizējieties, lai turpinātu.
            </p>

            <!-- poga pārejai uz autorizācijas lapu -->
            <button
                @click="redirectToLogin"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg"
            >
                Uz autorizāciju
            </button>
        </div>
    </div>
</template>

<script setup>

// importē onMounted funkciju no vue
import { onMounted } from 'vue';

// importē usePage no inertia
import { usePage } from '@inertiajs/vue3';

// iegūst pašreizējās lapas datus
const page = usePage();

// funkcija pārejai uz autorizācijas lapu
const redirectToLogin = () => {

    // pāradresē lietotāju uz auth lapu
    window.location.href = '/auth';
};

// izsaucas pēc komponentes ielādes
onMounted(() => {

    // pārbauda vai lietotājs nav autorizējies
    if (!page.props.auth.user) {

        // pēc 3 sekundēm automātiski pāradresē uz auth lapu
        setTimeout(() => {

            redirectToLogin();

        }, 3000);
    }
});
</script>