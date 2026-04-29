/* ── AIR QUALITY CHART ── */

// 1. Inisialisasi Context dan Gradient
const airCtx = document.getElementById("airChart").getContext("2d");
const airGrad = airCtx.createLinearGradient(0, 0, 0, 200);
airGrad.addColorStop(0, "rgba(0,212,255,0.35)");
airGrad.addColorStop(1, "rgba(0,212,255,0.0)");

// 2. Buat Instance Chart
const airChart = new Chart(airCtx, {
    type: "line",
    data: {
        labels: [],
        datasets: [
            {
                label: "Gas (ppm)",
                data: [],
                fill: true,
                backgroundColor: airGrad,
                borderColor: "#00d4ff",
                borderWidth: 2,
                pointBackgroundColor: "#00d4ff",
                pointRadius: 3,
                tension: 0.4,
            },
        ],
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                grid: { color: "rgba(26,48,80,0.6)" },
                ticks: {
                    color: "#7998b8",
                    font: { family: "Share Tech Mono", size: 9 },
                },
            },
            y: {
                grid: { color: "rgba(26,48,80,0.6)" },
                ticks: {
                    color: "#7998b8",
                    font: { family: "Share Tech Mono", size: 9 },
                },
                suggestedMin: 0,
                suggestedMax: 600,
            },
        },
        plugins: {
            legend: { display: false },
        },
    },
});

// 3. Fungsi Fetch Data API
async function fetchSensorData() {
    try {
        // PERBAIKAN: Tambahkan prefix /api jika file route berada di api.php
        const response = await fetch("/api/sensor-logs");

        if (!response.ok) throw new Error("Gagal mengambil data dari API");

        const result = await response.json();

        // --- UPDATE GRAFIK GAS ---
        if (result.grafik_gas && result.grafik_gas.length > 0) {
            const newLabels = result.grafik_gas.map((item) => {
                const date = new Date(item.created_at);
                return date.toLocaleTimeString("id-ID", {
                    hour: "2-digit",
                    minute: "2-digit",
                });
            });

            const newData = result.grafik_gas.map((item) => item.gas_value);

            airChart.data.labels = newLabels;
            airChart.data.datasets[0].data = newData;
            airChart.update("none"); // 'none' agar update lebih ringan tanpa animasi berlebih
        }
    } catch (error) {
        console.error("Error Fetch Sensor:", error);
    }
}

// 4. Inisialisasi & Polling
fetchSensorData();
// const delay = 1 * 60 * 1000;
// setInterval(fetchSensorData, delay);

// Konfigurasi Echo yang Dinamis
// window.Pusher = Pusher;
// window.Echo = new Echo({
//     broadcaster: "reverb",
//     key: "edgecommanderkey", // Key kamu
//     wsHost: window.location.hostname, // Ini otomatis jadi 192.168.2.108
//     wsPort: 8080,
//     wssPort: 8080,
//     forceTLS: false, // PAKSA matikan TLS/SSL
//     encrypted: false, // Matikan enkripsi (karena masih lokal)
//     enabledTransports: ["ws", "wss"],
// });
// // Pastikan library Echo & Pusher/Reverb sudah ter-import
// window.Echo.channel("sensors").listen(".SensorUpdated", (e) => {
//     console.log("Data baru masuk via WebSocket:", e.sensorLog);

//     fetchSensorData();
// });
