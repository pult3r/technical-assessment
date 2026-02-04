import axios from 'axios'

const API_URL = 'https://api.student-cribs.com/api/cleaning'
const STATIC_AUTH =
  'e0df1c3052807c51d3c3b146ec51d3c3b146e18418447f37a7e035fe0df1c305280747f37a7e035f'

export default {
  async fetchDetails({ authToken, username }) {
    const response = await axios.post(
      `${API_URL}/details`,
      {
        auth_token: authToken,
        username: username
      },
      {
        params: {
          auth: STATIC_AUTH
        }
      }
    )

    return response.data
  }
}
