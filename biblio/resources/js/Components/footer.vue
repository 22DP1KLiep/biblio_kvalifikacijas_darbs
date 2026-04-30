<script setup>
const year = new Date().getFullYear()
</script>

<template>
  <footer class="footer">

    <div class="footer-container">

      <!-- LOGO / PAR -->
      <div class="footer-section">
        <h2 class="footer-logo">Biblio</h2>
        <p class="footer-text">
          Atrodi, saglabā un dalies ar savām iecienītākajām grāmatām.
        </p>
      </div>

      <!-- NAV -->
      <div class="footer-section">
        <h3>Navigācija</h3>
        <ul>
          <li><a href="/">Sākums</a></li>
          <li><a href="/gramatas">Grāmatas</a></li>
        </ul>
      </div>

      <!-- KONTAKTI -->
      <div class="footer-section">
        <h3>Kontakti</h3>
        <p>Email: info@biblio.lv</p>
        <p>Tel: +371 00000000</p>
      </div>

      <!-- SOCIAL -->
      <div class="footer-section">
        <h3>Seko mums</h3>
        <div class="socials">
          <a href="#">FB</a>
          <a href="#">IG</a>
          <a href="#">TW</a>
        </div>
      </div>

    </div>

    <!-- BOTTOM -->
    <div class="footer-bottom">
      <p>© {{ year }} Biblio. Visas tiesības aizsargātas.</p>
    </div>

  </footer>
</template>

<style scoped>
.footer {
  background: linear-gradient(135deg, #213555, #3E5879);
  color: #f5efe7;
  padding: 50px 6% 20px;
}

/* GRID */
.footer-container {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 30px;
  max-width: 1200px;
  margin: 0 auto;
}

/* SECTION */
.footer-section h3 {
  margin-bottom: 12px;
  font-size: 15px;
  color: #ffffff;
}

.footer-text {
  font-size: 13px;
  color: #d1d5db;
  line-height: 1.6;
}

/* LOGO */
.footer-logo {
  font-size: 20px;
  font-weight: bold;
  margin-bottom: 10px;
}

/* LINKS */
.footer-section ul {
  list-style: none;
  padding: 0;
}

.footer-section li {
  margin-bottom: 6px;
}

.footer-section a {
  text-decoration: none;
  color: #d1d5db;
  font-size: 13px;
  transition: 0.2s;
}

.footer-section a:hover {
  color: #e6722a;
}

/* SOCIAL */
.socials {
  display: flex;
  gap: 10px;
}

.socials a {
  background: rgba(255,255,255,0.1);
  padding: 6px 10px;
  border-radius: 8px;
  font-size: 12px;
}

.socials a:hover {
  background: #e6722a;
  color: white;
}

/* BOTTOM */
.footer-bottom {
  text-align: center;
  margin-top: 40px;
  font-size: 12px;
  color: #aaa;
  border-top: 1px solid rgba(255,255,255,0.1);
  padding-top: 15px;
}
</style>