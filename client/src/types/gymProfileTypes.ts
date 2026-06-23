export interface GymProfile {
  id: number
  name: string
  email: string | null
  phone: string
  address: string
  logo_path: string | null
  status: number
  role: string
}

export interface GymProfileFormData {
  name: string
  phone: string
  address: string
}

export type GymProfileFields = keyof GymProfileFormData
