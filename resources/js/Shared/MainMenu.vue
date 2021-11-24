<template>
  <div>
    <div class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('dashboard')">
        <icon name="dashboard" class="w-4 h-4 mr-2" :class="isUrl('') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Dashboard</div>
      </inertia-link>
    </div>
    <div v-if="user.canViewCustomers" class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('customers.index')">
        <icon name="office" class="w-4 h-4 mr-2" :class="isUrl('customers.index') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('customers.index') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Customers</div>
      </inertia-link>
    </div>
    <div v-if="user.canViewNumbers" class="mb-4">
      <inertia-link class="flex items-center group py-3" :href="route('numbers.index')">
        <icon name="users" class="w-4 h-4 mr-2" :class="isUrl('numbers.index') ? 'fill-white' : 'fill-indigo-400 group-hover:fill-white'" />
        <div :class="isUrl('numbers.index') ? 'text-white' : 'text-indigo-300 group-hover:text-white'">Numbers</div>
      </inertia-link>
    </div>
  </div>
</template>

<script>
import Icon from '@/Shared/Icon'

export default {
  components: {
    Icon,
  },
  computed: {
    user() {
      return this.$page.props.auth.user
    },
  },
  methods: {
    isUrl(...urls) {
      let currentUrl = this.$page.url.substr(1)
      if (currentUrl === '') {
        return urls.filter(url => url === currentUrl).length
      }
      return urls.filter(url => url.startsWith(currentUrl)).length
    },
  },
}
</script>
