<template>
    <AppLayout>
        <div class="flex flex-col h-full">
            <div class="flex items-center justify-between mb-4">
                <h1 class="text-2xl font-bold">Shippers</h1>
                <Link 
                    :href="route('shippers.create')"
                    class="btn-primary"
                >
                    New Shipper
                </Link>
            </div>
            
            <div class="flex-1 overflow-hidden">
                <!-- Search and Table -->
                <div class="h-1/2 pb-4">
                    <TextInput
                        v-model="search"
                        placeholder="Search shippers..."
                        class="w-full mb-4"
                    />
                    
                    <DataTable
                        :data="shippers"
                        :columns="columns"
                        @row-click="selectShipper"
                        :selected-id="selectedShipperId"
                        skeleton-class="h-8"
                    />
                </div>

                <!-- Details Panel -->
                <div v-if="selectedShipper" class="h-1/2 pt-4 border-t">
                    <Tabs default-value="general">
                        <TabsList class="grid w-full grid-cols-7">
                            <TabsTrigger value="general">General</TabsTrigger>
                            <TabsTrigger value="accounting">Accounting</TabsTrigger>
                            <TabsTrigger value="locations">Locations</TabsTrigger>
                            <TabsTrigger value="shipments">Shipments</TabsTrigger>
                            <TabsTrigger value="contacts">Contacts</TabsTrigger>
                            <TabsTrigger value="notes">Notes</TabsTrigger>
                            <TabsTrigger value="documents">Documents</TabsTrigger>
                        </TabsList>

                        <Skeleton v-if="loadingDetails" class="h-full" />
                        
                        <template v-else>
                            <TabsContent value="general">
                                <ShipperGeneral :shipper="selectedShipper" />
                            </TabsContent>
                            <!-- Other tab contents -->
                        </template>
                    </Tabs>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import debounce from 'lodash/debounce'

const props = defineProps({
    shippers: Array,
    selectedShipper: Object,
})

const search = ref('')
const selectedShipperId = ref(null)
const loadingDetails = ref(false)

const selectShipper = (shipper) => {
    selectedShipperId.value = shipper.id
    loadingDetails.value = true
    router.get(route('shippers.show', shipper.id), {}, {
        preserveState: true,
        onFinish: () => loadingDetails.value = false
    })
}

const searchShippers = debounce(() => {
    router.get(route('shippers.index'), { search: search.value }, {
        preserveScroll: true,
        replace: true
    })
}, 300)

watch(search, searchShippers)
</script> 