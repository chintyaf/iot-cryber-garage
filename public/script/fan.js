/* ── CONFIGURATION ── */
const deviceName = "exhaust-fan";

// State global: Gunakan ini sebagai satu-satunya sumber kebenaran (Source of Truth)
let currentState = {
    fanOn: false,
    mode: "AUTO",
    lastSync: null,
};

// 1. Fungsi RE-RENDER (Pusat segala update UI)
function renderUI(data) {
    // Pastikan data memiliki properti yang kita butuhkan, atau gunakan nilai saat ini
    const is_active =
        data.is_active !== undefined ? data.is_active : currentState.fanOn;
    const mode = data.mode || currentState.mode;

    // Update state global agar sinkron
    currentState.fanOn = is_active;
    currentState.mode = mode;

    const btn = document.getElementById("btnFan");
    const dot = document.getElementById("fanDot");
    const icon = document.getElementById("fanIcon");
    const txt = document.getElementById("fanStatusText");
    const fval = document.getElementById("fanStatusVal");
    const modeLabel = document.getElementById("overrideMode");

    // Efek visual Kipas
    dot.classList.toggle("active", is_active);
    icon.classList.toggle("spinning", is_active);
    btn.classList.toggle("active-state", is_active);

    // Update Teks & Warna
    if (is_active) {
        txt.textContent = "EXHAUST FAN — AKTIF";
        txt.style.color = "var(--green)";
        fval.textContent = "ON";
        fval.className = "s-val ok";
        btn.innerHTML = "⛔ MATIKAN KIPAS";
    } else {
        txt.textContent = "EXHAUST FAN — STANDBY";
        txt.style.color = "";
        fval.textContent = "OFF";
        fval.className = "s-val";
        btn.innerHTML = "⚡ NYALAKAN KIPAS";
    }

    // Update Mode (AUTO/MANUAL)
    if (modeLabel) {
        modeLabel.textContent = mode;
        modeLabel.style.color =
            mode === "AUTO" ? "var(--primary)" : "var(--orange)";
    }
}

// 2. Polling Status
async function pollFanStatus() {
    try {
        const response = await fetch(`/api/device/${deviceName}/status`);
        if (!response.ok) return;

        const data = await response.json();

        // Cek perubahan sebelum re-render untuk efisiensi
        if (
            data.is_active !== currentState.fanOn ||
            data.mode !== currentState.mode
        ) {
            renderUI(data);
            showToast(
                `Status sinkron: ${data.is_active ? "ON" : "OFF"} (${data.mode})`,
            );
        }
    } catch (e) {
        console.error("Sync error:", e);
    }
}

// 3. Toggle Manual
async function toggleFan() {
    const btn = document.getElementById("btnFan");
    const last = document.getElementById("lastCmd");

    // Perbaikan: Ambil targetState dari currentState
    const targetState = !currentState.fanOn;

    try {
        btn.disabled = true;
        btn.innerHTML = "⏳ Mengirim...";

        const response = await fetch(`/api/device/${deviceName}/override`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content"),
            },
            body: JSON.stringify({ is_active: targetState, mode: "MANUAL" }),
        });

        if (!response.ok) throw new Error("Gagal merespons server");

        // Perbaikan: Kirim objek ke renderUI, bukan cuma boolean
        renderUI({ is_active: targetState, mode: "MANUAL" });

        showToast(targetState ? "✅ Kipas MANUAL ON" : "🔕 Kipas MANUAL OFF");
        last.textContent = new Date().toLocaleTimeString("id-ID", {
            hour12: false,
        });
    } catch (error) {
        showToast("❌ Gagal: " + error.message);
        // Jika gagal, poll ulang untuk kembalikan UI ke status server asli
        pollFanStatus();
    } finally {
        btn.disabled = false;
    }
}

// 4. Reset ke Auto
async function resetToAuto() {
    const btnAuto = document.getElementById("btnAuto");

    // Simpan konten asli tombol (agar bisa dikembalikan nanti)
    const originalContent = btnAuto.innerHTML;

    try {
        // --- START LOADING ---
        btnAuto.disabled = true;
        // Gunakan icon jam pasir atau spinner sederhana
        btnAuto.innerHTML = `⏳ Memproses...`;
        btnAuto.style.opacity = "0.7";
        btnAuto.style.cursor = "not-allowed";

        const response = await fetch(`/api/device/${deviceName}/override`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
                "X-CSRF-TOKEN": document
                    .querySelector('meta[name="csrf-token"]')
                    ?.getAttribute("content"),
            },
            body: JSON.stringify({
                is_active: false,
                mode: "AUTO",
            }),
        });

        if (response.ok) {
            showToast("🔄 Mode AUTO Aktif");
            await pollFanStatus(); // Tunggu sinkronisasi status terbaru
        } else {
            const errorData = await response.json();
            throw new Error(errorData.message || "Gagal ke mode AUTO");
        }
    } catch (error) {
        console.error("Error Reset Auto:", error);
        showToast("❌ Gagal: " + error.message);
    } finally {
        // --- STOP LOADING ---
        // Kembalikan ke kondisi semula setelah selesai (berhasil maupun gagal)
        btnAuto.disabled = false;
        btnAuto.innerHTML = originalContent;
        btnAuto.style.opacity = "1";
        btnAuto.style.cursor = "pointer";
    }
}

/* ── RUNTIME ── */
setInterval(pollFanStatus, 2000);
document.addEventListener("DOMContentLoaded", pollFanStatus);

function showToast(msg) {
    const t = document.getElementById("toast");
    if (!t) return;
    t.textContent = msg;
    t.style.display = "block";
    clearTimeout(t._tid);
    t._tid = setTimeout(() => (t.style.display = "none"), 3000);
}
