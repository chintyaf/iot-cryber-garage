/* ── CLOCK ── */
function updateClock() {
    const now = new Date();
    document.getElementById("clock").textContent = now.toLocaleTimeString(
        "id-ID",
        { hour12: false },
    );
}
setInterval(updateClock, 5000);
updateClock();
