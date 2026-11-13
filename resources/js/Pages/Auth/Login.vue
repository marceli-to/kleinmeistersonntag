<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import BaseButton from '@/Components/BaseButton.vue';
import FormError from '@/Components/Form/FormError.vue';
import FormLabel from '@/Components/Form/FormLabel.vue';
import TextInput from '@/Components/Form/TextInput.vue';
import PageLayout from '@/Layouts/PageLayout.vue';

const form = useForm({
	email: '',
	password: '',
});

function submit() {
	form.post('/login', {
		onFinish: () => form.reset('password'),
	});
}
</script>

<template>
	<Head title="Anmelden" />

	<PageLayout>
		<h1 class="-ml-4 font-serif text-lg font-medium md:text-xl">Anmelden</h1>

		<form novalidate class="mt-28" @submit.prevent="submit">
			<FormLabel for="email">E-Mail-Adresse</FormLabel>
			<TextInput
				id="email"
				v-model="form.email"
				type="email"
				placeholder="E-Mail-Adresse"
				autocomplete="username"
				:error="!!form.errors.email"
			/>
			<FormError :message="form.errors.email" />

			<div class="mt-12">
				<FormLabel for="password">Passwort</FormLabel>
				<TextInput
					id="password"
					v-model="form.password"
					type="password"
					placeholder="Passwort"
					autocomplete="current-password"
					:error="!!form.errors.password"
				/>
				<FormError :message="form.errors.password" />
			</div>

			<BaseButton type="submit" class="mt-32" :disabled="form.processing">Anmelden</BaseButton>
		</form>
	</PageLayout>
</template>
