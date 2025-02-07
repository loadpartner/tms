<template>
  <div class="max-w-3xl mx-auto p-4">
    <h1 class="text-xl font-bold mb-6">Create New Shipper</h1>
    
    <form @submit.prevent="submitForm" class="space-y-4">
      <div>
        <label class="block mb-1 font-semibold">Name</label>
        <Input v-model="form.name" placeholder="Shipper Name" />
      </div>
      <div>
        <label class="block mb-1 font-semibold">City</label>
        <Input v-model="form.city" placeholder="City" />
      </div>
      <div>
        <label class="block mb-1 font-semibold">State</label>
        <Input v-model="form.state" placeholder="State/Province" />
      </div>
      <div>
        <label class="block mb-1 font-semibold">Country</label>
        <Input v-model="form.country" placeholder="Country" />
      </div>

      <Button type="submit">Create</Button>
    </form>
  </div>
</template>

<script lang="ts">
import { defineComponent, reactive } from 'vue'
import { Inertia } from '@inertiajs/inertia'

export default defineComponent({
  setup() {
    const form = reactive({
      name: '',
      city: '',
      state: '',
      country: '',
    })

    function submitForm() {
      // Example Inertia post request to create a new shipper
      Inertia.post(route('shippers.store'), form, {
        onFinish: () => {
          // Possibly handle success or show a flash message
        },
      })
    }

    return {
      form,
      submitForm,
    }
  },
})
</script> 