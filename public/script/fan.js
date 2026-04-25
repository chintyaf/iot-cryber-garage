/* ── FAN TOGGLE & POLLING ── */
let fanOn = false;
const deviceName = "exhaust-fan";

// 1. Fungsi khusus untuk mengelola perubahan UI
function updateFanUI(isActive) {
    fanOn = isActive;

    const btn = document.getElementById("btnFan");
    const dot = document.getElementById("fanDot");
    const icon = document.getElementById("fanIcon");
    const txt = document.getElementById("fanStatusText");
    const fval = document.getElementById("fanStatusVal");
    const mode = document.getElementById("overrideMode");

    dot.classList.toggle("active", fanOn);
    icon.classList.toggle("spinning", fanOn);
    btn.classList.toggle("active-state", fanOn);

    if (fanOn) {
        txt.textContent = "EXHAUST FAN — AKTIF";
        txt.style.color = "var(--green)";
        fval.textContent = "ON";
        fval.className = "s-val ok";
        btn.textContent = "⛔ MATIKAN KIPAS";
        mode.textContent = "MANUAL";
    } else {
        txt.textContent = "EXHAUST FAN — STANDBY";
        txt.style.color = "";
        fval.textContent = "OFF";
        fval.className = "s-val"; // Pastikan class sesuai dengan default di CSS kamu
        btn.innerHTML = "⚡ NYALAKAN KIPAS";
        mode.textContent = "AUTO";
    }
}

// 2. Fungsi Toggle (Saat tombol ditekan user)
async function toggleFan() {
    const btn = document.getElementById("btnFan");
    const last = document.getElementById("lastCmd");
    const targetState = !fanOn;

    try {
        btn.disabled = true;
        btn.innerHTML = "⏳ Mengirim perintah...";

        // PERBAIKAN: Gunakan endpoint /api/ dan hapus CSRF Token
        const response = await fetch(`/api/device/${deviceName}/override`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify({ is_active: targetState }),
        });

        if (!response.ok) throw new Error("Gagal merespons dari server API");

        // Panggil fungsi UI
        updateFanUI(targetState);
        showToast(
            targetState
                ? "✅ API Request terkirim — Kipas AKTIF"
                : "🔕 API Request terkirim — Kipas MATI",
        );

        last.textContent = new Date().toLocaleTimeString("id-ID", {
            hour12: false,
        });
    } catch (error) {
        console.error("Error:", error);
        showToast("❌ Gagal menyalakan/mematikan kipas. Cek koneksi!");

        // Kembalikan teks tombol ke state semula jika gagal
        updateFanUI(fanOn);
    } finally {
        btn.disabled = false;
    }
}

// 3. Fungsi Polling untuk cek status terbaru di Background
async function pollFanStatus() {
    try {
        // PERBAIKAN: Gunakan endpoint /api/
        const response = await fetch(`/api/device/${deviceName}/status`, {
            method: "GET",
            headers: {
                Accept: "application/json",
            },
        });

        if (response.ok) {
            const data = await response.json();

            // Ambil data dari response JSON Laravel
            const serverState = data.is_active;

            // Jika status di server BERBEDA dengan status di layar saat ini, baru update UI
            if (serverState !== fanOn) {
                updateFanUI(serverState);

                // Notif kalau status berubah karena trigger dari mikrokontroler/device lain
                showToast(
                    serverState
                        ? "🔄 Exhaust Fan dinyalakan"
                        : "🔄 Exhaust Fan dimatikan",
                );
            }
        }
    } catch (error) {
        // Log disembunyikan agar console tidak spam saat koneksi putus sebentar
        console.error("Polling error:", error);
    }
}

// 4. Jalankan Polling setiap 1 detik (1000 ms)
// Catatan: 1 detik sangat responsif, pastikan server kuat menangani request konstan.
setInterval(pollFanStatus, 1000);

// Fungsi Toast tetap sama
function showToast(msg) {
    const t = document.getElementById("toast");
    t.textContent = msg;
    t.style.display = "block";
    clearTimeout(t._tid);
    t._tid = setTimeout(() => (t.style.display = "none"), 3200);
}
