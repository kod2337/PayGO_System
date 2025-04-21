<script setup>
import { Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/ui/card.vue';

const payrollStats = {
    totalEmployees: '1,234',
    totalPayroll: '₱4,567,890',
    avgSalary: '₱45,670',
    pendingRequests: '23',
    payrollEfficiency: '98.5%',
    nextPayday: 'April 30, 2025'
};

const recentPayrolls = [
    { id: 1, period: 'April 15-30, 2025', amount: '₱2,345,678', status: 'Completed', date: 'April 30, 2025', efficiency: '99.2%' },
    { id: 2, period: 'April 1-15, 2025', amount: '₱2,222,111', status: 'Completed', date: 'April 15, 2025', efficiency: '98.7%' },
    { id: 3, period: 'March 16-31, 2025', amount: '₱2,111,999', status: 'Completed', date: 'March 31, 2025', efficiency: '98.9%' },
];

const quickLinks = [
    { title: 'Attendance Summary', count: '98%', trend: 'up' },
    { title: 'Leave Requests', count: '12', trend: 'neutral' },
    { title: 'Tax Compliance', count: '100%', trend: 'up' },
    { title: 'Pending Actions', count: '5', trend: 'down' },
];
</script>

<template>
    <Head title="PayGo Enterprise Dashboard" />

    <AuthenticatedLayout>
        <div class="min-h-screen bg-background">
            <!-- Top Header Bar -->
            <div class="bg-card border-b border-border mb-6">
                <div class="container mx-auto px-4 py-6">
                    <div class="flex justify-between items-center">
                        <div>
                            <h1 class="text-3xl font-bold text-foreground tracking-tight">Dashboard</h1>
                            <div class="flex items-center mt-1 space-x-4">
                                <p class="text-muted-foreground">Current Pay Period: April 16-30, 2025</p>
                                <span class="text-muted-foreground">|</span>
                                <p class="text-muted-foreground flex items-center">
                                    <span class="inline-block w-2 h-2 rounded-full bg-green-500 mr-2"></span>
                                    System Status: Operational
                                </p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4">
                            <button class="bg-accent text-accent-foreground px-4 py-2 rounded-lg hover:bg-accent/90 transition-colors">
                                Generate Reports
                            </button>
                            <button class="bg-primary text-primary-foreground px-6 py-2 rounded-lg hover:opacity-90 transition-colors font-medium">
                                Process Payroll
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="container mx-auto px-4 space-y-6">
                <!-- Quick Info Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    <Card v-for="link in quickLinks" :key="link.title" 
                          class="p-4 hover:shadow-lg transition-shadow cursor-pointer">
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-sm text-muted-foreground">{{ link.title }}</p>
                                <p class="text-2xl font-semibold mt-1">{{ link.count }}</p>
                            </div>
                            <span :class="{
                                'text-green-500': link.trend === 'up',
                                'text-yellow-500': link.trend === 'neutral',
                                'text-red-500': link.trend === 'down'
                            }">
                                <svg v-if="link.trend === 'up'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                                <!-- Add other trend icons as needed -->
                            </span>
                        </div>
                    </Card>
                </div>

                <!-- Main Grid -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <!-- Payroll Stats -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Stats Overview -->
                        <Card class="p-6">
                            <h2 class="text-xl font-semibold mb-6">Payroll Overview</h2>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <div class="space-y-1">
                                    <p class="text-sm text-muted-foreground">Total Employees</p>
                                    <p class="text-2xl font-semibold">{{ payrollStats.totalEmployees }}</p>
                                    <p class="text-xs text-muted-foreground">+2.5% from last month</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm text-muted-foreground">Monthly Payroll</p>
                                    <p class="text-2xl font-semibold">{{ payrollStats.totalPayroll }}</p>
                                    <p class="text-xs text-muted-foreground">Within budget</p>
                                </div>
                                <div class="space-y-1">
                                    <p class="text-sm text-muted-foreground">Processing Efficiency</p>
                                    <p class="text-2xl font-semibold">{{ payrollStats.payrollEfficiency }}</p>
                                    <p class="text-xs text-green-500">Above target</p>
                                </div>
                            </div>
                        </Card>

                        <!-- Recent Payrolls -->
                        <Card class="p-6">
                            <div class="flex justify-between items-center mb-6">
                                <h2 class="text-xl font-semibold">Recent Payroll History</h2>
                                <button class="text-sm text-primary hover:underline">View All</button>
                            </div>
                            <div class="space-y-4">
                                <div v-for="payroll in recentPayrolls" :key="payroll.id" 
                                     class="flex items-center justify-between p-4 rounded-lg hover:bg-accent/50 transition-colors border border-border">
                                    <div class="space-y-1">
                                        <h4 class="font-medium">{{ payroll.period }}</h4>
                                        <p class="text-sm text-muted-foreground">Processed: {{ payroll.date }}</p>
                                    </div>
                                    <div class="text-right space-y-1">
                                        <span class="font-medium">{{ payroll.amount }}</span>
                                        <p class="text-sm">
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                {{ payroll.status }}
                                            </span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </Card>
                    </div>

                    <!-- Right Sidebar -->
                    <div class="space-y-6">
                        <!-- Quick Actions -->
                        <Card class="p-6">
                            <h2 class="text-xl font-semibold mb-4">Quick Actions</h2>
                            <div class="space-y-3">
                                <button class="w-full text-left p-4 hover:bg-accent rounded-lg transition-colors flex items-center space-x-3 border border-border">
                                    <span class="p-2 rounded-lg bg-primary/10 text-primary">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                                        </svg>
                                    </span>
                                    <div>
                                        <span class="font-medium block">New Employee</span>
                                        <span class="text-sm text-muted-foreground">Add to payroll</span>
                                    </div>
                                </button>

                                <button class="w-full text-left p-4 hover:bg-accent rounded-lg transition-colors flex items-center space-x-3 border border-border">
                                    <span class="p-2 rounded-lg bg-primary/10 text-primary">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                        </svg>
                                    </span>
                                    <div>
                                        <span class="font-medium block">Reports</span>
                                        <span class="text-sm text-muted-foreground">View analytics</span>
                                    </div>
                                </button>

                                <button class="w-full text-left p-4 hover:bg-accent rounded-lg transition-colors flex items-center space-x-3 border border-border">
                                    <span class="p-2 rounded-lg bg-primary/10 text-primary">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                        </svg>
                                    </span>
                                    <div>
                                        <span class="font-medium block">Settings</span>
                                        <span class="text-sm text-muted-foreground">Configure system</span>
                                    </div>
                                </button>
                            </div>
                        </Card>

                        <!-- Next Payroll Info -->
                        <Card class="p-6 bg-primary/5">
                            <div class="space-y-4">
                                <h3 class="font-semibold">Next Payroll Date</h3>
                                <div class="flex items-center space-x-2 text-primary">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    <span class="font-medium">{{ payrollStats.nextPayday }}</span>
                                </div>
                                <div class="text-sm text-muted-foreground">
                                    <p>Processing starts in 5 days</p>
                                    <p>23 pending approvals</p>
                                </div>
                            </div>
                        </Card>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
