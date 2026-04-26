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
