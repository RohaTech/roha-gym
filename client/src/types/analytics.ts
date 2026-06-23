export type RangeOption = '7d' | '30d' | '3m' | '12m'

export type DashboardData = {
    snapshot: {
        total_members: number
        active_members: number
        expired_members: number
        checkins_today: number
    }
    recent_checkins: {
        member_name: string
        member_photo: string | null
        checked_in_at: string
    }[]
    expiring_soon: {
        name: string
        photo: string | null
        expiry_date: string
        days_left: number
    }[]
    weekly_checkins: {
        date: string
        count: number
    }[]
}

export type AnalyticsData = {
    snapshot: {
        total_members: number
        active_members: number
        expired_members: number
        new_this_month: number
        checkins_today: number
        avg_daily_checkins: number
    }
    daily_checkins: { date: string; count: number }[]
    checkins_by_day_of_week: { day: string; count: number }[]
    checkins_by_hour: { hour: number; count: number }[]
    checkin_method_ratio: { qr: number; manual: number }
    monthly_registrations: { month: string; count: number }[]
    cumulative_members: { month: string; count: number }[]
    retention_rate: number
    membership_distribution: { type: string; count: number }[]
    expiring_soon: { name: string; photo: string | null; days_left: number }[]
    expired_this_month: number
    most_active_members: { name: string; photo: string | null; checkin_count: number }[]
    inactive_members: { name: string; photo: string | null }[]
    avg_checkins_per_member_per_week: number
}
