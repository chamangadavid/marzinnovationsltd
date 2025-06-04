
 <script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="min-h-screen flex items-center justify-center bg-white">
            <!-- <div class="w-full bg-white shadow-xl rounded-2xl overflow-hidden flex flex-col md:flex-row"> -->
            <div class="w-full h-screen bg-white shadow-xl rounded-2xl overflow-hidden flex flex-col md:flex-row">
 
                <!-- Left: Form -->
                <div class="w-full md:w-1/2 p-8 md:p-16">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-2 flex justify-center items-center gap-2"> MyMARZ </h2>
                        <p class="text-gray-900">
                          Already Registered <br />
                          Sign in
                        </p>
                      </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <InputLabel for="email" value="Email" class="text-sm font-medium text-gray-700" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full rounded-md border border-gray-300 p-2"
                                placeholder="Example@email.com"
                                v-model="form.email"
                                required
                                autofocus
                                autocomplete="username"
                            />
                            <InputError class="mt-1 text-red-500 text-sm" :message="form.errors.email" />
                        </div>

                        <div>
                            <InputLabel for="password" value="Password" class="text-sm font-medium text-gray-700" />
                            <TextInput
                                id="password"
                                type="password"
                                class="mt-1 block w-full rounded-md border border-gray-300 p-2"
                                placeholder="At least 8 characters"
                                v-model="form.password"
                                required
                                autocomplete="current-password"
                            />
                            <InputError class="mt-1 text-red-500 text-sm" :message="form.errors.password" />
                        </div>

                        <div class="flex justify-between items-center text-sm">
                            <label class="flex items-center gap-2">
                                <Checkbox name="remember" v-model:checked="form.remember" />
                                Remember me
                            </label>
                            <Link
                                v-if="canResetPassword"
                                :href="route('password.request')"
                                class="text-purple-900 font-semibold hover:underline"
                            >
                                Forgot Password?
                            </Link>
                        </div>
                        <PrimaryButton
                            class="w-full bg-purple-900 hover:bg-purple-800 text-white py-2 rounded-md flex justify-center items-center"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Sign in
                        </PrimaryButton>
                    </form>

                    <p class="mt-6 text-sm text-center text-gray-600">
                        Don't you have an account? 
                        <Link href="/register" class="text-purple-900 font-medium hover:underline">Sign up</Link>
                    </p>

                    <p class="mt-6 text-xs text-center text-gray-400">
                        © 2025 ALL RIGHTS RESERVED
                    </p>
                </div>

                <!-- Right: Image -->
                <div class="hidden md:block md:w-1/2">
                    <img
                        src="/assets/login-3.jpg"
                        alt="Login Visual"
                        class="h-full w-full object-cover"
                    />
                </div>
            </div>
        </div>
    </GuestLayout>
</template>



<!-- 
<template>
    <GuestLayout>
        <Head title="Log in" />

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600">
            {{ status }}
        </div>

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="mt-4">
                <InputLabel for="password" value="Password" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4 block">
                <label class="flex items-center">
                    <Checkbox name="remember" v-model:checked="form.remember" />
                    <span class="ms-2 text-sm text-gray-600"
                        >Remember me</span
                    >
                </label>
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Forgot your password?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Log in
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>  -->
