<script setup>
import { useForm } from '@inertiajs/vue3';
import AnswerFields from '@/Components/AnswerFields.vue';
import BaseButton from '@/Components/BaseButton.vue';
import Modal from '@/Components/Modal.vue';
import PersonFields from '@/Components/PersonFields.vue';

const props = defineProps({
	attendee: { type: Object, required: true },
	deletable: { type: Boolean, default: false },
});

const emit = defineEmits(['close']);

const form = useForm({
	person: {
		firstname: props.attendee.firstname,
		lastname: props.attendee.lastname,
		email: props.attendee.email,
	},
	attending: props.attendee.attending,
});

function submit() {
	form
		.transform((data) => ({ ...data.person, attending: data.attending }))
		.put(`/attendees/${props.attendee.id}`, {
			preserveScroll: true,
			onSuccess: () => emit('close'),
		});
}

function destroy() {
	if (!window.confirm(`${props.attendee.name} wirklich löschen?`)) {
		return;
	}

	form.delete(`/attendees/${props.attendee.id}`, {
		preserveScroll: true,
		onSuccess: () => emit('close'),
	});
}
</script>

<template>
	<Modal @close="emit('close')">
		<form novalidate @submit.prevent="submit">
			<PersonFields
				id="edit"
				v-model="form.person"
				:errors="form.errors"
			/>
			<AnswerFields
				v-model="form.attending"
				name="edit-attending"
				:error="form.errors.attending"
			/>
			<div class="mt-32 grid gap-y-4">
				<BaseButton type="submit" :disabled="form.processing">Speichern</BaseButton>
				<BaseButton @click="emit('close')">Abbrechen</BaseButton>
				<BaseButton v-if="deletable" :disabled="form.processing" @click="destroy">Löschen</BaseButton>
			</div>
		</form>
	</Modal>
</template>
