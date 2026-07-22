<template>
  <div class="min-h-screen bg-gradient-to-br from-secondary-50 via-white to-primary-50 dark:from-secondary-950 dark:via-secondary-900 dark:to-primary-950">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-8">
        <div>
          <h1 class="text-3xl sm:text-4xl font-bold text-secondary-900 dark:text-white flex items-center gap-3">
            <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/40 rounded-xl flex items-center justify-center">
              <svg class="w-6 h-6 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
              </svg>
            </div>
            Vehicle Twin
          </h1>
          <p class="mt-2 text-secondary-600 dark:text-secondary-400">
            Your digital vehicle profiles — powered by VIN identification
          </p>
        </div>
        <button
          @click="showRegisterModal = true"
          class="mt-4 sm:mt-0 inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl shadow-lg shadow-primary-500/25 transition-all duration-200"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Add Vehicle
        </button>
      </div>

      <!-- Loading -->
      <div v-if="isLoading && vehicles.length === 0" class="flex justify-center py-20">
        <div class="animate-spin rounded-full h-12 w-12 border-4 border-primary-200 border-t-primary-600"></div>
      </div>

      <!-- Empty state -->
      <div v-else-if="vehicles.length === 0" class="text-center py-20">
        <div class="w-20 h-20 bg-primary-100 dark:bg-primary-900/30 rounded-2xl flex items-center justify-center mx-auto mb-6">
          <svg class="w-10 h-10 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z" />
          </svg>
        </div>
        <h3 class="text-xl font-semibold text-secondary-900 dark:text-white mb-2">No vehicles yet</h3>
        <p class="text-secondary-500 dark:text-secondary-400 max-w-md mx-auto mb-6">
          Add your first vehicle using its VIN to create a digital twin — a permanent, intelligent profile for diagnostics and maintenance.
        </p>
        <button
          @click="showRegisterModal = true"
          class="inline-flex items-center gap-2 px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl shadow-lg shadow-primary-500/25 transition-all duration-200"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          Register Your First Vehicle
        </button>
      </div>

      <!-- Vehicle grid -->
      <div v-else class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div
          v-for="vehicle in vehicles"
          :key="vehicle.id"
          class="bg-white dark:bg-secondary-800 rounded-2xl shadow-soft border border-secondary-200 dark:border-secondary-700 p-6 hover:shadow-lg transition-all duration-300 group"
        >
          <!-- Badge -->
          <div class="flex items-center justify-between mb-4">
            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
              Active
            </span>
            <div class="flex items-center gap-1">
              <button
                @click="editVehicle(vehicle)"
                class="p-1.5 rounded-lg text-secondary-400 hover:text-primary-600 hover:bg-primary-50 dark:hover:bg-primary-900/20 transition-colors"
                title="Edit"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                </svg>
              </button>
              <button
                @click="confirmArchive(vehicle)"
                class="p-1.5 rounded-lg text-secondary-400 hover:text-danger-600 hover:bg-danger-50 dark:hover:bg-danger-900/20 transition-colors"
                title="Archive"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                </svg>
              </button>
            </div>
          </div>

          <!-- Vehicle info -->
          <div class="mb-4">
            <h3 class="text-lg font-bold text-secondary-900 dark:text-white">
              {{ vehicle.nickname || displayName(vehicle) }}
            </h3>
            <p v-if="vehicle.nickname && vehicle.brand" class="text-sm text-secondary-500 dark:text-secondary-400">
              {{ displayName(vehicle) }}
            </p>
          </div>

          <!-- VIN -->
          <div class="bg-secondary-50 dark:bg-secondary-900 rounded-xl p-3 mb-4">
            <div class="text-xs text-secondary-500 dark:text-secondary-400 uppercase tracking-wider mb-1">VIN</div>
            <div class="font-mono text-sm text-secondary-800 dark:text-secondary-200 break-all">{{ vehicle.vin }}</div>
          </div>

          <div
            v-if="vehicle.latest_vin_decode || vehicle.brand || vehicle.model"
            class="mb-4 rounded-xl border border-primary-100 bg-primary-50/80 p-3 dark:border-primary-900/40 dark:bg-primary-900/10"
          >
            <div class="mb-2 flex items-center justify-between gap-2">
              <span class="text-xs font-semibold uppercase tracking-wide text-primary-700 dark:text-primary-300">
                VIN Identified
              </span>
              <button
                @click="runIdentification(vehicle)"
                class="text-xs font-medium text-primary-700 hover:text-primary-800 dark:text-primary-300 dark:hover:text-primary-200"
              >
                Refresh
              </button>
            </div>
            <p class="text-sm font-medium text-secondary-900 dark:text-white">
              {{ displayName(vehicle) }}
            </p>
            <p
              v-if="vehicle.latest_vin_decode?.provider"
              class="mt-1 text-xs text-secondary-500 dark:text-secondary-400"
            >
              Source: {{ vehicle.latest_vin_decode.provider.toUpperCase() }}
            </p>
          </div>

          <!-- Details grid -->
          <div class="grid grid-cols-2 gap-3 text-sm">
            <div v-if="vehicle.year">
              <span class="text-secondary-500 dark:text-secondary-400">Year</span>
              <p class="font-medium text-secondary-800 dark:text-secondary-200">{{ vehicle.year }}</p>
            </div>
            <div v-if="vehicle.fuel_type">
              <span class="text-secondary-500 dark:text-secondary-400">Fuel</span>
              <p class="font-medium text-secondary-800 dark:text-secondary-200">{{ vehicle.fuel_type }}</p>
            </div>
            <div v-if="vehicle.engine">
              <span class="text-secondary-500 dark:text-secondary-400">Engine</span>
              <p class="font-medium text-secondary-800 dark:text-secondary-200">{{ vehicle.engine }}</p>
            </div>
            <div v-if="vehicle.current_mileage">
              <span class="text-secondary-500 dark:text-secondary-400">Mileage</span>
              <p class="font-medium text-secondary-800 dark:text-secondary-200">{{ vehicle.current_mileage.toLocaleString() }} km</p>
            </div>
            <div v-if="vehicle.transmission">
              <span class="text-secondary-500 dark:text-secondary-400">Transmission</span>
              <p class="font-medium text-secondary-800 dark:text-secondary-200">{{ vehicle.transmission }}</p>
            </div>
            <div v-if="vehicle.horsepower">
              <span class="text-secondary-500 dark:text-secondary-400">Power</span>
              <p class="font-medium text-secondary-800 dark:text-secondary-200">{{ vehicle.horsepower }} HP</p>
            </div>
          </div>

          <!-- Connector + diagnostics -->
          <div class="mt-4 pt-4 border-t border-secondary-100 dark:border-secondary-700">
            <div class="flex items-center justify-between gap-3 mb-3">
              <div>
                <p class="text-xs font-semibold uppercase tracking-wide text-secondary-500 dark:text-secondary-400">
                  Smart Connector
                </p>
                <p class="text-xs text-secondary-400 dark:text-secondary-500">
                  Status: {{ connectorLabel(vehicle.connector_state) }}
                </p>
              </div>
              <button
                @click="openDiagnostics(vehicle)"
                class="text-xs font-medium text-primary-600 hover:text-primary-700 dark:text-primary-400 dark:hover:text-primary-300"
              >
                {{ activeDiagnosticsVehicleId === vehicle.id ? 'Hide panel' : 'Open panel' }}
              </button>
            </div>

            <div v-if="activeDiagnosticsVehicleId === vehicle.id" class="space-y-3 rounded-xl bg-secondary-50 p-3 dark:bg-secondary-900">
              <div class="flex flex-wrap gap-2">
                <button
                  @click="pairVehicleConnector(vehicle)"
                  class="px-3 py-1.5 rounded-lg bg-primary-600 text-white text-xs font-medium hover:bg-primary-700 transition-colors"
                >
                  Pair Connector
                </button>
                <button
                  @click="revokeVehicleConnector(vehicle)"
                  class="px-3 py-1.5 rounded-lg border border-secondary-300 dark:border-secondary-600 text-xs font-medium text-secondary-700 dark:text-secondary-300 hover:bg-secondary-100 dark:hover:bg-secondary-800 transition-colors"
                >
                  Revoke
                </button>
                <button
                  @click="runIdentification(vehicle)"
                  class="px-3 py-1.5 rounded-lg border border-primary-200 bg-white text-xs font-medium text-primary-700 hover:bg-primary-50 dark:border-primary-700 dark:bg-secondary-800 dark:text-primary-300"
                >
                  Re-identify VIN
                </button>
              </div>

              <form @submit.prevent="submitManualScan(vehicle)" class="space-y-3">
                <div class="grid grid-cols-2 gap-3">
                  <input
                    v-model="manualScans[vehicle.id].mileage"
                    type="number"
                    min="0"
                    class="w-full rounded-lg border border-secondary-300 dark:border-secondary-600 bg-white dark:bg-secondary-800 px-3 py-2 text-sm text-secondary-900 dark:text-white"
                    placeholder="Mileage"
                  />
                  <input
                    v-model="manualScans[vehicle.id].scan_date"
                    type="datetime-local"
                    class="w-full rounded-lg border border-secondary-300 dark:border-secondary-600 bg-white dark:bg-secondary-800 px-3 py-2 text-sm text-secondary-900 dark:text-white"
                  />
                </div>
                <textarea
                  v-model="manualScans[vehicle.id].engine_dtcs"
                  rows="2"
                  class="w-full rounded-lg border border-secondary-300 dark:border-secondary-600 bg-white dark:bg-secondary-800 px-3 py-2 text-sm text-secondary-900 dark:text-white"
                  placeholder="Engine DTCs, e.g. P0300 P0420"
                />
                <textarea
                  v-model="manualScans[vehicle.id].abs_errors"
                  rows="2"
                  class="w-full rounded-lg border border-secondary-300 dark:border-secondary-600 bg-white dark:bg-secondary-800 px-3 py-2 text-sm text-secondary-900 dark:text-white"
                  placeholder="ABS errors"
                />
                <textarea
                  v-model="manualScans[vehicle.id].airbag_errors"
                  rows="2"
                  class="w-full rounded-lg border border-secondary-300 dark:border-secondary-600 bg-white dark:bg-secondary-800 px-3 py-2 text-sm text-secondary-900 dark:text-white"
                  placeholder="Airbag errors"
                />
                <textarea
                  v-model="manualScans[vehicle.id].transmission_errors"
                  rows="2"
                  class="w-full rounded-lg border border-secondary-300 dark:border-secondary-600 bg-white dark:bg-secondary-800 px-3 py-2 text-sm text-secondary-900 dark:text-white"
                  placeholder="Transmission errors"
                />
                <textarea
                  v-model="manualScans[vehicle.id].notes"
                  rows="2"
                  class="w-full rounded-lg border border-secondary-300 dark:border-secondary-600 bg-white dark:bg-secondary-800 px-3 py-2 text-sm text-secondary-900 dark:text-white"
                  placeholder="Technician or driver notes"
                />
                <div class="flex items-center justify-between gap-3">
                  <p class="text-xs text-secondary-500 dark:text-secondary-400">
                    Latest scans: {{ vehicle.recent_scans?.length || 0 }}
                  </p>
                  <button
                    type="submit"
                    class="px-3 py-2 rounded-lg bg-secondary-900 text-white text-xs font-medium hover:bg-secondary-800 dark:bg-primary-600 dark:hover:bg-primary-700"
                  >
                    Save Manual Scan
                  </button>
                </div>
              </form>

              <div v-if="vehicle.recent_scans?.length" class="space-y-2">
                <div
                  v-for="scan in vehicle.recent_scans.slice(0, 3)"
                  :key="scan.id"
                  class="rounded-lg border border-secondary-200 bg-white p-3 text-xs dark:border-secondary-700 dark:bg-secondary-800"
                >
                  <div class="flex items-center justify-between gap-2">
                    <span class="font-semibold text-secondary-800 dark:text-secondary-200">
                      {{ formatScanDate(scan.scan_date) }}
                    </span>
                    <span class="uppercase tracking-wide text-secondary-500 dark:text-secondary-400">
                      {{ scan.source }}
                    </span>
                  </div>
                  <p class="mt-1 text-secondary-600 dark:text-secondary-300">
                    Engine: {{ joinCodes(scan.engine_dtcs) }} | ABS: {{ joinCodes(scan.abs_errors) }}
                  </p>
                  <button
                    @click="runAiAnalysis(vehicle, scan)"
                    :disabled="analyzingScanId === scan.id"
                    class="mt-2 inline-flex items-center gap-1 rounded-lg bg-primary-600 px-2.5 py-1.5 text-xs font-medium text-white hover:bg-primary-700 disabled:opacity-50"
                  >
                    <span v-if="analyzingScanId === scan.id" class="inline-block h-3 w-3 animate-spin rounded-full border-2 border-white/30 border-t-white"></span>
                    {{ analyzingScanId === scan.id ? 'Analyzing…' : 'AI Analyze' }}
                  </button>
                </div>
              </div>

              <div
                v-if="vehicle.latest_analysis"
                class="rounded-xl border border-amber-200 bg-amber-50/80 p-3 dark:border-amber-900/40 dark:bg-amber-900/10"
              >
                <div class="mb-2 flex items-center justify-between gap-2">
                  <span class="text-xs font-semibold uppercase tracking-wide text-amber-700 dark:text-amber-300">
                    AI Diagnostic Assistant
                  </span>
                  <span
                    class="rounded-full px-2 py-0.5 text-[10px] font-bold uppercase tracking-wide"
                    :class="severityClass(vehicle.latest_analysis.severity)"
                  >
                    {{ vehicle.latest_analysis.severity }}
                  </span>
                </div>
                <p class="text-sm font-medium text-secondary-900 dark:text-white">
                  {{ vehicle.latest_analysis.problem_description }}
                </p>
                <p class="mt-2 text-xs text-secondary-600 dark:text-secondary-300">
                  {{ vehicle.latest_analysis.safety_recommendation }}
                </p>
                <div class="mt-2 grid grid-cols-2 gap-2 text-xs text-secondary-600 dark:text-secondary-300">
                  <div>
                    Drive OK:
                    <span class="font-semibold">{{ vehicle.latest_analysis.can_continue_driving ? 'Yes' : 'No' }}</span>
                  </div>
                  <div>
                    Confidence:
                    <span class="font-semibold">{{ formatConfidence(vehicle.latest_analysis.confidence_score) }}</span>
                  </div>
                  <div v-if="vehicle.latest_analysis.estimated_repair_cost_min != null" class="col-span-2">
                    Est. cost:
                    <span class="font-semibold">
                      €{{ vehicle.latest_analysis.estimated_repair_cost_min }}
                      –
                      €{{ vehicle.latest_analysis.estimated_repair_cost_max }}
                    </span>
                    <span v-if="vehicle.latest_analysis.estimated_repair_time_hours">
                      · ~{{ vehicle.latest_analysis.estimated_repair_time_hours }}h
                    </span>
                  </div>
                </div>
                <ul
                  v-if="vehicle.latest_analysis.possible_causes?.length"
                  class="mt-2 list-disc space-y-1 pl-4 text-xs text-secondary-600 dark:text-secondary-300"
                >
                  <li v-for="(cause, idx) in vehicle.latest_analysis.possible_causes.slice(0, 3)" :key="idx">
                    {{ cause }}
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Register Modal -->
      <div v-if="showRegisterModal" class="fixed inset-0 z-50 flex items-center justify-center px-4">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="closeModal"></div>
        <div class="relative bg-white dark:bg-secondary-800 rounded-2xl shadow-2xl w-full max-w-lg p-6 sm:p-8">
          <button @click="closeModal" class="absolute top-4 right-4 p-2 rounded-lg text-secondary-400 hover:text-secondary-600 hover:bg-secondary-100 dark:hover:bg-secondary-700 transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>

          <h2 class="text-2xl font-bold text-secondary-900 dark:text-white mb-1">
            {{ editingVehicle ? 'Edit Vehicle' : 'Register Vehicle' }}
          </h2>
          <p class="text-sm text-secondary-500 dark:text-secondary-400 mb-6">
            {{ editingVehicle ? 'Update your vehicle details.' : 'Enter the VIN to create a digital twin for your vehicle.' }}
          </p>

          <form @submit.prevent="submitForm" class="space-y-5">
            <!-- VIN -->
            <div>
              <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">
                VIN Number <span class="text-danger-500">*</span>
              </label>
              <input
                v-model="form.vin"
                type="text"
                maxlength="17"
                :disabled="!!editingVehicle"
                class="w-full px-4 py-2.5 rounded-xl border border-secondary-300 dark:border-secondary-600 bg-white dark:bg-secondary-900 text-secondary-900 dark:text-white font-mono uppercase tracking-wider focus:ring-2 focus:ring-primary-500 focus:border-primary-500 disabled:opacity-50 transition-colors"
                placeholder="e.g. 1HGBH41JXMN109186"
              />
              <p class="mt-1 text-xs text-secondary-400">17 characters — letters and numbers (no I, O, Q)</p>
            </div>

            <!-- Nickname -->
            <div>
              <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Nickname</label>
              <input
                v-model="form.nickname"
                type="text"
                maxlength="100"
                class="w-full px-4 py-2.5 rounded-xl border border-secondary-300 dark:border-secondary-600 bg-white dark:bg-secondary-900 text-secondary-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                placeholder='e.g. "Family SUV"'
              />
            </div>

            <!-- License plate + Mileage -->
            <div class="grid grid-cols-2 gap-4">
              <div>
                <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">License Plate</label>
                <input
                  v-model="form.license_plate"
                  type="text"
                  maxlength="20"
                  class="w-full px-4 py-2.5 rounded-xl border border-secondary-300 dark:border-secondary-600 bg-white dark:bg-secondary-900 text-secondary-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                  placeholder="AB-123-CD"
                />
              </div>
              <div>
                <label class="block text-sm font-medium text-secondary-700 dark:text-secondary-300 mb-1">Mileage (km)</label>
                <input
                  v-model.number="form.current_mileage"
                  type="number"
                  min="0"
                  class="w-full px-4 py-2.5 rounded-xl border border-secondary-300 dark:border-secondary-600 bg-white dark:bg-secondary-900 text-secondary-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-primary-500 transition-colors"
                  placeholder="84200"
                />
              </div>
            </div>

            <!-- Error -->
            <div v-if="formError" class="bg-danger-50 dark:bg-danger-900/20 text-danger-700 dark:text-danger-400 text-sm rounded-xl p-3">
              {{ formError }}
            </div>

            <!-- Buttons -->
            <div class="flex justify-end gap-3 pt-2">
              <button
                type="button"
                @click="closeModal"
                class="px-5 py-2.5 rounded-xl border border-secondary-300 dark:border-secondary-600 text-secondary-700 dark:text-secondary-300 hover:bg-secondary-50 dark:hover:bg-secondary-700 transition-colors font-medium"
              >
                Cancel
              </button>
              <button
                type="submit"
                :disabled="isLoading"
                class="px-5 py-2.5 rounded-xl bg-primary-600 hover:bg-primary-700 text-white font-medium shadow-lg shadow-primary-500/25 transition-all duration-200 disabled:opacity-50 flex items-center gap-2"
              >
                <div v-if="isLoading" class="animate-spin rounded-full h-4 w-4 border-2 border-white/30 border-t-white"></div>
                {{ editingVehicle ? 'Save Changes' : 'Register Vehicle' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, reactive } from 'vue'
import { useVehicleTwin } from '../composables/useVehicleTwin'

export default {
  name: 'VehicleTwin',
  setup() {
    const {
      vehicles,
      isLoading,
      loadVehicles,
      createVehicle,
      updateVehicle,
      archiveVehicle,
      identifyVehicle,
      loadConnectorStatus,
      pairConnector,
      revokeConnector,
      loadScans,
      createManualScan,
      analyzeScan,
      loadAnalyses,
    } = useVehicleTwin()

    const showRegisterModal = ref(false)
    const editingVehicle = ref(null)
    const formError = ref(null)
    const activeDiagnosticsVehicleId = ref(null)
    const analyzingScanId = ref(null)
    const manualScans = reactive({})
    const form = ref({ vin: '', nickname: '', license_plate: '', current_mileage: null })

    const ensureManualScanForm = (vehicleId) => {
      if (!manualScans[vehicleId]) {
        manualScans[vehicleId] = {
          mileage: '',
          scan_date: '',
          engine_dtcs: '',
          abs_errors: '',
          airbag_errors: '',
          transmission_errors: '',
          notes: '',
        }
      }
    }

    const resetForm = () => {
      form.value = { vin: '', nickname: '', license_plate: '', current_mileage: null }
      formError.value = null
      editingVehicle.value = null
    }

    const closeModal = () => {
      showRegisterModal.value = false
      resetForm()
    }

    const displayName = (v) => {
      const parts = [v.year, v.brand, v.model].filter(Boolean)
      return parts.length ? parts.join(' ') : `Vehicle ${v.vin}`
    }

    const editVehicle = (vehicle) => {
      editingVehicle.value = vehicle
      form.value = {
        vin: vehicle.vin,
        nickname: vehicle.nickname || '',
        license_plate: vehicle.license_plate || '',
        current_mileage: vehicle.current_mileage,
      }
      showRegisterModal.value = true
    }

    const confirmArchive = async (vehicle) => {
      const name = vehicle.nickname || displayName(vehicle)
      if (confirm(`Archive "${name}"? History will be preserved.`)) {
        await archiveVehicle(vehicle.id)
      }
    }

    const runIdentification = async (vehicle) => {
      const result = await identifyVehicle(vehicle.id)
      if (result.success && window.$notify) {
        window.$notify.success('VIN Identified', 'Vehicle details were refreshed from VIN providers.')
      }
    }

    const connectorLabel = (state) => {
      if (!state) return 'Not checked'
      return state.replace(/_/g, ' ')
    }

    const openDiagnostics = async (vehicle) => {
      ensureManualScanForm(vehicle.id)
      activeDiagnosticsVehicleId.value = activeDiagnosticsVehicleId.value === vehicle.id ? null : vehicle.id
      if (activeDiagnosticsVehicleId.value === vehicle.id) {
        await Promise.all([
          loadConnectorStatus(vehicle.id),
          loadScans(vehicle.id),
          loadAnalyses(vehicle.id),
        ])
      }
    }

    const pairVehicleConnector = async (vehicle) => {
      const result = await pairConnector(vehicle.id)
      if (window.$notify) {
        if (result.success) {
          window.$notify.success('Connector Ready', result.message || 'Connector pairing placeholder has been prepared.')
        } else {
          window.$notify.error('Connector Error', result.message)
        }
      }
    }

    const revokeVehicleConnector = async (vehicle) => {
      const result = await revokeConnector(vehicle.id)
      if (result.success && window.$notify) {
        window.$notify.success('Connector Revoked', 'Vehicle connector pairing was removed.')
      }
    }

    const submitManualScan = async (vehicle) => {
      ensureManualScanForm(vehicle.id)
      const payload = { ...manualScans[vehicle.id] }
      const result = await createManualScan(vehicle.id, payload)

      if (result.success) {
        manualScans[vehicle.id] = {
          mileage: '',
          scan_date: '',
          engine_dtcs: '',
          abs_errors: '',
          airbag_errors: '',
          transmission_errors: '',
          notes: '',
        }

        if (window.$notify) {
          window.$notify.success('Scan Saved', 'Manual diagnostic scan has been stored in vehicle history.')
        }

        if (result.scan?.id) {
          await runAiAnalysis(vehicle, result.scan)
        }
      } else if (window.$notify) {
        window.$notify.error('Scan Error', result.message)
      }
    }

    const runAiAnalysis = async (vehicle, scan) => {
      analyzingScanId.value = scan.id
      const result = await analyzeScan(vehicle.id, scan.id)
      analyzingScanId.value = null

      if (result.success && window.$notify) {
        window.$notify.success('AI Analysis Ready', 'Diagnostic assistant generated repair guidance for this scan.')
      } else if (!result.success && window.$notify) {
        window.$notify.error('AI Analysis Failed', result.message)
      }
    }

    const severityClass = (severity) => {
      const map = {
        low: 'bg-green-100 text-green-700 dark:bg-green-900/40 dark:text-green-300',
        medium: 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/40 dark:text-yellow-300',
        high: 'bg-orange-100 text-orange-800 dark:bg-orange-900/40 dark:text-orange-300',
        critical: 'bg-red-100 text-red-700 dark:bg-red-900/40 dark:text-red-300',
      }
      return map[severity] || map.medium
    }

    const formatConfidence = (score) => {
      if (score == null) return '—'
      const pct = score <= 1 ? Math.round(score * 100) : Math.round(score)
      return `${pct}%`
    }

    const joinCodes = (codes) => Array.isArray(codes) && codes.length ? codes.join(', ') : 'None'

    const formatScanDate = (value) => {
      if (!value) return 'Unknown date'
      return new Date(value).toLocaleString()
    }

    const submitForm = async () => {
      formError.value = null

      if (editingVehicle.value) {
        const result = await updateVehicle(editingVehicle.value.id, {
          nickname: form.value.nickname || null,
          license_plate: form.value.license_plate || null,
          current_mileage: form.value.current_mileage || null,
        })
        if (result.success) {
          closeModal()
        } else {
          formError.value = result.message
        }
      } else {
        if (!form.value.vin || form.value.vin.length !== 17) {
          formError.value = 'VIN must be exactly 17 characters.'
          return
        }
        const result = await createVehicle({
          vin: form.value.vin.toUpperCase(),
          nickname: form.value.nickname || null,
          license_plate: form.value.license_plate || null,
          current_mileage: form.value.current_mileage || null,
        })
        if (result.success) {
          closeModal()
        } else {
          formError.value = result.message
        }
      }
    }

    onMounted(() => {
      loadVehicles()
    })

    return {
      vehicles,
      isLoading,
      showRegisterModal,
      editingVehicle,
      form,
      formError,
      manualScans,
      activeDiagnosticsVehicleId,
      analyzingScanId,
      displayName,
      connectorLabel,
      openDiagnostics,
      pairVehicleConnector,
      revokeVehicleConnector,
      submitManualScan,
      runAiAnalysis,
      severityClass,
      formatConfidence,
      joinCodes,
      formatScanDate,
      editVehicle,
      confirmArchive,
      runIdentification,
      closeModal,
      submitForm,
    }
  },
}
</script>
