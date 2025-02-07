<template>
    <AppLayout>
        <form @submit.prevent="submit">
            <div class="max-w-3xl mx-auto">
                <Card>
                    <CardHeader>
                        <CardTitle>{{ form.id ? 'Edit' : 'Create' }} Shipper</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <InputLabel value="Name" />
                        <TextInput v-model="form.name" class="w-full" />
                        
                        <!-- Add other form fields -->
                        
                        <FormActions :processing="processing" />
                    </CardContent>
                </Card>
            </div>
        </form>
    </AppLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'

const props = defineProps({
    shipper: Object
})

const form = useForm({
    name: props.shipper?.name || '',
    // other fields
})

const submit = () => {
    if (form.id) {
        form.put(route('shippers.update', form.id))
    } else {
        form.post(route('shippers.store'))
    }
}
</script> 