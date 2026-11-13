<script setup>
const model = defineModel({ default: null });

defineProps({
	name: { type: String, required: true },
	value: { required: true },
});
</script>

<template>
	<label class="group relative flex h-28 cursor-pointer items-center border-[0.5px] border-olive px-8 hover:border-orange hover:text-orange">
		<input
			v-model="model"
			type="radio"
			:name="name"
			:value="value"
			class="peer sr-only"
		>
		<svg
			class="absolute top-1/2 right-full mr-10 -translate-y-1/2 rounded-full peer-focus-visible:outline peer-focus-visible:outline-offset-2 md:mr-16"
			width="12"
			height="12"
			viewBox="0 0 12 12"
			fill="none"
			aria-hidden="true"
		>
			<circle cx="6" cy="6" r="5.5" stroke="currentColor" stroke-width="0.9" />
			<circle v-if="model === value" cx="6" cy="6" r="2.5" fill="currentColor" />
		</svg>
		<slot />
	</label>
</template>
