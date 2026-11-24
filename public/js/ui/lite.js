export function isLite() {
    return document.documentElement.classList.contains('is-lite');
}

export function prefersReducedEffects() {
    return isLite() || window.matchMedia('(prefers-reduced-motion: reduce)').matches;
}
