import { ref } from 'vue'

export const useSingleClick = () => {
    const isRunning = ref(false)

    const run = async (callback: () => Promise<void>) => {
        if (isRunning.value) return
        isRunning.value = true

        try {
            await callback()
        } finally {
            isRunning.value = false
        }
    }

    return {
        isRunning,
        run,
    }
}