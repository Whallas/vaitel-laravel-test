<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">Numbers</h1>
    <div class="mb-6 flex justify-between items-center">
      <search-filter v-model="form.search" class="w-full max-w-md mr-4" @reset="reset">
        <label class="block text-gray-700">Trashed:</label>
        <select v-model="form.trashed" class="mt-1 w-full form-select">
          <option :value="null" />
          <option value="with">With Trashed</option>
          <option value="only">Only Trashed</option>
        </select>
      </search-filter>
      <inertia-link v-if="canCreate" class="btn-indigo" :href="route('numbers.create')">
        <span>Create</span>
        <span class="hidden md:inline">Number</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded-md shadow overflow-x-auto">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Number</th>
          <th class="px-6 pt-6 pb-4">Customer</th>
          <th class="px-6 pt-6 pb-4">Status</th>
        </tr>
        <tr v-for="number in numbers.data" :key="number.id" class="hover:bg-gray-100 focus-within:bg-gray-100">
          <td class="border-t">
            <component :is="getTdComponent(number)" class="px-6 py-4 flex items-center focus:text-indigo-500" :href="route('numbers.edit', number.id)">
              {{ number.number | VMask($options.mask.formats.phone) }}
              <icon v-if="number.deleted_at" name="trash" class="flex-shrink-0 w-3 h-3 fill-gray-400 ml-2" />
            </component>
          </td>
          <td class="border-t">
            <component :is="getTdComponent(number)" class="px-6 py-4 flex items-center" :href="route('numbers.edit', number.id)" tabindex="-1">
              <div v-if="number.customer">
                {{ number.customer.name }}
              </div>
            </component>
          </td>
          <td class="border-t">
            <component :is="getTdComponent(number)" class="px-6 py-4 flex items-center" :href="route('numbers.edit', number.id)" tabindex="-1">
              {{ number.status }}
            </component>
          </td>
          <td class="border-t w-px">
            <inertia-link v-if="number.can_i_edit" class="px-4 flex items-center" :href="route('numbers.edit', number.id)" tabindex="-1">
              <icon name="cheveron-right" class="block w-6 h-6 fill-gray-400" />
            </inertia-link>
          </td>
        </tr>
        <tr v-if="numbers.data.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No numbers found.</td>
        </tr>
      </table>
    </div>
    <pagination class="mt-6" :links="numbers.links" />
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
import { formats } from '../../utils/mask'

export default {
  metaInfo: { title: 'Numbers' },
  components: {
    Icon,
    Pagination,
    SearchFilter,
  },
  layout: Layout,
  props: {
    filters: Object,
    numbers: Object,
    canCreate: Boolean,
  },
  data() {
    return {
      form: {
        search: this.filters.search,
        trashed: this.filters.trashed,
      },
    }
  },
  watch: {
    form: {
      deep: true,
      handler: throttle(function () {
        this.$inertia.get(this.route('numbers.index'), pickBy(this.form), { preserveState: true })
      }, 150),
    },
  },
  methods: {
    reset() {
      this.form = mapValues(this.form, () => null)
    },
    getTdComponent(customer) {
      return customer.can_i_edit ? 'inertia-link' : 'span'
    },
  },
  mask: {
    formats,
  },
}
</script>
