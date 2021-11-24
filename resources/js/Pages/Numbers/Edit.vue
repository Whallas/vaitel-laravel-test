<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link class="text-indigo-400 hover:text-indigo-600" :href="route('numbers.index')">Numbers</inertia-link>
      <span class="text-indigo-400 font-medium">/</span>
      {{ number.number | VMask($options.mask.formats.phone) }}
    </h1>
    <trashed-message v-if="number.deleted_at" class="mb-6" @restore="restore"> This number has been deleted. </trashed-message>
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
          <button v-if="!number.deleted_at" :disabled="isAnyFormProcessing" class="text-red-600 hover:underline" tabindex="-1" type="button" @click="destroy">Delete Number</button>
          <loading-button :loading="form.processing" :disabled="isAnyFormProcessing" class="btn-indigo ml-auto" type="submit">Update Number</loading-button>
        </div>
      </form>
    </div>

    <h2 class="mt-12 font-bold text-2xl">Preferences</h2>
    <div class="mt-6 bg-white rounded shadow overflow-x-auto max-w-4xl">
      <table class="w-full whitespace-nowrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Name</th>
          <th class="px-6 pt-6 pb-4">Value</th>
          <!-- eslint-disable-next-line vue/html-self-closing -->
          <th></th>
        </tr>
        <tr v-for="(preference, index) in number.preferences" :key="preference.id" class="group hover:bg-gray-100 focus-within:bg-gray-100" :class="{ 'bg-gray-50': index === preferenceEditingIndex }">
          <td v-if="index !== preferenceEditingIndex" class="border-t">
            <span class="px-6 py-4 flex items-center focus:text-indigo-500">
              {{ preference.name }}
            </span>
          </td>
          <td v-if="index !== preferenceEditingIndex" class="border-t">
            <span class="px-6 py-4 flex items-center" tabindex="-1">
              {{ preference.value }}
            </span>
          </td>
          <td v-if="index !== preferenceEditingIndex" class="border-t relative">
            <div class="hidden group-hover:flex absolute right-3 top-2 space-x-1 items-center">
              <button :disabled="isAnyFormProcessing" class="btn-indigo p-2 bg-red-600 hover:bg-red-400 focus:bg-red-400" tabindex="-1" type="button" title="Delete number preference" @click.prevent="destroyPreference(index)">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                </svg>
              </button>
              <button type="button" class="btn-indigo p-2" title="Update number preference" @click.prevent="preferenceEditingIndex = index; isCreatingPreference = false">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                </svg>
              </button>
            </div>
          </td>
          <td v-else-if="editPreferenceForm" class="border-t px-6 py-4" colspan="3">
            <form class="flex flex-col lg:flex-row items-center lg:items-end space-y-3 lg:space-y-0" @submit.prevent="updatePreference">
              <text-input v-model="editPreferenceForm.name" class="w-full lg:w-auto" :error="editPreferenceForm.errors.name" label="Name" />
              <text-input v-model="editPreferenceForm.value" class="lg:px-6 w-full lg:w-auto" :error="editPreferenceForm.errors.value" label="Value" />
              <div class="w-full lg:w-auto flex lg:flex-1 items-center justify-between lg:justify-center lg:space-x-4">
                <button :disabled="isAnyFormProcessing" class="text-red-600 hover:underline" tabindex="-1" type="button" @click.prevent="preferenceEditingIndex = null">Cancel</button>
                <loading-button :loading="editPreferenceForm.processing" :disabled="isUpdatePreferenceButtonDisabled" class="btn-indigo" type="submit">Update Preference</loading-button>
              </div>
            </form>
          </td>
        </tr>
        <tr v-if="!number.preferences.length">
          <td class="border-t px-6 py-4" colspan="3">No preferences found.</td>
        </tr>
        <!-- add preference button/form -->
        <tr :class="{ 'bg-gray-50': isCreatingPreference }">
          <td class="border-t px-6 py-4" colspan="3">
            <button v-if="!isCreatingPreference" :disabled="isAnyFormProcessing" class="flex items-center btn-indigo mx-auto" type="button" @click.prevent="isCreatingPreference = true; preferenceEditingIndex = null">
              <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd" />
              </svg>
              <span> Add</span>
            </button>
            <form v-else class="flex flex-col lg:flex-row items-center lg:items-end space-y-3 lg:space-y-0" @submit.prevent="storePreference">
              <text-input v-model="newPreferenceForm.name" class="w-full lg:w-auto" :error="newPreferenceForm.errors.name" label="Name" />
              <text-input v-model="newPreferenceForm.value" class="lg:px-6 w-full lg:w-auto" :error="newPreferenceForm.errors.value" label="Value" />
              <div class="w-full lg:w-auto flex lg:flex-1 items-center justify-between lg:justify-center lg:space-x-4">
                <button :disabled="isAnyFormProcessing" class="text-red-600 hover:underline" tabindex="-1" type="button" @click.prevent="isCreatingPreference = false">Cancel</button>
                <loading-button :loading="newPreferenceForm.processing" :disabled="isNewPreferenceButtonDisabled" class="btn-indigo" type="submit">Add Preference</loading-button>
              </div>
            </form>
          </td>
        </tr>
      </table>
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
      newPreferenceForm: this.$inertia.form({
        number_id: this.number.id,
        name: null,
        value: null,
      }),
      editPreferenceForm: null,
      preferenceEditingIndex: null,
      isCreatingPreference: false,
    }
  },
  computed: {
    isAnyFormProcessing() {
      return this.form.processing || this.newPreferenceForm.processing || (this.editPreferenceForm && this.editPreferenceForm.processing)
    },
    isNewPreferenceButtonDisabled() {
      return this.isAnyFormProcessing || !this.newPreferenceForm.name || !this.newPreferenceForm.value
    },
    isUpdatePreferenceButtonDisabled() {
      return this.isAnyFormProcessing || (this.editPreferenceForm && !this.editPreferenceForm.name) || !this.editPreferenceForm.value
    },
  },
  watch: {
    preferenceEditingIndex(index) {
      if (index !== null) {
        this.editPreferenceForm = this.$inertia.form({
          _method: 'put',
          number_id: this.number.preferences[index].number_id,
          name: this.number.preferences[index].name,
          value: this.number.preferences[index].value,
        })
      } else {
        this.editPreferenceForm = null
      }
    },
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
    storePreference() {
      this.newPreferenceForm.post(this.route('number-preferences.store'), {
        onSuccess: () => (this.isCreatingPreference = false),
        preserveState: (page) => Object.keys(page.props.errors).length,
      })
    },
    updatePreference() {
      this.editPreferenceForm.post(this.route('number-preferences.update', this.number.preferences[this.preferenceEditingIndex].id), {
        onSuccess: () => (this.preferenceEditingIndex = null),
        preserveState: (page) => Object.keys(page.props.errors).length,
      })
    },
    destroyPreference(index) {
      if (confirm('Are you sure you want to delete this preference?')) {
        this.$inertia.delete(this.route('number-preferences.destroy', this.number.preferences[index].id))
      }
    },
  },
  mask: {
    formats,
  },
}
</script>
