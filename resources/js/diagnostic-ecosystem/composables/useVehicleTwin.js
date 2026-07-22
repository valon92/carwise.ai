import { ref, computed, readonly } from 'vue'
import { vehicleTwinAPI } from '../services/diagnosticEcosystemAPI'

const vehicles = ref([])
const isLoading = ref(false)
const error = ref(null)

export function useVehicleTwin() {
  const mergeVehicle = (id, patch) => {
    const idx = vehicles.value.findIndex(v => v.id === id)
    if (idx !== -1) {
      vehicles.value[idx] = { ...vehicles.value[idx], ...patch }
    }
  }

  const loadVehicles = async () => {
    try {
      isLoading.value = true
      error.value = null
      const response = await vehicleTwinAPI.list()
      if (response.data.success) {
        vehicles.value = response.data.data
        return { success: true }
      }
      return { success: false, message: 'Failed to load vehicles' }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to load vehicles'
      return { success: false, message: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const createVehicle = async (data) => {
    try {
      isLoading.value = true
      error.value = null
      const response = await vehicleTwinAPI.create(data)
      if (response.data.success) {
        vehicles.value.push(response.data.data)
        if (window.$notify) {
          window.$notify.success('Vehicle Registered', 'Your vehicle has been added to the Digital Twin.')
        }
        return { success: true, vehicle: response.data.data }
      }
      return { success: false, message: response.data.message }
    } catch (err) {
      const msg = err.response?.data?.message || 'Failed to register vehicle'
      error.value = msg
      return { success: false, message: msg }
    } finally {
      isLoading.value = false
    }
  }

  const updateVehicle = async (id, data) => {
    try {
      isLoading.value = true
      error.value = null
      const response = await vehicleTwinAPI.update(id, data)
      if (response.data.success) {
        const idx = vehicles.value.findIndex(v => v.id === id)
        if (idx !== -1) vehicles.value[idx] = response.data.data
        return { success: true, vehicle: response.data.data }
      }
      return { success: false, message: response.data.message }
    } catch (err) {
      error.value = err.response?.data?.message || 'Failed to update vehicle'
      return { success: false, message: error.value }
    } finally {
      isLoading.value = false
    }
  }

  const archiveVehicle = async (id) => {
    try {
      isLoading.value = true
      const response = await vehicleTwinAPI.archive(id)
      if (response.data.success) {
        vehicles.value = vehicles.value.filter(v => v.id !== id)
        if (window.$notify) {
          window.$notify.success('Vehicle Archived', 'Vehicle removed. History is preserved.')
        }
        return { success: true }
      }
      return { success: false, message: response.data.message }
    } catch (err) {
      return { success: false, message: err.response?.data?.message || 'Failed to archive vehicle' }
    } finally {
      isLoading.value = false
    }
  }

  const identifyVehicle = async (id) => {
    try {
      isLoading.value = true
      error.value = null
      const response = await vehicleTwinAPI.identify(id)
      if (response.data.success) {
        const reload = await vehicleTwinAPI.get(id)
        const idx = vehicles.value.findIndex(v => v.id === id)
        if (reload.data.success && idx !== -1) {
          vehicles.value[idx] = reload.data.data
        }
        return { success: true, decode: response.data.data }
      }
      return { success: false, message: response.data.message }
    } catch (err) {
      const msg = err.response?.data?.message || 'Failed to identify VIN'
      error.value = msg
      return { success: false, message: msg }
    } finally {
      isLoading.value = false
    }
  }

  const loadConnectorStatus = async (id) => {
    try {
      const response = await vehicleTwinAPI.connectorStatus(id)
      if (response.data.success) {
        mergeVehicle(id, {
          connector_state: response.data.data.status?.state,
          connector_pairing: response.data.data.pairing,
          connector_capabilities: response.data.data.capabilities,
        })
        return { success: true, data: response.data.data }
      }
      return { success: false, message: response.data.message }
    } catch (err) {
      return { success: false, message: err.response?.data?.message || 'Failed to load connector status' }
    }
  }

  const pairConnector = async (id, data = {}) => {
    try {
      const response = await vehicleTwinAPI.pairConnector(id, data)
      if (response.data.success) {
        mergeVehicle(id, {
          connector_state: response.data.data.status?.state,
          connector_pairing: response.data.data.pairing,
        })
        return { success: true, data: response.data.data, message: response.data.message }
      }
      return { success: false, message: response.data.message }
    } catch (err) {
      return { success: false, message: err.response?.data?.message || 'Failed to pair connector' }
    }
  }

  const revokeConnector = async (id) => {
    try {
      const response = await vehicleTwinAPI.revokeConnector(id)
      if (response.data.success) {
        mergeVehicle(id, {
          connector_state: 'revoked',
          connector_pairing: response.data.data.pairing,
        })
        return { success: true, data: response.data.data }
      }
      return { success: false, message: response.data.message }
    } catch (err) {
      return { success: false, message: err.response?.data?.message || 'Failed to revoke connector' }
    }
  }

  const loadScans = async (id) => {
    try {
      const response = await vehicleTwinAPI.scans(id)
      if (response.data.success) {
        mergeVehicle(id, { recent_scans: response.data.data })
        return { success: true, scans: response.data.data }
      }
      return { success: false, message: response.data.message }
    } catch (err) {
      return { success: false, message: err.response?.data?.message || 'Failed to load scans' }
    }
  }

  const createManualScan = async (id, data) => {
    try {
      const response = await vehicleTwinAPI.createManualScan(id, data)
      if (response.data.success) {
        const scans = await loadScans(id)
        return { success: true, scan: response.data.data, scans: scans.scans }
      }
      return { success: false, message: response.data.message }
    } catch (err) {
      return { success: false, message: err.response?.data?.message || 'Failed to save manual scan' }
    }
  }

  const analyzeScan = async (vehicleId, scanId) => {
    try {
      const response = await vehicleTwinAPI.analyzeScan(scanId)
      if (response.data.success) {
        mergeVehicle(vehicleId, {
          latest_analysis: response.data.data,
          analyses: [
            response.data.data,
            ...(vehicles.value.find(v => v.id === vehicleId)?.analyses || []).filter(a => a.id !== response.data.data.id),
          ],
        })
        return { success: true, analysis: response.data.data }
      }
      return { success: false, message: response.data.message }
    } catch (err) {
      return { success: false, message: err.response?.data?.message || 'Failed to analyze scan' }
    }
  }

  const loadAnalyses = async (id) => {
    try {
      const response = await vehicleTwinAPI.vehicleAnalyses(id)
      if (response.data.success) {
        mergeVehicle(id, {
          analyses: response.data.data,
          latest_analysis: response.data.data[0] || null,
        })
        return { success: true, analyses: response.data.data }
      }
      return { success: false, message: response.data.message }
    } catch (err) {
      return { success: false, message: err.response?.data?.message || 'Failed to load analyses' }
    }
  }

  const totalVehicles = computed(() => vehicles.value.length)

  return {
    vehicles: readonly(vehicles),
    isLoading: readonly(isLoading),
    error: readonly(error),
    totalVehicles,
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
  }
}
