<template>
  <div class="space-y-4">
    <!-- Page Header -->
    <div class="flex items-center justify-between">
      <h1 class="text-xl font-bold">Shipper Management</h1>
      <Button @click="goToCreateShipper">+ New Shipper</Button>
    </div>

    <!-- Table & Search -->
    <div class="bg-white p-4 rounded-md shadow">
      <div class="mb-2">
        <!-- Search bar for filtering shippers -->
        <Input
          type="text"
          placeholder="Search Shippers..."
          v-model="searchQuery"
          @input="searchShippers"
          class="w-full"
        />
      </div>
      <div class="overflow-x-auto">
        <!-- Shipper table -->
        <table class="table-auto w-full">
          <thead>
            <tr>
              <th class="px-4 py-2">Name</th>
              <th class="px-4 py-2">City</th>
              <th class="px-4 py-2">State</th>
              <th class="px-4 py-2">Country</th>
            </tr>
          </thead>
          <tbody>
            <tr
              v-for="shipper in filteredShippers"
              :key="shipper.id"
              :class="{'bg-gray-100': shipper.id === selectedShipperId}"
              @click="selectShipper(shipper.id)"
              class="cursor-pointer hover:bg-gray-50"
            >
              <td class="px-4 py-2">{{ shipper.name }}</td>
              <td class="px-4 py-2">{{ shipper.city }}</td>
              <td class="px-4 py-2">{{ shipper.state }}</td>
              <td class="px-4 py-2">{{ shipper.country }}</td>
            </tr>
          </tbody>
        </table>

        <!-- Skeleton loading (if needed) -->
        <skeleton v-if="loadingShippers" class="mt-4" />
      </div>
    </div>

    <!-- Selected Shipper Details -->
    <div class="bg-white rounded-md shadow p-4" v-if="selectedShipperData">
      <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold">Shipper Details: {{ selectedShipperData.name }}</h2>
        <!-- Example button to list loads related to this shipper -->
        <Button @click="loadShipperLoads">View Loads</Button>
      </div>

      <!-- Tabs -->
      <div class="border-b mb-2 flex">
        <button
          v-for="tab in tabs"
          :key="tab"
          @click="activeTab = tab"
          :class="[
            'px-4 py-2',
            activeTab === tab ? 'border-b-2 border-blue-500 font-semibold' : ''
          ]"
        >
          {{ tab }}
        </button>
      </div>

      <!-- Tab Panels -->
      <div>
        <!-- General -->
        <div v-if="activeTab === 'General'">
          <p class="mb-2">[General Ship Information Here]</p>
          <Button class="mt-2" @click="editShipper">Edit Shipper</Button>
        </div>

        <!-- Accounting -->
        <div v-else-if="activeTab === 'Accounting'">
          <p>[Accounting Info Component Here]</p>
        </div>

        <!-- Locations -->
        <div v-else-if="activeTab === 'Locations'">
          <p>[Location List Component Here]</p>
          <Button class="mt-2" @click="addLocation">Add Location</Button>
        </div>

        <!-- Shipment History -->
        <div v-else-if="activeTab === 'Shipment History'">
          <p>[Shipment History for this Shipper]</p>
        </div>

        <!-- Contacts -->
        <div v-else-if="activeTab === 'Contacts'">
          <p>[Contacts Component]</p>
        </div>

        <!-- Notes -->
        <div v-else-if="activeTab === 'Notes'">
          <p>[Notes List Component]</p>
          <!-- Use something like the "Add note" UI from your shipment show page -->
          <Button class="mt-2" @click="addNote">Add Note</Button>
        </div>

        <!-- Documents -->
        <div v-else-if="activeTab === 'Documents'">
          <p>[Documents Component]</p>
        </div>
      </div>

      <!-- Skeleton loading (if needed) -->
      <skeleton v-else-if="loadingDetails" />
    </div>
  </div>
</template>

<script lang="ts">
import { defineComponent, ref, computed, onMounted } from 'vue'
import { Inertia } from '@inertiajs/inertia'
// Import your existing UI components
// import Button from '@/Components/Button' 
// import Input from '@/Components/Input'
// import skeleton from '@/Components/Skeleton'

export default defineComponent({
  setup() {
    const loadingShippers = ref(false)
    const loadingDetails = ref(false)
    const shippers = ref<any[]>([])
    const selectedShipperId = ref<number | null>(null)
    const selectedShipperData = ref<any>(null)

    const searchQuery = ref('')
    const tabs = ['General', 'Accounting', 'Locations', 'Shipment History', 'Contacts', 'Notes', 'Documents']
    const activeTab = ref('General')

    onMounted(() => {
      // Load initial list of shippers
      loadShippers()
    })

    function loadShippers() {
      loadingShippers.value = true
      // Replace with your inertia or axios call
      setTimeout(() => {
        // Demo data
        shippers.value = [
          { id: 1, name: 'ABC Logistics', city: 'Seattle', state: 'WA', country: 'USA' },
          { id: 2, name: 'XYZ Freight', city: 'Portland', state: 'OR', country: 'USA' },
          // ...
        ]
        loadingShippers.value = false
      }, 1000)
    }

    function selectShipper(id: number) {
      selectedShipperId.value = id
      loadShipperDetails(id)
    }

    function loadShipperDetails(id: number) {
      loadingDetails.value = true
      // Example async load. Replace with your inertia partial reload or axios call
      setTimeout(() => {
        selectedShipperData.value = {
          id,
          name: 'Dummy Shipper Name',
          // ...
        }
        loadingDetails.value = false
      }, 500)
    }

    // Filter shippers by search query
    const filteredShippers = computed(() => {
      return shippers.value.filter(s =>
        s.name.toLowerCase().includes(searchQuery.value.toLowerCase())
      )
    })

    function searchShippers() {
      // Potentially an API call or just local filtering
    }

    function editShipper() {
      // Show an edit form or route to an Edit page
    }

    function addLocation() {
      // Show a dialog or route to a create location flow
    }

    function addNote() {
      // Show a dialog or inline form for adding a new note
    }

    function loadShipperLoads() {
      // A button to see a list of loads related to the shipper, filter loads by active or not
      // Possibly trigger a modal or route
    }

    function goToCreateShipper() {
      Inertia.get(route('shippers.create'))
    }

    return {
      loadingShippers,
      loadingDetails,
      shippers,
      searchQuery,
      filteredShippers,
      selectedShipperId,
      selectedShipperData,
      tabs,
      activeTab,
      selectShipper,
      searchShippers,
      editShipper,
      addLocation,
      addNote,
      loadShipperLoads,
      goToCreateShipper,
    }
  },
})
</script> 