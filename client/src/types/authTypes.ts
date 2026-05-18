export interface LoginPayload {
  email: string
  password: string
}

export interface RegisterPayload {
  name: string
  email: string
  password: string
  password_confirmation: string
  phone: string
  address: string
}

export interface AuthResponse {
  token: string
  user: {
    id: number
    name: string
    email: string
    phone: string
    address: string
    status: string
  }
}

export interface AuthError {
  message: string
  errors?: Record<string, string[]>
}
