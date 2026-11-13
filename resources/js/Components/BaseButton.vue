<script setup>
defineProps({
	type: { type: String, default: 'button' },
	disabled: { type: Boolean, default: false },
});
</script>

<template>
	<button
		:type="type"
		:disabled="disabled"
		class="block h-28 w-full border-[0.5px] border-olive px-8 text-center text-sm hover:border-orange hover:bg-orange hover:text-white focus-visible:border-orange focus-visible:bg-orange focus-visible:text-white focus-visible:outline-none disabled:pointer-events-none disabled:opacity-50"
	>
		<slot />
	</button>
</template>
