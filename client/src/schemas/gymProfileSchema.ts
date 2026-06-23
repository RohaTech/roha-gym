import { z } from 'zod'

export const gymProfileSchema = z.object({
  name: z.string().min(1, 'Gym name is required').max(255),
  phone: z.string().min(1, 'Phone is required').max(20),
  email: z
    .string()
    .max(255)
    .refine((v) => v === '' || z.string().email().safeParse(v).success, {
      message: 'Invalid email address',
    })
    .optional(),
  address: z.string().min(1, 'Address is required').max(500),
})
