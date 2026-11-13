<script setup>
const model = defineModel({ type: String, default: '' });

defineProps({
	type: { type: String, default: 'text' },
	error: { type: Boolean, default: false },
});
</script>

<template>
	<input
		v-model="model"
		:type="type"
		:aria-invalid="error || undefined"
		class="block h-28 w-full rounded-none border-[0.5px] px-8 text-sm text-black outline-none placeholder:text-olive placeholder:italic hover:border-black focus:border-black not-placeholder-shown:border-black"
		:class="error ? 'border-orange' : 'border-olive'"
	>
</template>
