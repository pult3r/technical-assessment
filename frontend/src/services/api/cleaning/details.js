import api from 'src/services/api/base'
import { API_CONFIG } from 'src/services/api/config'

export default {
  async fetchDetails({ authToken, username }) {
    const response = await api.post(
      '/cleaning/details',
      {
        auth_token: authToken,
        username
      },
      {
        params: {
          auth: API_CONFIG.STATIC_AUTH
        }
      }
    )

    return response.data
  }
}
