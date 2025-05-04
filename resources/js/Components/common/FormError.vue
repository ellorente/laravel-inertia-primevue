<script setup>
defineProps({
    error: {
        type: String,
        required: false,
    }
});
</script>

<template>
    <small v-if="error" class="text-red-500 text-sm">
        {{ error }}
    </small>
</template>