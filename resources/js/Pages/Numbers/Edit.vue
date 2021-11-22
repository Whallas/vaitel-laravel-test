<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('numbers.index')">Numbers</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ number.number | VMask($options.mask.formats.phone) }}
    </h1>
    <trashed-message v-if="number.deleted_at" class="mb-6" @restore="restore">
      This number has been deleted.
    </trashed-message>
    <div class="bg-white rounded-md shadow overflow-hidden max-w-3xl">
      <h2 v-if="number.customer" class="mt-4 pl-8 font-bold text-xl">
        <span class="text-indigo-400 font-bold">Customer:</span>
        <inertia-link class="font-medium" :href="route('customers.edit', number.customer.id)">
          {{ number.customer.name }}
        </inertia-link>
      </h2>
      <form @submit.prevent="update">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <text-input v-model="form.number" v-mask="$options.mask.formats.phone" :error="form.errors.number" class="pr-6 pb-8 w-full lg:w-1/2" label="Number" maxlength="14" />
          <select-input v-model="form.status" :error="form.errors.status" class="pr-6 pb-8 w-full lg:w-1/2" label="Status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
            <option value="cancelled">Cancelled</option>
          </select-input>
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex items-center">
          <button v-if="!number.deleted_at" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Number</button>
          <loading-button :loading="form.processing" class="btn-indigo ml-auto" type="submit">Update Number</loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Layout from '@/Shared/Layout'
import TextInput from '@/Shared/TextInput'
import SelectInput from '@/Shared/SelectInput'
import LoadingButton from '@/Shared/LoadingButton'
import TrashedMessage from '@/Shared/TrashedMessage'
import { formats } from '../../utils/mask'

export default {
  metaInfo() {
    return {
      title: `Customer number ${this.form.number}`,
    }
  },
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
    TrashedMessage,
  },
  layout: Layout,
  props: {
    number: Object,
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        number: this.number.number,
        status: this.number.status,
      }),
    }
  },
  methods: {
    update() {
      this.form.put(this.route('numbers.update', this.number.id))
    },
    destroy() {
      if (confirm('Are you sure you want to delete this number?')) {
        this.$inertia.delete(this.route('numbers.destroy', this.number.id))
      }
    },
    restore() {
      if (confirm('Are you sure you want to restore this number?')) {
        this.$inertia.put(this.route('numbers.restore', this.number.id))
      }
    },
  },
  mask: {
    formats,
  },
}
</script>
