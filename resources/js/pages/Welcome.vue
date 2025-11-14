<script setup lang="ts">
import { dashboard, login, register } from '@/routes';
import { Head, Link } from '@inertiajs/vue3';

withDefaults(
    defineProps<{
        canRegister: boolean;
    }>(),
    {
        canRegister: true,
    },
);

const featureHighlights = [
    {
        icon: '⚡',
        title: 'Real-time balances',
        description:
            'Broadcast-powered updates ensure every wallet reflects new transfers the moment they are processed.',
    },
    {
        icon: '🛡️',
        title: 'Safe & secure',
        description:
            'Row-level locking, audited transfers, and granular permissions keep funds protected at scale.',
    },
    {
        icon: '🤝',
        title: 'Team-ready workflows',
        description:
            'Share wallets with teammates, track responsibilities, and invite stakeholders with a single link.',
    },
];

const onboardingSteps = [
    {
        step: '01',
        title: 'Create your wallet',
        description:
            'Register an account in minutes and seed balances with test data to explore Mini Wallet safely.',
    },
    {
        step: '02',
        title: 'Invite collaborators',
        description:
            'Add teammates, set spending limits, and keep everyone aligned with shared transaction feeds.',
    },
    {
        step: '03',
        title: 'Automate transfers',
        description:
            'Schedule payouts, track commissions, and let the platform broadcast updates to every device instantly.',
    },
];
</script>

<template>
    <Head title="Welcome">
        <link rel="preconnect" href="https://rsms.me/" />
        <link rel="stylesheet" href="https://rsms.me/inter/inter.css" />
    </Head>
    <div class="relative min-h-screen overflow-hidden bg-gradient-to-br from-slate-950 via-slate-900 to-slate-950">
        <!-- Animated background gradients -->
        <div class="pointer-events-none absolute inset-0">
            <div class="absolute -left-1/4 top-0 h-[500px] w-[500px] rounded-full bg-purple-500/20 blur-[120px] animate-pulse"></div>
            <div class="absolute -right-1/4 bottom-0 h-[500px] w-[500px] rounded-full bg-blue-500/20 blur-[120px] animate-pulse" style="animation-delay: 1s"></div>
        </div>

        <div class="relative flex min-h-screen flex-col">
            <!-- Header -->
            <header class="px-6 py-6 lg:px-8">
                <nav class="mx-auto flex max-w-7xl items-center justify-between">
                    <div class="flex items-center gap-2">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-gradient-to-br from-purple-500 to-blue-500">
                            <span class="text-xl">💰</span>
                        </div>
                        <span class="text-xl font-bold text-white">Mini Wallet</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="dashboard()"
                            class="rounded-lg bg-white/10 px-5 py-2.5 text-sm font-medium text-white backdrop-blur-sm transition hover:bg-white/20"
                        >
                            Dashboard
                        </Link>
                        <template v-else>
                            <Link
                                :href="login()"
                                class="rounded-lg px-5 py-2.5 text-sm font-medium text-white/90 transition hover:text-white"
                            >
                                Log in
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="register()"
                                class="rounded-lg bg-gradient-to-r from-purple-500 to-blue-500 px-5 py-2.5 text-sm font-medium text-white shadow-lg shadow-purple-500/30 transition hover:shadow-purple-500/50"
                            >
                                Get Started
                            </Link>
                        </template>
                    </div>
                </nav>
            </header>

            <!-- Hero Section -->
            <main class="flex flex-1 flex-col items-center justify-center px-6 py-16 lg:px-8">
                <div class="mx-auto max-w-7xl text-center">
                    <div class="mb-6 inline-flex items-center gap-2 rounded-full bg-white/5 px-4 py-2 text-sm text-white/80 backdrop-blur-sm">
                        <span class="relative flex h-2 w-2">
                            <span class="absolute inline-flex h-full w-full animate-ping rounded-full bg-green-400 opacity-75"></span>
                            <span class="relative inline-flex h-2 w-2 rounded-full bg-green-500"></span>
                        </span>
                        Live transfers • Real-time updates
                    </div>

                    <h1 class="mb-6 bg-gradient-to-r from-white via-white to-white/60 bg-clip-text text-5xl font-bold text-transparent lg:text-7xl">
                        Modern wallet<br />for modern teams
                    </h1>
                    
                    <p class="mx-auto mb-10 max-w-2xl text-lg text-white/60 lg:text-xl">
                        Send funds instantly, track every transaction, and collaborate seamlessly. Built for teams that move fast.
                    </p>

                    <div class="flex flex-col items-center justify-center gap-4 sm:flex-row">
                        <Link
                            v-if="!$page.props.auth.user && canRegister"
                            :href="register()"
                            class="group relative inline-flex items-center gap-2 overflow-hidden rounded-lg bg-gradient-to-r from-purple-500 to-blue-500 px-8 py-4 text-base font-medium text-white shadow-2xl shadow-purple-500/30 transition hover:shadow-purple-500/50"
                        >
                            <span class="relative z-10">Start for free</span>
                            <span class="relative z-10 transition-transform group-hover:translate-x-1">→</span>
                        </Link>
                        <Link
                            :href="$page.props.auth.user ? dashboard() : login()"
                            class="inline-flex items-center gap-2 rounded-lg border border-white/10 bg-white/5 px-8 py-4 text-base font-medium text-white backdrop-blur-sm transition hover:bg-white/10"
                        >
                            {{ $page.props.auth.user ? 'Go to Dashboard' : 'View Demo' }}
                        </Link>
                    </div>

                    <!-- Stats -->
                    <div class="mt-20 grid grid-cols-3 gap-8 border-t border-white/10 pt-12">
                        <div>
                            <div class="text-3xl font-bold text-white lg:text-4xl">$2.4M+</div>
                            <div class="mt-1 text-sm text-white/60">Transferred</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-white lg:text-4xl">10K+</div>
                            <div class="mt-1 text-sm text-white/60">Transactions</div>
                        </div>
                        <div>
                            <div class="text-3xl font-bold text-white lg:text-4xl">&lt;100ms</div>
                            <div class="mt-1 text-sm text-white/60">Avg Response</div>
                        </div>
                    </div>
                </div>
            </main>

          

            <!-- CTA Section -->
            <section class="px-6 py-20 lg:px-8">
                <div class="mx-auto max-w-4xl rounded-3xl border border-white/10 bg-gradient-to-br from-purple-500/10 to-blue-500/10 p-12 text-center backdrop-blur-sm">
                    <h2 class="mb-4 text-3xl font-bold text-white lg:text-4xl">Ready to get started?</h2>
                    <p class="mb-8 text-lg text-white/60">Join teams already using Mini Wallet to streamline their finances</p>
                    <Link
                        v-if="!$page.props.auth.user && canRegister"
                        :href="register()"
                        class="inline-flex items-center gap-2 rounded-lg bg-white px-8 py-4 text-base font-medium text-slate-900 shadow-lg transition hover:bg-white/90"
                    >
                        Create your account
                        <span>→</span>
                    </Link>
                </div>
            </section>

            <!-- Footer -->
            <footer class="border-t border-white/10 px-6 py-8 lg:px-8">
                <div class="mx-auto max-w-7xl text-center text-sm text-white/40">
                    <p>&copy; 20255 Mini Wallet by Austin Opia. Built with Laravel & Vue.</p>
                </div>
            </footer>
        </div>
    </div>
</template>