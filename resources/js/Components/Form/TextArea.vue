<script setup>
const model = defineModel({ type: String, default: '' });

defineProps({
	error: { type: Boolean, default: false },
});
</script>

<template>
	<textarea
		v-model="model"
		:aria-invalid="error || undefined"
		class="block h-84 w-full resize-none rounded-none border-[0.5px] px-8 py-4 text-sm text-black outline-none placeholder:text-olive placeholder:italic hover:border-black focus:border-black not-placeholder-shown:border-black"
		:class="error ? 'border-orange' : 'border-olive'"
	/>
</template>
