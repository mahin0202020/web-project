<?php
// ============================================================
//  Paws & Pour – Main Entry Point
//  File: index.php
// ============================================================
require_once __DIR__ . '/includes/config.php';

// Grab current session user if logged in
$logged_in   = is_logged_in();
$current_name = $logged_in ? htmlspecialchars($_SESSION['user_name']) : '';
?>





<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>Paws &amp; Pour – Where Every Tail Has a Tale</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=Outfit:wght@300;400;500;600;700&family=Dancing+Script:wght@600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Custom cursor -->
<div id="cursor"></div>
<div id="cursor-dot"></div>

<!-- Toast notification -->
<div id="toast" class="toast"></div>

<!-- ── NAV ──────────────────────────────────── -->
<nav id="mainNav">
  <a href="#" class="logo"><span class="logo-paw">🐾</span> Paws <span style="color:var(--caramel)">&amp;</span> Pour</a>
  <ul class="nav-links">
    <li><a href="#why">About</a></li>
    <li><a href="#menu">Menu</a></li>
    <li><a href="#monitor">Pet Monitor</a></li>
    <li><a href="#reserve">Reserve</a></li>
    <li><a href="#testimonials">Reviews</a></li>
  </ul>
  <div id="user-nav-area">
    <?php if ($logged_in): ?>
      <?php $initials = strtoupper(implode('', array_map(fn($w)=>$w[0], array_slice(explode(' ', $current_name), 0, 2)))); ?>
      <button class="user-chip" onclick="openDashboard('<?= $current_name ?>')">
        <span class="av"><?= $initials ?></span><?= $current_name ?> ▾
      </button>
    <?php else: ?>
      <button class="btn-nav-login" onclick="openModal('login')">Log In</button>
      <button class="btn-nav-signup" onclick="openModal('signup')">Sign Up</button>
    <?php endif; ?>
  </div>
  <button class="hamburger" id="hambBtn" onclick="toggleMenu()">
    <span></span><span></span><span></span>
  </button>
</nav>

<!-- Mobile menu -->
<div class="mobile-menu" id="mobileMenu">
  <button class="close-menu" onclick="toggleMenu()">✕</button>
  <a href="#why" onclick="toggleMenu()">About</a>
  <a href="#menu" onclick="toggleMenu()">Menu</a>
  <a href="#monitor" onclick="toggleMenu()">Pet Monitor</a>
  <a href="#reserve" onclick="toggleMenu()">Reserve</a>
  <a href="#testimonials" onclick="toggleMenu()">Reviews</a>
  <div style="display:flex;gap:12px;margin-top:12px">
    <button class="btn-nav-login" onclick="toggleMenu();openModal('login')">Log In</button>
    <button class="btn-nav-signup" onclick="toggleMenu();openModal('signup')">Sign Up</button>
  </div>
</div>

<!-- ── HERO ──────────────────────────────────── -->
<section id="hero">
  <div class="hero-bg"></div>
  <div class="paw-prints paw1">🐾🐾🐾</div>
  <div class="paw-prints paw2">🐾🐾</div>
  <div class="paw-prints paw3">🐾🐾🐾🐾</div>

  <div class="hero-left">
    <div class="hero-badge">🌿 Dhaka's First Pet-Friendly Café</div>
    <h1 class="hero-h1">Where Every<br><em>Tail Has</em><br>a Tale ☕</h1>
    <p class="hero-p">A cozy café designed for pet owners and their beloved companions. Sip, relax, and bond — all in one beautifully curated space in the heart of the city.</p>
    <div class="hero-btns">
      <button class="btn-prim" onclick="openModal('signup')">🐾 Join the Pack</button>
      <a href="#reserve" class="btn-sec">📅 Book a Table</a>
    </div>
    <div class="hero-stats">
      <div><div class="stat-n">500+</div><div class="stat-l">Happy Pets</div></div>
      <div><div class="stat-n">4.9★</div><div class="stat-l">Rating</div></div>
      <div><div class="stat-n">3 Yrs</div><div class="stat-l">Serving Love</div></div>
    </div>
  </div>

  <div class="hero-right">
    <div class="hero-img-frame">
      <div class="hero-img-placeholder">
        <span class="big-pet">🐶☕🐱</span>
        <p>Paws &amp; Pour Café</p>
        <span style="font-size:.85rem;opacity:.8">A place you both belong</span>
      </div>
    </div>
    <div class="float-card fc1">
      <span class="fc-icon">🐶</span>
      <div><span class="fc-label">Just joined</span><strong>Max is here!</strong></div>
    </div>
    <div class="float-card fc2">
      <span class="fc-icon">✅</span>
      <div><span class="fc-label">Pet-safe menu</span><strong>100% Verified</strong></div>
    </div>
    <div class="float-card fc3">
      <span class="fc-icon">📍</span>
      <div><span class="fc-label">Location</span><strong>Savar, Dhaka</strong></div>
    </div>
  </div>
</section>

<!-- ── MARQUEE ────────────────────────────────── -->
<div class="marquee-bar">
  <div class="marquee-inner">
    <span class="marquee-item">Pet-Friendly Seating</span>
    <span class="marquee-item">Live Pet Monitoring</span>
    <span class="marquee-item">Artisan Coffee</span>
    <span class="marquee-item">Handcrafted Pet Treats</span>
    <span class="marquee-item">Safe &amp; Hygienic Space</span>
    <span class="marquee-item">Community Events</span>
    <span class="marquee-item">Pet-Friendly Seating</span>
    <span class="marquee-item">Live Pet Monitoring</span>
    <span class="marquee-item">Artisan Coffee</span>
    <span class="marquee-item">Handcrafted Pet Treats</span>
    <span class="marquee-item">Safe &amp; Hygienic Space</span>
    <span class="marquee-item">Community Events</span>
  </div>
</div>

<!-- ── WHY US ──────────────────────────────────── -->
<section id="why">
  <div class="center">
    <span class="tag">Why Choose Us</span>
    <h2 class="section-h">More Than Just a Café</h2>
    <p class="section-p">We built Paws &amp; Pour because we believe pets deserve to be included in every moment — not left behind.</p>
  </div>
  <div class="why-grid">
    <div class="why-card"><span class="why-em">🏠</span><h3>Pet-Safe Environment</h3><p>Fully sanitized, climate-controlled spaces. Every corner designed with your pet's comfort and safety in mind.</p></div>
    <div class="why-card"><span class="why-em">📱</span><h3>Live Pet Monitoring</h3><p>Left your pet at the café while you're at work? Watch them through our live camera system anytime, anywhere.</p></div>
    <div class="why-card"><span class="why-em">🍖</span><h3>Pet-Friendly Menu</h3><p>Specially curated treats, biscuits, and snacks for dogs, cats and more — vet-approved and delicious.</p></div>
    <div class="why-card"><span class="why-em">🤝</span><h3>Pet Owner Community</h3><p>Meet fellow pet lovers, share experiences, and join our monthly pet meetups and adoption drives.</p></div>
    <div class="why-card"><span class="why-em">☕</span><h3>Premium Brews</h3><p>Specialty coffee, artisan teas, and freshly baked pastries crafted by experienced baristas every morning.</p></div>
    <div class="why-card"><span class="why-em">🌿</span><h3>Calm &amp; Cozy Vibes</h3><p>Soft lighting, warm wood tones, and ambient music make this your and your pet's favorite weekend retreat.</p></div>
  </div>
</section>

<!-- ── HOW IT WORKS ────────────────────────────── -->
<section id="how">
  <div class="center">
    <span class="tag">How It Works</span>
    <h2 class="section-h">Four Simple Steps</h2>
    <p class="section-p">Getting started is easy. Create your account, register your pet, and let the good times roll.</p>
  </div>
  <div class="steps">
    <div class="step"><div class="step-num">📝</div><h4>Sign Up</h4><p>Create your free account in under 60 seconds.</p></div>
    <div class="step"><div class="step-num">🐾</div><h4>Add Your Pet</h4><p>Register your pet's profile with photo and health notes.</p></div>
    <div class="step"><div class="step-num">📅</div><h4>Reserve a Table</h4><p>Pick your date, time, and preferred seating zone.</p></div>
    <div class="step"><div class="step-num">☕</div><h4>Enjoy Together</h4><p>Arrive, relax, and enjoy a perfect day with your pet!</p></div>
  </div>
</section>

<!-- ── MENU ──────────────────────────────────────── -->
<section id="menu">
  <div class="center">
    <span class="tag">Our Menu</span>
    <h2 class="section-h">Crafted with Love &amp; Care</h2>
    <p class="section-p">For you and for them. Every item is freshly prepared and items marked 🐾 are safe for your pets too!</p>
  </div>
  <div class="menu-tabs">
    <button class="tab-btn active" onclick="filterMenu('all',this)">All Items</button>
    <button class="tab-btn" onclick="filterMenu('Coffee',this)">☕ Coffee</button>
    <button class="tab-btn" onclick="filterMenu('Tea',this)">🍵 Tea</button>
    <button class="tab-btn" onclick="filterMenu('Bites',this)">🥐 Bites</button>
    <button class="tab-btn" onclick="filterMenu('pet',this)">🐾 Pet Friendly</button>
  </div>
  <div class="menu-grid" id="menuGrid"><!-- populated by JS --></div>
</section>

<!-- ── PET MONITORING ─────────────────────────── -->
<section id="monitor">
  <div class="monitor-layout">
    <div>
      <span class="tag">Pet Monitoring System</span>
      <h2 class="section-h">Always Know Your Pet is Safe 🐾</h2>
      <p class="section-p">Our smart monitoring system lets you keep an eye on your fur baby whether you're at a meeting or just at the other end of the café.</p>
      <div class="monitor-features" style="margin-top:36px">
        <div class="mf-item"><div class="mf-icon">📸</div><div><h4>Live Camera Feed</h4><p>Real-time video access to your pet's play area through our secure app dashboard.</p></div></div>
        <div class="mf-item"><div class="mf-icon">📊</div><div><h4>Activity Tracker</h4><p>Log feedings, walks, play sessions and health updates all from your dashboard.</p></div></div>
        <div class="mf-item"><div class="mf-icon">🔔</div><div><h4>Instant Alerts</h4><p>Get notified if your pet seems distressed or needs attention — peace of mind always.</p></div></div>
        <div class="mf-item"><div class="mf-icon">📋</div><div><h4>Pet Health Log</h4><p>Keep a complete history of vet visits, vaccinations, and daily care routines.</p></div></div>
      </div>
      <button class="btn-prim" style="margin-top:32px" onclick="openModal('signup')">🚀 Get Started Free</button>
    </div>
    <div class="monitor-phone">
      <div class="phone-mock">
        <div class="phone-notch"></div>
        <div class="phone-screen">
          <div class="phone-header">
            <strong>Paws &amp; Pour</strong>
            <p>Pet Monitor Live</p>
          </div>
          <div class="pet-status-mini">
            <div class="psm-item"><div class="psm-dot"></div><div><strong>Max</strong> (Labrador)<br><span style="opacity:.6;font-size:.65rem">Playing • 2:34 PM</span></div></div>
            <div class="psm-item"><div class="psm-dot warn"></div><div><strong>Luna</strong> (Persian Cat)<br><span style="opacity:.6;font-size:.65rem">Napping • Fed 1hr ago</span></div></div>
            <div class="psm-item"><div class="psm-dot"></div><div><strong>Buddy</strong> (Beagle)<br><span style="opacity:.6;font-size:.65rem">Active • Needs water</span></div></div>
            <div class="psm-item" style="background:var(--sage-light);"><div class="psm-dot" style="background:#fff"></div><div><strong>All pets</strong> are safe ✓</div></div>
          </div>
        </div>
      </div>
      <div class="phone-float">📍 Live from Café Zone B</div>
      <div class="phone-float2">🎉 Max just got a treat!</div>
    </div>
  </div>
</section>

<!-- ── GALLERY ────────────────────────────────── -->
<!-- <section id="gallery">
  <h2>Moments from Our Café</h2>
  <div class="gallery-scroll">
    <div class="gallery-item gi1"><div class="gallery-placeholder"><span style="font-size:3rem">🐶☕</span><p>Morning Vibes</p></div></div>
    <div class="gallery-item gi2"><div class="gallery-placeholder"><span style="font-size:3rem">🐱🌿</span><p>Cozy Corner</p></div></div>
    <div class="gallery-item gi3"><div class="gallery-placeholder"><span style="font-size:3rem">🐶🎾</span><p>Playtime</p></div></div>
    <div class="gallery-item gi4"><div class="gallery-placeholder"><span style="font-size:3rem">☕🥐</span><p>Fresh Brews</p></div></div>
    <div class="gallery-item gi5"><div class="gallery-placeholder"><span style="font-size:3rem">🐾❤️</span><p>Pet Love</p></div></div>
    <div class="gallery-item gi6"><div class="gallery-placeholder"><span style="font-size:3rem">🐶🐱</span><p>New Friends</p></div></div>
    <div class="gallery-item gi1"><div class="gallery-placeholder"><span style="font-size:3rem">🌸☕</span><p>Weekend Bliss</p></div></div>
  </div>
</section> -->



<section id="gallery">
  <h2>Moments from Our Café </h2>

  <div class="gallery-scroll">

    <div class="gallery-item">
      <img src="one.jpeg" alt="picture of cafe">
    </div>

    <div class="gallery-item">
      <img src="two.jpeg" alt="picture of cafe">
    </div>

    <div class="gallery-item">
      <img src="three.jpeg" alt="picture of cafe">
    </div>

    <div class="gallery-item">
      <img src="four.jpeg" alt="picture of cafe">
    </div>

    <div class="gallery-item">
      <img src="five.jpeg" alt="picture of cafe">
    </div>

    <div class="gallery-item">
      <img src="six.jpeg" alt="picture of cafe">
    </div>

    <div class="gallery-item">
      <img src="seven.jpeg" alt="picture of cafe">
    </div>

    <!-- <div class="gallery-item">
      <img src="images/img8.jpg" alt="Birthday Celebration">
    </div> -->

  </div>
</section>











<!-- ── TESTIMONIALS ───────────────────────────── -->
<section id="testimonials">
  <div class="center">
    <span class="tag">Happy Customers</span>
    <h2 class="section-h">What Pet Parents Say</h2>
  </div>
  <div class="testi-grid">
    <div class="testi-card">
      <div class="testi-stars">★★★★★</div>
      <p class="testi-q">"Finally a place where I don't have to choose between my morning coffee and my dog. Max loves it here — and so do I!"</p>
      <div class="testi-author"><div class="testi-av av-a">R</div><div><div class="testi-name">Rahim Chowdhury</div><div class="testi-pet">🐶 Max, 3yr Labrador</div></div></div>
    </div>
    <div class="testi-card">
      <div class="testi-stars">★★★★★</div>
      <p class="testi-q">"The pet monitoring feature is a lifesaver! I can see Luna on my phone during work breaks. The café is gorgeous too."</p>
      <div class="testi-author"><div class="testi-av av-b">N</div><div><div class="testi-name">Nadia Islam</div><div class="testi-pet">🐱 Luna, Persian Cat</div></div></div>
    </div>
    <div class="testi-card">
      <div class="testi-stars">★★★★★</div>
      <p class="testi-q">"The staff understands pets so well. They welcomed Buddy like a VIP guest. The puppuccino is his absolute favorite!"</p>
      <div class="testi-author"><div class="testi-av av-c">T</div><div><div class="testi-name">Tahmid Hassan</div><div class="testi-pet">🐶 Buddy, 2yr Beagle</div></div></div>
    </div>
  </div>
</section>

<!-- ── RESERVATION ────────────────────────────── -->
<section id="reserve">
  <div class="reserve-wrap">
    <span class="tag">Table Reservation</span>
    <h2 class="section-h">Book Your Spot Today</h2>
    <p class="section-p">Reserve a cozy table for you and your pet. Walk-ins welcome but bookings get priority seating!</p>
    <div class="reserve-form" id="reserveForm">
      <div class="form-field"><label>Your Name</label><input type="text" id="r-name" placeholder="Enter your name"></div>
      <div class="form-field"><label>Date</label><input type="date" id="r-date"></div>
      <div class="form-field">
        <label>Time Slot</label>
        <select id="r-time">
          <option value="">Select time</option>
          <option value="08:00 AM – 10:00 AM">08:00 AM – 10:00 AM</option>
          <option value="10:00 AM – 12:00 PM">10:00 AM – 12:00 PM</option>
          <option value="12:00 PM – 02:00 PM">12:00 PM – 02:00 PM</option>
          <option value="02:00 PM – 04:00 PM">02:00 PM – 04:00 PM</option>
          <option value="04:00 PM – 06:00 PM">04:00 PM – 06:00 PM</option>
          <option value="06:00 PM – 08:00 PM">06:00 PM – 08:00 PM</option>
        </select>
      </div>
      <div class="form-field">
        <label>Number of Guests</label>
        <select id="r-guests">
          <option value="1">1</option>
          <option value="2">2</option>
          <option value="3">3</option>
          <option value="4">4</option>
          <option value="5">5+</option>
        </select>
      </div>
      <div class="form-field full"><label>Special Notes (optional)</label>
        <textarea id="r-notes" placeholder="Any dietary needs, pet breed info, accessibility needs..."></textarea>
      </div>
    </div>
    <button class="btn-prim" style="margin-top:24px;width:100%;justify-content:center" onclick="makeReservation()">
      🐾 Confirm My Reservation
    </button>
  </div>
</section>

<!-- ── FOOTER ──────────────────────────────────── -->
<footer>
  <div class="footer-grid">
    <div class="footer-brand">
      <a href="#" class="logo">🐾 Paws &amp; Pour</a>
      <p>A pet-friendly café in Savar, Dhaka where every visit is a memory for you and your beloved companion.</p>
      <div class="footer-socials">
        <a class="social-btn" href="#" title="Facebook">📘</a>
        <a class="social-btn" href="#" title="Instagram">📸</a>
        <a class="social-btn" href="#" title="TikTok">🎵</a>
        <a class="social-btn" href="#" title="WhatsApp">💬</a>
      </div>
    </div>
    <div class="footer-col">
      <h5>Quick Links</h5>
      <ul>
        <li><a href="#why">About Us</a></li>
        <li><a href="#menu">Our Menu</a></li>
        <li><a href="#monitor">Pet Monitor</a></li>
        <li><a href="#reserve">Reservations</a></li>
        <li><a href="#gallery">Gallery</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h5>For Pet Owners</h5>
      <ul>
        <li><a href="#" onclick="openModal('signup')">Create Account</a></li>
        <li><a href="#" onclick="openModal('login')">Login</a></li>
        <li><a href="#">Pet Care Tips</a></li>
        <li><a href="#">Pet Events</a></li>
        <li><a href="#">Adoption Drive</a></li>
      </ul>
    </div>
    <div class="footer-col">
      <h5>Contact Us</h5>
      <ul>
        <li><a href="#">📍 Savar, Dhaka</a></li>
        <li><a href="tel:+8801700000000">📞 +880 1700-000000</a></li>
        <li><a href="mailto:hello@pawsandpour.com">✉️ hello@pawsandpour.com</a></li>
        <li><a href="#">🕗 Open 8AM – 9PM daily</a></li>
      </ul>
    </div>
  </div>
  <div class="footer-bottom">
    <span>© 2025 Paws &amp; Pour. All rights reserved. Made with ❤️ for pets &amp; people.</span>
    <span>Privacy Policy · Terms of Service</span>
  </div>
</footer>

<!-- ══════════════════════════════════════════════
AUTH MODAL
══════════════════════════════════════════════ -->
<div class="modal-overlay" id="authModal">
  <div class="modal">
    <button class="modal-close" onclick="closeModal('authModal')">✕</button>
    <div class="modal-logo">🐾 Paws &amp; Pour</div>
    <p class="modal-sub">Your pet-friendly café companion</p>
    <div class="modal-tabs">
      <div class="mtab active" id="tab-login" onclick="switchTab('login')">Log In</div>
      <div class="mtab" id="tab-signup" onclick="switchTab('signup')">Sign Up</div>
    </div>
    <div id="auth-msg" class="modal-msg"></div>

    <!-- LOGIN FORM -->
    <div class="modal-form active" id="form-login">
      <div class="mform-field"><label>Email Address</label><input type="email" id="l-email" placeholder="you@email.com" autocomplete="email"></div>
      <div class="mform-field"><label>Password</label><input type="password" id="l-pass" placeholder="Your password" autocomplete="current-password"></div>
      <button class="btn-prim btn-block" onclick="doLogin()">☕ Log In</button>
      <div class="or-divider">or</div>
      <p class="auth-switch">Don't have an account? <a href="#" onclick="switchTab('signup')">Sign up free</a></p>
    </div>

    <!-- SIGNUP FORM -->
    <div class="modal-form" id="form-signup">
      <div class="mform-field"><label>Full Name *</label><input type="text" id="s-name" placeholder="Your full name" autocomplete="name"></div>
      <div class="mform-field"><label>Email Address *</label><input type="email" id="s-email" placeholder="you@email.com" autocomplete="email"></div>
      <div class="mform-field"><label>Phone (optional)</label><input type="tel" id="s-phone" placeholder="+880 1XXX-XXXXXX"></div>
      <div class="mform-field"><label>Password *</label><input type="password" id="s-pass" placeholder="Min. 6 characters" autocomplete="new-password"></div>
      <button class="btn-prim btn-block" onclick="doSignup()">🐾 Create Account</button>
      <div class="or-divider">or</div>
      <p class="auth-switch">Already a member? <a href="#" onclick="switchTab('login')">Log in</a></p>
    </div>
  </div>
</div>

<!-- ══════════════════════════════════════════════
DASHBOARD MODAL
══════════════════════════════════════════════ -->
<div class="modal-overlay" id="dashModal">
  <div class="modal dash-modal">
    <button class="modal-close" onclick="closeModal('dashModal')">✕</button>
    <h2 class="dash-welcome" id="dash-welcome">Welcome back! 🐾</h2>
    <p class="dash-sub">Manage your pets, view activity logs, and more.</p>

    <div class="dash-tabs">
      <button class="dtab active" onclick="switchDash('pets',this)">🐾 My Pets</button>
      <button class="dtab" onclick="switchDash('monitor',this)">📊 Activity Log</button>
      <button class="dtab" onclick="switchDash('addpet',this)">➕ Add Pet</button>
      <button class="dtab" onclick="switchDash('reservations',this)">📅 My Bookings</button>
      <button class="dtab" onclick="switchDash('profile',this)">👤 Profile</button>
      <button class="dtab dtab-logout" onclick="doLogout()">🚪 Logout</button>
    </div>

    <!-- MY PETS -->
    <div class="dash-panel active" id="dp-pets">
      <div class="pets-grid" id="pets-grid">
        <div class="loading-msg">Loading your pets...</div>
      </div>
    </div>

    <!-- ACTIVITY LOG -->
    <div class="dash-panel" id="dp-monitor">
      <p class="dash-hint">Select a pet from <strong>My Pets</strong> tab, then log activities here.</p>
      <div id="selected-pet-name" class="selected-pet-title"></div>
      <div class="log-form">
        <div>
          <label class="inline-label">Activity Type</label>
          <select class="inline-input" id="log-type">
            <option value="feeding">🍖 Feeding</option>
            <option value="walk">🦮 Walk</option>
            <option value="play">🎾 Play</option>
            <option value="grooming">✂️ Grooming</option>
            <option value="vet">💉 Vet Visit</option>
            <option value="note">📝 Note</option>
          </select>
        </div>
        <div>
          <label class="inline-label">Description</label>
          <input class="inline-input" type="text" id="log-desc" placeholder="e.g. Fed 200g kibble at 8am">
        </div>
        <div class="full">
          <button class="btn-prim" onclick="logActivity()">📝 Log Activity</button>
        </div>
      </div>
      <div style="margin-top:24px">
        <h4 class="log-section-title">Recent Activity</h4>
        <div class="activity-log" id="activity-log">
          <div class="empty-log">Select a pet and activities will appear here.</div>
        </div>
      </div>
    </div>

    <!-- ADD PET -->
    <div class="dash-panel" id="dp-addpet">
      <h3 class="dash-panel-title">Register a New Pet</h3>
      <div class="add-pet-grid">
        <div><label class="inline-label">Pet Name *</label><input class="inline-input" type="text" id="p-name" placeholder="e.g. Max"></div>
        <div>
          <label class="inline-label">Species *</label>
          <select class="inline-input" id="p-species">
            <option value="dog">🐶 Dog</option>
            <option value="cat">🐱 Cat</option>
            <option value="rabbit">🐰 Rabbit</option>
            <option value="bird">🐦 Bird</option>
            <option value="other">🐾 Other</option>
          </select>
        </div>
        <div><label class="inline-label">Breed</label><input class="inline-input" type="text" id="p-breed" placeholder="e.g. Labrador"></div>
        <div><label class="inline-label">Age</label><input class="inline-input" type="text" id="p-age" placeholder="e.g. 2 years"></div>
        <div><label class="inline-label">Weight (kg)</label><input class="inline-input" type="number" id="p-weight" placeholder="e.g. 12.5" step="0.1" min="0"></div>
        <div><label class="inline-label">Health Notes</label><input class="inline-input" type="text" id="p-notes" placeholder="Allergies, diet info..."></div>
        <div style="grid-column:1/-1;margin-top:8px">
          <button class="btn-prim" onclick="addPet()">🐾 Add My Pet</button>
        </div>
      </div>
    </div>

    <!-- MY RESERVATIONS -->
    <div class="dash-panel" id="dp-reservations">
      <h3 class="dash-panel-title">My Reservations</h3>
      <div id="reservations-list">
        <div class="loading-msg">Loading reservations...</div>
      </div>
    </div>

    <!-- PROFILE -->
    <div class="dash-panel" id="dp-profile">
      <div class="profile-header">
        <div class="profile-avatar" id="profile-av">?</div>
        <div>
          <h3 class="profile-name" id="profile-name">Loading...</h3>
          <p class="profile-email" id="profile-email"></p>
        </div>
      </div>
      <div class="profile-stats">
        <div class="pstat-card">
          <div class="pstat-num" id="profile-pet-count">0</div>
          <div class="pstat-label">Pets Registered</div>
        </div>
        <div class="pstat-card">
          <div class="pstat-num" id="profile-res-count">0</div>
          <div class="pstat-label">Reservations</div>
        </div>
        <div class="pstat-card">
          <div class="pstat-num">🌟</div>
          <div class="pstat-label">Member Status</div>
        </div>
      </div>
    </div>

  </div>
</div>

<!-- Pass PHP session state to JS -->
<script>
  window.PP_SESSION = {
    loggedIn: <?= $logged_in ? 'true' : 'false' ?>,
    name:     <?= json_encode($current_name) ?>,
    userId:   <?= $logged_in ? (int)$_SESSION['user_id'] : 'null' ?>,
    email:    <?= json_encode($logged_in ? ($_SESSION['user_email'] ?? '') : '') ?>
  };
</script>
<script src="assets/js/script.js"></script>
</body>
</html>
