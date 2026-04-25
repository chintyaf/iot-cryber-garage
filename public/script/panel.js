/* ── DISTANCE RING ANIMATE (Tetap sama) ── */
/* ── FUNGSI ASYNC UNTUK MENGAMBIL DATA SENSOR LIVE ── */
async function fetchLiveSensorData() {
    try {
        // Sesuaikan URL ini dengan route di api.php kamu (misal: '/api/sensors')
        const response = await fetch("/api/panel/sensor-logs");
        const result = await response.json();

        // Mengambil objek 'terkini' dari response Laravel kamu
        const latestData = result.terkini;

        if (latestData) {
            const currentDist = latestData.parking_distance;
            const currentGas = latestData.gas_value;

            /* 1. UPDATE ANIMASI LINGKARAN JARAK (DISTANCE RING) */
            const maxDist = 300; // Asumsi jarak maksimal sensor adalah 300 cm
            // Cegah jarak melebihi batas maksimal agar animasi ring tidak error/terbalik
            const safeDist = currentDist > maxDist ? maxDist : currentDist;

            const pct = safeDist / maxDist;
            const circ = 2 * Math.PI * 80;
            const offset = circ * (1 - pct * 0.85);
            document.getElementById("ringFill").style.strokeDashoffset = offset;

            /* 2. UPDATE TEKS JARAK */
            document.getElementById("distVal").textContent = currentDist;
            document.getElementById("distLabel").textContent = currentDist;

            /* 3. UPDATE TEKS DAN WARNA GAS */
            document.getElementById("gasVal").textContent = currentGas;

            // Logika pewarnaan otomatis berdasarkan tingkat gas
            document.getElementById("gasVal").className =
                currentGas > 400
                    ? "s-val danger"
                    : currentGas > 350
                      ? "s-val warn"
                      : "s-val ok";
        }
    } catch (error) {
        console.error("Gagal mengambil data sensor live:", error);
    }
}

/* ── JALANKAN PROGRAM ── */
// 1. Panggil fungsi sekali agar saat halaman pertama dibuka, data langsung terisi
fetchLiveSensorData();

// 2. Gunakan setInterval untuk mengecek data baru ke database setiap 4 detik
setInterval(fetchLiveSensorData, 1000);
