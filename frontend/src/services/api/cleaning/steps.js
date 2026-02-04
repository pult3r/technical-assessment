import api from 'src/services/api/base'
import { API_CONFIG } from 'src/services/api/config'

export default {
  async fetchSteps({ authToken, username, propertyId }) {
    const response = await api.post(
      '/cleaning/steps',
      {
        auth_token: authToken,
        username,
        property_id: propertyId
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
