<script lang="ts" setup>
import DialogModal from "@/Components/DialogModal.vue";
import TextInput from "@/Components/TextInput.vue";
import DangerButton from "@/Components/DangerButton.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import InputError from "@/Components/InputError.vue";
import {useForm} from "@inertiajs/vue3";
import {ref} from "vue";

const props = defineProps<{
    task: Number,
    show: Boolean,
}>()
const emit = defineEmits(['close'])
const passwordInput = ref<HTMLElement | null>(null);
const form = useForm({
    password: '',
});

const deleteTask = () => {
    if (props.task != null) {
        form.delete(route('tasks.destroy', props.task), <Partial<VisitOptions>>{
            preserveScroll: true,
            onFinish: () => form.reset(),
            onSuccess: () => emit('close'),
            onError: () => passwordInput.value!.focus(),
        });
    }
}

const closeModal = () => {
    form.reset()
    emit('close')
}

const focusToInput = () => passwordInput.value?.focus()
defineExpose({ focusToInput })
</script>

<template>
    <DialogModal :show="show" @close="closeModal">
        <template #title>
            Delete Task
        </template>

        <template #content>
            Are you sure you want to delete This task?

            <div class="mt-4">
                <TextInput ref="passwordInput"
                           v-model="form.password"
                           autocomplete="current-password"
                           class="mt-1 block w-3/4"
                           placeholder="Password"
                           type="password"
                           @keyup.enter="deleteTask"/>

                <InputError :message="form.errors.password" class="mt-2"/>
            </div>
        </template>

        <template #footer>
            <SecondaryButton @click="closeModal">Cancel</SecondaryButton>

            <DangerButton :class="{ 'opacity-25': form.processing }"
                          :disabled="form.processing"
                          class="ml-3"
                          @click="deleteTask">
                Delete Task
            </DangerButton>
        </template>
    </DialogModal>
</template>
