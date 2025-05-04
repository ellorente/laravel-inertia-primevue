<script setup>
defineProps({
    type: {
        default: 'submit',
    },
});
</script>

<template>
    <Button :type="type">
        <slot />
    </Button>
</template>
