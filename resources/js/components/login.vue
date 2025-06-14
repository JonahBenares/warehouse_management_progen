<script setup>
import { reactive, ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import { ExclamationCircleIcon } from '@heroicons/vue/24/solid';

const router = useRouter();

let form = reactive({
    email: '',
    password: '',
});

let error = ref('');
let loading = ref(false);

const login = async () => {
    loading.value = true;
    error.value = '';

    try {
        let response = await axios.post('/api/login_process', form);
        if (response.data.success) {
            localStorage.setItem('token', response.data.data.token);
            router.push('/dashboard');
        } else {
            error.value = response.data.message;
        }
    } catch (err) {
        error.value = "An error occurred. Please try again.";
    } finally {
        loading.value = false;
    }
};

const company = ref({});

onMounted(async () => {
    const response = await fetch('/api/constants');
    const data = await response.json();
    company.value = data.company;
});
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-gray-100 px-4">
        <div class="w-full max-w-md bg-white rounded-lg shadow-lg p-6">
            <div class="text-center">
                <img :src="company.logo" class="d-inline-block align-top max-w-60 mt-3 max-h-32" alt="">
                <h2 class="mt-2 text-lg font-semibold text-gray-700 uppercase mb-0 leading-tight">{{ company.name }}</h2>
                <p class="text-sm text-gray-500">Warehouse Management System</p>
            </div>
            <hr class="my-4">
            <div v-if="error" class="flex items-center p-3 mb-4 text-sm text-red-700 bg-red-100 rounded-lg">
                <ExclamationCircleIcon class="w-5 h-5 mr-2" />
                <span>{{ error }}</span>
            </div>
            <form @submit.prevent="login">
                <div class="mb-4">
                    <label class="block text-gray-600 text-sm mb-1">Email Address</label>
                    <input type="email" v-model="form.email" class="w-full px-3 py-2 border rounded-lg " placeholder="email@example.com">
                </div>
                <div class="mb-4">
                    <label class="block text-gray-600 text-sm mb-1">Password</label>
                    <input type="password" v-model="form.password" class="w-full px-3 py-2 border rounded-lg " placeholder="••••••••">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700 transition duration-300 mt-2 mt-3" :disabled="loading">
                    <span v-if="loading">Logging in...</span>
                    <span v-else>Login</span>
                </button>
            </form>
        </div>
    </div>
</template>
