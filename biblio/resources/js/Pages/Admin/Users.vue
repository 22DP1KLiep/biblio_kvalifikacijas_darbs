<script setup>
import { ref, computed } from 'vue'
import { usePage, router } from '@inertiajs/vue3'
import AdminLayout from '@/Layouts/AdminLayout.vue'

// ===== PAGE PROPS =====
const page = usePage()

const users = computed(() => {
    return Array.isArray(page.props.users) ? page.props.users : []
})

// ===== SEARCH (ja vēlāk gribi filtrēt) =====
const search = ref('')

const filteredUsers = computed(() => {
    if (!search.value) return users.value

    return users.value.filter(user =>
        user.email.toLowerCase().includes(search.value.toLowerCase())
    )
})

// ===== RESTRICT MODAL STATE =====
const showRestrictModal = ref(false)
const selectedUser = ref(null)
const restrictDays = ref(7)
const restrictReason = ref('')

// ===== OPEN / CLOSE MODAL =====
const openRestrictModal = (user) => {
    selectedUser.value = user
    restrictDays.value = 7
    restrictReason.value = ''
    showRestrictModal.value = true
}

const closeRestrictModal = () => {
    showRestrictModal.value = false
    selectedUser.value = null
}

// ===== CONFIRM RESTRICTION =====
const confirmRestriction = () => {
    if (!selectedUser.value) return

    router.patch(
        `/admin/users/${selectedUser.value.id}/restrict`,
        {
            days: restrictDays.value,
            reason: restrictReason.value,
        },
        {
            preserveScroll: true,
            onSuccess: () => {
                closeRestrictModal()
                router.reload({ only: ['users'] })
            },
        }
    )
}

// ===== REMOVE RESTRICTION =====
const removeRestriction = () => {
    if (!selectedUser.value) return

    if (!confirm(`Remove restriction for ${selectedUser.value.email}?`)) return

    router.patch(
        `/admin/users/${selectedUser.value.id}/unrestrict`,
        {},
        {
            preserveScroll: true,
            onSuccess: () => {
                closeRestrictModal()
                router.reload({ only: ['users'] })
            },
        }
    )
}
</script>

<template>
    <AdminLayout>
        <div class="min-h-screen bg-[#f0f4f8]">
            <div class="container mx-auto p-6">
                <h1 class="text-2xl font-bold mb-6 text-[#213555]">
                    Users
                </h1>

                <!-- SEARCH -->
                <input
                    v-model="search"
                    type="text"
                    placeholder="Search by email..."
                    class="mb-4 w-full max-w-sm border rounded px-3 py-2"
                />

                <!-- TABLE -->
                <div class="bg-white rounded-lg shadow overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-100 text-left">
                            <tr>
                                <th class="p-3">Email</th>
                                <th class="p-3">Role</th>
                                <th class="p-3">Status</th>
                                <th class="p-3 text-right">Restrict</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="user in filteredUsers"
                                :key="user.id"
                                class="border-t"
                            >
                                <td class="p-3">{{ user.email }}</td>
                                <td class="p-3">{{ user.role }}</td>
                                <td class="p-3">
                                    <span
                                        v-if="user.restricted_until"
                                        class="text-orange-600 font-medium"
                                    >
                                        Restricted
                                    </span>
                                    <span
                                        v-else
                                        class="text-green-600 font-medium"
                                    >
                                        Active
                                    </span>
                                </td>
                                <td class="p-3 text-right">
                                    <button
                                        class="text-xs text-orange-600 hover:underline"
                                        @click="openRestrictModal(user)"
                                    >
                                        Manage
                                    </button>
                                </td>
                            </tr>

                            <tr v-if="!filteredUsers.length">
                                <td colspan="4" class="p-6 text-center text-gray-500">
                                    No users found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- RESTRICT MODAL -->
        <div
            v-if="showRestrictModal"
            class="fixed inset-0 bg-black/40 flex items-center justify-center z-50"
        >
            <div class="bg-white p-6 rounded-lg shadow-lg w-full max-w-sm">
                <h2 class="text-lg font-bold mb-2">
                    Restrict user
                </h2>

                <p class="text-sm mb-2">
                    User: <strong>{{ selectedUser?.email }}</strong>
                </p>

                <p
                    v-if="selectedUser?.restricted_until"
                    class="mb-2 text-sm text-orange-700"
                >
                    ⚠️ User is currently restricted
                </p>

                <p
                    v-if="selectedUser?.restriction_reason"
                    class="mb-4 text-sm text-gray-600"
                >
                    <strong>Reason:</strong> {{ selectedUser.restriction_reason }}
                </p>

                <!-- FORM (TIKAI JA NAV RESTRICTED) -->
                <div v-if="!selectedUser?.restricted_until">
                    <div class="mb-4">
                        <label class="block text-sm mb-1">Days</label>
                        <select
                            v-model="restrictDays"
                            class="w-full border rounded px-3 py-2"
                        >
                            <option :value="1">1 day</option>
                            <option :value="3">3 days</option>
                            <option :value="7">7 days</option>
                            <option :value="30">30 days</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm mb-1">Reason</label>
                        <textarea
                            v-model="restrictReason"
                            class="w-full border rounded px-3 py-2"
                            rows="3"
                        ></textarea>
                    </div>
                </div>

                <!-- ACTIONS -->
                <div class="flex justify-between items-center mt-4">
                    <button
                        v-if="selectedUser?.restricted_until"
                        class="px-4 py-2 text-sm rounded bg-red-600 text-white hover:bg-red-700"
                        @click="removeRestriction"
                    >
                        Remove restriction
                    </button>

                    <div class="flex gap-2 ml-auto">
                        <button
                            class="px-3 py-2 border rounded"
                            @click="closeRestrictModal"
                        >
                            Cancel
                        </button>

                        <button
                            v-if="!selectedUser?.restricted_until"
                            class="px-3 py-2 bg-orange-600 text-white rounded hover:bg-orange-700"
                            @click="confirmRestriction"
                        >
                            Confirm restriction
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>
