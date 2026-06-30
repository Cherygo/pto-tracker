<script setup lang="ts">
import { useForm, Head } from '@inertiajs/vue3';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';
// FIX: Imported Wayfinder auto-generated route functions
// BEFORE: import { login } from '@/routes';
import { login, register } from '@/routes';

defineProps<{
    passwordRules?: string;
}>();

defineOptions({
    layout: {
        title: 'Create an account',
        description: 'Enter your details below to create your account',
    },
});

const form = useForm({
    name: '',
    email: '',
    tenant_name: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    // FIX: Using Wayfinder auto-generated route function instead of hardcoded string
    // BEFORE: form.post('/register', {
    form.post(register.url(), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <!-- FIX: Vue components must have a single root element. Wrapped Head and form in a div. -->
    <!-- BEFORE: <Head title="Register" /> followed by <form @submit... -->
    <div>
        <Head title="Register" />

        <form @submit.prevent="submit" class="flex flex-col gap-6">
        <div class="grid gap-6">
            <div class="grid gap-2">
                <Label for="name">Name</Label>
                <Input
                    id="name"
                    type="text"
                    required
                    autofocus
                    :tabindex="1"
                    autocomplete="name"
                    name="name"
                    placeholder="Full name"
                    v-model="form.name"
                />
                <InputError :message="form.errors.name" />
            </div>

            <div class="grid gap-2">
                <Label for="tenant_name">Company Name</Label>
                <Input
                    id="tenant_name"
                    type="text"
                    required
                    :tabindex="2"
                    name="tenant_name"
                    placeholder="Acme Corp"
                    v-model="form.tenant_name"
                />
                <InputError :message="form.errors.tenant_name" />
            </div>

            <div class="grid gap-2">
                <Label for="email">Email address</Label>
                <Input
                    id="email"
                    type="email"
                    required
                    :tabindex="3"
                    autocomplete="email"
                    name="email"
                    placeholder="email@example.com"
                    v-model="form.email"
                />
                <InputError :message="form.errors.email" />
            </div>

            <div class="grid gap-2">
                <Label for="password">Password</Label>
                <PasswordInput
                    id="password"
                    required
                    :tabindex="4"
                    autocomplete="new-password"
                    name="password"
                    placeholder="Password"
                    :passwordrules="passwordRules"
                    v-model="form.password"
                />
                <InputError :message="form.errors.password" />
            </div>

            <div class="grid gap-2">
                <Label for="password_confirmation">Confirm password</Label>
                <PasswordInput
                    id="password_confirmation"
                    required
                    :tabindex="5"
                    autocomplete="new-password"
                    name="password_confirmation"
                    placeholder="Confirm password"
                    :passwordrules="passwordRules"
                    v-model="form.password_confirmation"
                />
                <InputError :message="form.errors.password_confirmation" />
            </div>

            <Button
                type="submit"
                class="mt-2 w-full"
                tabindex="6"
                :disabled="form.processing"
                data-test="register-user-button"
            >
                <Spinner v-if="form.processing" />
                Create account
            </Button>
        </div>

        <div class="text-center text-sm text-muted-foreground">
            Already have an account?
            <!-- FIX: Using Wayfinder route function -->
            <!-- BEFORE: <TextLink href="/login" class="underline underline-offset-4" :tabindex="7">Log in</TextLink> -->
            <TextLink
                :href="login.url()"
                class="underline underline-offset-4"
                :tabindex="7"
                >Log in</TextLink
            >
        </div>
    </form>
    </div>
</template>
