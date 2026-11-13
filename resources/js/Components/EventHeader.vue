<script setup>
import IconPencil from '@/Components/Icons/IconPencil.vue';

defineProps({
	title: { type: String, required: true },
	description: { type: String, default: null },
	editable: { type: Boolean, default: false },
});

defineEmits(['edit']);
</script>

<template>
	<header class="relative">
		<h1 class="-ml-4 font-serif text-lg font-medium md:-mt-2 md:text-xl">{{ title }}</h1>
		<p v-if="description" class="mt-9 whitespace-pre-line md:mt-2">{{ description }}</p>
		<button
			v-if="editable"
			type="button"
			class="absolute top-0 left-full ml-12 flex h-28 items-end hover:text-orange md:h-48 md:pb-6"
			aria-label="Titel und Beschreibung bearbeiten"
			@click="$emit('edit')"
		>
			<IconPencil />
		</button>
	</header>
</template>
