<script setup>
import IconCheck from '@/Components/Icons/IconCheck.vue';
import IconCross from '@/Components/Icons/IconCross.vue';
import IconPencil from '@/Components/Icons/IconPencil.vue';

defineProps({
	title: { type: String, required: true },
	attendees: { type: Array, required: true },
	attending: { type: Boolean, required: true },
	showTotal: { type: Boolean, default: false },
});

defineEmits(['edit']);
</script>

<template>
	<section v-if="showTotal || attendees.length" :class="showTotal ? 'mt-40' : 'mt-28'">
		<h2 class="mb-4 text-xs font-bold">{{ title }}</h2>
		<ul>
			<li
				v-for="(attendee, index) in attendees"
				:key="attendee.id"
				class="relative h-28 border-t-[0.5px]"
				:class="[
					index === 0 ? 'border-t-olive' : 'border-t-olive-light',
					!showTotal && index === attendees.length - 1 ? 'border-b-[0.5px] border-b-olive-light' : '',
				]"
			>
				<button
					v-if="attendee.editable"
					type="button"
					class="flex h-full w-full items-center justify-between pr-8 text-left hover:text-orange focus-visible:text-orange focus-visible:outline-none"
					:aria-label="`${attendee.name} bearbeiten`"
					@click="$emit('edit', attendee)"
				>
					<span class="truncate pr-16">{{ attendee.name }}</span>
					<IconCheck v-if="attending" class="shrink-0" />
					<IconCross v-else class="shrink-0" />
					<IconPencil class="absolute top-1/2 left-full ml-12 -translate-y-1/2" />
				</button>
				<div v-else class="flex h-full items-center justify-between pr-8">
					<span class="truncate pr-16">{{ attendee.name }}</span>
					<IconCheck v-if="attending" class="shrink-0" />
					<IconCross v-else class="shrink-0" />
				</div>
			</li>
			<li
				v-if="showTotal"
				class="flex h-28 items-center justify-between border-y-[0.5px] border-olive pr-8"
			>
				<span>Total</span>
				<span class="tabular-nums">{{ attendees.length }}</span>
			</li>
		</ul>
	</section>
</template>
