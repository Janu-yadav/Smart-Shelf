// assets/main.js
// Simple animations + theme toggle + graceful theme update to server.

document.addEventListener('DOMContentLoaded', function(){
  // reveal animated elements
  document.querySelectorAll('.animate-up').forEach((el, i)=>{
    setTimeout(()=> el.classList.add('reveal'), 80 + i * 80);
  });

  // theme toggle
  const themeBtn = document.getElementById('themeToggle');
  themeBtn && themeBtn.addEventListener('click', async () => {
    // client-side transition
    document.body.classList.add('theme-switching');
    const isDark = document.body.classList.contains('theme-dark');
    document.body.classList.toggle('theme-dark', !isDark);

    // send update to server so session/theme persists
    // toggle_theme.php should flip session theme and return JSON {theme:'dark'|'light'}
    try {
      await fetch('toggle_theme.php', { method: 'POST', headers: {'Content-Type':'application/x-www-form-urlencoded'}, body: 'ajax=1' });
    } catch (e) {
      // silently ignore if server toggle not present
      console.warn('Theme toggle server request failed', e);
    }

    setTimeout(()=> document.body.classList.remove('theme-switching'), 450);
  });

  // small fallback: add reveal for feature cards on scroll
  const featureObserver = new IntersectionObserver((entries) => {
    entries.forEach(e=>{
      if (e.isIntersecting) e.target.classList.add('reveal');
    });
  }, { threshold: 0.12 });

  document.querySelectorAll('.feature-card').forEach(el => featureObserver.observe(el));
});
