<script setup>
import FormError from '@/Components/Form/FormError.vue';
import RadioOption from '@/Components/Form/RadioOption.vue';

const attending = defineModel({ type: Boolean, default: null });

defineProps({
	name: { type: String, required: true },
	plural: { type: Boolean, default: false },
	error: { type: String, default: null },
});
</script>

<template>
	<fieldset class="mt-28">
		<legend class="mb-4 text-xs font-bold">{{ plural ? 'Eure Antwort' : 'Deine Antwort' }}</legend>
		<div class="grid gap-y-8">
			<RadioOption v-model="attending" :name="name" :value="true">Ja gerne</RadioOption>
			<RadioOption v-model="attending" :name="name" :value="false">Leider nein</RadioOption>
		</div>
		<FormError :message="error" />
	</fieldset>
</template>
