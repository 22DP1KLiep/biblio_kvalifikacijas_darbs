<template>
    <div v-if="!$page.props.auth.user" class="flex items-center justify-center h-screen bg-gray-100">
        <div class="bg-white shadow-lg rounded-lg p-8 text-center">
            <h2 class="text-2xl font-semibold mb-4 text-gray-700">Jūs esat viesis</h2>
            <p class="mb-6 text-gray-500">Lūdzu, autorizējieties, lai turpinātu.</p>
            <button @click="redirectToLogin" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">
                Uz autorizāciju
            </button>
        </div>
    </div>
</template>

<script setup>
import { onMounted } from 'vue';
import { usePage } from '@inertiajs/vue3';

const page = usePage();

const redirectToLogin = () => {
    window.location.href = '/auth';
};

onMounted(() => {
    if (!page.props.auth.user) {
        setTimeout(() => {
            redirectToLogin();
        }, 3000); // automātiska pāradresācija pēc 3 sekundēm
    }
});
</script>
