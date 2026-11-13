<script setup>
defineProps({
	for: { type: String, default: null },
	required: { type: Boolean, default: false },
});
</script>

<template>
	<label :for="$props.for" class="mb-4 block text-xs font-bold">
		<slot />
		<template v-if="required">&#8239;*</template>
	</label>
</template>
