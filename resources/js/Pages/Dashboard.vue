<script setup>
import BestSellingWidget from '@/Components/dashboard/BestSellingWidget.vue';
import NotificationsWidget from '@/Components/dashboard/NotificationsWidget.vue';
import RecentSalesWidget from '@/Components/dashboard/RecentSalesWidget.vue';
import RevenueStreamWidget from '@/Components/dashboard/RevenueStreamWidget.vue';
import StatsWidget from '@/Components/dashboard/StatsWidget.vue';
import AppLayout from '@/Layout/AppLayout.vue';
</script>

<template>
    <AppLayout>
    <div class="grid grid-cols-12 gap-8">
        <StatsWidget />

        <div class="col-span-12 xl:col-span-6">
            <RecentSalesWidget />
            <BestSellingWidget />
        </div>
        <div class="col-span-12 xl:col-span-6">
            <RevenueStreamWidget />
            <NotificationsWidget />
        </div>
    </div>
</AppLayout>
</template>
