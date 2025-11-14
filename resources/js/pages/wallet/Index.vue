<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, usePage } from '@inertiajs/vue3';
import axios from 'axios';
import type Echo from 'laravel-echo';
import { computed, getCurrentInstance, onBeforeUnmount, onMounted, ref } from 'vue';
import type { AppPageProps } from '@/types';

type Transaction = {
    id: number;
    sender: { id: number; name?: string | null };
    receiver: { id: number; name?: string | null };
    amount: string;
    commission_fee: string;
    status: string;
    direction: 'incoming' | 'outgoing' | 'external';
    reference: string | null;
    created_at: string | null;
};

type PaginationLinks = {
    first?: string | null;
    last?: string | null;
    prev?: string | null;
    next?: string | null;
};

type Meta = {
    current_page: number;
    last_page: number;
    per_page: number;
    total: number;
    balance?: string;
};

type TransactionIndexResponse = {
    data: Transaction[];
    links: PaginationLinks;
    meta: Meta;
};

type EchoInstance = Echo<any>;

const page = usePage<AppPageProps>();
const currentUserId = computed(() => page.props.auth.user?.id ?? null);

const instance = getCurrentInstance();
const echo = instance?.appContext.config.globalProperties.$echo as EchoInstance | undefined;

const loading = ref(false);
const transactions = ref<Transaction[]>([]);
const paginationLinks = ref<PaginationLinks>({});
const paginationMeta = ref<Meta | null>(null);
const balance = ref('0.0000');
const errors = ref<Record<string, string[]>>({});
const form = ref({ receiver_id: '', amount: '' });
const successMessage = ref('');

const currencyFormatter = new Intl.NumberFormat('en-US', {
    style: 'currency',
    currency: 'USD',
    minimumFractionDigits: 2,
});

const canSubmit = computed(
    () => Boolean(form.value.receiver_id && form.value.amount && !loading.value),
);

const directionLabel = (transaction: Transaction) => {
    if (transaction.direction === 'incoming') {
        return 'Received';
    }

    if (transaction.direction === 'outgoing') {
        return 'Sent';
    }

    return 'External';
};

const fetchTransactions = async (url?: string | null) => {
    loading.value = true;
    errors.value = {};

    try {
        const endpoint = url ?? '/api/transactions';
        const { data } = await axios.get<TransactionIndexResponse>(endpoint);

        transactions.value = data.data;
        paginationLinks.value = data.links ?? {};
        paginationMeta.value = data.meta;

        if (data.meta.balance) {
            balance.value = data.meta.balance;
        }
    } finally {
        loading.value = false;
    }
};

const submitTransfer = async () => {
    loading.value = true;
    errors.value = {};
    successMessage.value = '';

    try {
        const { data } = await axios.post<TransactionIndexResponse>(
            '/api/transactions',
            {
                receiver_id: form.value.receiver_id,
                amount: form.value.amount,
            },
        );

        form.value = { receiver_id: '', amount: '' };
        if (data.meta.balance) {
            balance.value = data.meta.balance;
        }

        successMessage.value = 'Transfer completed successfully.';

        await fetchTransactions();
    } catch (error: any) {
        if (error.response?.status === 422) {
            errors.value =
                error.response.data.errors ?? {
                    general: [error.response.data.message ?? 'Validation failed.'],
                };
        }
    } finally {
        loading.value = false;
    }
};

const goToLink = async (key: keyof PaginationLinks) => {
    const link = paginationLinks.value[key];
    if (!link || loading.value) {
        return;
    }

    await fetchTransactions(link);
};

let privateChannel: ReturnType<EchoInstance['private']> | null = null;

const ensureCsrfCookie = async () => {
    try {
        await axios.get('/sanctum/csrf-cookie');
    } catch (error) {
        console.error('Failed to initialize Sanctum CSRF cookie.', error);
    }
};

const subscribeToTransferEvents = () => {
    if (!echo || !currentUserId.value) {
        return;
    }

    const channelName = `users.${currentUserId.value}`;
    privateChannel = echo
        .private(channelName)
        .listen('.transfer.completed', () => {
            fetchTransactions();
        });
};

onMounted(() => {
    ensureCsrfCookie().finally(() => {
        fetchTransactions();
        subscribeToTransferEvents();
    });
});

onBeforeUnmount(() => {
    if (privateChannel) {
        privateChannel.stopListening('.transfer.completed');
        privateChannel = null;
    }

    if (echo && currentUserId.value) {
        echo.leave(`users.${currentUserId.value}`);
    }
});
</script>

<template>
    <Head title="Wallet" />

    <AppLayout>
        <div class="space-y-6">
            <section
                class="rounded-xl border border-sidebar-border/70 bg-sidebar-card p-6 shadow-sm dark:border-sidebar-border"
            >
                <header class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-muted">Current Balance</p>
                        <h2 class="text-3xl font-semibold text-sidebar-primary">
                            {{ currencyFormatter.format(Number(balance)) }}
                        </h2>
                    </div>
                    <div v-if="paginationMeta" class="text-right text-xs text-muted">
                        <p>Total transactions: {{ paginationMeta.total }}</p>
                        <p>Showing page {{ paginationMeta.current_page }} of {{ paginationMeta.last_page }}</p>
                    </div>
                </header>
            </section>

            <section
                class="rounded-xl border border-sidebar-border/70 bg-sidebar-card p-6 shadow-sm dark:border-sidebar-border"
            >
                <header class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-sidebar-primary">Send Money</h2>
                </header>

                <form @submit.prevent="submitTransfer" class="space-y-4">
                    <div>
                        <label class="mb-1 block text-sm font-medium text-sidebar-primary"
                            >Receiver ID</label
                        >
                        <input
                            v-model="form.receiver_id"
                            type="number"
                            min="1"
                            class="w-full rounded-md border border-sidebar-border bg-sidebar px-3 py-2 text-sm focus:border-sidebar-primary focus:outline-none"
                            placeholder="Enter receiver user ID"
                        />
                        <p v-if="errors.receiver_id" class="mt-1 text-xs text-red-500">
                            {{ errors.receiver_id.join(', ') }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-sidebar-primary"
                            >Amount</label
                        >
                        <input
                            v-model="form.amount"
                            type="number"
                            step="0.0001"
                            min="0"
                            class="w-full rounded-md border border-sidebar-border bg-sidebar px-3 py-2 text-sm focus:border-sidebar-primary focus:outline-none"
                            placeholder="Amount to transfer"
                        />
                        <p v-if="errors.amount" class="mt-1 text-xs text-red-500">
                            {{ errors.amount.join(', ') }}
                        </p>
                    </div>

                    <button
                        type="submit"
                        :disabled="!canSubmit"
                        class="inline-flex w-full items-center justify-center rounded-md bg-sidebar-primary px-3 py-2 text-sm font-medium text-black transition disabled:opacity-50"
                    >
                        {{ loading ? 'Processing…' : 'Send Funds' }}
                    </button>

                    <p v-if="errors.general" class="text-xs text-red-500">
                        {{ errors.general.join(', ') }}
                    </p>
                    <p v-if="successMessage" class="text-xs text-emerald-500">
                        {{ successMessage }}
                    </p>
                </form>
            </section>

            <section
                class="rounded-xl border border-sidebar-border/70 bg-sidebar-card p-6 shadow-sm dark:border-sidebar-border"
            >
                <header class="mb-4 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-sidebar-primary">Transactions</h2>
                    <div class="flex items-center gap-2 text-sm">
                        <button
                            type="button"
                            class="rounded-md border border-sidebar-border px-3 py-1"
                            :class="{ 'opacity-50': !paginationLinks.prev || loading }"
                            :disabled="!paginationLinks.prev || loading"
                            @click="goToLink('prev')"
                        >
                            Prev
                        </button>
                        <button
                            type="button"
                            class="rounded-md border border-sidebar-border px-3 py-1"
                            :class="{ 'opacity-50': !paginationLinks.next || loading }"
                            :disabled="!paginationLinks.next || loading"
                            @click="goToLink('next')"
                        >
                            Next
                        </button>
                    </div>
                </header>

                <div v-if="loading" class="py-6 text-center text-sm text-muted">
                    Loading transactions…
                </div>

                <div v-else>
                    <p v-if="!transactions.length" class="py-6 text-center text-sm text-muted">
                        No transactions yet.
                    </p>

                    <ul v-else class="divide-y divide-sidebar-border/60">
                        <li v-for="transaction in transactions" :key="transaction.id" class="flex items-center justify-between py-4">
                            <div class="space-y-1">
                                <p class="text-sm font-medium text-sidebar-primary">
                                    {{ directionLabel(transaction) }}
                                    <span v-if="transaction.direction === 'outgoing'">
                                        to {{ transaction.receiver.name ?? `#${transaction.receiver.id}` }}
                                    </span>
                                    <span v-else-if="transaction.direction === 'incoming'">
                                        from {{ transaction.sender.name ?? `#${transaction.sender.id}` }}
                                    </span>
                                </p>
                                <p class="text-xs text-muted">
                                    Ref: {{ transaction.reference ?? 'N/A' }}
                                    <span v-if="transaction.created_at">
                                        · {{ new Date(transaction.created_at).toLocaleString() }}
                                    </span>
                                </p>
                                <p class="text-xs uppercase tracking-wide text-muted">
                                    Status: {{ transaction.status }}
                                </p>
                            </div>

                            <div class="text-right">
                                <p
                                    class="text-sm font-semibold"
                                    :class="transaction.direction === 'incoming' ? 'text-emerald-500' : 'text-rose-500'"
                                >
                                    {{ transaction.direction === 'incoming' ? '+' : '-' }}
                                    {{ Number(transaction.amount).toFixed(2) }}
                                </p>
                                <p class="text-xs text-muted">Fee: {{ Number(transaction.commission_fee).toFixed(2) }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </section>
        </div>
    </AppLayout>
</template>
