<script setup>
import { computed, ref } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import AnswerFields from '@/Components/AnswerFields.vue';
import AttendeeList from '@/Components/AttendeeList.vue';
import BaseButton from '@/Components/BaseButton.vue';
import EditAttendeeModal from '@/Components/EditAttendeeModal.vue';
import EditEventModal from '@/Components/EditEventModal.vue';
import EventHeader from '@/Components/EventHeader.vue';
import PersonFields from '@/Components/PersonFields.vue';
import PageLayout from '@/Layouts/PageLayout.vue';

const props = defineProps({
	event: { type: Object, required: true },
	attendees: { type: Array, required: true },
});

const page = usePage();
const isAdmin = computed(() => page.props.isAdmin);

const accepted = computed(() => props.attendees.filter((attendee) => attendee.attending));
const declined = computed(() => props.attendees.filter((attendee) => !attendee.attending));

const emptyPerson = () => ({ firstname: '', lastname: '', email: '' });

const form = useForm({
	persons: [emptyPerson()],
	attending: true,
});

const multiple = computed(() => form.persons.length > 1);

function personErrors(index) {
	const prefix = `persons.${index}.`;

	return Object.fromEntries(
		Object.entries(form.errors)
			.filter(([key]) => key.startsWith(prefix))
			.map(([key, message]) => [key.slice(prefix.length), message]),
	);
}

function addPerson() {
	form.persons.push(emptyPerson());
}

function removePerson(index) {
	form.persons.splice(index, 1);
	form.clearErrors();
}

function submit() {
	form.post('/attendees', {
		preserveScroll: true,
		onSuccess: () => form.reset(),
	});
}

const editingAttendee = ref(null);
const editingEvent = ref(false);

function logout() {
	router.post('/logout');
}
</script>

<template>
	<Head :title="event.title" />

	<PageLayout>
		<EventHeader
			:title="event.title"
			:description="event.description"
			:editable="isAdmin"
			@edit="editingEvent = true"
		/>

		<p v-if="!isAdmin" class="mt-40 font-medium md:mt-48">
			Bitte tragt Euch einzeln ein, so lässt’s sich leichter zählen.
		</p>

		<form novalidate :class="isAdmin ? 'mt-28' : 'mt-20'" @submit.prevent="submit">
			<div class="grid gap-y-12">
				<PersonFields
					v-for="(person, index) in form.persons"
					:id="`person-${index}`"
					:key="index"
					v-model="form.persons[index]"
					:number="multiple ? index + 1 : null"
					:errors="personErrors(index)"
					:removable="index > 0"
					@remove="removePerson(index)"
				/>
			</div>

			<BaseButton class="mt-32" @click="addPerson">Person hinzufügen</BaseButton>

			<AnswerFields
				v-model="form.attending"
				name="attending"
				:plural="multiple"
				:error="form.errors.attending"
			/>

			<BaseButton type="submit" class="mt-32" :disabled="form.processing">Antwort speichern</BaseButton>
		</form>

		<AttendeeList
			title="Bisher haben zugesagt"
			:attendees="accepted"
			attending
			show-total
			@edit="editingAttendee = $event"
		/>

		<AttendeeList
			title="Bisher haben abgesagt"
			:attendees="declined"
			:attending="false"
			@edit="editingAttendee = $event"
		/>

		<button
			v-if="isAdmin"
			type="button"
			class="mt-40 text-xs font-bold hover:text-orange"
			@click="logout"
		>
			Abmelden
		</button>
	</PageLayout>

	<EditAttendeeModal
		v-if="editingAttendee"
		:key="editingAttendee.id"
		:attendee="editingAttendee"
		:deletable="isAdmin"
		@close="editingAttendee = null"
	/>

	<EditEventModal
		v-if="editingEvent"
		:event="event"
		@close="editingEvent = false"
	/>
</template>
