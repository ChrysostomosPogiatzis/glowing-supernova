<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    device: Object,
    qrCode: String,
    qrData: String,
});
</script>

<template>
    <Head :title="'Pairing ' + device.name" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Pair Device: {{ device.name }}
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 p-8 shadow sm:rounded-lg text-center">
                    <h3 class="text-xl font-medium text-gray-900 dark:text-gray-100 mb-6">
                        Scan with your Mobile App
                    </h3>
                    
                    <div class="flex justify-center mb-8 p-4 bg-white rounded-lg">
                        <div v-html="qrCode"></div>
                    </div>
                    
                    <div class="bg-gray-100 dark:bg-gray-700 p-4 rounded-md mb-6">
                        <p class="text-sm font-semibold text-gray-600 dark:text-gray-300">Pairing Code</p>
                        <p class="text-2xl font-mono tracking-widest text-indigo-600 dark:text-indigo-400">
                            {{ device.pairing_code }}
                        </p>
                        <p class="text-xs text-gray-400 mt-2">
                            This code can only be used once. If it's empty, the device is already paired.
                        </p>
                    </div>

                    <div class="mt-8">
                        <Link :href="route('devices.index')" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                            &larr; Back to Device Manager
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
