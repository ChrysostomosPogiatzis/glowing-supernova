<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';

const props = defineProps({
    customers: Array,
    companies: Array,
    outlets: Array,
    filters: Object,
});

const form = useForm({
    name: '',
    surname: '',
    mobile_number: '',
    email: '',
    company_id: '',
    outlet_id: '',
});

const search = ref(props.filters.search || '');

watch(search, (value) => {
    router.get(route('customers.index'), { search: value }, {
        preserveState: true,
        replace: true,
    });
});

const isEditing = ref(false);
const currentCustomerId = ref(null);

// Filter outlets based on selected company
const filteredOutlets = computed(() => {
    if (!form.company_id) return [];
    return props.outlets.filter(outlet => outlet.company_id == form.company_id);
});

const submit = () => {
    if (isEditing.value) {
        form.put(route('customers.update', currentCustomerId.value), {
            onSuccess: () => resetForm(),
        });
    } else {
        form.post(route('customers.store'), {
            onSuccess: () => resetForm(),
        });
    }
};

const editCustomer = (customer) => {
    form.name = customer.name;
    form.surname = customer.surname;
    form.mobile_number = customer.mobile_number;
    form.email = customer.email;
    form.company_id = customer.company_id || '';
    form.outlet_id = customer.outlet_id || '';
    currentCustomerId.value = customer.id;
    isEditing.value = true;
};

const resetForm = () => {
    form.reset();
    isEditing.value = false;
    currentCustomerId.value = null;
};

const deleteCustomer = (id) => {
    if (confirm('Are you sure you want to delete this customer?')) {
        form.delete(route('customers.destroy', id));
    }
};
</script>

<template>
    <Head title="Customers" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                Customers
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Form Section -->
                    <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4">
                            {{ isEditing ? 'Edit Customer' : 'Add New Customer' }}
                        </h3>
                        <form @submit.prevent="submit" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Name</label>
                                <input v-model="form.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Surname</label>
                                <input v-model="form.surname" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Mobile Number</label>
                                <input v-model="form.mobile_number" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white" required>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
                                <input v-model="form.email" type="email" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                            </div>
                            
                            <hr class="border-gray-200 dark:border-gray-700 my-4" />
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Company (Optional)</label>
                                <select v-model="form.company_id" @change="form.outlet_id = ''" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">No Company</option>
                                    <option v-for="company in companies" :key="company.id" :value="company.id">{{ company.name }}</option>
                                </select>
                            </div>

                            <div v-if="form.company_id">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Outlet (Optional)</label>
                                <select v-model="form.outlet_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                                    <option value="">No Outlet</option>
                                    <option v-for="outlet in filteredOutlets" :key="outlet.id" :value="outlet.id">{{ outlet.name }}</option>
                                </select>
                            </div>

                            <div class="flex gap-2">
                                <button type="submit" class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    {{ isEditing ? 'Update' : 'Save' }}
                                </button>
                                <button @click="resetForm" v-if="isEditing" type="button" class="inline-flex justify-center rounded-md border border-gray-300 bg-white py-2 px-4 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600">
                                    Cancel
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- List Section -->
                    <div class="col-span-3 bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                        <div class="mb-4">
                            <input v-model="search" type="text" placeholder="Search name, surname, email, mobile, company..." class="block w-full md:w-1/2 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                        </div>
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Full Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company/Outlet</th>
                                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                <tr v-for="customer in customers" :key="customer.id">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ customer.name }} {{ customer.surname }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <div>{{ customer.mobile_number }}</div>
                                        <div class="text-xs">{{ customer.email }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        <div class="font-medium text-gray-900 dark:text-gray-200">{{ customer.company?.name || '---' }}</div>
                                        <div class="text-xs">{{ customer.outlet?.name }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button @click="editCustomer(customer)" class="text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-300 mr-3">Edit</button>
                                        <button @click="deleteCustomer(customer.id)" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
