<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    devices: Array,
});

const form = useForm({
    name: '',
});

const submit = () => {
    form.post(route('devices.store'), {
        onSuccess: () => {
            form.reset();
        },
    });
};

const deleteDevice = (id) => {
    if (confirm('Are you sure you want to remove this device?')) {
        form.delete(route('devices.destroy', id));
    }
};
</script>

<template>
    <Head title="Device Manager" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Device Manager
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Add Device -->
                    <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            Add New Device
                        </h3>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Device Name (e.g., My Phone)</label>
                                <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>
                            <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                Register Device
                            </button>
                        </form>
                    </div>

                    <!-- Device List -->
                    <div class="col-span-2 bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                        <div v-if="devices.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
                            No devices registered yet.
                        </div>
                        <ul v-else class="divide-y divide-gray-200 dark:divide-gray-700">
                            <li v-for="device in devices" :key="device.id" class="py-4 flex items-center justify-between">
                                <div>
                                    <div class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ device.name }}</div>
                                    <div class="text-sm text-gray-500 dark:text-gray-400">
                                        Last sync: {{ device.last_sync_at ? new Date(device.last_sync_at).toLocaleString() : 'Never' }}
                                    </div>
                                </div>
                                <div class="flex gap-4">
                                    <Link :href="route('devices.show', device.id)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300">
                                        Pair (QR Code)
                                    </Link>
                                    <button @click="deleteDevice(device.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">
                                        Remove
                                    </button>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
