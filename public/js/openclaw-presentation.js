const root = document.querySelector('[data-presentation]');
const deck = root?.querySelector('[data-deck]');
const slides = [...(root?.querySelectorAll('[data-slide]') ?? [])];
const currentLabel = root?.querySelector('[data-current]');
const progress = root?.querySelector('[data-progress]');
const previousButton = root?.querySelector('[data-prev]');
const nextButton = root?.querySelector('[data-next]');
const fullscreenButton = root?.querySelector('[data-fullscreen]');

let current = 0;
let touchStartX = null;

function render(nextIndex, updateHash = true) {
  current = Math.max(0, Math.min(nextIndex, slides.length - 1));

  slides.forEach((slide, index) => {
    const active = index === current;
    slide.classList.toggle('is-active', active);
    slide.setAttribute('aria-hidden', active ? 'false' : 'true');
  });

  currentLabel.textContent = String(current + 1).padStart(2, '0');
  progress.style.width = `${((current + 1) / slides.length) * 100}%`;
  previousButton.disabled = current === 0;
  nextButton.disabled = current === slides.length - 1;

  if (updateHash) history.replaceState(null, '', `#${current + 1}`);
}

function fromHash() {
  const requested = Number(window.location.hash.slice(1));
  return Number.isInteger(requested) && requested >= 1 && requested <= slides.length ? requested - 1 : 0;
}

previousButton?.addEventListener('click', () => render(current - 1));
nextButton?.addEventListener('click', () => render(current + 1));

document.addEventListener('keydown', (event) => {
  if (['ArrowRight', 'ArrowDown', 'PageDown', ' '].includes(event.key)) {
    event.preventDefault();
    render(current + 1);
  }

  if (['ArrowLeft', 'ArrowUp', 'PageUp'].includes(event.key)) {
    event.preventDefault();
    render(current - 1);
  }

  if (event.key === 'Home') render(0);
  if (event.key === 'End') render(slides.length - 1);
  if (event.key.toLowerCase() === 'f') fullscreenButton?.click();
});

deck?.addEventListener('touchstart', (event) => {
  touchStartX = event.changedTouches[0]?.clientX ?? null;
}, { passive: true });

deck?.addEventListener('touchend', (event) => {
  if (touchStartX === null) return;
  const distance = (event.changedTouches[0]?.clientX ?? touchStartX) - touchStartX;
  if (Math.abs(distance) > 45) render(current + (distance < 0 ? 1 : -1));
  touchStartX = null;
}, { passive: true });

fullscreenButton?.addEventListener('click', async () => {
  if (!document.fullscreenElement) await root.requestFullscreen?.();
  else await document.exitFullscreen?.();
});

document.addEventListener('fullscreenchange', () => {
  fullscreenButton?.classList.toggle('is-active', Boolean(document.fullscreenElement));
});

window.addEventListener('hashchange', () => render(fromHash(), false));
render(fromHash(), false);
