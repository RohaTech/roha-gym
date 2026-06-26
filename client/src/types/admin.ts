export interface GymSummary {
  id: number
  name: string
  phone: string
  address: string | null
  status: number // 1 = active, 0 = suspended, 2 = pending approval
  logo: string | null
  members_count: number
  active_members_count: number
  created_at: string | null
}

export interface PaginatedGyms {
  data: GymSummary[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface GymDetail {
  gym: GymSummary
  stats: {
    total_members: number
    active_members: number
    expired_members: number
    membership_types: number
    checkins_today: number
  }
  recent_members: {
    id: number
    full_name: string
    phone: string
    status: string
    photo: string | null
    expiry_date: string | null
  }[]
}

export interface AdminDashboardData {
  snapshot: {
    total_gyms: number
    active_gyms: number
    suspended_gyms: number
    pending_gyms: number
    new_gyms_this_month: number
    total_members: number
    active_members: number
    checkins_today: number
  }
  gyms_growth: { month: string; count: number }[]
  members_growth: { month: string; count: number }[]
  checkins_trend: { date: string; count: number }[]
  top_gyms_by_members: {
    id: number
    name: string
    logo: string | null
    members_count: number
  }[]
  top_gyms_by_activity: {
    id: number
    name: string | null
    logo: string | null
    checkin_count: number
  }[]
}

export interface GymFormPayload {
  name: string
  phone: string
  address: string
  password?: string
  logo?: File | null
}

export type SubscriptionStatus =
  | 'lifetime'
  | 'active'
  | 'expiring_soon'
  | 'expired'
  | 'none'

export interface SubscriptionSummary {
  id: number // gym id
  name: string
  phone: string
  logo: string | null
  gym_status: number // 1 = active, 0 = suspended
  subscription_status: SubscriptionStatus
  plan_type: 'lifetime' | 'monthly' | null
  expiry_date: string | null
  days_until_expiry: number | null
  total_paid: number
  currency: string
  last_paid_at: string | null
}

export interface PaginatedSubscriptions {
  data: SubscriptionSummary[]
  current_page: number
  last_page: number
  per_page: number
  total: number
}

export interface Payment {
  id: number
  plan_type: 'lifetime' | 'monthly'
  months: number | null
  amount: number
  currency: string
  starts_at: string | null
  ends_at: string | null
  paid_at: string | null
  note: string | null
}

export interface SubscriptionDetail {
  summary: SubscriptionSummary
  payments: Payment[]
}

export interface RecordPaymentPayload {
  plan_type: 'lifetime' | 'monthly'
  months?: number | null
  amount: number
  currency: string
  paid_at: string
  note?: string | null
}
