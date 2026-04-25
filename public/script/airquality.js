/* ── AIR QUALITY CHART ── */

// 1. Inisialisasi Context dan Gradient
const airCtx = document.getElementById("airChart").getContext("2d");
const airGrad = airCtx.createLinearGradient(0, 0, 0, 200);
airGrad.addColorStop(0, "rgba(0,212,255,0.35)");
airGrad.addColorStop(1, "rgba(0,212,255,0.0)");

// 2. Buat Instance Chart (Kosongkan data awal)
const airChart = new Chart(airCtx, {
    type: "line",
    data: {
        labels: [], // Akan diisi secara dinamis dari API
        datasets: [
            {
                label: "Gas (ppm)",
                data: [], // Akan diisi secara dinamis dari API
                fill: true,
                backgroundColor: airGrad,
                borderColor: "#00d4ff",
                borderWidth: 2,
                pointBackgroundColor: "#00d4ff",
                pointRadius: 3,
                pointHoverRadius: 6,
                tension: 0.4,
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: { display: false },
            tooltip: {
                backgroundColor: "#101d2e",
                borderColor: "#1a3050",
                borderWidth: 1,
                titleColor: "#00d4ff",
                bodyColor: "#c8dff0",
                titleFont: { family: "Share Tech Mono", size: 11 },
                bodyFont: { family: "Share Tech Mono", size: 11 },
            },
        },
        scales: {
            x: {
                grid: { color: "rgba(26,48,80,0.6)" },
                ticks: {
                    color: "#4a6580",
                    font: { family: "Share Tech Mono", size: 9 },
                },
            },
            y: {
                grid: { color: "rgba(26,48,80,0.6)" },
                ticks: {
                    color: "#4a6580",
                    font: { family: "Share Tech Mono", size: 9 },
                },
                suggestedMin: 100,
                suggestedMax: 600,
            },
        },
    },
});

// 3. Fungsi Asynchronous untuk Fetch Data API
async function fetchSensorData() {
    try {
        // Sesuaikan endpoint. Jika route ada di api.php, gunakan '/api/sensor-logs'
        const response = await fetch("/sensor-logs");

        if (!response.ok) throw new Error("Gagal mengambil data dari API");

        const result = await response.json();

        // --- UPDATE GRAFIK GAS ---
        if (result.grafik_gas && result.grafik_gas.length > 0) {
            // Map created_at menjadi format waktu (misal: 14:30)
            const newLabels = result.grafik_gas.map((item) => {
                const date = new Date(item.created_at);
                return date.toLocaleTimeString("id-ID", {
                    hour: "2-digit",
                    minute: "2-digit",
                });
            });

            // Map gas_value menjadi array angka
            const newData = result.grafik_gas.map((item) => item.gas_value);

            // Suntikkan data baru ke chart dan update
            airChart.data.labels = newLabels;
            airChart.data.datasets[0].data = newData;
            airChart.update();
        }

        // --- UPDATE PANEL TERKINI (Opsional) ---
        // Jika kamu ingin menghubungkan result.terkini ke UI, kamu bisa uncomment ini:
        if (result.terkini) {
            const currentGas = result.terkini.gas_value;
            const currentDist = result.terkini.parking_distance;

            document.getElementById("gasVal").textContent = currentGas;
            document.getElementById("gasVal").className =
                currentGas > 400
                    ? "s-val danger"
                    : currentGas > 350
                      ? "s-val warn"
                      : "s-val ok";

            document.getElementById("distVal").textContent = currentDist;
            document.getElementById("distLabel").textContent = currentDist;
        }
    } catch (error) {
        console.error("Error Fetch Sensor:", error);
    }
}

// 4. Panggil fungsi saat halaman pertama kali dimuat
fetchSensorData();

// 5. Polling data setiap 5 detik agar grafik live/real-time
// (Hapus bagian /* ── SIMULATE LIVE DATA ── */ yang lama jika menggunakan ini)
setInterval(fetchSensorData, 5000);
