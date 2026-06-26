<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
  Card,
  CardContent,
  CardDescription,
  CardHeader,
  CardTitle,
} from '@/components/ui/card';
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog';

const props = defineProps<{
    role: 'admin' | 'manager' | 'employee';
    total_companies?: number;
    users?: any[];
    balance?: {
        allocated_days: number;
        used_days: number;
    };
    requests?: any[];
}>();

const requestForm = useForm({
    start_date: '',
    end_date: '',
    reason: '',
});

const isRequestModalOpen = ref(false);

const submitRequest = () => {
    requestForm.post('/pto-requests', {
        onSuccess: () => {
            isRequestModalOpen.value = false;
            requestForm.reset();
        },
    });
};

const promoteToManager = (userId: number) => {
    router.patch(`/users/${userId}/promote`, {}, {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Dashboard" />

    <div class="flex h-full flex-1 flex-col gap-6 overflow-x-auto p-4 max-w-7xl mx-auto w-full">
        <!-- Dashboard Header & Actions -->
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold tracking-tight text-foreground">Dashboard</h1>
                <p class="text-muted-foreground mt-1">
                    Welcome back! You are logged in as <span class="capitalize font-semibold text-primary">{{ role }}</span>.
                </p>
            </div>
            
            <Dialog v-model:open="isRequestModalOpen" v-if="role !== 'admin'">
                <DialogTrigger as-child>
                    <Button variant="default">Request Time Off</Button>
                </DialogTrigger>
                <DialogContent class="sm:max-w-[425px]">
                    <DialogHeader>
                        <DialogTitle>Request Time Off</DialogTitle>
                        <DialogDescription>
                            Select your dates and submit your PTO request for manager approval.
                        </DialogDescription>
                    </DialogHeader>
                    
                    <form @submit.prevent="submitRequest" class="grid gap-4 py-4">
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="start_date" class="text-right">Start Date</Label>
                            <Input id="start_date" type="date" v-model="requestForm.start_date" class="col-span-3" required />
                        </div>
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="end_date" class="text-right">End Date</Label>
                            <Input id="end_date" type="date" v-model="requestForm.end_date" class="col-span-3" required />
                        </div>
                        <div class="grid grid-cols-4 items-center gap-4">
                            <Label for="reason" class="text-right">Reason</Label>
                            <Input id="reason" v-model="requestForm.reason" class="col-span-3" placeholder="Vacation, Sick, etc." required />
                        </div>
                        <DialogFooter>
                            <Button type="submit" :disabled="requestForm.processing">Submit Request</Button>
                        </DialogFooter>
                    </form>
                </DialogContent>
            </Dialog>
        </div>

        <!-- Admin View -->
        <template v-if="role === 'admin'">
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Total Registered Companies</CardDescription>
                        <CardTitle class="text-4xl font-bold text-primary">{{ total_companies }}</CardTitle>
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Total Users</CardDescription>
                        <CardTitle class="text-4xl font-bold">{{ users?.length || 0 }}</CardTitle>
                    </CardHeader>
                </Card>
            </div>

            <Card class="mt-4">
                <CardHeader>
                    <CardTitle>System Users</CardTitle>
                    <CardDescription>Manage users across all tenants.</CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto rounded-md border border-border">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs uppercase bg-muted/50 text-muted-foreground border-b border-border">
                                <tr>
                                    <th class="px-4 py-3">Name</th>
                                    <th class="px-4 py-3">Email</th>
                                    <th class="px-4 py-3">Company</th>
                                    <th class="px-4 py-3">Role</th>
                                    <th class="px-4 py-3 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="user in users" :key="user.id" class="border-b border-border/50 last:border-0 hover:bg-muted/20">
                                    <td class="px-4 py-3 font-medium">{{ user.name }}</td>
                                    <td class="px-4 py-3">{{ user.email }}</td>
                                    <td class="px-4 py-3">{{ user.tenant?.name || 'N/A' }}</td>
                                    <td class="px-4 py-3 capitalize">
                                        <span :class="{'text-secondary font-semibold': user.role === 'manager', 'text-muted-foreground': user.role === 'employee'}">
                                            {{ user.role }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right">
                                        <Button v-if="user.role === 'employee'" @click="promoteToManager(user.id)" variant="outline" size="sm" class="text-xs">
                                            Promote to Manager
                                        </Button>
                                        <span v-else class="text-xs text-muted-foreground italic">Manager</span>
                                    </td>
                                </tr>
                                <tr v-if="!users || users.length === 0">
                                    <td colspan="5" class="px-4 py-8 text-center text-muted-foreground">No users found.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </template>

        <!-- Manager / Employee View -->
        <template v-else>
            <!-- Stats -->
            <div class="grid gap-4 md:grid-cols-3">
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Allocated Days ({{ new Date().getFullYear() }})</CardDescription>
                        <CardTitle class="text-4xl font-bold">{{ balance?.allocated_days || 0 }}</CardTitle>
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader class="pb-2">
                        <CardDescription>Used Days</CardDescription>
                        <CardTitle class="text-4xl font-bold">{{ balance?.used_days || 0 }}</CardTitle>
                    </CardHeader>
                </Card>
                <Card class="border-primary/50 bg-primary/5">
                    <CardHeader class="pb-2">
                        <CardDescription class="text-primary/80">Remaining Days</CardDescription>
                        <CardTitle class="text-4xl font-bold text-primary">
                            {{ (balance?.allocated_days || 0) - (balance?.used_days || 0) }}
                        </CardTitle>
                    </CardHeader>
                </Card>
            </div>

            <!-- PTO Requests Table -->
            <Card class="mt-4">
                <CardHeader>
                    <CardTitle>{{ role === 'manager' ? 'Company PTO Requests' : 'My Recent Requests' }}</CardTitle>
                    <CardDescription>
                        {{ role === 'manager' ? 'Review and manage time off requests for your team.' : 'Track the status of your submitted requests.' }}
                    </CardDescription>
                </CardHeader>
                <CardContent>
                    <div class="overflow-x-auto rounded-md border border-border">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs uppercase bg-muted/50 text-muted-foreground border-b border-border">
                                <tr>
                                    <th v-if="role === 'manager'" class="px-4 py-3">Employee</th>
                                    <th class="px-4 py-3">Dates</th>
                                    <th class="px-4 py-3">Reason</th>
                                    <th class="px-4 py-3">Status</th>
                                    <th v-if="role === 'manager'" class="px-4 py-3 text-right">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="req in requests" :key="req.id" class="border-b border-border/50 last:border-0 hover:bg-muted/20">
                                    <td v-if="role === 'manager'" class="px-4 py-3 font-medium">{{ req.user?.name || 'Unknown' }}</td>
                                    <td class="px-4 py-3">{{ req.start_date }} to {{ req.end_date }}</td>
                                    <td class="px-4 py-3 text-muted-foreground">{{ req.reason }}</td>
                                    <td class="px-4 py-3">
                                        <span :class="{
                                            'text-yellow-500 bg-yellow-500/10 px-2 py-1 rounded-full text-xs font-medium': req.status === 'pending',
                                            'text-emerald-500 bg-emerald-500/10 px-2 py-1 rounded-full text-xs font-medium': req.status === 'approved',
                                            'text-red-500 bg-red-500/10 px-2 py-1 rounded-full text-xs font-medium': req.status === 'denied'
                                        }" class="capitalize">
                                            {{ req.status }}
                                        </span>
                                    </td>
                                    <td v-if="role === 'manager'" class="px-4 py-3 text-right flex justify-end gap-2">
                                        <Button v-if="req.status === 'pending'" variant="outline" size="sm" class="text-xs border-emerald-500/50 hover:bg-emerald-500/10 text-emerald-500">
                                            Approve
                                        </Button>
                                        <Button v-if="req.status === 'pending'" variant="outline" size="sm" class="text-xs border-red-500/50 hover:bg-red-500/10 text-red-500">
                                            Deny
                                        </Button>
                                    </td>
                                </tr>
                                <tr v-if="!requests || requests.length === 0">
                                    <td :colspan="role === 'manager' ? 5 : 4" class="px-4 py-8 text-center text-muted-foreground">
                                        No PTO requests found.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </CardContent>
            </Card>
        </template>
    </div>
</template>
