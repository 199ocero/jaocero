/**
 * Scroll reveals: adds `is-visible` to [data-reveal] elements as they scroll into view.
 * Runs again after wire:navigate page changes and whenever Livewire adds new content.
 */
let observer = null

function observeRevealElements() {
    let elements = document.querySelectorAll('[data-reveal]:not(.is-visible):not([data-reveal-observed])')

    if (!('IntersectionObserver' in window)) {
        elements.forEach((element) => element.classList.add('is-visible'))

        return
    }

    observer ??= new IntersectionObserver(
        (entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible')
                    observer.unobserve(entry.target)
                }
            })
        },
        { rootMargin: '0px 0px -40px 0px', threshold: 0.05 },
    )

    elements.forEach((element) => {
        element.setAttribute('data-reveal-observed', '')
        observer.observe(element)
    })
}

let scheduled = false

function scheduleObserve() {
    if (scheduled) return

    scheduled = true
    requestAnimationFrame(() => {
        scheduled = false
        observeRevealElements()
    })
}

document.addEventListener('DOMContentLoaded', () => {
    observeRevealElements()
    new MutationObserver(scheduleObserve).observe(document.body, { childList: true, subtree: true })
})

document.addEventListener('livewire:navigated', observeRevealElements)
