<script setup>
import { Link, usePage } from '@inertiajs/vue3'
import Navbar from '@/Components/Navbar.vue'

const page = usePage()
</script>

<template>
    <div class="min-h-screen bg-[#f0f4f8]">
        <!-- Top navbar (paliek tavs) -->
        <Navbar />

        <div class="flex max-w-[1600px] mx-auto px-4 py-6 gap-6">

            <!-- SIDEBAR -->
            <aside
                class="w-64 bg-[#f5f7fa] border-r border-gray-200
                        p-6 flex-shrink-0"
                >
                    <h2 class="text-lg font-bold text-[#213555] mb-6">
                        Administrātors
                    </h2>

                    <nav class="space-y-1">
                        <Link
                            href="/admin"
                            class="block px-4 py-2 rounded-md text-sm font-medium
                                text-[#213555] hover:bg-[#e6ecf3]"
                        >
                            Informācijas panelis
                        </Link>

                        <Link
                            href="/admin/users"
                            class="block px-4 py-2 rounded-md text-sm font-medium
                                text-[#213555] hover:bg-[#e6ecf3]"
                        >
                            Lietotāji
                        </Link>

                        <Link
                            href="/admin/reports"
                            class="block px-4 py-2 rounded-md text-sm font-medium text-[#213555] hover:bg-[#e6ecf3]"
                            >
                            Ziņojumi
                        </Link>

                        <Link
                            href="/"
                            class="block px-4 py-2 mt-6 text-sm text-gray-500
                                hover:bg-gray-100 rounded-md"
                        >
                            Atpakaļ uz mājaslapu
                        </Link>
                    </nav>
            </aside>


            <!-- CONTENT -->
            <main class="flex-1">
                <slot />
            </main>

        </div>
    </div>
</template>
