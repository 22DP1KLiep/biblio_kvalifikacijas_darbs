<template>
    <div v-if="$page.props.auth.user && $page.props.auth.user.role === 'admin'">
        <button @click="goToAdmin">Go to Admin Panel</button>
    </div>
</template>

<script setup>
import { router } from '@inertiajs/vue3';

const goToAdmin = () => {
    router.visit('/admin');
};
</script>
