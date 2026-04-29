/* ── PARKING CHART ── */
const parkLabels = ["Sen", "Sel", "Rab", "Kam", "Jum", "Sab", "Min"];
const parkData = [4, 6, 3, 7, 5, 8, 2];

const pkCtx = document.getElementById("parkChart").getContext("2d");
const pkGrad = pkCtx.createLinearGradient(0, 0, 0, 200);
pkGrad.addColorStop(0, "rgba(255,107,53,0.8)");
pkGrad.addColorStop(1, "rgba(255,107,53,0.2)");

async function renderParkChart() {
    try {
        // 1. Ambil data dari API Laravel
        const response = await fetch("/api/parking-logs"); // Sesuaikan URL route kamu
        const apiData = await response.json();

        // 2. Map data API ke format Chart.js
        const labels = apiData.map((item) => item.label); // Menjadi ["Sen", "Sel", ...]
        const values = apiData.map((item) => item.total); // Menjadi [4, 6, ...]

        const pkCtx = document.getElementById("parkChart").getContext("2d");
        const pkGrad = pkCtx.createLinearGradient(0, 0, 0, 200);
        pkGrad.addColorStop(0, "rgba(255,107,53,0.8)");
        pkGrad.addColorStop(1, "rgba(255,107,53,0.2)");

        // 3. Render Chart
        new Chart(pkCtx, {
            type: "bar",
            data: {
                labels: labels, // Data dari API
                datasets: [
                    {
                        label: "Frekuensi Parkir",
                        data: values, // Data dari API
                        backgroundColor: pkGrad,
                        borderColor: "#ff6b35",
                        borderWidth: 1,
                        borderRadius: 4,
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
                        titleFont: { family: "Share Tech Mono", size: 11 },
                        bodyFont: { family: "Share Tech Mono", size: 11 },
                    },
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: "#7998b8",
                            font: { family: "Share Tech Mono", size: 9 },
                        },
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: "rgba(26,48,80,0.6)" },
                        ticks: {
                            color: "#7998b8",
                            font: { family: "Share Tech Mono", size: 9 },
                            stepSize: 1, // Karena jumlah parkir biasanya bulat
                        },
                    },
                },
            },
        });
    } catch (error) {
        console.error("Gagal mengambil data parkir:", error);
    }
}

// Jalankan fungsi
renderParkChart();

// setInterval(renderParkChart, 5 * 60 * 1000);
