/* ── DISTANCE RING ANIMATE (Tetap sama) ── */
/* ── FUNGSI ASYNC UNTUK MENGAMBIL DATA SENSOR LIVE ── */
async function fetchLiveSensorData() {
    try {
        const response = await fetch("/api/panel/sensor-logs");
        const result = await response.json();
        const latestData = result.terkini;

        if (latestData) {
            const currentDist = latestData.parking_distance;
            const currentGas = latestData.gas_value;

            /* 1. UPDATE ANIMASI LINGKARAN JARAK */
            const maxDist = 30; // Batas maksimal cm
            const r = 80; // Radius sesuai di HTML
            const circ = 2 * Math.PI * r; // Keliling lingkaran ≈ 502.6

            // Batasi agar tidak minus dan tidak lebih dari maxDist
            const safeDist = Math.min(Math.max(currentDist, 0), maxDist);

            // Perhitungan Persentase:
            // Jika ingin: 30cm = Ring Kosong, 0cm = Ring Penuh (Logika Parkir)
            const pct = (maxDist - safeDist) / maxDist;

            // Jika ingin: 0cm = Ring Kosong, 30cm = Ring Penuh, gunakan:
            // const pct = safeDist / maxDist;

            const offset = circ * (1 - pct);

            const ringFill = document.getElementById("ringFill");
            ringFill.style.strokeDasharray = circ;
            ringFill.style.strokeDashoffset = offset;

            /* 2. UPDATE TEKS */
            document.getElementById("distVal").textContent = currentDist;
            document.getElementById("distLabel").textContent = currentDist;
            document.getElementById("gasVal").textContent = currentGas;

            // Update Status Gas
            const gasElement = document.getElementById("gasVal");
            gasElement.className =
                "s-val " +
                (currentGas > 400
                    ? "danger"
                    : currentGas > 350
                      ? "warn"
                      : "ok");
        }
    } catch (error) {
        console.error("Gagal mengambil data sensor live:", error);
    }
}

/* ── JALANKAN PROGRAM ── */
// 1. Panggil fungsi sekali agar saat halaman pertama dibuka, data langsung terisi
fetchLiveSensorData();

// 2. Gunakan setInterval untuk mengecek data baru ke database setiap 4 detik
// setInterval(fetchLiveSensorData, 1 * 60 * 1000);
// setInterval(fetchLiveSensorData, 60000);
