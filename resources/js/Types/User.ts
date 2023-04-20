export interface User {
    id: number
    name: string
    email: string
    created_at: string
    updated_at: string
    current_team_id: number
    deleted_at: string | null
    profile_photo_url: string
    email_verified_at: string | null
    profile_photo_path: string | null
}
