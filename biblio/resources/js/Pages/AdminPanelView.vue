<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <AdminLayout>
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-bold text-[#213555] mb-6">
                📊 Admin Dashboard
            </h1>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <!-- Users -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-lg font-semibold text-[#213555] mb-2">
                        👤 Users
                    </h2>
                    <p class="text-sm text-gray-600 mb-4">
                        Manage user accounts and roles
                    </p>
                    <a
                        href="/admin/users"
                        class="inline-block bg-[#213555] hover:bg-[#3E5879] text-white px-4 py-2 rounded-lg text-sm transition"
                    >
                        Go to Users
                    </a>
                </div>

                <!-- Reports -->
                <div class="bg-white rounded-xl shadow p-6">
                    <h2 class="text-lg font-semibold text-[#213555] mb-2">
                        🚩 Reports
                    </h2>
                    <p class="text-sm text-gray-600 mb-4">
                        Review and moderate reported comments
                    </p>
                    <a
                        href="/admin/reports"
                        class="inline-block bg-[#213555] hover:bg-[#3E5879] text-white px-4 py-2 rounded-lg text-sm transition"
                    >
                        Go to Reports
                    </a>
                </div>

                <!-- Books (future) -->
                <div class="bg-white rounded-xl shadow p-6 opacity-60">
                    <h2 class="text-lg font-semibold text-[#213555] mb-2">
                        📚 Books
                    </h2>
                    <p class="text-sm text-gray-600">
                        Book management (coming soon)
                    </p>
                </div>

                <!-- Comments (future) -->
                <div class="bg-white rounded-xl shadow p-6 opacity-60">
                    <h2 class="text-lg font-semibold text-[#213555] mb-2">
                        💬 Comments
                    </h2>
                    <p class="text-sm text-gray-600">
                        Comment moderation (coming soon)
                    </p>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
