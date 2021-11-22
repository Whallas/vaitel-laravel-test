<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('customers.index')">Customers</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ form.name }}
    </h1>
    <trashed-message v-if="customer.deleted_at" class="mb-6" @restore="restore">
      This customer has been deleted.
    </trashed-message>
    <div class="bg-white rounded-md shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="update">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.name" :error="form.errors.name" class="pr-6 pb-8 w-full lg:w-1/2" label="Name" />
          <text-input v-model="form.document" :error="form.errors.document" class="pr-6 pb-8 w-full lg:w-1/2" label="Document" />
          <select-input v-model="form.status" :error="form.errors.status" class="pr-6 pb-8 w-full lg:w-1/2" label="Status">
            <option value="new">New</option>
            <option value="active">Active</option>
            <option value="suspended">Suspended</option>
            <option value="cancelled">Cancelled</option>
          </select-input>
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
          <button v-if="!customer.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Customer</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Update Customer</loading-button>
        </div>
      </form>
    </div>
    <h2 class="mt-12 font-bold text-2xl">Numbers</h2>
    <div class="mt-6 bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Number</th>
          <th class="px-6 pt-6 pb-4">Status</th>
        </tr>
        <tr v-for="number in customer.numbers" :key="number.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('numbers.edit', number.id)">
              {{ number.number | VMask($options.mask.formats.phone) }}
              <icon v-if="number.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </inertia-link>
          </td>
          <td class="border-t">
            <inertia-link class="px-6 py-4 flex items-center" :href="route('numbers.edit', number.id)" tabindex="-1">
              {{ number.status }}
            </inertia-link>
          </td>
          <td class="border-t w-px">
            <inertia-link class="px-4 flex items-center" :href="route('numbers.edit', number.id)" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
        </tr>
        <tr v-if="customer.numbers.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No numbers found.</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'
import { formats } from '../../utils/mask'

export default {
  metaInfo() {
    return { title: this.form.name }
  },
  components: {
    Icon,
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
  },
  layout: Layout,
  props: {
    customer: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        name: this.customer.name,
        document: this.customer.document,
        status: this.customer.status,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(this.route('customers.update', this.customer.id))
    },
    destroy() {
      if (confirm('Are you sure you want to delete this customer?')) {
        this.$inertia.delete(this.route('customers.destroy', this.customer.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this customer?')) {
        this.$inertia.put(this.route('customers.restore', this.customer.id))
      }
    },
  },
  mask: {
    formats,
  },
}
</script>
