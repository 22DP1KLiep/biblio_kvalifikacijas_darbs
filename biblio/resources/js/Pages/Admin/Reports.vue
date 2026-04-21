<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { computed, ref } from 'vue'

const props = defineProps({
    reports: Array
})

/* ===========================
   STAT COUNTERS
=========================== */

const pendingCount = computed(() =>
    props.reports.filter(r => r.status === 'pending').length
)

const resolvedCount = computed(() =>
    props.reports.filter(r => r.status === 'resolved').length
)

/* ===========================
   MODAL STATE
=========================== */

const showModal = ref(false)
const selectedReport = ref(null)

const restrictionDays = ref(7)
const restrictionReason = ref('')

const openManageModal = (report) => {
    selectedReport.value = report
    showModal.value = true
}

const closeModal = () => {
    showModal.value = false
    selectedReport.value = null
    restrictionReason.value = ''
    restrictionDays.value = 7
}

/* ===========================
   ACTIONS
=========================== */

const resolveReport = () => {
    if (!selectedReport.value) return

    router.patch(
        `/admin/reports/${selectedReport.value.id}/resolve`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                closeModal()
                router.reload({ only: ['reports'] })
            }
        }
    )
}

const deleteComment = () => {
    if (!selectedReport.value) return

    const confirmDelete = confirm("Vai tiešām dzēst šo komentāru?")

    if (!confirmDelete) return

    router.delete(
        `/comments/${selectedReport.value.comment.id}`,
        {
            preserveScroll: true,
            onSuccess: () => {
                closeModal()


                router.reload({ only: ['reports'] })
            }
        }
    )
}

const restrictUser = () => {
    if (!selectedReport.value) return

    router.patch(
        `/admin/users/${selectedReport.value.comment.user.id}/restrict`,
        {
            days: restrictionDays.value,
            reason: restrictionReason.value
        },
        {
            preserveScroll: true,
            onSuccess: () => {

                
                router.patch(`/admin/reports/${selectedReport.value.id}/resolve`)

                closeModal()
                router.reload({ only: ['reports'] })
            }
        }
    )
}

/* ===========================
   NAVIGATE TO COMMENT
=========================== */

const goToComment = (report = selectedReport.value) => {
    if (!report) return

    const bookId = report.comment.book_id
    const commentId = report.comment.id

    router.visit(`/book/${bookId}#comment-${commentId}`)
}
</script>

<template>
    <AdminLayout>
        <div class="p-6">

            <!-- TITLE -->
            <h1 class="text-2xl font-bold mb-6 text-[#213555]">Ziņojumi</h1>

            <!-- STAT CARDS -->
            <div class="grid grid-cols-3 gap-4 mb-8">
                <div class="bg-white p-4 rounded-xl shadow">
                    <p class="text-sm text-gray-500">Gaida</p>
                    <p class="text-2xl font-bold text-red-600">
                        {{ pendingCount }}
                    </p>
                </div>

                <div class="bg-white p-4 rounded-xl shadow">
                    <p class="text-sm text-gray-500">Atrisināts</p>
                    <p class="text-2xl font-bold text-green-600">
                        {{ resolvedCount }}
                    </p>
                </div>

                <div class="bg-white p-4 rounded-xl shadow">
                    <p class="text-sm text-gray-500">Kopā</p>
                    <p class="text-2xl font-bold text-[#213555]">
                        {{ reports.length }}
                    </p>
                </div>
            </div>

            <!-- TABLE -->
            <div v-if="reports.length" class="bg-white rounded-xl shadow overflow-hidden">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-b text-left text-gray-600">
                        <tr>
                            <th class="p-4">Grāmata</th>
                            <th>Komentārs</th>
                            <th>Ziņotājs</th>
                            <th>Statuss</th>
                            <th class="text-right pr-6">Darbības</th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr v-for="r in reports"
                            :key="r.id"
                            class="border-b hover:bg-gray-50 transition">

                            <!-- BOOK TITLE -->
                            <td class="p-4">
                                <button
                                    @click="goToComment(r)"
                                    class="text-[#213555] font-medium hover:underline"
                                >
                                    {{ r.comment?.book?.title }}
                                </button>
                            </td>

                            <!-- COMMENT PREVIEW -->
                            <td class="max-w-xs truncate">
                                {{ r.comment?.comment }}
                            </td>

                            <td>
                                {{ r.comment?.user?.username }}
                            </td>

                            <td>
                                <span
                                    v-if="r.status === 'pending'"
                                    class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-600"
                                >
                                    Gaida
                                </span>

                                <span
                                    v-else
                                    class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-600"
                                >
                                    Atrisināts
                                </span>
                            </td>

                            <td class="text-right pr-6">
                                <button
                                    @click="openManageModal(r)"
                                    class="text-[#213555] hover:underline font-medium"
                                >
                                    Pārvaldīt
                                </button>
                            </td>

                        </tr>
                    </tbody>
                </table>
            </div>

            <div v-else class="text-gray-500">
                Vēl nav ziņojumu.
            </div>

        </div>

        <!-- ===========================
             MANAGE MODAL
        ============================ -->

        <div v-if="showModal"
             class="fixed inset-0 bg-black bg-opacity-40 flex items-center justify-center z-50">

            <div class="bg-white w-[500px] rounded-xl shadow-2xl p-6">

                <h2 class="text-xl font-bold mb-4 text-[#213555]">
                    Pārvaldīt pārskatu
                </h2>

                <!-- CONTEXT -->
                <div class="mb-4">
                    <p class="text-sm text-gray-500">Grāmata</p>
                    <p class="font-semibold text-[#213555]">
                        {{ selectedReport?.comment?.book?.title }}
                    </p>
                </div>

                <div class="mb-4">
                    <p class="text-sm text-gray-500">Pilns komentārs</p>
                    <div class="bg-gray-50 p-3 rounded-lg text-sm">
                        {{ selectedReport?.comment?.comment }}
                    </div>
                </div>

                <div class="mb-6 text-sm text-gray-600">
                    Paziņoja:
                    <strong>{{ selectedReport?.comment?.user?.username }}</strong>
                </div>

                <button
                    @click="goToComment()"
                    class="mb-6 text-blue-600 hover:underline text-sm"
                >
                    Skatīt kontekstu
                </button>

                <!-- ACTIONS -->
                <div class="space-y-3 mb-6">

                    <button
                        v-if="selectedReport?.status === 'pending'"
                        @click="resolveReport"
                        class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition"
                    >
                        Atrisināt ziņojumu
                    </button>

                    <button
                        @click="deleteComment"
                        class="w-full bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition"
                    >
                        Dzēst komentāru
                    </button>

                </div>

                <hr class="my-4">

                <!-- RESTRICT -->
                <h3 class="font-semibold mb-2 text-[#213555]">
                    Ierobežot lietotāju
                </h3>

                <label class="text-sm text-gray-600">Days</label>
                <select
                    v-model="restrictionDays"
                    class="w-full border rounded-lg p-2 mb-3"
                >
                    <option :value="1">1 diena</option>
                    <option :value="7">7 dienas</option>
                    <option :value="30">30 dienas</option>
                    <option :value="999">Pastāvīgi</option>
                </select>

                <label class="text-sm text-gray-600">Iemesls</label>
                <textarea
                    v-model="restrictionReason"
                    class="w-full border rounded-lg p-2 mb-4"
                    rows="3"
                    placeholder="Ierobežojuma iemesls..."
                ></textarea>

                <div class="flex justify-between">

                    <button
                        @click="closeModal"
                        class="px-4 py-2 bg-gray-200 rounded-lg"
                    >
                        Atcelt
                    </button>

                    <button
                        @click="restrictUser"
                        class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition"
                    >
                        Apstiprināt ierobežojumu
                    </button>

                </div>

            </div>
        </div>

    </AdminLayout>
</template>