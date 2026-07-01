<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>NoDrowsy</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@400;600;800&display=swap"
    rel="stylesheet">
  <style>
    :root {
      --bg: #0a0a0f;
      --surface: #12121a;
      --surface2: #1a1a26;
      --border: rgba(255, 255, 255, 0.07);
      --accent: #00ff88;
      --accent2: #ff4466;
      --accent3: #4488ff;
      --warn: #ffaa00;
      --text: #e8e8f0;
      --text2: #6b6b80;
      --font-display: 'Syne', sans-serif;
      --font-mono: 'Space Mono', monospace;
    }

    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      -webkit-tap-highlight-color: transparent;
    }

    body {
      background: var(--bg);
      color: var(--text);
      font-family: var(--font-display);
      min-height: 100dvh;
      overflow-x: hidden;
    }

    /* ── Background grid ── */
    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image:
        linear-gradient(rgba(0, 255, 136, 0.03) 1px, transparent 1px),
        linear-gradient(90deg, rgba(0, 255, 136, 0.03) 1px, transparent 1px);
      background-size: 40px 40px;
      pointer-events: none;
      z-index: 0;
    }

    .app {
      position: relative;
      z-index: 1;
      max-width: 480px;
      margin: 0 auto;
      padding: 0 0 100px;
    }

    /* ── Header ── */
    .header {
      padding: 20px 20px 12px;
      display: flex;
      align-items: center;
      justify-content: space-between;
    }

    .logo {
      font-size: 18px;
      font-weight: 800;
      letter-spacing: -0.5px;
      color: var(--accent);
    }

    .logo span {
      color: var(--text2);
      font-weight: 400;
    }

    .status-dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--accent);
      box-shadow: 0 0 8px var(--accent);
      animation: pulse-dot 2s infinite;
    }

    @keyframes pulse-dot {

      0%,
      100% {
        opacity: 1;
        transform: scale(1);
      }

      50% {
        opacity: 0.5;
        transform: scale(0.8);
      }
    }

    /* ── Camera section ── */
    .camera-wrap {
      position: relative;
      margin: 0 16px;
      border-radius: 20px;
      overflow: hidden;
      background: var(--surface);
      border: 1px solid var(--border);
      aspect-ratio: 3/4;
    }

    #video {
      width: 100%;
      height: 100%;
      object-fit: cover;
      transform: scaleX(-1);
      display: block;
    }

    #canvas {
      display: none;
    }

    .camera-overlay {
      position: absolute;
      inset: 0;
      pointer-events: none;
    }

    /*Status Card*/
    /* ── Status Card (mandiri, mengisi sisa tinggi di bawah kamera) ── */
    .status-card {
      position: relative;
      flex: 1;
      margin-top: 10px;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 20px;
      padding: 24px;
      display: flex;
      align-items: center;
      justify-content: center;
      overflow: hidden;
      font-family: var(--font-mono);
      font-size: 22px;
      font-weight: 700;
      letter-spacing: 1.5px;
      text-align: center;
      transition: all 0.4s ease;
      isolation: isolate;
    }

    .status-card::before {
      content: '';
      position: absolute;
      inset: -40%;
      background: radial-gradient(circle, var(--status-glow, transparent) 0%, transparent 65%);
      opacity: 0.5;
      animation: status-pulse 3s ease-in-out infinite;
      z-index: -1;
    }

    .status-card span {
      position: relative;
      z-index: 1;
    }

    @keyframes status-pulse {

      0%,
      100% {
        transform: scale(0.9);
        opacity: 0.35;
      }

      50% {
        transform: scale(1.1);
        opacity: 0.6;
      }
    }

    .status-card.init {
      color: var(--text2);
      --status-glow: rgba(255, 255, 255, 0.06);
    }

    .status-card.alert {
      background: rgba(0, 255, 136, 0.06);
      color: var(--accent);
      border-color: rgba(0, 255, 136, 0.3);
      --status-glow: rgba(0, 255, 136, 0.35);
    }

    .status-card.warning {
      background: rgba(255, 170, 0, 0.06);
      color: var(--warn);
      border-color: rgba(255, 170, 0, 0.3);
      --status-glow: rgba(255, 170, 0, 0.35);
    }

    .status-card.drowsy {
      background: rgba(255, 68, 102, 0.1);
      color: var(--accent2);
      border-color: rgba(255, 68, 102, 0.4);
      --status-glow: rgba(255, 68, 102, 0.5);
      animation: drowsy-shake 0.4s ease-in-out infinite;
    }

    .status-card.drowsy::before {
      animation: status-pulse 0.8s ease-in-out infinite;
    }

    @keyframes drowsy-shake {

      0%,
      100% {
        transform: translateX(0);
      }

      25% {
        transform: translateX(-2px);
      }

      75% {
        transform: translateX(2px);
      }
    }

    /* Corner brackets */
    .corner {
      position: absolute;
      width: 28px;
      height: 28px;
      border-color: var(--accent);
      border-style: solid;
      opacity: 0.6;
    }

    .corner.tl {
      top: 16px;
      left: 16px;
      border-width: 2px 0 0 2px;
      border-radius: 4px 0 0 0;
    }

    .corner.tr {
      top: 16px;
      right: 16px;
      border-width: 2px 2px 0 0;
      border-radius: 0 4px 0 0;
    }

    .corner.bl {
      bottom: 16px;
      left: 16px;
      border-width: 0 0 2px 2px;
      border-radius: 0 0 0 4px;
    }

    .corner.br {
      bottom: 16px;
      right: 16px;
      border-width: 0 2px 2px 0;
      border-radius: 0 0 4px 0;
    }

    /* Status badge on camera */
    .cam-status {
      position: absolute;
      bottom: 16px;
      left: 50%;
      transform: translateX(-50%);
      padding: 8px 20px;
      border-radius: 100px;
      font-family: var(--font-mono);
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 1px;
      transition: all 0.3s ease;
      white-space: nowrap;
      backdrop-filter: blur(10px);
    }

    .cam-status.alert {
      background: rgba(0, 255, 136, 0.15);
      color: var(--accent);
      border: 1px solid rgba(0, 255, 136, 0.3);
    }

    .cam-status.warning {
      background: rgba(255, 170, 0, 0.15);
      color: var(--warn);
      border: 1px solid rgba(255, 170, 0, 0.3);
    }

    .cam-status.drowsy {
      background: rgba(255, 68, 102, 0.15);
      color: var(--accent2);
      border: 1px solid rgba(255, 68, 102, 0.3);
      animation: flash 0.5s infinite;
    }

    .cam-status.critical {
      background: rgba(255, 68, 102, 0.3);
      color: var(--accent2);
      border: 1px solid rgba(255, 68, 102, 0.5);
      animation: flash 0.3s infinite;
    }

    .cam-status.init {
      background: rgba(255, 255, 255, 0.05);
      color: var(--text2);
      border: 1px solid var(--border);
    }

    @keyframes flash {

      0%,
      100% {
        opacity: 1;
      }

      50% {
        opacity: 0.4;
      }
    }

    /* No face indicator */
    .no-face {
      position: absolute;
      top: 16px;
      left: 50%;
      transform: translateX(-50%);
      font-family: var(--font-mono);
      font-size: 10px;
      color: var(--text2);
      background: rgba(0, 0, 0, 0.5);
      padding: 4px 10px;
      border-radius: 100px;
      backdrop-filter: blur(6px);
      display: none;
    }

    /* ── Metrics grid ── */
    .metrics {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
      margin: 14px 16px 0;
    }

    .metric-card {
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 16px;
      position: relative;
      overflow: hidden;
      transition: border-color 0.3s;
    }

    .metric-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 2px;
      background: var(--card-color, var(--accent3));
      opacity: 0.6;
    }

    .metric-card.warn-active {
      border-color: rgba(255, 68, 102, 0.4);
    }

    .metric-card.warn-active::before {
      background: var(--accent2);
      opacity: 1;
    }

    .metric-label {
      font-family: var(--font-mono);
      font-size: 9px;
      letter-spacing: 2px;
      color: var(--text2);
      text-transform: uppercase;
      margin-bottom: 8px;
    }

    .metric-value {
      font-family: var(--font-mono);
      font-size: 26px;
      font-weight: 700;
      color: var(--text);
      line-height: 1;
      transition: color 0.3s;
    }

    .metric-value.danger {
      color: var(--accent2);
    }

    .metric-value.good {
      color: var(--accent);
    }

    .metric-sub {
      font-size: 10px;
      color: var(--text2);
      margin-top: 4px;
      font-family: var(--font-mono);
    }

    /* ── Confidence bar ── */
    .confidence-section {
      margin: 10px 16px 0;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 16px;
    }

    .conf-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 10px;
    }

    .conf-label {
      font-family: var(--font-mono);
      font-size: 9px;
      letter-spacing: 2px;
      color: var(--text2);
      text-transform: uppercase;
    }

    .conf-value {
      font-family: var(--font-mono);
      font-size: 13px;
      font-weight: 700;
      color: var(--accent);
      transition: color 0.3s;
    }

    .bar-track {
      height: 6px;
      background: var(--surface2);
      border-radius: 100px;
      overflow: hidden;
      margin-bottom: 8px;
    }

    .bar-fill {
      height: 100%;
      border-radius: 100px;
      width: 0%;
      transition: width 0.4s ease, background 0.3s;
      background: var(--accent3);
    }

    .bar-fill.medium {
      background: var(--warn);
    }

    .bar-fill.high {
      background: var(--accent2);
    }

    .ear-bar-fill {
      height: 100%;
      border-radius: 100px;
      width: 0%;
      transition: width 0.4s ease, background 0.3s;
      background: var(--accent);
    }

    .ear-bar-fill.low {
      background: var(--accent2);
    }

    /* ── IoT section ── */
    .iot-section {
      margin: 10px 16px 0;
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: 16px;
      padding: 16px;
    }

    .iot-header {
      display: flex;
      align-items: center;
      gap: 8px;
      margin-bottom: 12px;
    }

    .iot-dot {
      width: 6px;
      height: 6px;
      border-radius: 50%;
      background: var(--accent3);
      box-shadow: 0 0 6px var(--accent3);
    }

    .iot-title {
      font-family: var(--font-mono);
      font-size: 9px;
      letter-spacing: 2px;
      color: var(--text2);
      text-transform: uppercase;
    }

    .iot-timestamp {
      margin-left: auto;
      font-family: var(--font-mono);
      font-size: 9px;
      color: var(--text2);
    }

    .iot-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 10px;
    }

    .iot-metric {
      background: var(--surface2);
      border-radius: 12px;
      padding: 12px;
      text-align: center;
    }

    .iot-metric-label {
      font-family: var(--font-mono);
      font-size: 9px;
      letter-spacing: 1px;
      color: var(--text2);
      margin-bottom: 6px;
    }

    .iot-metric-value {
      font-family: var(--font-mono);
      font-size: 22px;
      font-weight: 700;
      color: var(--accent3);
      transition: color 0.3s;
    }

    .iot-metric-value.warn {
      color: var(--warn);
    }

    .iot-metric-value.danger {
      color: var(--accent2);
    }

    .iot-metric-unit {
      font-size: 10px;
      color: var(--text2);
      font-family: var(--font-mono);
    }

    .range-btn {
      background: var(--surface2);
      border: 1px solid var(--border);
      color: var(--text2);
      padding: 3px 7px;
      border-radius: 6px;
      font-family: var(--font-mono);
      font-size: 9px;
      cursor: pointer;
      transition: all 0.2s;
    }

    .range-btn.active-range {
      background: rgba(0, 255, 136, 0.15);
      border-color: rgba(0, 255, 136, 0.4);
      color: var(--accent);
    }

    /* ── Loading screen ── */
    .loading-screen {
      position: fixed;
      inset: 0;
      background: var(--bg);
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      z-index: 100;
      gap: 16px;
      transition: opacity 0.5s;
    }

    .loading-screen.hidden {
      opacity: 0;
      pointer-events: none;
    }

    .loading-logo {
      font-family: var(--font-display);
      font-size: 28px;
      font-weight: 800;
      color: var(--accent);
      letter-spacing: -1px;
    }

    .loading-text {
      font-family: var(--font-mono);
      font-size: 11px;
      color: var(--text2);
      letter-spacing: 2px;
      animation: blink 1s infinite;
    }

    @keyframes blink {

      0%,
      100% {
        opacity: 1
      }

      50% {
        opacity: 0.3
      }
    }

    .spinner {
      width: 32px;
      height: 32px;
      border: 2px solid var(--border);
      border-top-color: var(--accent);
      border-radius: 50%;
      animation: spin 0.8s linear infinite;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    /* ── Alert overlay ── */
    .alert-overlay {
      position: fixed;
      inset: 0;
      background: rgba(255, 68, 102, 0.1);
      pointer-events: none;
      z-index: 50;
      opacity: 0;
      transition: opacity 0.3s;
    }

    .alert-overlay.active {
      opacity: 1;
      animation: flash-bg 0.5s infinite;
    }

    @keyframes flash-bg {

      0%,
      100% {
        opacity: 0.1
      }

      50% {
        opacity: 0.3
      }
    }

    /* ── PC layout ── */
    @media (min-width: 768px) {
      .app {
        max-width: 900px;
        padding-bottom: 40px;
      }

      .header {
        padding: 24px 24px 16px;
      }

      .main-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        padding: 0 24px;
        align-items: start;
      }

      .camera-wrap {
        margin: 0;
        aspect-ratio: 4/3;
      }

      .left-panel {
        display: flex;
        flex-direction: column;
        gap: 10px;
      }

      .right-panel {
        display: flex;
        flex-direction: column;
        gap: 10px;
      }

      .metrics {
        margin: 0;
      }

      .confidence-section {
        margin: 0;
      }

      .iot-section {
        margin: 0;
      }
    }
  </style>
</head>

<body>

  <!-- Loading screen -->
  <div class="loading-screen" id="loadingScreen">
    <div class="loading-logo">NoDrowsy</div>
    <div class="spinner"></div>
    <div class="loading-text">MEMUAT SISTEM...</div>
  </div>

  <!-- Alert overlay -->
  <div class="alert-overlay" id="alertOverlay"></div>

  </div>

  <div class="app">
    <div class="header">
      <div style="font-family:'Syne',sans-serif; font-size:26px; font-weight:800; color:#00ff88; letter-spacing:-1px">
        NoDrowsy
      </div>
      <div class="status-dot" id="statusDot"></div>
    </div>

    <!-- PC: wrap in grid -->
    <div class="main-grid">

      <!-- Camera -->
      <div class="left-panel">
        <div class="camera-wrap" id="cameraWrap">
          <video id="video" autoplay playsinline muted></video>
          <canvas id="canvas"></canvas>
          <div class="camera-overlay">
            <div class="corner tl"></div>
            <div class="corner tr"></div>
            <div class="corner bl"></div>
            <div class="corner br"></div>
            <div class="no-face" id="noFace">WAJAH TIDAK TERDETEKSI</div>
          </div>
        </div>

        <div class="cam-status init" id="camStatus"><span>MEMUAT...</span></div>
      </div>

      <!-- Right panel -->
      <div class="right-panel">

        <!-- Metrics -->
        <div class="metrics">
          <div class="metric-card" id="earCard" style="--card-color: #4488ff">
            <div class="metric-label">EAR Score</div>
            <div class="metric-value" id="earVal">—</div>
            <div class="metric-sub" id="earSub">Eye aspect ratio</div>
          </div>
          <div class="metric-card" id="modelCard" style="--card-color: #00ff88">
            <div class="metric-label">Model</div>
            <div class="metric-value" id="modelVal">—</div>
            <div class="metric-sub" id="eyeClosedSub">Confidence</div>
          </div>
        </div>

        <!-- Confidence bars -->
        {{-- <div class="confidence-section">
          <div class="conf-header">
            <span class="conf-label">Drowsiness Confidence</span>
            <span class="conf-value" id="confPct">0%</span>
          </div>
          <div class="bar-track">
            <div class="bar-fill" id="confBar"></div>
          </div>

          <div class="conf-header" style="margin-top:10px">
            <span class="conf-label">Eye Openness</span>
            <span class="conf-value" id="earPct" style="color:var(--accent)">0%</span>
          </div>
          <div class="bar-track">
            <div class="ear-bar-fill" id="earBar"></div>
          </div>
        </div> --}}

        <!-- IoT data -->
        <div class="iot-section">
          <div class="iot-header">
            <div class="iot-dot"></div>
            <div class="iot-title">ESP32 Sensor</div>
            <div class="iot-timestamp" id="iotTime">--:--:--</div>
          </div>
          <div class="iot-grid">
            <div class="iot-metric">
              <div class="iot-metric-label">HEART RATE</div>
              <div class="iot-metric-value" id="hrVal">--</div>
              <div class="iot-metric-unit">BPM</div>
            </div>
            <div class="iot-metric">
              <div class="iot-metric-label">SpO2</div>
              <div class="iot-metric-value" id="spo2Val">--</div>
              <div class="iot-metric-unit">%</div>
            </div>
          </div>

          <!-- Baseline Status - Di sini -->
          <div style="
                    margin-top: 14px; 
                    padding-top: 14px; 
                    border-top: 1px solid var(--border);
                  ">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <div style="flex:1;">
                <div class="iot-metric-label" style="font-size:8px; margin-bottom:4px;">CARDIO BASELINE</div>
                <div id="baselineVal" style="
                          font-family: var(--font-mono); 
                          font-size: 20px; 
                          font-weight: 700; 
                          color: var(--accent);
                        ">--</div>
                <div id="baselineCountdown" style="
                font-family: var(--font-mono);
                font-size: 11px;
                color: var(--text2);
                margin-top: 4px;
                display: none;
              "></div>
              </div>

              <!-- Tombol Reset -->
              <button onclick="resetBaseline()" style="
                        background: rgba(255, 68, 102, 0.1);
                        border: 1px solid rgba(255, 68, 102, 0.3);
                        color: var(--accent2);
                        padding: 6px 10px;
                        border-radius: 6px;
                        font-family: var(--font-mono);
                        font-size: 9px;
                        font-weight: 700;
                        cursor: pointer;
                        transition: all 0.2s;
                        display: flex;
                        align-items: center;
                        gap: 4px;
                      " onmouseover="this.style.background='rgba(255, 68, 102, 0.2)'"
                onmouseout="this.style.background='rgba(255, 68, 102, 0.1)'">
                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                  <path d="M23 4v6h-6M1 20v-6h6" />
                  <path d="M3.51 9a9 9 0 0 1 14.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0 0 20.49 15" />
                </svg>
                RESET CARDIO <br> BASELINE
              </button>
            </div>

            <div style="font-family: var(--font-mono); font-size: 8px; color: var(--text2); margin-top:6px;">
              Status: <span id="baselineStatusText" style="color: var(--warn);">MENGUMPULKAN...</span>
            </div>
          </div>
        </div>

        <!-- HR Chart -->
        <div class="iot-section" id="hrChartSection">
          <div class="iot-header">
            <div class="iot-dot"></div>
            <div class="iot-title">Heart Rate History</div>
            <!-- Tombol rentang waktu -->
            <div style="display:flex; gap:4px; margin-left:auto;">
              <button onclick="setRange(30)" id="btn30" class="range-btn active-range">30m</button>
              <button onclick="setRange(60)" id="btn60" class="range-btn">1h</button>
              <button onclick="setRange(180)" id="btn180" class="range-btn">3h</button>
              <button onclick="setRange(360)" id="btn360" class="range-btn">6h</button>
            </div>
          </div>
          <div style="position:relative; height:160px;">
            <canvas id="hrChart"></canvas>
          </div>
        </div>

      </div><!-- end right-panel -->
    </div><!-- end main-grid -->
  </div>

  <!-- MediaPipe -->
  <script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.10.0"></script>
  <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-tflite@0.0.1-alpha.9/dist/tf-tflite.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <script
    src="https://cdn.jsdelivr.net/npm/chartjs-adapter-date-fns@3.0.0/dist/chartjs-adapter-date-fns.bundle.min.js"></script>

  <script>
    // ── Config ────────────────────────────────────────────────────────
    const LARAVEL_URL = `${window.location.protocol}//${window.location.hostname}:8000`; //sesuai ip laptop
    const EAR_OPEN = 0.38;
    const EAR_CLOSED = 0.27;
    const EAR_THRESH = 0.32;
    const MODEL_THRESH = 0.6;
    const EYE_LIMIT = 30; // waktu berapa lama mata tertutup. 30 frame = 1 detik.

    // ── State ─────────────────────────────────────────────────────────
    let earValue = 0;
    let confidence = 0;
    let eyeClosedCount = 0;
    let smoothEAR = 0.38; // ★ mulai dari nilai tengah yang wajar
    const ALPHA = 0.3;    // ★ smoothing factor
    let faceDetected = false;
    let lastBeep = 0;
    let frameCount = 0;
    let model, scaler;
    let isPredicting = false;
    let cameraActive = false;
    let cameraInstance = null;

    let hrChart = null;
    let currentRange = 30;

    // ── EAR Indices ───────────────────────────────────────────────────
    const LEFT_EYE = [362, 385, 387, 263, 373, 380];
    const RIGHT_EYE = [33, 160, 158, 133, 153, 144];

    function dist(a, b) {
      return Math.sqrt((a.x - b.x) ** 2 + (a.y - b.y) ** 2);
    }
    function calcEAR(lm, idx) {
      const A = dist(lm[idx[1]], lm[idx[5]]);
      const B = dist(lm[idx[2]], lm[idx[4]]);
      const C = dist(lm[idx[0]], lm[idx[3]]);
      return C === 0 ? 0 : (A + B) / (2 * C);
    }

    // ── MediaPipe ────────────────────────────────────────────────────
    const faceMesh = new FaceMesh({
      locateFile: f => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${f}`
    });
    faceMesh.setOptions({
      maxNumFaces: 1,
      refineLandmarks: false,
      minDetectionConfidence: 0.5,
      minTrackingConfidence: 0.5,
    });
    faceMesh.onResults(async (results) => {
      if (!cameraActive) return;

      frameCount++;

      if (!results.multiFaceLandmarks || results.multiFaceLandmarks.length === 0) {
        faceDetected = false;
        eyeClosedCount = 0;
        document.getElementById('noFace').style.display = 'block';
        updateUI();
        return;
      }

      faceDetected = true;
      document.getElementById('noFace').style.display = 'none';

      const lm = results.multiFaceLandmarks[0];
      const earL = calcEAR(lm, LEFT_EYE);
      const earR = calcEAR(lm, RIGHT_EYE);
      const rawEAR = (calcEAR(lm, LEFT_EYE) + calcEAR(lm, RIGHT_EYE)) / 2;
      smoothEAR = ALPHA * rawEAR + (1 - ALPHA) * smoothEAR; //new
      earValue = smoothEAR; //new

      if (earValue < EAR_THRESH) eyeClosedCount++;
      else eyeClosedCount = 0;

      if (frameCount % 10 === 0) {
        const flat = [];
        for (let i = 0; i < 468; i++) {
          flat.push(lm[i].x);
          flat.push(lm[i].y);
        }
        predictLocal(flat);
      }

      updateUI();
    });

    // ── Fetch IoT ─────────────────────────────────────────────────────
    async function fetchIoT() {
      try {
        const res = await fetch(`${LARAVEL_URL}/api/sensor/latest`);
        const data = await res.json();

        const hr = parseFloat(data.hr || 0);
        const spo2 = parseFloat(data.spo2 || 0);
        const hrLow = data.hr_low === true;
        const baselineReady = data.baseline_ready === true;
        const baseline = data.baseline;

        const baselineRemaining = data.baseline_remaining ?? 0;
        const baselineActive = data.baseline_active === true;
        const countdownEl = document.getElementById('baselineCountdown');

        // Update tampilan sensor
        document.getElementById('hrVal').textContent = hr > 0 ? hr.toFixed(0) : '--';
        document.getElementById('spo2Val').textContent = spo2 > 0 ? spo2.toFixed(1) : '--';
        document.getElementById('hrVal').className = 'iot-metric-value' + (hrLow ? ' warn' : '');
        document.getElementById('spo2Val').className = 'iot-metric-value' + (spo2 > 0 && spo2 < 95 ? ' danger' : '');
        if (data.timestamp) document.getElementById('iotTime').textContent = data.timestamp;

        // ★ Update live chart
        if (hr > 0 && hrChart) {
          addLivePoint(hr, baseline || null);
        }

        // Update status baseline
        if (!baselineReady) {
          document.getElementById('baselineVal').textContent = '--';
          document.getElementById('baselineVal').style.color = 'var(--text2)';

          countdownEl.style.display = 'block';
          const mins = Math.floor(baselineRemaining / 60);
          const secs = baselineRemaining % 60;
          const timeStr = `${mins}:${secs.toString().padStart(2, '0')}`;

          if (baselineActive) {
            document.getElementById('baselineStatusText').textContent = 'MENGUMPULKAN...';
            document.getElementById('baselineStatusText').style.color = 'var(--warn)';
            countdownEl.textContent = `⏱ ${timeStr} tersisa`;
            countdownEl.style.color = 'var(--warn)';
          } else {
            document.getElementById('baselineStatusText').textContent = 'JEDA (jari tidak terdeteksi)';
            document.getElementById('baselineStatusText').style.color = 'var(--text2)';
            countdownEl.textContent = `⏸ ${timeStr} tersisa (dijeda)`;
            countdownEl.style.color = 'var(--text2)';
          }
        } else {
          document.getElementById('baselineStatusText').textContent = 'TERKUNCI ✓';
          document.getElementById('baselineStatusText').style.color = 'var(--accent)';
          document.getElementById('baselineVal').textContent = baseline + ' BPM';
          document.getElementById('baselineVal').style.color = 'var(--accent)';
          countdownEl.style.display = 'none';
        }

        // Logika serial
        // Kamera aktif sekali, tidak pernah dimatikan otomatis oleh HR
        if (hrLow && !cameraActive) {
          await activateCamera();
        }

      } catch (e) { }
    }

    // ── Reset baseline ────────────────────────────────────────────────
    async function resetBaseline() {
      await fetch(`${LARAVEL_URL}/api/baseline/reset`, { method: 'POST' }).catch(() => { });
      deactivateCamera();
      document.getElementById('baselineStatusText').textContent = 'MENGUMPULKAN...';
      document.getElementById('baselineStatusText').style.color = 'var(--warn)';
      document.getElementById('baselineVal').textContent = '--';
      document.getElementById('baselineVal').style.color = 'var(--text2)';

      const countdownEl = document.getElementById('baselineCountdown');
      countdownEl.style.display = 'block';
      countdownEl.textContent = '⏱ 2:00 tersisa';
      countdownEl.style.color = 'var(--warn)';

      console.log('Baseline direset');
    }

    // ── HR Chart ──────────────────────────────────────────────────────
    function initChart() {
      const ctx = document.getElementById('hrChart').getContext('2d');
      hrChart = new Chart(ctx, {
        type: 'line',
        data: {
          datasets: [
            {
              label: 'Heart Rate',
              data: [],
              borderColor: '#4488ff',
              backgroundColor: 'rgba(68, 136, 255, 0.08)',
              borderWidth: 2,
              pointRadius: 0,
              tension: 0.3,
              fill: true,
              segment: {
                borderColor: ctx => {
                  const p0 = ctx.p0?.raw;
                  const p1 = ctx.p1?.raw;
                  if (p0?.cam || p1?.cam) return '#00ff88';
                  return '#4488ff';
                },
                backgroundColor: ctx => {
                  const p0 = ctx.p0?.raw;
                  const p1 = ctx.p1?.raw;
                  if (p0?.cam || p1?.cam) return 'rgba(0, 255, 136, 0.08)';
                  return 'rgba(68, 136, 255, 0.08)';
                },
              },
            },
            {
              label: 'Baseline',
              data: [],
              borderColor: 'rgba(255, 170, 0, 0.7)',
              borderWidth: 1.5,
              borderDash: [6, 4],
              pointRadius: 0,
              fill: false,
            }
          ]
        },
        options: {
          responsive: true,
          maintainAspectRatio: false,
          animation: false,
          interaction: { mode: 'index', intersect: false },
          plugins: {
            legend: { display: false },
            tooltip: {
              backgroundColor: 'rgba(18,18,26,0.95)',
              titleColor: '#6b6b80',
              bodyColor: '#e8e8f0',
              borderColor: 'rgba(255,255,255,0.07)',
              borderWidth: 1,
              titleFont: { family: 'Space Mono', size: 9 },
              bodyFont: { family: 'Space Mono', size: 11 },
              callbacks: {
                title: (items) => new Date(items[0].parsed.x).toLocaleTimeString('id-ID'),
                label: (item) => item.datasetIndex === 0
                  ? `HR: ${item.parsed.y} BPM`
                  : `Baseline: ${item.parsed.y} BPM`,
              }
            }
          },
          scales: {
            x: {
              type: 'time',
              time: { unit: 'minute', displayFormats: { minute: 'HH:mm' } },
              grid: { color: 'rgba(255,255,255,0.04)' },
              ticks: { color: '#6b6b80', font: { family: 'Space Mono', size: 9 }, maxTicksLimit: 6 },
            },
            y: {
              min: 40,
              max: 140,
              grid: { color: 'rgba(255,255,255,0.04)' },
              ticks: { color: '#6b6b80', font: { family: 'Space Mono', size: 9 }, stepSize: 20 },
            }
          }
        }
      });
    }

    async function loadChartData(minutes) {
      try {
        const res = await fetch(`${LARAVEL_URL}/api/sensor/history?minutes=${minutes}`);
        const json = await res.json();

        hrChart.data.datasets[0].data = json.data.map(d => ({ x: d.t, y: d.hr }));

        // Garis baseline — tarik dari titik paling awal sampai paling akhir
        if (json.baseline && json.data.length > 0) {
          const first = json.data[0].t;
          const last = json.data[json.data.length - 1].t;
          hrChart.data.datasets[1].data = [
            { x: first, y: json.baseline },
            { x: last, y: json.baseline },
          ];
        } else {
          hrChart.data.datasets[1].data = [];
        }

        hrChart.update();
      } catch (e) { }
    }

    function setRange(minutes) {
      currentRange = minutes;

      // Update tombol aktif
      ['30', '60', '180', '360'].forEach(m => {
        const btn = document.getElementById(`btn${m}`);
        if (btn) btn.className = 'range-btn' + (parseInt(m) === minutes ? ' active-range' : '');
      });

      loadChartData(minutes);
    }

    function addLivePoint(hr, baseline) {
      if (!hrChart) return;
      const now = Date.now();

      // Tambah titik HR terbaru
      hrChart.data.datasets[0].data.push({ x: now, y: hr, cam: cameraActive });

      // Update ujung kanan garis baseline
      const baselineData = hrChart.data.datasets[1].data;
      if (baseline && baselineData.length > 0) {
        baselineData[baselineData.length - 1] = { x: now, y: baseline };
      } else if (baseline && baselineData.length === 0) {
        const oldest = hrChart.data.datasets[0].data[0]?.x || now;
        hrChart.data.datasets[1].data = [{ x: oldest, y: baseline }, { x: now, y: baseline }];
      }

      // Buang data di luar rentang waktu yang dipilih
      const cutoff = now - currentRange * 60 * 1000;
      hrChart.data.datasets[0].data = hrChart.data.datasets[0].data.filter(d => d.x >= cutoff);
      if (baselineData.length > 0) baselineData[0].x = cutoff;

      hrChart.update('none'); // 'none' = no animation, lebih ringan untuk live update
    }

    // ── Aktifkan kamera ───────────────────────────────────────────────
    async function activateCamera() {
      if (cameraActive) return;
      cameraActive = true;

      document.getElementById('camStatus').querySelector('span').textContent = '⏳ KAMERA MENYALA...';
      document.getElementById('camStatus').className = 'status-card init';

      try {
        const video = document.getElementById('video');
        const stream = await navigator.mediaDevices.getUserMedia({
          video: { facingMode: 'user', width: { ideal: 320 }, height: { ideal: 240 } },
          audio: false
        });
        video.srcObject = stream;
        await video.play();

        cameraInstance = new Camera(video, {
          onFrame: async () => { await faceMesh.send({ image: video }); },
          width: 320, height: 240
        });
        cameraInstance.start();
      } catch (e) {
        cameraActive = false;
        document.getElementById('camStatus').querySelector('span').textContent = 'IZIN KAMERA DITOLAK';
      }
    }

    // ── Matikan kamera ────────────────────────────────────────────────
    function deactivateCamera() {
      cameraActive = false;
      earValue = 0;
      confidence = 0;
      eyeClosedCount = 0;
      faceDetected = false;

      const video = document.getElementById('video');
      if (video.srcObject) {
        video.srcObject.getTracks().forEach(t => t.stop());
        video.srcObject = null;
      }
      if (cameraInstance) { cameraInstance.stop?.(); cameraInstance = null; }

      document.getElementById('camStatus').querySelector('span').textContent = 'MENUNGGU SENSOR HR...';
      document.getElementById('camStatus').className = 'status-card init';
      document.getElementById('earVal').textContent = '—';
      document.getElementById('modelVal').textContent = '—';
      const confBarEl = document.getElementById('confBar');
      const earBarEl = document.getElementById('earBar');
      const confPctEl = document.getElementById('confPct');
      const earPctEl = document.getElementById('earPct');
      if (confBarEl) confBarEl.style.width = '0%';
      if (earBarEl) earBarEl.style.width = '0%';
      if (confPctEl) confPctEl.textContent = '0%';
      if (earPctEl) earPctEl.textContent = '0%';
    }

    // ── Update UI ─────────────────────────────────────────────────────
    function updateUI() {
      if (!cameraActive) return;

      const eyeDrowsy = eyeClosedCount >= EYE_LIMIT;
      const modelDrowsy = confidence >= MODEL_THRESH;

      let status, statusClass;
      if (!faceDetected) {
        status = 'TIDAK ADA WAJAH'; statusClass = 'init';
      } else if (eyeDrowsy && modelDrowsy) {
        status = '⚠ MENGANTUK!'; statusClass = 'drowsy';
      } else if (eyeDrowsy || modelDrowsy) {
        status = '△ WASPADA'; statusClass = 'warning';
      } else {
        status = '● SIAGA'; statusClass = 'alert';
      }

      document.getElementById('camStatus').querySelector('span').textContent = status;
      document.getElementById('camStatus').className = `status-card ${statusClass}`;
      document.getElementById('alertOverlay').classList.toggle('active', eyeDrowsy && modelDrowsy);

      const now = Date.now();
      if ((eyeDrowsy || modelDrowsy) && now - lastBeep > 1000) { playBeep(); lastBeep = now; }

      const earPct = Math.max(0, Math.min(100,
        (earValue - EAR_CLOSED) / (EAR_OPEN - EAR_CLOSED) * 100
      ));
      document.getElementById('earVal').textContent = earValue.toFixed(3);
      document.getElementById('earVal').className = 'metric-value' + (eyeDrowsy ? ' danger' : ' good');
      document.getElementById('earCard').classList.toggle('warn-active', eyeDrowsy);

      // ★ Cek null karena confidence section di-comment di HTML
      const earBarEl = document.getElementById('earBar');
      const earPctEl = document.getElementById('earPct');
      if (earBarEl) { earBarEl.style.width = earPct + '%'; earBarEl.className = 'ear-bar-fill' + (earValue < EAR_THRESH ? ' low' : ''); }
      if (earPctEl) earPctEl.textContent = earPct.toFixed(0) + '%';

      const confPct = (confidence * 100).toFixed(1);
      document.getElementById('modelVal').textContent = confPct + '%';
      document.getElementById('modelVal').className = 'metric-value' + (modelDrowsy ? ' danger' : '');
      document.getElementById('modelCard').classList.toggle('warn-active', modelDrowsy);

      // ★ Cek null karena confidence bars di-comment di HTML
      const confBarEl = document.getElementById('confBar');
      const confPctEl = document.getElementById('confPct');
      if (confBarEl) confBarEl.style.width = confPct + '%';
      if (confBarEl) confBarEl.className = 'bar-fill' + (confidence >= MODEL_THRESH ? ' high' : confidence >= 0.4 ? ' medium' : '');
      if (confPctEl) { confPctEl.textContent = confPct + '%'; confPctEl.style.color = modelDrowsy ? 'var(--accent2)' : 'var(--accent)'; }
    }

    // ── Beep ──────────────────────────────────────────────────────────
    function playBeep() {
      try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator();
        const gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.frequency.value = 880; osc.type = 'sine';
        gain.gain.setValueAtTime(0.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
        osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.4);
      } catch (e) { }
    }

    // ── Load AI ───────────────────────────────────────────────────────
    async function loadAI() {
      model = await tflite.loadTFLiteModel('/model/drowsiness_model_v2.tflite');
      scaler = await fetch('/model/scaler_v2.json').then(r => r.json());
      console.log('AI loaded');
    }

    function normalize(data) {
      return data.map((v, i) => (v * scaler.scale[i]) + scaler.min[i]);
    }

    async function predictLocal(flat) {
      if (isPredicting) return;
      isPredicting = true;
      try {
        const normalized = normalize(flat);
        const input = tf.tensor([normalized], [1, 936]);
        const output = model.predict(input);
        confidence = output.dataSync()[0];
        input.dispose(); output.dispose();
      } catch (e) {
        console.error('PREDICT ERROR:', e);
      } finally {
        isPredicting = false;
      }
    }

    // ── Boot ──────────────────────────────────────────────────────────
    (async () => {
      try {
        document.querySelector('.loading-text').textContent = 'MEMUAT AI...';
        await loadAI();
        document.getElementById('loadingScreen').classList.add('hidden');
        document.getElementById('camStatus').querySelector('span').textContent = 'MENUNGGU SENSOR HR...';
        document.getElementById('camStatus').className = 'status-card init';

        // ★ Inisialisasi chart langsung saat halaman dibuka
        initChart();
        loadChartData(currentRange);
      } catch (e) {
        document.querySelector('.loading-text').textContent = 'GAGAL LOAD AI';
      }
    })();

    // Polling IoT setiap 2 detik
    setInterval(fetchIoT, 2000);
    fetchIoT();
  </script>
</body>

</html>