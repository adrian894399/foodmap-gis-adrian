let semuaPenyedia = [];   // semua data dari database
let daftarPenyedia = [];  // hasil filter yang ditampilkan
let posisiPengguna = null;

const rupiah = n => "Rp " + Number(n).toLocaleString("id-ID");
const esc = s => String(s ?? "").replace(/[&<>"']/g, c => ({ "&": "&amp;", "<": "&lt;", ">": "&gt;", '"': "&quot;", "'": "&#39;" }[c]));

// Jarak garis lurus (rumus Haversine), dalam km
function hitungJarak(lat1, lng1, lat2, lng2) {
  const R = 6371, rad = d => d * Math.PI / 180;
  const a = Math.sin(rad(lat2 - lat1) / 2) ** 2 +
            Math.cos(rad(lat1)) * Math.cos(rad(lat2)) * Math.sin(rad(lng2 - lng1) / 2) ** 2;
  return R * 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1 - a));
}

async function muatPenyedia() {
  try {
    const respon = await fetch("api_penyedia.php");
    semuaPenyedia = await respon.json();
    isiKategori();
    terapkanFilter();
  } catch (err) {
    document.getElementById("daftar").innerHTML = "<li>Gagal memuat data.</li>";
    console.error(err);
  }
}

function tampilkanDaftar() {
  const ul = document.getElementById("daftar");
  ul.innerHTML = "";
  daftarPenyedia.forEach(p => {
    const li = document.createElement("li");
    li.dataset.id = p.id_penyedia;
    li.className = "kat-" + p.kategori.toLowerCase();
    let jarak = "";
    if (posisiPengguna && p.jarak != null) jarak = " • " + p.jarak.toFixed(1) + " km (garis lurus)";
    li.innerHTML = "<strong>" + esc(p.nama_penyedia) + "</strong><small>" + esc(p.kategori) + jarak + "</small>";
    li.addEventListener("click", () => pilihPenyedia(p.id_penyedia));
    ul.appendChild(li);
  });
}

// Fitur 1 & 4: tampilkan lokasi di iframe + detail
function pilihPenyedia(id) {
  const p = semuaPenyedia.find(x => x.id_penyedia === id);
  if (!p) return;

  document.querySelectorAll("#daftar li").forEach(li => li.classList.toggle("aktif", Number(li.dataset.id) === id));

  const q = p.latitude + "," + p.longitude + "(" + p.nama_penyedia + ")";
  document.getElementById("peta").src =
    "https://maps.google.com/maps?q=" + encodeURIComponent(q) + "&z=16&output=embed";

  // Fitur 5: rute lewat Google Maps URLs (asal otomatis = lokasi pengguna)
  const linkRute = "https://www.google.com/maps/dir/?api=1&destination=" +
    p.latitude + "," + p.longitude + "&travelmode=driving";

  const menuHtml = p.menu.map(m =>
    "<li><span>" + esc(m.nama_menu) + "</span><span class='titik'></span><span class='harga'>" + rupiah(m.harga) + "</span></li>").join("");
  const jarakHtml = (posisiPengguna && p.jarak != null)
    ? "<p class='kecil'>Jarak garis lurus dari posisi Anda: " + p.jarak.toFixed(1) + " km</p>" : "";

  document.getElementById("detail").innerHTML =
    "<div class='detail-grid'>" +
      "<div>" +
        "<img class='foto' src='foto/" + esc(p.foto) + "' alt='' onerror=\"this.style.display='none'\">" +
        "<span class='chip kat-" + esc(p.kategori.toLowerCase()) + "'>" + esc(p.kategori) + "</span>" +
        "<h2>" + esc(p.nama_penyedia) + "</h2>" +
        "<p>" + esc(p.alamat) + "</p>" +
        "<p>Buka " + esc(p.jam_buka) + "–" + esc(p.jam_tutup) + " • Telp " + esc(p.no_telepon) + "</p>" + jarakHtml +
        "<a class='rute' href='" + linkRute + "' target='_blank' rel='noopener'>Buka rute di Google Maps</a>" +
      "</div>" +
      "<div><h3>Menu</h3><ul class='menu'>" + menuHtml + "</ul></div>" +
    "</div>";
}

// Fitur 6: lokasi pengguna, lalu urutkan daftar dari yang terdekat
document.getElementById("btnLokasi").addEventListener("click", () => {
  if (!navigator.geolocation) return alert("Browser tidak mendukung geolokasi.");
  navigator.geolocation.getCurrentPosition(
    pos => {
      posisiPengguna = { lat: pos.coords.latitude, lng: pos.coords.longitude };
      semuaPenyedia.forEach(p => p.jarak = hitungJarak(posisiPengguna.lat, posisiPengguna.lng, p.latitude, p.longitude));
      semuaPenyedia.sort((a, b) => a.jarak - b.jarak);
      document.getElementById("infoLokasi").textContent =
        "Lokasi Anda: " + posisiPengguna.lat.toFixed(5) + ", " + posisiPengguna.lng.toFixed(5) + " — daftar diurutkan dari yang terdekat.";
      terapkanFilter();
    },
    () => alert("Izin lokasi ditolak atau lokasi tidak tersedia.")
  );
});

// Fitur 3: pencarian dan filter (nama, kategori, rentang harga)
function isiKategori() {
  const sel = document.getElementById("filterKategori");
  [...new Set(semuaPenyedia.map(p => p.kategori))].sort().forEach(k => {
    const o = document.createElement("option");
    o.value = k; o.textContent = k;
    sel.appendChild(o);
  });
}

function terapkanFilter() {
  const nama = document.getElementById("cariNama").value.trim().toLowerCase();
  const kat  = document.getElementById("filterKategori").value;
  const min  = document.getElementById("hargaMin").value;
  const max  = document.getElementById("hargaMax").value;
  const hMin = min === "" ? 0 : Number(min);
  const hMax = max === "" ? Infinity : Number(max);

  daftarPenyedia = semuaPenyedia.filter(p =>
    (nama === "" || p.nama_penyedia.toLowerCase().includes(nama)) &&
    (kat === "" || p.kategori === kat) &&
    // lolos jika ada minimal satu menu dalam rentang harga
    p.menu.some(m => m.harga >= hMin && m.harga <= hMax)
  );
  tampilkanDaftar();
  const ul = document.getElementById("daftar");
  if (daftarPenyedia.length === 0) ul.innerHTML = "<li class='kecil'>Tidak ada penyedia yang cocok.</li>";
}

["cariNama", "hargaMin", "hargaMax"].forEach(id => document.getElementById(id).addEventListener("input", terapkanFilter));
document.getElementById("filterKategori").addEventListener("change", terapkanFilter);
document.getElementById("btnReset").addEventListener("click", () => {
  document.getElementById("cariNama").value = "";
  document.getElementById("filterKategori").value = "";
  document.getElementById("hargaMin").value = "";
  document.getElementById("hargaMax").value = "";
  terapkanFilter();
});

muatPenyedia();
