<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Customers</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
      <inertia-link v-if="canCreate" class="btn-indigo" :href="route('customers.create')">
        <span>Create</span>
        <span class="hidden md:inline">Customer</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Name</th>
          <th class="px-6 pt-6 pb-4">Document</th>
          <th class="px-6 pt-6 pb-4" colspan="2">Status</th>
        </tr>
        <tr v-for="customer in customers.data" :key="customer.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <component :is="getTdComponent(customer)" class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('customers.edit', customer.id)">
              {{ customer.name }}
              <icon v-if="customer.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </component>
          </td>
          <td class="border-t">
            <component :is="getTdComponent(customer)" class="px-6 py-4 flex items-center" :href="route('customers.edit', customer.id)" tabindex="-1">
              {{ customer.document }}
            </component>
          </td>
          <td class="border-t">
            <component :is="getTdComponent(customer)" class="px-6 py-4 flex items-center" :href="route('customers.edit', customer.id)" tabindex="-1">
              {{ customer.status }}
            </component>
          </td>
          <td class="border-t w-px">
            <inertia-link v-if="getTdComponent(customer)" class="px-4 flex items-center" :href="route('customers.edit', customer.id)" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
        </tr>
        <tr v-if="customers.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No customers found.</td>
        </tr>
      </table>
    </div>
    <pagination class="mt-6" :links="customers.links" />
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'
import pickBy from 'lodash/pickBy'
import Layout from '@/Shared/Layout'
import throttle from 'lodash/throttle'
import mapValues from 'lodash/mapValues'
import Pagination from '@/Shared/Pagination'
import SearchFilter from '@/Shared/SearchFilter'

export default {
  metaInfo: { title: 'Customers' },
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  layout: Layout,
  props: {
    filters: Object,
    customers: Object,
    canCreate: Boolean,
    canEdit: Boolean,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
      },
    }
  },
  computed: {
    user() {
      return this.$page.props.auth.user
    },
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function() {
        this.$inertia.get(this.route('customers.index'), pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    getTdComponent(customer) {
      return customer.can_edit ? 'inertia-link' : 'span'
    },
  },
}
</script>
