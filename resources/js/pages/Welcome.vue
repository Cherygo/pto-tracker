<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { ArrowRight, CalendarDays, ShieldCheck, Zap } from '@lucide/vue';
import { Button } from '@/components/ui/button';

defineProps<{
    auth: {
        user: any;
    };
}>();
</script>

<template>
    <Head title="Welcome to PTO Tracker" />

    <div class="min-h-screen bg-background text-foreground font-sans antialiased selection:bg-primary/30 selection:text-primary">
        <!-- Header -->
        <header class="sticky top-0 z-50 w-full border-b border-border/40 bg-background/95 backdrop-blur supports-[backdrop-filter]:bg-background/60">
            <div class="container mx-auto flex h-16 max-w-6xl items-center justify-between px-4 sm:px-6 lg:px-8">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/20 text-primary">
                        <CalendarDays class="h-5 w-5" />
                    </div>
                    <span class="text-xl font-bold tracking-tight">PTO Tracker</span>
                </div>

                <nav class="flex items-center gap-4">
                    <template v-if="auth.user">
                        <Link href="/dashboard" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">
                            Dashboard
                        </Link>
                    </template>
                    <template v-else>
                        <Link href="/login" class="text-sm font-medium text-muted-foreground transition-colors hover:text-foreground">
                            Log in
                        </Link>
                        <Button as-child variant="default" size="sm" class="h-9 px-4 shadow-md transition-transform hover:scale-105">
                            <Link href="/register">Get Started</Link>
                        </Button>
                    </template>
                </nav>
            </div>
        </header>

        <!-- Hero Section -->
        <main class="flex flex-col">
            <section class="relative overflow-hidden pt-24 pb-32 sm:pt-32 sm:pb-40">
                <!-- Decorative background elements -->
                <div class="absolute inset-x-0 -top-40 -z-10 transform-gpu overflow-hidden blur-3xl sm:-top-80" aria-hidden="true">
                    <div class="relative left-[calc(50%-11rem)] aspect-[1155/678] w-[36.125rem] -translate-x-1/2 rotate-[30deg] bg-gradient-to-tr from-primary to-secondary opacity-20 sm:left-[calc(50%-30rem)] sm:w-[72.1875rem]" style="clip-path: polygon(74.1% 44.1%, 100% 61.6%, 97.5% 26.9%, 85.5% 0.1%, 80.7% 2%, 72.5% 32.5%, 60.2% 62.4%, 52.4% 68.1%, 47.5% 58.3%, 45.2% 34.5%, 27.5% 76.7%, 0.1% 64.9%, 17.9% 100%, 27.6% 76.8%, 76.1% 97.7%, 74.1% 44.1%)"></div>
                </div>

                <div class="container mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 text-center">
                    <div class="inline-flex items-center rounded-full border border-primary/20 bg-primary/10 px-3 py-1 text-sm font-medium text-primary mb-8 shadow-sm">
                        <span class="flex h-2 w-2 rounded-full bg-primary mr-2 animate-pulse"></span>
                        The Modern Way to Manage Time Off
                    </div>

                    <h1 class="text-4xl font-extrabold tracking-tight sm:text-6xl mb-6 bg-gradient-to-br from-foreground to-foreground/70 bg-clip-text text-transparent">
                        Reclaim Your Time Off
                    </h1>

                    <p class="mx-auto max-w-2xl text-lg text-muted-foreground sm:text-xl mb-10 leading-relaxed">
                        The effortless way for modern teams to track, request, and manage Paid Time Off. Built for scale, designed for simplicity, and deeply integrated with your multi-tenant workflows.
                    </p>

                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <Button as-child size="lg" class="w-full sm:w-auto gap-2 shadow-lg shadow-primary/25 transition-all hover:scale-105">
                            <Link href="/register">
                                Start your free trial
                                <ArrowRight class="h-4 w-4" />
                            </Link>
                        </Button>
                        <Button as-child variant="outline" size="lg" class="w-full sm:w-auto group">
                            <Link href="/login">
                                Sign In
                            </Link>
                        </Button>
                    </div>
                </div>
            </section>

            <!-- Features Section -->
            <section class="py-20 bg-muted/30 border-y border-border/50">
                <div class="container mx-auto max-w-6xl px-4 sm:px-6 lg:px-8">
                    <div class="mb-16 text-center">
                        <h2 class="text-3xl font-bold tracking-tight sm:text-4xl">Everything you need for PTO</h2>
                        <p class="mt-4 text-muted-foreground text-lg max-w-2xl mx-auto">Our platform provides strict tenant isolation, rapid requests, and beautiful reporting.</p>
                    </div>

                    <div class="grid gap-8 sm:grid-cols-2 lg:grid-cols-3">
                        <!-- Feature 1 -->
                        <div class="group relative rounded-2xl border border-border/50 bg-card p-8 shadow-sm transition-all duration-300 hover:shadow-primary/10 hover:shadow-xl hover:-translate-y-1 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                            <div class="relative z-10">
                                <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary transition-colors group-hover:bg-primary group-hover:text-primary-foreground">
                                    <Zap class="h-6 w-6" />
                                </div>
                                <h3 class="mb-3 text-xl font-semibold">Effortless Requests</h3>
                                <p class="text-muted-foreground leading-relaxed">
                                    Submit, review, and approve time-off requests in seconds. Employees see their balances instantly without asking HR.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 2 -->
                        <div class="group relative rounded-2xl border border-border/50 bg-card p-8 shadow-sm transition-all duration-300 hover:shadow-secondary/10 hover:shadow-xl hover:-translate-y-1 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-secondary/5 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                            <div class="relative z-10">
                                <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-secondary/10 text-secondary transition-colors group-hover:bg-secondary group-hover:text-secondary-foreground">
                                    <ShieldCheck class="h-6 w-6" />
                                </div>
                                <h3 class="mb-3 text-xl font-semibold">Strict Multi-Tenancy</h3>
                                <p class="text-muted-foreground leading-relaxed">
                                    Your data is your data. Enterprise-grade tenant isolation ensures company workspaces are completely segregated and secure.
                                </p>
                            </div>
                        </div>

                        <!-- Feature 3 -->
                        <div class="group relative rounded-2xl border border-border/50 bg-card p-8 shadow-sm transition-all duration-300 hover:shadow-primary/10 hover:shadow-xl hover:-translate-y-1 overflow-hidden">
                            <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-transparent opacity-0 transition-opacity duration-300 group-hover:opacity-100"></div>
                            <div class="relative z-10">
                                <div class="mb-5 inline-flex h-12 w-12 items-center justify-center rounded-xl bg-primary/10 text-primary transition-colors group-hover:bg-primary group-hover:text-primary-foreground">
                                    <CalendarDays class="h-6 w-6" />
                                </div>
                                <h3 class="mb-3 text-xl font-semibold">Shared Calendars</h3>
                                <p class="text-muted-foreground leading-relaxed">
                                    See exactly who is out of the office at a glance. Prevent overlapping vacations and keep your team running smoothly.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </main>

        <!-- Footer -->
        <footer class="border-t border-border/40 py-10 bg-background">
            <div class="container mx-auto max-w-6xl px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-2 text-muted-foreground">
                    <CalendarDays class="h-4 w-4" />
                    <span class="text-sm">© 2026 PTO Tracker. All rights reserved.</span>
                </div>
                <div class="flex gap-6 text-sm text-muted-foreground">
                    <a href="#" class="hover:text-foreground transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-foreground transition-colors">Terms of Service</a>
                </div>
            </div>
        </footer>
    </div>
</template>
