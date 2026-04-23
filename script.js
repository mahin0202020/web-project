/*==================================================
  PAWS & POUR – Main JavaScript
  File: assets/js/script.js
  All actions go to auth.php via fetch (POST)
==================================================*/

'use strict';

// ── CURSOR ───────────────────────────────────────
const cursor    = document.getElementById('cursor');
const cursorDot = document.getElementById('cursor-dot');

document.addEventListener('mousemove', e => {
  cursor.style.left    = e.clientX + 'px';
  cursor.style.top     = e.clientY + 'px';
  cursorDot.style.left = e.clientX + 'px';
  cursorDot.style.top  = e.clientY + 'px';
});

document.querySelectorAll('a, button, .tab-btn, .menu-card, .why-card, .step, .gallery-item').forEach(el => {
  el.addEventListener('mouseenter', () => cursor.classList.add('hov'));
  el.addEventListener('mouseleave', () => cursor.classList.remove('hov'));
});

// ── NAV SCROLL ───────────────────────────────────
window.addEventListener('scroll', () => {
  document.getElementById('mainNav').classList.toggle('sc', window.scrollY > 40);
});

// ── MOBILE MENU ──────────────────────────────────
function toggleMenu() {
  document.getElementById('mobileMenu').classList.toggle('open');
}

// ── TOAST ────────────────────────────────────────
function showToast(msg, type = 'ok') {
  const t = document.getElementById('toast');
  t.textContent = msg;
  t.className = `toast show ${type}`;
  setTimeout(() => t.classList.remove('show'), 3500);
}

// ── MODALS ───────────────────────────────────────
function openModal(mode) {
  document.getElementById('authModal').classList.add('open');
  switchTab(mode || 'login');
  document.getElementById('auth-msg').className = 'modal-msg';
}

function closeModal(id) {
  document.getElementById(id).classList.remove('open');
}

function switchTab(tab) {
  document.getElementById('tab-login').classList.toggle('active', tab === 'login');
  document.getElementById('tab-signup').classList.toggle('active', tab === 'signup');
  document.getElementById('form-login').classList.toggle('active', tab === 'login');
  document.getElementById('form-signup').classList.toggle('active', tab === 'signup');
  document.getElementById('auth-msg').className = 'modal-msg';
}

// Close modal on overlay click
document.querySelectorAll('.modal-overlay').forEach(el => {
  el.addEventListener('click', e => {
    if (e.target === el) el.classList.remove('open');
  });
});

// ── DASHBOARD ─────────────────────────────────────
function switchDash(panel, btn) {
  document.querySelectorAll('.dash-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.dtab').forEach(b => b.classList.remove('active'));
  document.getElementById('dp-' + panel).classList.add('active');
  if (btn) btn.classList.add('active');

  // Lazy-load panel data when switching
  if (panel === 'pets')         loadPets();
  if (panel === 'profile')      loadProfile();
  if (panel === 'reservations') loadReservations();
}

function openDashboard(name) {
  closeModal('authModal');
  document.getElementById('dash-welcome').textContent = `Welcome back, ${name}! 🐾`;
  document.getElementById('dashModal').classList.add('open');
  // Activate first tab
  switchDash('pets', document.querySelector('.dtab'));
}

// ── AUTH STATE ───────────────────────────────────
// Initialised from PHP session via window.PP_SESSION
let currentUser = null;
let selectedPetId = null;
let petsData = [];

// ── HELPER: POST to auth.php ─────────────────────
async function apiPost(data) {
  const fd = new FormData();
  for (const [k, v] of Object.entries(data)) {
    if (v !== null && v !== undefined) fd.append(k, v);
  }
  const r = await fetch('auth.php', { method: 'POST', body: fd });
  if (!r.ok) throw new Error('Network response was not ok: ' + r.status);
  return r.json();
}

// ── AUTH MESSAGE ─────────────────────────────────
function setAuthMsg(msg, isErr = true) {
  const el = document.getElementById('auth-msg');
  el.textContent = msg;
  el.className = 'modal-msg ' + (isErr ? 'err' : 'ok');
}

// ── LOGIN ────────────────────────────────────────
async function doLogin() {
  const email = document.getElementById('l-email').value.trim();
  const pass  = document.getElementById('l-pass').value;
  if (!email || !pass) { setAuthMsg('Please fill in all fields.'); return; }

  try {
    const d = await apiPost({ action: 'login', email, password: pass });
    if (d.success) {
      currentUser = { name: d.name, email: d.email, id: d.user_id };
      updateNavForUser(d.name);
      showToast(d.message, 'ok');
      openDashboard(d.name);
    } else {
      setAuthMsg(d.message);
    }
  } catch (err) {
    setAuthMsg('Connection error. Please try again.');
    console.error(err);
  }
}

// ── SIGNUP ───────────────────────────────────────
async function doSignup() {
  const name  = document.getElementById('s-name').value.trim();
  const email = document.getElementById('s-email').value.trim();
  const phone = document.getElementById('s-phone').value.trim();
  const pass  = document.getElementById('s-pass').value;

  if (!name || !email || !pass) { setAuthMsg('Please fill all required fields.'); return; }
  if (pass.length < 6) { setAuthMsg('Password must be at least 6 characters.'); return; }
  if (!email.includes('@')) { setAuthMsg('Please enter a valid email address.'); return; }

  try {
    const d = await apiPost({ action: 'signup', full_name: name, email, phone, password: pass });
    if (d.success) {
      currentUser = { name: d.name, id: d.user_id };
      updateNavForUser(d.name);
      showToast(d.message, 'ok');
      openDashboard(d.name);
    } else {
      setAuthMsg(d.message);
    }
  } catch (err) {
    setAuthMsg('Connection error. Please try again.');
    console.error(err);
  }
}

// ── LOGOUT ───────────────────────────────────────
async function doLogout() {
  try {
    await apiPost({ action: 'logout' });
  } catch (e) { /* ignore network issues on logout */ }
  currentUser   = null;
  selectedPetId = null;
  petsData      = [];
  closeModal('dashModal');
  updateNavForUser(null);
  showToast('Logged out. See you soon! 🐾', 'ok');
}

// ── UPDATE NAV ───────────────────────────────────
function updateNavForUser(name) {
  const area = document.getElementById('user-nav-area');
  if (name) {
    const initials = name.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();
    area.innerHTML = `<button class="user-chip" onclick="openDashboard('${name.replace(/'/g,"\\'")}')"><span class="av">${initials}</span>${name} ▾</button>`;
  } else {
    area.innerHTML = `<button class="btn-nav-login" onclick="openModal('login')">Log In</button><button class="btn-nav-signup" onclick="openModal('signup')">Sign Up</button>`;
  }
}

// ── PETS ─────────────────────────────────────────
async function loadPets() {
  document.getElementById('pets-grid').innerHTML = '<div class="loading-msg">Loading your pets...</div>';
  try {
    const d = await apiPost({ action: 'get_pets' });
    if (d.success) {
      petsData = d.pets;
      renderPets(d.pets);
    } else {
      document.getElementById('pets-grid').innerHTML = `<div class="loading-msg">${d.message}</div>`;
    }
  } catch (err) {
    document.getElementById('pets-grid').innerHTML = '<div class="loading-msg">Could not load pets.</div>';
    console.error(err);
  }
}

const speciesEmoji = { dog: '🐶', cat: '🐱', rabbit: '🐰', bird: '🐦', other: '🐾' };

function renderPets(pets) {
  const grid = document.getElementById('pets-grid');
  grid.innerHTML = '';

  if (!pets || !pets.length) {
    grid.innerHTML = `<div class="pet-card add-pet-card" onclick="switchDash('addpet',null)"><div class="plus">+</div><p>Add your first pet!</p></div>`;
    return;
  }

  pets.forEach(p => {
    const em   = speciesEmoji[p.species] || '🐾';
    const card = document.createElement('div');
    card.className = 'pet-card';
    card.innerHTML = `<span class="pet-em">${em}</span><h4>${escHtml(p.name)}</h4><p>${escHtml(p.breed || p.species || '')}${p.age ? ' • ' + escHtml(p.age) : ''}</p>`;
    card.onclick = () => selectPet(p, card);
    grid.appendChild(card);
  });

  const add = document.createElement('div');
  add.className = 'pet-card add-pet-card';
  add.innerHTML = `<div class="plus">+</div><p>Add Another Pet</p>`;
  add.onclick = () => switchDash('addpet', null);
  grid.appendChild(add);
}

function selectPet(pet, card) {
  document.querySelectorAll('.pet-card').forEach(c => c.classList.remove('selected'));
  card.classList.add('selected');
  selectedPetId = pet.id;
  const em = speciesEmoji[pet.species] || '🐾';
  document.getElementById('selected-pet-name').textContent = `${em} ${pet.name}'s Activity Log`;
  switchDash('monitor', document.querySelectorAll('.dtab')[1]);
  loadLogs(pet.id);
  showToast(`Viewing ${pet.name}'s profile 🐾`, 'ok');
}

// ── ADD PET ──────────────────────────────────────
async function addPet() {
  const name    = document.getElementById('p-name').value.trim();
  const species = document.getElementById('p-species').value;
  const breed   = document.getElementById('p-breed').value.trim();
  const age     = document.getElementById('p-age').value.trim();
  const weight  = document.getElementById('p-weight').value;
  const notes   = document.getElementById('p-notes').value.trim();

  if (!name) { showToast('Pet name is required.', 'err'); return; }

  try {
    const d = await apiPost({ action: 'add_pet', pet_name: name, species, breed, age, weight: weight || 0, notes });
    if (d.success) {
      showToast(d.message, 'ok');
      ['p-name', 'p-breed', 'p-age', 'p-weight', 'p-notes'].forEach(id => document.getElementById(id).value = '');
      switchDash('pets', document.querySelectorAll('.dtab')[0]);
      loadPets();
    } else {
      showToast(d.message, 'err');
    }
  } catch (err) {
    showToast('Could not add pet. Please try again.', 'err');
    console.error(err);
  }
}

// ── ACTIVITY LOGS ────────────────────────────────
async function loadLogs(petId) {
  document.getElementById('activity-log').innerHTML = '<div class="loading-msg">Loading activities...</div>';
  try {
    const d = await apiPost({ action: 'get_logs', pet_id: petId });
    if (d.success) renderLogs(d.logs);
  } catch (err) {
    document.getElementById('activity-log').innerHTML = '<div class="empty-log">Could not load activity logs.</div>';
    console.error(err);
  }
}

function renderLogs(logs) {
  const logBox = document.getElementById('activity-log');
  if (!logs || !logs.length) {
    logBox.innerHTML = '<div class="empty-log">No activities logged yet. Start tracking!</div>';
    return;
  }
  logBox.innerHTML = logs.map(l => `
    <div class="log-entry">
      <span class="log-type-badge lt-${escHtml(l.log_type)}">${escHtml(l.log_type)}</span>
      <div class="log-content">
        <p>${escHtml(l.description)}</p>
        <time>${formatDate(l.logged_at)}</time>
      </div>
    </div>
  `).join('');
}

async function logActivity() {
  if (!selectedPetId) { showToast('Please select a pet first from My Pets tab.', 'err'); return; }

  const type = document.getElementById('log-type').value;
  const desc = document.getElementById('log-desc').value.trim();
  if (!desc) { showToast('Please enter a description.', 'err'); return; }

  try {
    const d = await apiPost({ action: 'log_activity', pet_id: selectedPetId, log_type: type, description: desc });
    if (d.success) {
      showToast(d.message, 'ok');
      document.getElementById('log-desc').value = '';
      loadLogs(selectedPetId);
    } else {
      showToast(d.message, 'err');
    }
  } catch (err) {
    showToast('Could not log activity.', 'err');
    console.error(err);
  }
}

// ── RESERVATIONS ────────────────────────────────
async function loadReservations() {
  document.getElementById('reservations-list').innerHTML = '<div class="loading-msg">Loading reservations...</div>';
  try {
    const d = await apiPost({ action: 'get_reservations' });
    if (d.success) {
      renderReservations(d.reservations);
      document.getElementById('profile-res-count').textContent = d.reservations.length;
    }
  } catch (err) {
    document.getElementById('reservations-list').innerHTML = '<div class="loading-msg">Could not load reservations.</div>';
    console.error(err);
  }
}

function renderReservations(res) {
  const box = document.getElementById('reservations-list');
  if (!res || !res.length) {
    box.innerHTML = '<div class="empty-log">No reservations yet. <a href="#reserve" onclick="closeModal(\'dashModal\')">Book a table!</a></div>';
    return;
  }
  box.innerHTML = res.map(r => `
    <div class="res-card">
      <div class="res-card-info">
        <h4>📅 ${escHtml(r.date)} &nbsp;·&nbsp; 🕐 ${escHtml(r.time_slot)}</h4>
        <p>Guests: ${r.guests}${r.pet_name ? ' &nbsp;·&nbsp; 🐾 ' + escHtml(r.pet_name) : ''}${r.notes ? ' &nbsp;·&nbsp; ' + escHtml(r.notes) : ''}</p>
      </div>
      <span class="res-badge ${r.status}">${r.status}</span>
    </div>
  `).join('');
}

// ── MAKE RESERVATION ─────────────────────────────
async function makeReservation() {
  if (!currentUser) {
    openModal('login');
    showToast('Please log in to make a reservation.', 'err');
    return;
  }

  const name   = document.getElementById('r-name').value.trim();
  const date   = document.getElementById('r-date').value;
  const time   = document.getElementById('r-time').value;
  const guests = document.getElementById('r-guests').value;
  const notes  = document.getElementById('r-notes').value.trim();

  if (!date || !time) { showToast('Please select a date and time slot.', 'err'); return; }

  try {
    const d = await apiPost({
      action: 'make_reservation',
      customer_name: name,
      date, time_slot: time, guests, notes
    });
    if (d.success) {
      showToast(d.message, 'ok');
      // Clear form
      ['r-name', 'r-date', 'r-notes'].forEach(id => document.getElementById(id).value = '');
      document.getElementById('r-time').value = '';
    } else {
      showToast(d.message, 'err');
    }
  } catch (err) {
    showToast('Could not complete reservation. Please try again.', 'err');
    console.error(err);
  }
}

// ── PROFILE ──────────────────────────────────────
async function loadProfile() {
  if (!currentUser) return;
  const n = currentUser.name || '';
  document.getElementById('profile-av').textContent    = n.split(' ').map(w => w[0]).join('').slice(0, 2).toUpperCase();
  document.getElementById('profile-name').textContent  = n;
  document.getElementById('profile-email').textContent = currentUser.email || '';
  document.getElementById('profile-pet-count').textContent = petsData.length;
}

// ── MENU DATA ─────────────────────────────────────
const menuData = [
  { category:'Coffee',   name:'Caramel Macchiato',   desc:'Espresso layered with vanilla syrup & steamed milk', price:320, pet:false, emoji:'☕' },
  { category:'Coffee',   name:'Cold Brew',            desc:'12-hour slow-brewed perfection, served over ice',   price:280, pet:false, emoji:'🧊' },
  { category:'Coffee',   name:'Puppuccino Latte',     desc:'Our signature latte with a side puppuccino for your dog', price:350, pet:true, emoji:'🐾' },
  { category:'Coffee',   name:'Hazelnut Flat White',  desc:'Bold espresso with velvety steamed milk & hazelnut', price:300, pet:false, emoji:'☕' },
  { category:'Coffee',   name:'Iced Mocha',           desc:'Rich chocolate espresso over ice with whipped cream', price:310, pet:false, emoji:'🍫' },
  { category:'Tea',      name:'Jasmine Green Tea',    desc:'Delicate floral notes with premium green tea leaves', price:220, pet:false, emoji:'🍵' },
  { category:'Tea',      name:'Chamomile Honey Tea',  desc:'Calming chamomile with raw wildflower honey drizzle', price:200, pet:false, emoji:'🌼' },
  { category:'Tea',      name:'Masala Chai',          desc:'Spiced milk tea brewed the traditional way',          price:180, pet:false, emoji:'🫖' },
  { category:'Bites',    name:'Avocado Toast',        desc:'Sourdough, smashed avocado, cherry tomatoes, feta',  price:380, pet:false, emoji:'🥑' },
  { category:'Bites',    name:'Butter Croissant',     desc:'Freshly baked, flaky French-style croissant',        price:180, pet:false, emoji:'🥐' },
  { category:'Bites',    name:'Chicken Sandwich',     desc:'Grilled chicken, lettuce, tomato on toasted brioche', price:420, pet:false, emoji:'🥪' },
  { category:'Bites',    name:'Banana Walnut Cake',   desc:'Moist homemade banana cake with walnut crumble',     price:250, pet:false, emoji:'🍰' },
  { category:'Pet',      name:'Pet Treat Platter',    desc:'Assorted dog biscuits & cat treats — vet approved!', price:150, pet:true,  emoji:'🦴' },
  { category:'Pet',      name:'Puppuccino Cup',       desc:'A small cup of whipped cream, just for your dog!',   price:80,  pet:true,  emoji:'🐶' },
  { category:'Specials', name:'Owner + Pet Combo',    desc:'1 beverage + 1 snack + 1 pet treat. Best value!',   price:520, pet:true,  emoji:'🎉' },
  { category:'Specials', name:'Weekend Brunch Box',   desc:'Eggs, toast, salad, coffee — full brunch for two',   price:750, pet:false, emoji:'🍳' },
];

function renderMenu(filter = 'all') {
  const grid  = document.getElementById('menuGrid');
  let items;
  if (filter === 'all')  items = menuData;
  else if (filter === 'pet') items = menuData.filter(i => i.pet);
  else items = menuData.filter(i => i.category === filter);

  grid.innerHTML = items.map(i => `
    <div class="menu-card">
      <span class="menu-emoji">${i.emoji}</span>
      <div class="menu-cat">${i.category}</div>
      <div class="menu-name">${escHtml(i.name)}</div>
      <p class="menu-desc">${escHtml(i.desc)}</p>
      <div class="menu-footer">
        <span class="menu-price">৳${i.price}</span>
        ${i.pet ? '<span class="pet-badge">🐾 Pet Safe</span>' : ''}
      </div>
    </div>
  `).join('');
}

function filterMenu(cat, btn) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  btn.classList.add('active');
  renderMenu(cat);
}

// ── SCROLL REVEAL ────────────────────────────────
const revealObserver = new IntersectionObserver(entries => {
  entries.forEach(e => {
    if (e.isIntersecting) {
      e.target.style.opacity   = '1';
      e.target.style.transform = 'translateY(0)';
    }
  });
}, { threshold: 0.1 });

document.querySelectorAll('.why-card, .step, .testi-card, .mf-item').forEach((el, i) => {
  el.style.opacity   = '0';
  el.style.transform = 'translateY(30px)';
  el.style.transition = `opacity .5s ${i * 0.08}s ease, transform .5s ${i * 0.08}s ease`;
  revealObserver.observe(el);
});

// ── UTILITY ──────────────────────────────────────
function escHtml(str) {
  if (!str) return '';
  return String(str).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}

function formatDate(str) {
  if (!str) return '';
  const d = new Date(str.replace(' ', 'T'));
  return d.toLocaleDateString('en-BD', { day:'numeric', month:'short', year:'numeric', hour:'2-digit', minute:'2-digit' });
}

// ── INIT ─────────────────────────────────────────
// Render menu on load
renderMenu('all');

// Set min date on reservation form
const rDate = document.getElementById('r-date');
if (rDate) rDate.min = new Date().toISOString().split('T')[0];

// Restore state from PHP session (injected into window.PP_SESSION by index.php)
if (window.PP_SESSION && window.PP_SESSION.loggedIn) {
  currentUser = {
    name:  window.PP_SESSION.name,
    email: window.PP_SESSION.email,
    id:    window.PP_SESSION.userId
  };
  updateNavForUser(currentUser.name);
}
