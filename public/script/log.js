/* ── LOG TABLE + CARDS ── */
const tagMap = {
    sensor: "tag-sensor",
    action: "tag-action",
    alert: "tag-alert",
};

const statusClassMap = {
    PARKIR: "lcs-parkir",
    NORMAL: "lcs-normal",
    TINGGI: "lcs-tinggi",
    AUTO: "lcs-auto",
    MASUK: "lcs-masuk",
};

/* ── FUNGSI ASYNC UNTUK MENGAMBIL DATA LOG API ── */
async function loadSystemLogs() {
    try {
        // Sesuaikan URL: Jika route di api.php gunakan '/api/system-logs'
        // Jika route di web.php gunakan '/system-logs'
        const response = await fetch("/api/system-logs");
        const result = await response.json();

        const logs = result.data; // Mengambil array data dari response Laravel

        const tbody = document.getElementById("logBody");
        const logCards = document.getElementById("logCards");

        // Bersihkan isi tabel/kartu sebelum merender ulang
        tbody.innerHTML = "";
        logCards.innerHTML = "";

        logs.forEach((l) => {
            // 1. Format Waktu (created_at dari Laravel menjadi Jam:Menit:Detik)
            const dateObj = new Date(l.created_at);
            const timeString = dateObj.toLocaleTimeString("id-ID", {
                hour: "2-digit",
                minute: "2-digit",
                second: "2-digit",
            });

            // 2. Normalisasi Tipe (karena di database kamu tipe huruf besar 'SENSOR')
            const logType = l.type.toLowerCase();

            // 3. Render Desktop Table
            const tr = document.createElement("tr");
            tr.innerHTML = `
            <td class="td-time">${timeString}</td>
            <td><span class="tag ${tagMap[logType] || "tag-action"}">${l.type.toUpperCase()}</span></td>
            <td>${l.parameter}</td>
            <td style="font-family:'Share Tech Mono',monospace">${l.value}</td>
            <td>${l.status}</td>
            `;
            tbody.appendChild(tr);

            // 4. Render Mobile Cards
            const card = document.createElement("div");
            card.className = "log-card";
            const sClass =
                statusClassMap[l.status.toUpperCase()] || "lcs-normal";
            card.innerHTML = `
            <div class="log-card-time">${timeString}</div>
            <div class="log-card-param">
              <span class="log-card-tag tag ${tagMap[logType] || "tag-action"}">${l.type.toUpperCase()}</span>${l.parameter}
            </div>
            <div class="log-card-status ${sClass}">${l.status}</div>
            <div class="log-card-val">${l.value}</div>
            `;
            logCards.appendChild(card);
        });
    } catch (error) {
        console.error("Gagal mengambil data system logs:", error);
    }
}

// Jalankan saat halaman pertama dimuat
loadSystemLogs();

// (Opsional) Refresh data log otomatis setiap 5 detik
// setInterval(loadSystemLogs, 5000);
