<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import BaseButton from '@/Components/BaseButton.vue';
import FormError from '@/Components/Form/FormError.vue';
import FormLabel from '@/Components/Form/FormLabel.vue';
import TextArea from '@/Components/Form/TextArea.vue';
import TextInput from '@/Components/Form/TextInput.vue';
import IconCheck from '@/Components/Icons/IconCheck.vue';
import IconCopy from '@/Components/Icons/IconCopy.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
	event: { type: Object, required: true },
});

const emit = defineEmits(['close']);

const form = useForm({
	title: props.event.title,
	description: props.event.description ?? '',
});

const copied = ref(false);

function submit() {
	form.put(`/events/${props.event.id}`, {
		preserveScroll: true,
		onSuccess: () => emit('close'),
	});
}

async function copyLink() {
	await navigator.clipboard.writeText(props.event.link);
	copied.value = true;
	setTimeout(() => (copied.value = false), 1500);
}
</script>

<template>
	<Modal @close="emit('close')">
		<form novalidate @submit.prevent="submit">
			<FormLabel for="event-title">Titel</FormLabel>
			<TextInput id="event-title" v-model="form.title" placeholder="Titel" :error="!!form.errors.title" />
			<FormError :message="form.errors.title" />

			<div class="mt-12">
				<FormLabel for="event-description">Beschreibung</FormLabel>
				<TextArea id="event-description" v-model="form.description" placeholder="Beschreibung" :error="!!form.errors.description" />
				<FormError :message="form.errors.description" />
			</div>

			<div class="mt-28 grid gap-y-4">
				<BaseButton type="submit" :disabled="form.processing">Speichern</BaseButton>
				<BaseButton @click="emit('close')">Abbrechen</BaseButton>
			</div>
		</form>

		<div class="mt-12">
			<FormLabel for="event-link">Link</FormLabel>
			<div class="relative">
				<input
					id="event-link"
					:value="event.link"
					readonly
					class="block h-28 w-full rounded-none border-[0.5px] border-olive pr-32 pl-8 text-sm outline-none hover:border-black focus:border-black"
					@focus="$event.target.select()"
				>
				<button
					type="button"
					class="absolute inset-y-0 right-0 flex w-28 items-center justify-center hover:text-orange"
					:aria-label="copied ? 'Link kopiert' : 'Link kopieren'"
					@click="copyLink"
				>
					<IconCheck v-if="copied" />
					<IconCopy v-else />
				</button>
			</div>
		</div>
	</Modal>
</template>
