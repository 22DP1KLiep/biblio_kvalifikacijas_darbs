<script setup>
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { Link } from '@inertiajs/vue3'
</script>

<template>
    <!-- Administratora paneļa izkārtojums -->
    <AdminLayout>

        <!-- Galvenais saturs -->
        <div class="container mx-auto p-6">

            <!-- Lapas virsraksts -->
            <h1 class="text-2xl font-bold text-[#213555] mb-6">
                Administrātora panelis
            </h1>

            <!-- Administratora funkciju kartītes -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">

                <!-- Lietotāju pārvaldības sadaļa -->
                <div class="bg-white rounded-xl shadow p-6">

                    <h2 class="text-lg font-semibold text-[#213555] mb-2">
                        Lietotāji
                    </h2>

                    <p class="text-sm text-gray-600 mb-4">
                        Pārvaldīt lietotāju kontus un lomas
                    </p>

                    <a
                        href="/admin/users"
                        class="inline-block bg-[#213555] hover:bg-[#3E5879] text-white px-4 py-2 rounded-lg text-sm transition"
                    >
                        Iet uz lietotājiem
                    </a>

                </div>

                <!-- Ziņojumu pārvaldības sadaļa -->
                <div class="bg-white rounded-xl shadow p-6">

                    <h2 class="text-lg font-semibold text-[#213555] mb-2">
                        Ziņojumi
                    </h2>

                    <p class="text-sm text-gray-600 mb-4">
                        Pārskatīt un moderēt ziņojumus
                    </p>

                    <a
                        href="/admin/reports"
                        class="inline-block bg-[#213555] hover:bg-[#3E5879] text-white px-4 py-2 rounded-lg text-sm transition"
                    >
                        Iet uz ziņojumiem
                    </a>

                </div>

            </div>

        </div>

    </AdminLayout>
</template>