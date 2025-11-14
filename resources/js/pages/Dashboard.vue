<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { dashboard } from '@/routes';
import type { BreadcrumbItem } from '@/types';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const breadcrumbs: BreadcrumbItem[] = [
    {
        title: 'Dashboard',
        href: dashboard().url,
    },
];

const page = usePage();
const userName = computed(() => page.props.auth?.user?.name ?? 'there');

const quickLinks = [
    {
        title: 'Send Money',
        description: 'Transfer funds instantly to another wallet user.',
        href: '/wallet#transfer',
        action: 'Open wallet',
    },
    {
        title: 'View History',
        description: 'Review incoming and outgoing transactions in one place.',
        href: '/wallet#transactions',
        action: 'See transactions',
    },
    {
        title: 'Invite a Friend',
        description: 'Share Mini Wallet with teammates to start collaborating.',
        href: '#',
        action: 'Copy invite link',
    },
];

const roadmap = [
    {
        headline: 'Scheduled transfers',
        copy: 'Plan recurring transfers so payroll and allowances run automatically.',
    },
    {
        headline: 'Multi-currency wallets',
        copy: 'Hold balances in USD, EUR, or NGN and convert in one tap.',
    },
    {
        headline: 'Spending insights',
        copy: 'Get visual analytics to track how money moves across your teams.',
    },
];
</script>

<template>
    <Head title="Dashboard" />

    <AppLayout :breadcrumbs="breadcrumbs">
        <div class="flex flex-1 flex-col gap-6 overflow-x-hidden p-6">
            <section
                class="grid gap-6 rounded-2xl border border-sidebar-border/70 bg-sidebar-card px-6 py-8 shadow-sm md:grid-cols-[1.3fr_1fr] dark:border-sidebar-border"
            >
                <div class="space-y-4">
                    <p class="text-sm font-medium text-muted">Welcome back</p>
                    <h1 class="text-3xl font-semibold text-sidebar-primary">
                        Hello, {{ userName }}
                    </h1>
                    <p class="max-w-xl text-sm text-muted">
                        Mini Wallet keeps your team’s transfers, balances, and approvals in sync. Jump back into your workflow or explore the product roadmap to see what’s shipping next.
                    </p>
                    <div class="flex flex-wrap gap-3 pt-2">
                        <Link
                            href="/wallet"
                            class="inline-flex items-center gap-2 rounded-lg bg-sidebar-primary px-4 py-2 text-sm font-medium text-white transition hover:opacity-90"
                        >
                            Manage Wallet
                        </Link>
                        <Link
                            href="#roadmap"
                            class="inline-flex items-center gap-2 rounded-lg border border-sidebar-border px-4 py-2 text-sm font-medium text-sidebar-primary transition hover:border-sidebar-primary"
                        >
                            View Roadmap
                        </Link>
                    </div>
                </div>
                <div class="grid gap-4">
                    <div
                        class="rounded-xl border border-sidebar-border/60 bg-sidebar px-5 py-4 text-sm shadow-sm dark:border-sidebar-border"
                    >
                        <p class="text-xs uppercase tracking-wide text-muted">Today’s snapshot</p>
                        <div class="mt-3 space-y-3">
                            <div class="flex items-baseline justify-between">
                                <span class="text-muted">Available balance</span>
                                <span class="text-xl font-semibold text-sidebar-primary">—</span>
                            </div>
                            <div class="flex items-baseline justify-between">
                                <span class="text-muted">Transfers processed</span>
                                <span class="text-sm text-sidebar-primary">Coming soon</span>
                            </div>
                        </div>
                    </div>
                    <div
                        class="rounded-xl border border-dashed border-sidebar-border/70 px-5 py-4 text-sm text-muted"
                    >
                        <p class="font-medium text-sidebar-primary">Need something custom?</p>
                        <p class="mt-1">Reach out to the team to request early access to upcoming automation features.</p>
                        <a
                            href="mailto:support@miniwalet.test"
                            class="mt-3 inline-block text-sm font-semibold text-sidebar-primary underline underline-offset-4"
                        >
                            support@miniwalet.test
                        </a>
                    </div>
                </div>
            </section>

            <section class="grid gap-4 md:grid-cols-3">
                <article
                    v-for="link in quickLinks"
                    :key="link.title"
                    class="flex h-full flex-col justify-between rounded-xl border border-sidebar-border/70 bg-sidebar-card p-5 text-sm shadow-sm transition hover:border-sidebar-primary/60 dark:border-sidebar-border"
                >
                    <div class="space-y-2">
                        <h2 class="text-lg font-semibold text-sidebar-primary">
                            {{ link.title }}
                        </h2>
                        <p class="text-muted">{{ link.description }}</p>
                    </div>
                    <Link
                        :href="link.href"
                        class="mt-4 inline-flex items-center gap-2 text-sm font-medium text-sidebar-primary"
                    >
                        {{ link.action }}
                        <span aria-hidden="true">→</span>
                    </Link>
                </article>
            </section>

            <section
                id="roadmap"
                class="rounded-2xl border border-sidebar-border/70 bg-sidebar-card p-6 shadow-sm dark:border-sidebar-border"
            >
                <header class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs uppercase tracking-wide text-muted">Product roadmap</p>
                        <h2 class="text-xl font-semibold text-sidebar-primary">
                            What we’re building next
                        </h2>
                    </div>
                    <a
                        href="mailto:product@miniwalet.test"
                        class="inline-flex items-center gap-2 rounded-lg border border-sidebar-border px-3 py-1.5 text-xs font-medium text-sidebar-primary transition hover:border-sidebar-primary"
                    >
                        Request a feature
                    </a>
                </header>
                <div class="mt-6 grid gap-4 md:grid-cols-3">
                    <article
                        v-for="item in roadmap"
                        :key="item.headline"
                        class="rounded-xl border border-dashed border-sidebar-border/70 bg-sidebar px-5 py-4 text-sm dark:border-sidebar-border"
                    >
                        <h3 class="text-base font-semibold text-sidebar-primary">
                            {{ item.headline }}
                        </h3>
                        <p class="mt-2 text-muted">{{ item.copy }}</p>
                    </article>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
