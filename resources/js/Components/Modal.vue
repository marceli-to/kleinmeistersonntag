<script setup>
import { onBeforeUnmount, onMounted } from 'vue';

const emit = defineEmits(['close']);

function onKeydown(event) {
	if (event.key === 'Escape') {
		emit('close');
	}
}

onMounted(() => {
	document.addEventListener('keydown', onKeydown);
	document.body.style.overflow = 'hidden';
});

onBeforeUnmount(() => {
	document.removeEventListener('keydown', onKeydown);
	document.body.style.overflow = '';
});
</script>

<template>
	<Teleport to="body">
		<div
			class="fixed inset-0 z-50 overflow-y-auto bg-white/80"
			@mousedown.self="emit('close')"
		>
			<div class="page-frame pointer-events-none flex min-h-full flex-col justify-center py-32">
				<div class="max-w-content">
					<div
						role="dialog"
						aria-modal="true"
						class="pointer-events-auto relative -mx-26 bg-white outline-[0.5px] -outline-offset-[0.5px] outline-olive px-26 pt-20 pb-40 md:-mx-40 md:px-40"
					>
						<slot />
					</div>
				</div>
			</div>
		</div>
	</Teleport>
</template>
