<template>
    <div class="min-h-screen flex items-center justify-center px-4" style="background-color: #F5EFE7;">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg overflow-hidden">
        <div class="bg-gradient-to-r from-[#213555] to-[#3E5879] p-6 text-center text-white rounded-t-xl">
            <h1 class="text-2xl font-bold mb-2">Laipni lūdzam!</h1>
            <a href="/" class="text-[#8CCDEB] hover:underline font-medium text-sm">
                ← Atpakaļ uz sākumlapu
            </a>
        </div>


        <!-- Tabs -->
            <div class="flex justify-center mt-4">
                <button
                    class="w-1/2 py-2 text-sm font-medium border-b-2"
                    :class="activeTab === 'login' ? 'border-[#213555] text-[#213555]' : 'border-transparent text-gray-400'"
                    @click="activeTab = 'login'"
                >
                    Pieslēgties
                </button>
                <button
                    class="w-1/2 py-2 text-sm font-medium border-b-2"
                    :class="activeTab === 'register' ? 'border-[#213555] text-[#213555]' : 'border-transparent text-gray-400'"
                    @click="activeTab = 'register'"
                >
                    Reģistrēties
                </button>
            </div>

            <!-- Login Form -->
            <form v-if="activeTab === 'login'" @submit.prevent="submitLogin" class="p-6 space-y-4">
                <div>
                    <label class="label">E-pasts</label>
                    <input v-model="loginForm.email" type="text" class="input" />
                    <p v-if="errors.email" class="error">{{ errors.email }}</p>
                </div>
                <div>
                    <label class="label">Parole</label>
                    <input v-model="loginForm.password" type="password" required class="input" />
                    <p v-if="errors.password" class="error">{{ errors.password }}</p>
                </div>
                <button type="submit" class="btn-primary">Pieslēgties</button>
            </form>

            <!-- Register Form -->
            <form v-else @submit.prevent="submitRegister" class="p-6 space-y-4">
                <div>
                    <label class="label">Lietotājvārds</label>
                    <input v-model="registerForm.username" type="text" required class="input" />
                    <p v-if="errors.username" class="error">{{ errors.username }}</p>
                </div>
                <div>
                    <label class="label">E-pasts</label>
                    <input v-model="registerForm.email" type="text" class="input" />
                    <p v-if="errors.email" class="error">{{ errors.email }}</p>
                </div>
                <div>
                    <label class="label">Parole</label>
                    <input v-model="registerForm.password" type="password" required class="input" />
                    <p v-if="errors.password" class="error">{{ errors.password }}</p>
                </div>
                <div>
                    <label class="label">Apstiprināt paroli</label>
                    <input v-model="registerForm.password_confirmation" type="password" required class="input" />
                    <p v-if="errors.password_confirmation" class="error">{{ errors.password_confirmation }}</p>
                </div>
                <button type="submit" class="btn-primary">Reģistrēties</button>
            </form>

        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { route } from 'ziggy-js'

const activeTab = ref('login')
const errors = ref({})

// Login forma
const loginForm = useForm({
    email: '',
    password: '',
})

// Reģistrācijas forma
const registerForm = useForm({
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
})

// Validācijas funkcijas
const isEmailValid = (email) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)
const isPasswordStrong = (password) => /[A-Za-z]/.test(password) && /\d/.test(password)
const isUsernameValid = (username) =>
    /^[A-Za-z0-9]+$/.test(username) && !/^\d+$/.test(username) && username.length >= 4

// Login submit
const submitLogin = () => {
    errors.value = {}

    if (!loginForm.email || !isEmailValid(loginForm.email)) {
        errors.value.email = 'Lūdzu ievadi derīgu e-pasta adresi.'
    }

    if (!loginForm.password) {
        errors.value.password = 'Lūdzu ievadi paroli.'
    }

    if (Object.keys(errors.value).length === 0) {
        loginForm.post(route('login'), {
            onFinish: () => loginForm.reset('password'),
        })
    }
}

// Register submit
const submitRegister = () => {
    errors.value = {}

    if (!registerForm.username) {
        errors.value.username = 'Lūdzu ievadi lietotājvārdu.'
    } else if (!isUsernameValid(registerForm.username)) {
        errors.value.username = 'Lietotājvārdam jābūt vismaz 4 simboli, tikai burti un cipari (ne tikai cipari).'
    }

    if (!registerForm.email) {
        errors.value.email = 'Lūdzu ievadi e-pastu.'
    } else if (!isEmailValid(registerForm.email)) {
        errors.value.email = 'E-pasta adrese nav derīga.'
    }


    if (!registerForm.password) {
        errors.value.password = 'Lūdzu ievadi paroli.'
    } else if (registerForm.password.length < 8 || !isPasswordStrong(registerForm.password)) {
        errors.value.password = 'Parolei jābūt vismaz 8 simboli, ar burtiem un cipariem.'
    }

    if (registerForm.password !== registerForm.password_confirmation) {
        errors.value.password_confirmation = 'Paroles nesakrīt.'
    }

    if (Object.keys(errors.value).length === 0) {
        registerForm.post(route('register'), {
            onFinish: () => registerForm.reset('password', 'password_confirmation'),
        })
    }
}
</script>


<style scoped>
.input {
    width: 100%;
    padding: 0.6rem 1rem;
    border: 1px solid #d1d5db;
    border-radius: 0.375rem;
    outline: none;
    transition: 0.2s;
}
.input:focus {
    border-color: #3e5879;
    box-shadow: 0 0 0 1px #3e5879;
}

.label {
    font-size: 0.875rem;
    margin-bottom: 0.25rem;
    color: #374151;
}

.btn-primary {
    width: 100%;
    background: linear-gradient(to right, #213555, #3e5879);
    color: white;
    font-weight: 600;
    padding: 0.6rem;
    border-radius: 0.375rem;
    transition: background 0.3s;
}
.btn-primary:hover {
    opacity: 0.9;
}

.error {
    font-size: 0.75rem;
    color: red;
    margin-top: 0.25rem;
}

</style>
