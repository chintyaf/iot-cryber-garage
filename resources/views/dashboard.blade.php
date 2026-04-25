<!doctype html>
<html lang="id">
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <title>Cyber-Garage Chronicles — Central Cloud</title>
        <link
            href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Rajdhani:wght@400;600;700&family=Orbitron:wght@400;700;900&display=swap"
            rel="stylesheet"
        />
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>

        <link rel="stylesheet" href="{{ asset('css/style.css') }}" />

    </head>
    <body>
        <!-- HEADER -->
        <header>
            <div class="logo">
                {{-- <div class="logo-icon">🚗</div> --}}
                <div class="logo-text">
                    <h1>CYBER-GARAGE CHRONICLES - Rasberry Pie Company</h1>
                    <p>CENTRAL CLOUD DASHBOARD ▸ ESP8266 NODE</p>
                </div>
            </div>
            <div class="header-right">
                <div class="live-badge">
                    <div class="live-dot"></div>
                    LIVE
                </div>
                <div class="clock" id="clock">--:--:--</div>
            </div>
        </header>

        <!-- MAIN -->
        <main>
            <!--
    Mobile order: 1=Status, 2=FanPanel, 3=Charts, 4=Logs
    On desktop: left-col (contains 1+2) sits in column 1, rest in column 2
  -->

            <!-- LEFT COLUMN -->
            <div class="left-col">
                <!-- 1. Panel Status Terkini -->
                <div class="panel distance-panel">
                    <div class="panel-title">Panel Status Terkini</div>
                    <div class="distance-ring">
                        <svg
                            viewBox="0 0 180 180"
                            xmlns="http://www.w3.org/2000/svg"
                        >
                            <circle
                                class="distance-ring-bg"
                                cx="90"
                                cy="90"
                                r="80"
                            />
                            <circle
                                class="distance-ring-fill"
                                id="ringFill"
                                cx="90"
                                cy="90"
                                r="80"
                            />
                        </svg>
                        <div class="distance-center">
                            <div class="distance-value" id="distVal">160</div>
                            <div class="distance-unit">CM</div>
                        </div>
                    </div>
                    <div class="distance-label">JARAK AKHIR</div>
                    <div class="distance-main-label">
                        JARAK AKHIR: <span id="distLabel">160</span> CM
                    </div>
                    <div class="distance-sub">
                        Current Final Parking Distance
                    </div>

                    <div class="status-bar">
                        <div class="status-item">
                            <div class="s-label">Mobil</div>
                            <div class="s-val ok" id="carStatus">PARKIR</div>
                        </div>
                        <div class="status-item">
                            <div class="s-label">Gas (ppm)</div>
                            <div class="s-val warn" id="gasVal">342</div>
                        </div>
                        <div class="status-item">
                            <div class="s-label">Kipas</div>
                            <div
                                class="s-val"
                                id="fanStatusVal"
                                {{-- style="color: var(--muted)" --}}
                            >
                                OFF
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Manual Override -->
                <div class="panel fan-panel">
                    <div class="panel-title">Panel Kendali Manual</div>
                    <div class="fan-status-row">
                        <div class="fan-indicator">
                            <div class="fan-dot" id="fanDot"></div>
                            <span id="fanStatusText" style="color: var(--muted)"
                                >EXHAUST FAN — STANDBY</span
                            >
                        </div>
                    </div>

                    <div class="fan-icon" id="fanIcon">🌀</div>

                    <button class="btn-fan" id="btnFan" onclick="toggleFan()">
                        ⚡ NYALAKAN KIPAS (Manual Override)
                    </button>

                    <div class="override-badge">
                        MODE: <span id="overrideMode">AUTO</span> &nbsp;|&nbsp;
                        LAST: <span id="lastCmd">—</span>
                    </div>
                </div>
            </div>

            <!-- 3. CHARTS -->
            <div class="charts-area">
                <div class="panel chart-panel">
                    <div class="chart-header">
                        <div>
                            <div class="panel-title" style="margin-bottom: 4px">
                                Data Insights
                            </div>
                            <div class="chart-title">
                                Kualitas Udara (Gas Value)
                            </div>
                        </div>
                        <div class="chart-badge badge-air">24H</div>
                    </div>
                    <div class="chart-wrap">
                        <canvas id="airChart"></canvas>
                    </div>
                </div>

                <div class="panel chart-panel">
                    <div class="chart-header">
                        <div>
                            <div class="panel-title" style="margin-bottom: 4px">
                                Data Insights
                            </div>
                            <div class="chart-title">
                                Aktivitas Parkir — Frekuensi
                            </div>
                        </div>
                        <div class="chart-badge badge-park">7D</div>
                    </div>
                    <div class="chart-wrap">
                        <canvas id="parkChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- 4. LOG TABLE / CARDS -->
            <div class="panel log-panel">
                <div class="log-header">
                    <div class="panel-title" style="margin-bottom: 0">
                        Record Sensor &amp; Actuator Logs
                    </div>
                    <div class="log-count" id="logCount">
                        Menampilkan 6 entri terbaru
                    </div>
                </div>

                <!-- Desktop: table view -->
                <table>
                    <thead>
                        <tr>
                            <th>Timestamp</th>
                            <th>Tipe</th>
                            <th>Parameter</th>
                            <th>Nilai</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="logBody"></tbody>
                </table>

                <!-- Mobile: card view -->
                <div class="log-cards" id="logCards"></div>
            </div>
        </main>

        <!-- TOAST -->
        <div class="notif-toast" id="toast"></div>

        <script src="{{ asset('script/app.js') }}"></script>
        <script src="{{ asset('script/fan.js') }}"></script>
        <script src="{{ asset('script/airquality.js') }}"></script>
        <script src="{{ asset('script/log.js') }}"></script>
        <script src="{{ asset('script/panel.js') }}"></script>
        <script src="{{ asset('script/parkinglog.js') }}"></script>
        </body>
</html>
