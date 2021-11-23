<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('numbers.index')">Numbers</inertia-link>
      <span class="text-indigo-400 font-medium">/</span> Create
    </h1>
    <div class="bg-white rounded-md shadow overflow-hidden max-w-3xl">
      <form @submit.prevent="store">
        <div class="p-8 -mr-6 -mb-8 flex flex-wrap">
          <select-input v-model="form.customer_id" :error="form.errors.customer_id" class="pr-6 pb-8 w-full lg:w-1/2" label="Customer">
            <option :value="null" />
            <option v-for="customer in customers" :key="customer.id" :value="customer.id">{{ customer.name }}</option>
          </select-input>
          <text-input v-model="form.number" v-mask="$options.mask.formats.phone" :error="form.errors.number" class="pr-6 pb-8 lg:w-1/2" label="Number" maxlength="14" />
          <select-input v-model="form.status" :error="form.errors.status" class="pr-6 pb-8 w-full lg:w-1/2" label="Status">
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select-input>
        </div>
        <div class="px-8 py-4 bg-gray-50 border-t border-gray-100 flex justify-end items-center">
          <loading-button :loading="form.processing" class="btn-indigo" type="submit">Create Number</loading-button>
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
import { formats } from '../../utils/mask'

export default {
  metaInfo: { title: 'Create Number' },
  components: {
    LoadingButton,
    SelectInput,
    TextInput,
  },
  layout: Layout,
  props: {
    customers: Array,
    customerId: {
      type: [Number, String],
      default: null,
    },
  },
  remember: 'form',
  data() {
    return {
      form: this.$inertia.form({
        number: null,
        customer_id: this.customerId,
        status: 'active',
      }),
    }
  },
  methods: {
    store() {
      this.form.post(this.route('numbers.store'))
    },
  },
  mask: {
    formats,
  },
}
</script>
