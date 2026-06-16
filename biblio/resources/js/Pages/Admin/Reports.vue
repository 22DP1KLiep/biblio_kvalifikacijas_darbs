<script setup>
import { router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'
import { computed, ref } from 'vue'
import axios from 'axios'

const props = defineProps({
    reports: Array
})


/* Statistika */
const pendingCount = computed(() =>
    props.reports.filter(r => r.status === 'pending').length
)

const resolvedCount = computed(() =>
    props.reports.filter(r => r.status === 'resolved').length
)


/* Modāla dati */
const showModal = ref(false)
const selectedReport = ref(null)

const restrictionDays = ref(7)
const restrictionReason = ref('')

const showDeleteModal = ref(false)


/* Atver pārvaldīšanas logu */
const openManageModal = (report) => {
    selectedReport.value = report
    showModal.value = true
}


/* Aizver logu */
const closeModal = () => {
    showModal.value = false
    selectedReport.value = null
    restrictionReason.value = ''
    restrictionDays.value = 7
}


/* Atrisina ziņojumu */
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


/* Atver dzēšanas logu */
const deleteComment = () => {
    showDeleteModal.value = true
}


/* Dzēš komentāru */
const confirmDeleteComment = () => {
    if (!selectedReport.value) return

    axios.delete(`/comments/${selectedReport.value.comment.id}`)
        .then(() => {
            showDeleteModal.value = false
            closeModal()

            router.reload({ only: ['reports'] })
        })
        .catch(error => {
            console.error(error)
        })
}


/* Ierobežo lietotāju */
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

                // automātiski atrisina ziņojumu
                router.patch(`/admin/reports/${selectedReport.value.id}/resolve`)

                closeModal()
                router.reload({ only: ['reports'] })
            }
        }
    )
}


/* Pāriet uz komentāru */
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

            <!-- Virsraksts -->
            <h1 class="text-2xl font-bold mb-6 text-[#213555]">
                Ziņojumi
            </h1>


            <!-- Statistika -->
            <div class="grid grid-cols-3 gap-4 mb-8">

                <div class="bg-white p-4 rounded-xl shadow">
                    <p class="text-sm text-gray-500">
                        Gaida
                    </p>

                    <p class="text-2xl font-bold text-red-600">
                        {{ pendingCount }}
                    </p>
                </div>

                <div class="bg-white p-4 rounded-xl shadow">
                    <p class="text-sm text-gray-500">
                        Atrisināts
                    </p>

                    <p class="text-2xl font-bold text-green-600">
                        {{ resolvedCount }}
                    </p>
                </div>

                <div class="bg-white p-4 rounded-xl shadow">
                    <p class="text-sm text-gray-500">
                        Kopā
                    </p>

                    <p class="text-2xl font-bold text-[#213555]">
                        {{ reports.length }}
                    </p>
                </div>

            </div>


            <!-- Ziņojumu tabula -->
            <div
                v-if="reports.length"
                class="bg-white rounded-xl shadow overflow-hidden"
            >

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

                        <tr
                            v-for="r in reports"
                            :key="r.id"
                            class="border-b hover:bg-gray-50 transition"
                        >

                            <!-- Grāmata -->
                            <td class="p-4">
                                <button
                                    @click="goToComment(r)"
                                    class="text-[#213555] font-medium hover:underline"
                                >
                                    {{ r.comment?.book?.title }}
                                </button>
                            </td>


                            <!-- Komentārs -->
                            <td class="max-w-xs truncate">
                                {{ r.comment?.comment }}
                            </td>


                            <!-- Ziņotājs -->
                            <td>
                                <div class="flex flex-col">

                                    <span class="font-medium text-[#213555]">
                                        {{ r.user?.username }}
                                    </span>

                                    <span class="text-xs text-gray-400">
                                        ziņoja par
                                        {{ r.comment?.user?.username }}
                                    </span>

                                </div>
                            </td>


                            <!-- Statuss -->
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


                            <!-- Darbības -->
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


            <!-- Ja nav ziņojumu -->
            <div
                v-else
                class="text-gray-500"
            >
                Vēl nav ziņojumu.
            </div>

        </div>


        <!-- Pārvaldīšanas logs -->
        <div
            v-if="showModal"
            class="fixed inset-0 bg-black bg-opacity-40 flex items-start justify-center z-50 overflow-y-auto pt-24 pb-10"
        >

            <div class="bg-white w-[500px] max-h-[90vh] overflow-y-auto rounded-2xl shadow-2xl p-6">


                <!-- Virsraksts -->
                <h2 class="text-xl font-bold mb-4 text-[#213555]">
                    Pārvaldīt pārskatu
                </h2>


                <!-- Informācija par ziņojumu -->
                <div class="mb-4">

                    <p class="text-sm text-gray-500">
                        Grāmata
                    </p>

                    <p class="font-semibold text-[#213555]">
                        {{ selectedReport?.comment?.book?.title }}
                    </p>

                </div>


                <!-- Pilns komentārs -->
                <div class="mb-4">

                    <p class="text-sm text-gray-500">
                        Pilns komentārs
                    </p>

                    <div class="bg-gray-50 p-3 rounded-lg text-sm">
                        {{ selectedReport?.comment?.comment }}
                    </div>

                </div>


                <!-- Iemesls -->
                <div class="mb-4">

                    <p class="text-sm text-gray-500">
                        Ziņojuma iemesls
                    </p>

                    <div class="bg-orange-50 border border-orange-200 p-3 rounded-xl text-sm text-orange-800">
                        {{ selectedReport?.reason || 'Nav norādīts iemesls' }}
                    </div>

                </div>


                <!-- Lietotāji -->
                <div class="mb-6 text-sm text-gray-600 space-y-1">

                    <div>
                        Ziņotājs:
                        <strong class="text-[#213555]">
                            {{ selectedReport?.user?.username }}
                        </strong>
                    </div>

                    <div>
                        Komentāra autors:
                        <strong class="text-red-600">
                            {{ selectedReport?.comment?.user?.username }}
                        </strong>
                    </div>

                </div>


                <!-- Konteksts -->
                <button
                    @click="goToComment()"
                    class="mb-6 text-blue-600 hover:underline text-sm"
                >
                    Skatīt kontekstu
                </button>


                <!-- Pogas -->
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


                <!-- Ierobežošana -->
                <h3 class="font-semibold mb-2 text-[#213555]">
                    Ierobežot lietotāju
                </h3>


                <!-- Dienas -->
                <label class="text-sm text-gray-600">
                    Dienas
                </label>

                <select
                    v-model="restrictionDays"
                    class="w-full border rounded-lg p-2 mb-3"
                >
                    <option :value="1">1 diena</option>
                    <option :value="7">7 dienas</option>
                    <option :value="30">30 dienas</option>
                    <option :value="999">Pastāvīgi</option>
                </select>


                <!-- Iemesls -->
                <label class="text-sm text-gray-600">
                    Iemesls
                </label>

                <textarea
                    v-model="restrictionReason"
                    class="w-full border rounded-lg p-2 mb-4"
                    rows="3"
                    placeholder="Ierobežojuma iemesls..."
                ></textarea>


                <!-- Pogas -->
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


        <!-- Dzēšanas logs -->
        <div
            v-if="showDeleteModal"
            class="fixed inset-0 bg-black/40 backdrop-blur-sm flex items-center justify-center z-[60]"
        >

            <div class="bg-white w-[400px] rounded-2xl shadow-2xl p-6">

                <div class="flex items-center gap-3 mb-4">

                    <span class="material-icons text-red-500 text-3xl">
                        warning
                    </span>

                    <h2 class="text-xl font-bold text-[#213555]">
                        Dzēst komentāru?
                    </h2>

                </div>

                <p class="text-gray-600 text-sm mb-6">
                    Šo darbību nevar atsaukt.
                </p>

                <div class="flex justify-end gap-3">

                    <button
                        @click="showDeleteModal = false"
                        class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition"
                    >
                        Atcelt
                    </button>

                    <button
                        @click="confirmDeleteComment"
                        class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition"
                    >
                        Dzēst
                    </button>

                </div>

            </div>

        </div>

    </AdminLayout>
</template>