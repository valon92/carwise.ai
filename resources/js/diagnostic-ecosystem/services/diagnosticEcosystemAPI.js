import api from '../../services/api'

const BASE = '/de'

export const vehicleTwinAPI = {
  list: () => api.get(`${BASE}/vehicles`),
  get: (id) => api.get(`${BASE}/vehicles/${id}`),
  create: (data) => api.post(`${BASE}/vehicles`, data),
  update: (id, data) => api.put(`${BASE}/vehicles/${id}`, data),
  archive: (id) => api.delete(`${BASE}/vehicles/${id}`),
  identify: (id) => api.post(`${BASE}/vehicles/${id}/identify`),
  vinHistory: (id) => api.get(`${BASE}/vehicles/${id}/vin-history`),
  connectorStatus: (id) => api.get(`${BASE}/vehicles/${id}/connector`),
  pairConnector: (id, data = {}) => api.post(`${BASE}/vehicles/${id}/connector/pair`, data),
  revokeConnector: (id) => api.delete(`${BASE}/vehicles/${id}/connector`),
  scans: (id) => api.get(`${BASE}/vehicles/${id}/scans`),
  createScan: (id) => api.post(`${BASE}/vehicles/${id}/scans`),
  createManualScan: (id, data) => api.post(`${BASE}/vehicles/${id}/scans/manual`, data),
  analyzeScan: (scanId) => api.post(`${BASE}/scans/${scanId}/analyze`),
  getScanAnalysis: (scanId) => api.get(`${BASE}/scans/${scanId}/analysis`),
  vehicleAnalyses: (id) => api.get(`${BASE}/vehicles/${id}/analyses`),
  history: (id, params = {}) => api.get(`${BASE}/vehicles/${id}/history`, { params }),
  createHistoryEvent: (id, data) => api.post(`${BASE}/vehicles/${id}/history`, data),
  exportHistoryJson: (id) => api.get(`${BASE}/vehicles/${id}/history/export.json`),
  exportHistoryPdfUrl: (id) => `${BASE}/vehicles/${id}/history/export.pdf`,
  maintenance: (id, params = {}) => api.get(`${BASE}/vehicles/${id}/maintenance`, { params }),
  generateMaintenance: (id) => api.post(`${BASE}/vehicles/${id}/maintenance/generate`),
  updateMaintenance: (recommendationId, data) => api.patch(`${BASE}/maintenance/${recommendationId}`, data),
  searchParts: (id, q = '') => api.get(`${BASE}/vehicles/${id}/marketplace/parts`, { params: { q } }),
  partsForAnalysis: (analysisId) => api.get(`${BASE}/analyses/${analysisId}/parts`),
  previewVin: (vin) => api.post(`${BASE}/vin/preview`, { vin }),
  status: () => api.get(`${BASE}/status`),
}
