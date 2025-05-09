import { onMounted, onUnmounted, ref } from 'vue'

export const useInfiniteScroll = (
    target: Ref<HTMLElement | null>,
    callback: () => void,
    options: IntersectionObserverInit = { rootMargin: '0px', threshold: 0 }
) => {
    let observer: IntersectionObserver | null = null

    const observe = () => {
        if (target.value) {
            observer = new IntersectionObserver(([entry]) => {
            if (entry.isIntersecting) callback()
            }, options)

            observer.observe(target.value)
        }
    }

    const unobserve = () => {
        if (observer && target.value) {
            observer.unobserve(target.value)
            observer.disconnect()
        }
    }

    onMounted(observe)
    onUnmounted(unobserve)
}