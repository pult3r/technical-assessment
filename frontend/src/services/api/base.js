import axios from 'axios'
import { API_CONFIG } from 'src/services/api/config'

const api = axios.create({
  baseURL: API_CONFIG.BASE_URL,
  timeout: 15000
})

export default api
