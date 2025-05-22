import * as yup from 'yup'

export const createMessageSchema = (field: string, label: string) =>
    yup.object({
        [field]: yup
        .string()
        .required(`${label}は必須です`)
        .max(120, `${label}は120文字以内で入力してください`),
    })