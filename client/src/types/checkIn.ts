export type CheckInMethod = 'qr' | 'manual'

export type CheckInSuccess = {
  success: true
  member: {
    name: string
    photo: string
    remaining_days: number
    last_check_in: string | null
    today_count: number
  }
}

export type CheckInFailure = {
  success: false
  reason: 'not_found' | 'expired' | 'limit_reached'
}

export type CheckInResult = CheckInSuccess | CheckInFailure
