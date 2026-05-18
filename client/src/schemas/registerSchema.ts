import { z } from 'zod'

export const registerSchema = z
  .object({
    name: z.string().min(1, 'Gym name is required').max(255),
    phone: z.string().min(1, 'Phone is required').max(20),
    address: z.string().min(1, 'Address is required').max(500),
    password: z
      .string()
      .min(8, 'Password must be at least 8 characters'),
    password_confirmation: z.string().min(1, 'Please confirm your password'),
  })
  .refine((data) => data.password === data.password_confirmation, {
    message: 'Passwords do not match',
    path: ['password_confirmation'],
  })

export type RegisterFormData = z.infer<typeof registerSchema>
