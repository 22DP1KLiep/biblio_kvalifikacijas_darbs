<template>
    <div v-if="$page.props.auth.user && $page.props.auth.user.role === 'user'">
        <p>Welcome back, {{ $page.props.auth.user.name }}!</p>
    </div>
</template>
