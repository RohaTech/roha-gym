export type MemberCardData = {
    member: {
        name: string
        photo_url: string | null
        code: string
        slug: string
        membership_type: string
        start_date: string
        expiry_date: string
    }
    gym: {
        name: string
        logo_url: string | null
    }
}
