<script setup>
import FormError from '@/Components/Form/FormError.vue';
import FormLabel from '@/Components/Form/FormLabel.vue';
import TextInput from '@/Components/Form/TextInput.vue';
import IconTrash from '@/Components/Icons/IconTrash.vue';

const person = defineModel({ type: Object, required: true });

defineProps({
	id: { type: String, required: true },
	number: { type: Number, default: null },
	errors: { type: Object, default: () => ({}) },
	removable: { type: Boolean, default: false },
});

defineEmits(['remove']);
</script>

<template>
	<div class="relative grid gap-x-16 gap-y-12 md:grid-cols-2">
		<div class="relative">
			<FormLabel :for="`${id}-firstname`" required>Vorname<template v-if="number">&nbsp;{{ number }}</template></FormLabel>
			<TextInput
				:id="`${id}-firstname`"
				v-model="person.firstname"
				:placeholder="number ? `Vorname ${number}` : 'Vorname'"
				:error="!!errors.firstname"
				autocomplete="given-name"
			/>
			<FormError :message="errors.firstname" />
		</div>
		<div class="relative">
			<FormLabel :for="`${id}-lastname`" required>Nachname<template v-if="number">&nbsp;{{ number }}</template></FormLabel>
			<TextInput
				:id="`${id}-lastname`"
				v-model="person.lastname"
				:placeholder="number ? `Nachname ${number}` : 'Nachname'"
				:error="!!errors.lastname"
				autocomplete="family-name"
			/>
			<FormError :message="errors.lastname" />
			<button
				v-if="removable"
				type="button"
				class="absolute top-20 left-full ml-12 flex h-28 items-center hover:text-orange"
				:aria-label="`Person ${number} entfernen`"
				@click="$emit('remove')"
			>
				<IconTrash />
			</button>
		</div>
		<div class="md:col-span-2">
			<FormLabel :for="`${id}-email`" required>E-Mail-Adresse<template v-if="number && number > 1">&nbsp;{{ number }}</template></FormLabel>
			<TextInput
				:id="`${id}-email`"
				v-model="person.email"
				type="email"
				:placeholder="number ? `E-Mail-Adresse ${number}` : 'E-Mail-Adresse'"
				:error="!!errors.email"
				autocomplete="email"
			/>
			<FormError :message="errors.email" />
		</div>
	</div>
</template>
