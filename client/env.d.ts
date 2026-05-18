/// <reference types="vite/client" />

import 'vue'

export {}

declare module 'vue' {
  interface ComponentCustomProperties {
    $lang: Record<string, string>
  }
}
