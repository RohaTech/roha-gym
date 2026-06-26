export const API_URL = 'http://127.0.0.1:8001/api'

// Base URL for files served from Laravel's public storage disk.
// Derived from API_URL so it stays in sync (e.g. https://gym-back.rohatechs.com/storage).
export const STORAGE_URL = API_URL.replace(/\/api\/?$/, '') + '/storage'

/**
 * Build a full URL to a file stored on the backend public disk.
 * `photo_path` values are stored relative to the storage root (e.g. "members/abc.jpg").
 */
export function storageUrl(path: string | null | undefined): string {
  if (!path) return ''
  return `${STORAGE_URL}/${path.replace(/^\/+/, '')}`
}
