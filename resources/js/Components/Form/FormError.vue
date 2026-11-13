<script setup>
defineProps({
	message: { type: String, default: null },
});
</script>

<template>
	<p v-if="message" class="mt-4 text-xs font-bold text-orange">{{ message }}</p>
</template>
