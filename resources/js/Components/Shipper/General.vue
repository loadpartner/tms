<template>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">General Information</h3>
            <Button variant="outline" @click="editShipper">Edit</Button>
        </div>
        
        <dl class="grid grid-cols-2 gap-4">
            <div>
                <dt>Name</dt>
                <dd>{{ shipper.name }}</dd>
            </div>
            <!-- Other fields -->
        </dl>
    </div>
</template>

<script setup>
defineProps({
    shipper: Object
})

const emit = defineEmits(['edit'])

const editShipper = () => {
    emit('edit')
}
</script> 