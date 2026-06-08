<template>
    <!-- Poga tiek attēlota tikai administratoriem -->
    <div v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'">
        <button @click="goToAdmin">
            Iet uz administratora paneli
        </button>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

// Atver administratora paneli
const goToAdmin = () => {
    router.visit('/admin');
};
</script>