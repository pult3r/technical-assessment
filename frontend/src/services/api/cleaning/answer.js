import api from 'src/services/api/base'
import { API_CONFIG } from 'src/services/api/config'

export default {
  submitAnswer({ authToken, username, propertyId, stepId, answer }) {
    return api.post(
      '/cleaning/answer',
      {
        auth_token: authToken,
        username,
        property_id: propertyId,
        step_id: stepId,
        answer
      },
      {
        params: {
          auth: API_CONFIG.STATIC_AUTH
        }
      }
    )
  }
}