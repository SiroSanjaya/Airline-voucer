import axios from 'axios'

const api = axios.create({
  baseURL: '/api',
  headers: {
    'Content-Type': 'application/json',
    Accept: 'application/json',
  },
})

/**
 * Generate 3 voucher seats.
 * @returns {{ data: { success: boolean, seats: string[], details: object } }}
 */
export async function generateVoucher(payload) {
  const { data } = await api.post('/generate', payload)
  return data
}
