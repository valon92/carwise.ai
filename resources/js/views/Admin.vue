<template>
  <div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Header -->
    <div class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center py-6">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">
              {{ t('admin_panel') }}
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
              {{ t('manage_system_overview') }}
            </p>
          </div>
          <div class="flex items-center space-x-4">
            <button
              @click="refreshData"
              class="btn-secondary flex items-center space-x-2"
              :disabled="isLoading"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
              </svg>
              <span>{{ t('refresh') }}</span>
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Navigation Tabs -->
    <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex space-x-8">
          <button
            v-for="tab in tabs"
            :key="tab.id"
            @click="activeTab = tab.id"
            :class="[
              'py-4 px-1 border-b-2 font-medium text-sm transition-colors duration-200',
              activeTab === tab.id
                ? 'border-primary-500 text-primary-600 dark:text-primary-400'
                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'
            ]"
          >
            {{ tab.name }}
          </button>
        </nav>
      </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <!-- Dashboard Tab -->
      <div v-if="activeTab === 'dashboard'" class="space-y-6">
        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          <div
            v-for="stat in dashboardStats"
            :key="stat.title"
            class="bg-white dark:bg-gray-800 rounded-lg shadow p-6"
          >
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div :class="['w-8 h-8 rounded-md flex items-center justify-center', stat.bgColor]">
                  <component :is="stat.icon" class="w-5 h-5 text-white" />
                </div>
              </div>
              <div class="ml-4">
                <p class="text-sm font-medium text-gray-500 dark:text-gray-400">
                  {{ stat.title }}
                </p>
                <p class="text-2xl font-semibold text-gray-900 dark:text-white">
                  {{ stat.value }}
                </p>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">
              {{ t('recent_activity') }}
            </h3>
          </div>
          <div class="p-6">
            <div class="space-y-4">
              <div
                v-for="activity in recentActivities"
                :key="activity.id"
                class="flex items-center space-x-4"
              >
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                  </div>
                </div>
                <div class="flex-1 min-w-0">
                  <p class="text-sm text-gray-900 dark:text-white">
                    {{ activity.message }}
                  </p>
                  <p class="text-sm text-gray-500 dark:text-gray-400">
                    {{ formatTime(activity.timestamp) }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Users Tab -->
      <div v-if="activeTab === 'users'" class="space-y-6">
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
          <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex justify-between items-center">
              <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                {{ t('users_management') }}
              </h3>
              <div class="flex space-x-4">
                <select
                  v-model="userFilters.role"
                  class="input-sm"
                >
                  <option value="all">{{ t('all_roles') }}</option>
                  <option value="customer">{{ t('customers') }}</option>
                  <option value="mechanic">{{ t('mechanics') }}</option>
                  <option value="admin">{{ t('admins') }}</option>
                </select>
                <input
                  v-model="userFilters.search"
                  type="text"
                  :placeholder="t('search_users')"
                  class="input-sm"
                />
              </div>
            </div>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
              <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    {{ t('user') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    {{ t('role') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    {{ t('status') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    {{ t('joined') }}
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                    {{ t('actions') }}
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                <tr v-for="user in users" :key="user.id">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-10 w-10">
                        <div class="h-10 w-10 rounded-full bg-primary-100 dark:bg-primary-900 flex items-center justify-center">
                          <span class="text-sm font-medium text-primary-600 dark:text-primary-400">
                            {{ user.first_name ? user.first_name[0] : user.name[0] }}
                          </span>
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900 dark:text-white">
                          {{ user.first_name ? `${user.first_name} ${user.last_name}` : user.name }}
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">
                          {{ user.email }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="[
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                      user.role === 'admin' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' :
                      user.role === 'mechanic' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' :
                      'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200'
                    ]">
                      {{ t(user.role) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="[
                      'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                      user.status === 'active' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' :
                      'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                    ]">
                      {{ t(user.status) }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                    {{ formatDate(user.created_at) }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <select
                      :value="user.status"
                      @change="updateUserStatus(user.id, $event.target.value)"
                      class="input-sm"
                    >
                      <option value="active">{{ t('active') }}</option>
                      <option value="inactive">{{ t('inactive') }}</option>
                      <option value="suspended">{{ t('suspended') }}</option>
                    </select>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>

      <!-- Other tabs content would go here -->
      <div v-if="activeTab === 'cars'" class="text-center py-12">
        <p class="text-gray-500 dark:text-gray-400">{{ t('cars_management_coming_soon') }}</p>
      </div>
      
      <div v-if="activeTab === 'diagnoses'" class="text-center py-12">
        <p class="text-gray-500 dark:text-gray-400">{{ t('diagnoses_management_coming_soon') }}</p>
      </div>
      
      <div v-if="activeTab === 'settings'" class="text-center py-12">
        <p class="text-gray-500 dark:text-gray-400">{{ t('system_settings_coming_soon') }}</p>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, computed } from 'vue'
import { t } from '../utils/translations'
import api from '../services/api'

export default {
  name: 'Admin',
  setup() {
    const activeTab = ref('dashboard')
    const isLoading = ref(false)
    const users = ref([])
    const recentActivities = ref([])
    const userFilters = ref({
      role: 'all',
      status: 'all',
      search: ''
    })

    const tabs = [
      { id: 'dashboard', name: t('dashboard') },
      { id: 'users', name: t('users') },
      { id: 'cars', name: t('cars') },
      { id: 'diagnoses', name: t('diagnoses') },
      { id: 'settings', name: t('settings') }
    ]

    const dashboardStats = ref([
      {
        title: t('total_users'),
        value: 0,
        icon: 'svg',
        bgColor: 'bg-blue-500'
      },
      {
        title: t('total_cars'),
        value: 0,
        icon: 'svg',
        bgColor: 'bg-green-500'
      },
      {
        title: t('total_diagnoses'),
        value: 0,
        icon: 'svg',
        bgColor: 'bg-yellow-500'
      },
      {
        title: t('active_mechanics'),
        value: 0,
        icon: 'svg',
        bgColor: 'bg-purple-500'
      }
    ])

    const loadDashboardData = async () => {
      try {
        isLoading.value = true
        const response = await api.get('/admin/dashboard')
        if (response.data.success) {
          const stats = response.data.data
          dashboardStats.value = [
            {
              title: t('total_users'),
              value: stats.users.total,
              icon: 'svg',
              bgColor: 'bg-blue-500'
            },
            {
              title: t('total_cars'),
              value: stats.cars.total,
              icon: 'svg',
              bgColor: 'bg-green-500'
            },
            {
              title: t('total_diagnoses'),
              value: stats.diagnoses.total,
              icon: 'svg',
              bgColor: 'bg-yellow-500'
            },
            {
              title: t('active_mechanics'),
              value: stats.users.mechanics,
              icon: 'svg',
              bgColor: 'bg-purple-500'
            }
          ]
        }
      } catch (error) {
        console.error('Error loading dashboard data:', error)
      } finally {
        isLoading.value = false
      }
    }

    const loadUsers = async () => {
      try {
        const response = await api.get('/admin/users', {
          params: userFilters.value
        })
        if (response.data.success) {
          users.value = response.data.data.data
        }
      } catch (error) {
        console.error('Error loading users:', error)
      }
    }

    const loadSystemLogs = async () => {
      try {
        const response = await api.get('/admin/system-logs')
        if (response.data.success) {
          recentActivities.value = response.data.data
        }
      } catch (error) {
        console.error('Error loading system logs:', error)
      }
    }

    const updateUserStatus = async (userId, status) => {
      try {
        await api.put(`/admin/users/${userId}/status`, { status })
        await loadUsers()
      } catch (error) {
        console.error('Error updating user status:', error)
      }
    }

    const refreshData = () => {
      if (activeTab.value === 'dashboard') {
        loadDashboardData()
        loadSystemLogs()
      } else if (activeTab.value === 'users') {
        loadUsers()
      }
    }

    const formatDate = (date) => {
      return new Date(date).toLocaleDateString()
    }

    const formatTime = (timestamp) => {
      return new Date(timestamp).toLocaleString()
    }

    onMounted(() => {
      loadDashboardData()
      loadSystemLogs()
    })

    return {
      activeTab,
      isLoading,
      users,
      recentActivities,
      userFilters,
      tabs,
      dashboardStats,
      loadUsers,
      updateUserStatus,
      refreshData,
      formatDate,
      formatTime,
      t
    }
  }
}
</script>
