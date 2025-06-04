<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="min-h-screen flex items-center justify-center bg-white">
            <div class="w-full h-screen bg-white shadow-xl rounded-2xl overflow-hidden flex flex-col md:flex-row">

                <!-- Left: Form -->
                <div class="w-full md:w-1/2 p-8 md:p-16">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-2 flex justify-center items-center gap-2">
                            MyMARZ
                        </h2>
                        <p class="text-gray-900">
                            Create your account <br />
                            Sign up
                        </p>
                    </div>

                    <form @submit.prevent="submit" class="space-y-4">
                        <div>
                            <InputLabel for="name" value="Name" class="text-sm font-medium text-gray-700" />
                            <TextInput
                                id="name"
                                type="text"
                                class="mt-1 block w-full rounded-md border border-gray-300 p-2"
                                placeholder="John Doe"
                                v-model="form.name"
                                required
                                autofocus
                                autocomplete="name"
                            />
                            <InputError class="mt-1 text-red-500 text-sm" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="email" value="Email" class="text-sm font-medium text-gray-700" />
                            <TextInput
                                id="email"
                                type="email"
                                class="mt-1 block w-full rounded-md border border-gray-300 p-2"
                                placeholder="example@email.com"
                                v-model="form.email"
                                required
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
                                autocomplete="new-password"
                            />
                            <InputError class="mt-1 text-red-500 text-sm" :message="form.errors.password" />
                        </div>

                        <div>
                            <InputLabel for="password_confirmation" value="Confirm Password" class="text-sm font-medium text-gray-700" />
                            <TextInput
                                id="password_confirmation"
                                type="password"
                                class="mt-1 block w-full rounded-md border border-gray-300 p-2"
                                placeholder="Repeat password"
                                v-model="form.password_confirmation"
                                required
                                autocomplete="new-password"
                            />
                            <InputError class="mt-1 text-red-500 text-sm" :message="form.errors.password_confirmation" />
                        </div>

                        <PrimaryButton
                            class="w-full bg-purple-900 hover:bg-purple-800 text-white py-2 rounded-md flex justify-center items-center"
                            :class="{ 'opacity-25': form.processing }"
                            :disabled="form.processing"
                        >
                            Register
                        </PrimaryButton>
                    </form>

                    <p class="mt-6 text-sm text-center text-gray-600">
                        Already have an account?
                        <Link href="/login" class="text-purple-900 font-medium hover:underline">Sign in</Link>
                    </p>

                    <p class="mt-6 text-xs text-center text-gray-400">
                        © 2025 ALL RIGHTS RESERVED
                    </p>
                </div>

                <!-- Right: Image -->
                <div class="hidden md:block md:w-1/2">
                    <img
                        src="/assets/login-3.jpg"
                        alt="Register Visual"
                        class="h-full w-full object-cover"
                    />
                </div>
            </div>
        </div>
    </GuestLayout>
</template>



<!-- <script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <form @submit.prevent="submit">
            <div>
                <InputLabel for="name" value="Name" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div class="mt-4">
                <InputLabel for="email" value="Email" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
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
                    autocomplete="new-password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="mt-4">
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="mt-4 flex items-center justify-end">
                <Link
                    :href="route('login')"
                    class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    Already registered?
                </Link>

                <PrimaryButton
                    class="ms-4"
                    :class="{ 'opacity-25': form.processing }"
                    :disabled="form.processing"
                >
                    Register
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template> -->
