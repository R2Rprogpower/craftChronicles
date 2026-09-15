const reveals = document.querySelectorAll('.reveal');
const observer = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (!entry.isIntersecting) return;
    entry.target.classList.add('visible');
    observer.unobserve(entry.target);
  });
}, { threshold: 0.13 });
reveals.forEach((item) => observer.observe(item));

const numberObserver = new IntersectionObserver((entries) => {
  entries.forEach((entry) => {
    if (!entry.isIntersecting) return;
    const node = entry.target;
    const target = Number(node.dataset.count);
    const duration = 900;
    const start = performance.now();
    const format = new Intl.NumberFormat('ru-RU');
    const tick = (now) => {
      const progress = Math.min((now - start) / duration, 1);
      const eased = 1 - Math.pow(1 - progress, 3);
      node.textContent = format.format(Math.round(target * eased));
      if (progress < 1) requestAnimationFrame(tick);
    };
    requestAnimationFrame(tick);
    numberObserver.unobserve(node);
  });
}, { threshold: 0.7 });
document.querySelectorAll('[data-count]').forEach((item) => numberObserver.observe(item));

const modal = document.querySelector('.player-modal');
const shell = document.querySelector('[data-player-shell]');

function openPlayer() {
  if (!shell.querySelector('iframe')) {
    const iframe = document.createElement('iframe');
    const parent = window.location.hostname || 'localhost';
    iframe.src = `https://player.twitch.tv/?channel=aaskar&parent=${encodeURIComponent(parent)}&autoplay=true`;
    iframe.allow = 'autoplay; fullscreen';
    iframe.title = 'Прямой эфир Twitch-канала aaskar';
    shell.appendChild(iframe);
  }
  if (typeof modal.showModal === 'function') modal.showModal();
  else window.open('https://www.twitch.tv/aaskar', '_blank', 'noopener');
}

document.querySelectorAll('[data-open-player]').forEach((button) => button.addEventListener('click', openPlayer));
document.querySelector('[data-close-player]').addEventListener('click', () => modal.close());
modal.addEventListener('click', (event) => {
  if (event.target === modal) modal.close();
});
