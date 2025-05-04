<script setup>
import SectionTitle from './SectionTitle.vue';
</script>

<template>
    <div class="md:grid md:gap-6">
        <SectionTitle>
            <template #title>
                <slot name="title" />
            </template>
            <template #description>
                <slot name="description" />
            </template>
        </SectionTitle>

        <div class="mt-5 md:mt-0 md:col-span-2">
            <div class="px-4 py-5 sm:p-6 bg-red-100 dark:bg-red-400/10 shadow sm:rounded-lg">
                <div class="px-4 py-5 sm:p-6 " >
                    <slot name="content" />
                </div>
            </div>
        </div>        
    </div>
</template>
