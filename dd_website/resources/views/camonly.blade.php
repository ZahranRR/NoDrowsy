<!DOCTYPE html>
<html lang="id">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <title>NoDrowsy — Camera Only</title>
  <link
    href="https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Syne:wght@400;600;800&display=swap"
    rel="stylesheet">
  <style>
    /* ── CSS sama persis dengan dashboard, MINUS .iot-section dan turunannya ── */
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

    body::before {
      content: '';
      position: fixed;
      inset: 0;
      background-image: linear-gradient(rgba(0, 255, 136, 0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(0, 255, 136, 0.03) 1px, transparent 1px);
      background-size: 40px 40px;
      pointer-events: none;
      z-index: 0;
    }

    .app {
      position: relative;
      z-index: 1;
      max-width: 480px;
      margin: 0 auto;
      padding: 0 0 40px;
    }

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
        transform: scale(1)
      }

      50% {
        opacity: 0.5;
        transform: scale(0.8)
      }
    }

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
      z-index: 100;
    }

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
        opacity: 0.35
      }

      50% {
        transform: scale(1.1);
        opacity: 0.6
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
        transform: translateX(0)
      }

      25% {
        transform: translateX(-2px)
      }

      75% {
        transform: translateX(2px)
      }
    }

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

    /* face guide */
    .face-guide-img {
      position: absolute;
      inset: 0;
      width: 100%;
      height: 100%;
      object-fit: contain;
      object-position: center 20%;
      /* geser turun/naik biar pas sama posisi kepala di frame */
      opacity: 0.5;
      pointer-events: none;
      transition: filter 0.3s, opacity 0.3s;
      z-index: 10;
    }

    .face-guide-img.out-of-range {
      filter: drop-shadow(0 0 4px rgba(255, 68, 102, 0.5)) hue-rotate(140deg) saturate(3);
    }

    .distance-hint {
      position: absolute;
      bottom: 16px;
      left: 16px;
      right: 16px;
      display: flex;
      align-items: flex-start;
      gap: 8px;
      background: rgba(0, 0, 0, 0.6);
      backdrop-filter: blur(6px);
      border: 1px solid var(--border);
      border-radius: 12px;
      padding: 10px 14px;
      font-family: var(--font-mono);
      font-size: 12px;
      line-height: 1.4;
      color: var(--text2);
    }

    .distance-hint .dot {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      background: var(--accent);
      margin-top: 4px;
      flex-shrink: 0;
    }

    .distance-hint.warn {
      border-color: rgba(255, 68, 102, 0.4);
      color: var(--accent2);
    }

    .distance-hint.warn .dot {
      background: var(--accent2);
    }

    /* face guide */

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
        transform: rotate(360deg)
      }
    }

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

    @media (min-width: 768px) {
      .app {
        max-width: 900px;
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

      .right-panel.paused {
        opacity: 0.35;
        pointer-events: none;
        filter: grayscale(0.9);
        transition: all 0.4s ease;
      }

      /* Semua nilai metrik menjadi abu-abu dan tidak terang */
      .right-panel.paused .metric-value,
      .right-panel.paused .conf-value,
      .right-panel.paused #perclosPct,
      .right-panel.paused #earPct {
        color: var(--text2) !important;
      }

      /* Semua bar progress menjadi abu-abu dan lebarnya 0 */
      .right-panel.paused .bar-fill,
      .right-panel.paused .ear-bar-fill {
        background: var(--text2) !important;
        width: 0% !important;
      }

      /* Background section ikut redup */
      .right-panel.paused .confidence-section,
      .right-panel.paused .metric-card {
        background: var(--surface2) !important;
        border-color: var(--border) !important;
      }

      /* Sembunyikan indikator card (garis atas) */
      .right-panel.paused .metric-card::before {
        opacity: 0 !important;
      }

      /* Jika ada tombol atau elemen interaktif lain, non-aktifkan */
      .right-panel.paused button,
      .right-panel.paused #btnLog {
        opacity: 0.3;
        pointer-events: none;
      }

      .metrics {
        margin: 0;
      }

      .confidence-section {
        margin: 0;
      }
    }

    #earLog {
      font-family: var(--font-mono);
      font-size: 10px;
      line-height: 1.8;

      max-height: 150px;
      /* tinggi tetap */
      overflow-y: auto;
      /* muncul scrollbar */
      overflow-x: hidden;

      color: var(--text2);
    }
  </style>
</head>

<body>

  <div class="loading-screen" id="loadingScreen">
    <div class="loading-logo">NoDrowsy</div>
    <div class="spinner"></div>
    <div class="loading-text">MEMUAT SISTEM...</div>
  </div>

  <div class="alert-overlay" id="alertOverlay"></div>

  <div class="app">
    <div class="header">
      <div class="logo">NoDrowsy<span>Cam</span></div>
      <div class="status-dot"></div>
    </div>

    <div class="main-grid">

      <!-- Left: Camera -->
      <div class="left-panel">
        <div class="camera-wrap">
          <video id="video" autoplay playsinline muted></video>
          <canvas id="canvas"></canvas>
          <div class="camera-overlay">
            <div class="corner tl"></div>
            <div class="corner tr"></div>
            <div class="corner bl"></div>
            <div class="corner br"></div>
            <div class="no-face" id="noFace">WAJAH TIDAK TERDETEKSI</div>

            <img src="/assets/outline.png" class="face-guide-img" id="faceGuide" alt="">

            <!-- ★ Info jarak -->
            <div class="distance-hint" id="distanceHint">
              <div class="dot"></div>
              <span id="distanceText">Posisikan wajah Anda di dalam outline untuk hasil terbaik</span>
            </div>
          </div>
        </div>
        <div class="status-card init" id="camStatus"><span>MEMUAT...</span></div>
      </div>

      <!-- Right: Metrics only -->
      <div class="right-panel">
        <div class="metrics">
          <div class="metric-card" id="earCard" style="--card-color:#4488ff">
            <div class="metric-label">EAR Score</div>
            <div class="metric-value" id="earVal">—</div>
            <div class="metric-sub">Eye aspect ratio</div>
          </div>
          <div class="metric-card" id="modelCard" style="--card-color:#00ff88">
            <div class="metric-label">Model</div>
            <div class="metric-value" id="modelVal">—</div>
            <div class="metric-sub">Confidence</div>
          </div>
        </div>

        <div class="confidence-section">
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

          <!-- 🆕 PERCLOS live gauge -- supaya user lihat window sedang
               "terisi" sebelum confidence model naik, bukan cuma nunggu -->
          <div class="conf-header" style="margin-top:10px">
            <span class="conf-label">PERCLOS (1 detik terakhir)</span>
            <span class="conf-value" id="perclosPct" style="color:var(--warn)">0%</span>
          </div>
          <div class="bar-track">
            <div class="bar-fill" id="perclosBar" style="background:var(--warn)"></div>
          </div>

          <!-- 🆕 Closure timer -- informatif, TIDAK mempengaruhi alert -->
          <div id="closureTimer" style="
            margin-top:8px; font-family:var(--font-mono); font-size:10px;
            color:var(--text2); text-align:center; min-height:14px;
          "></div>
        </div>

        <!-- EAR Log -->
        <div style="
          background: var(--surface);
          border: 1px solid var(--border);
          border-radius: 16px;
          padding: 16px;
        ">
          <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:10px;">
            <span
              style="font-family:var(--font-mono); font-size:9px; letter-spacing:2px; color:var(--text2); text-transform:uppercase;">EAR
              Log</span>
            <div style="display:flex; gap:6px;">
              <button id="btnLog" onclick="toggleLog()" style="
                  background:rgba(0,255,136,0.1); border:1px solid rgba(0,255,136,0.3);
                  color:var(--accent); padding:3px 8px; border-radius:6px;
                  font-family:var(--font-mono); font-size:9px; cursor:pointer;
                ">■ STOP</button>
              <button onclick="clearLog()" style="
                  background:rgba(255,255,255,0.05); border:1px solid var(--border);
                  color:var(--text2); padding:3px 8px; border-radius:6px;
                  font-family:var(--font-mono); font-size:9px; cursor:pointer;
                ">CLEAR</button>
            </div>
          </div>
          <div id="earLog" style="
            font-family: var(--font-mono);
            font-size: 10px;
            line-height: 1.8;
            max-height: 200px;
            overflow-y: auto;
            color: var(--text2);
          ">
            <span style="color:var(--text2); opacity:0.5">— menunggu data —</span>
          </div>
        </div>
      </div>

    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/face_mesh.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/@mediapipe/camera_utils/camera_utils.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs@4.10.0"></script>
  <script src="https://cdn.jsdelivr.net/npm/@tensorflow/tfjs-tflite@0.0.1-alpha.9/dist/tf-tflite.min.js"></script>

  <script>
    // ══════════════════════════════════════════════════════════════
    // KONSTANTA
    // ══════════════════════════════════════════════════════════════
    const DEBUG = false;
    const WINDOW_SIZES = [5, 10, 15];
    const MAX_WINDOW = 15;
    const EAR_CLOSE_THRESH_GENERIC = 0.21;
    const EAR_CLOSE_THRESH_V5 = 0.15;
    const MAR_OPEN_THRESH = 0.5;

    const MODEL_THRESH = 0.3;
    const MODEL_STREAK_NEEDED = 1;
    const DROWSY_HOLD_MS = 800;
    const HEAD_TURN_MIN = 0.85, HEAD_TURN_MAX = 1.20;
    const HEAD_TURN_SMOOTH_WINDOW = 5;

    // 🔧 KONSTANTA KALIBRASI
    const CALIBRATION_FRAMES = 10;        // ~1.5 detik (30fps)
    // Threshold closure relatif -- HARUS SAMA PERSIS dengan angka yang
    // dicetak perclosdiag_rldd_v2_relative.py saat training ("Threshold
    // closure relatif otomatis (percentile-20): X.XXXX"). Update angka
    // ini kalau kamu retrain dengan threshold yang beda.
    const REL_CLOSE_THRESH = 0.8154;

    const EYE_REGION = [7, 33, 133, 144, 145, 153, 154, 155, 157, 158, 159, 160, 161, 163, 173,
      246, 249, 263, 362, 373, 374, 380, 381, 382, 384, 385, 386, 387, 388, 390, 398, 466];
    const MOUTH_REGION = [0, 12, 13, 14, 15, 17, 39, 61, 78, 82, 87, 88, 95, 181, 191,
      269, 291, 308, 312, 317, 318, 324, 405, 415];
    const LANDMARK_ORDER = [...EYE_REGION, ...MOUTH_REGION];

    const LEFT_EYE_EAR = [362, 385, 387, 263, 373, 380];
    const RIGHT_EYE_EAR = [33, 160, 158, 133, 153, 144];
    const NOSE_TIP = 1, LEFT_EYE_OUT = 33, RIGHT_EYE_OUT = 263;
    const MOUTH_TOP = 13, MOUTH_BOTTOM = 14, MOUTH_LEFT = 78, MOUTH_RIGHT = 308,
      MOUTH_TOP_OUTER = 12, MOUTH_BOT_OUTER = 15;

    // ══════════════════════════════════════════════════════════════
    // STATE 
    // ══════════════════════════════════════════════════════════════
    let model, scaler, isPredicting = false;
    let faceDetected = false, lastBeep = 0, frameCount = 0;
    let confidence = 0;
    let isFrontal = true, headTurnRatio = 1;
    let headTurnHistory = [];
    let modelHighStreak = 0, modelDrowsy = false;
    let drowsyOnUntil = 0;
    let consecutiveClosedFrames = 0;
    let drowsyActive = false;
    let lastPerclosW15 = 0;  // untuk gauge PERCLOS di UI (informatif)

    let isCalibrating = true;
    let calibrationEars = [];
    let baselineEAR = 1.0;   // rata-rata EAR mentah saat kalibrasi (mata terbuka normal)
    let calibrationDone = false;

    let calibrationHTRs = [];
    let baselineHTR = 1.0;      // rata-rata headTurnRatio saat kalibrasi (posisi frontal personal)
    const HTR_TOLERANCE = 0.25; // toleransi deviasi dari baseline personal sebelum dianggap "tidak frontal"

    const frameBuffer = {
      ear_avg: [], mar: [], eye_closed_v5: [], mouth_open: [],
      ear_relative: [], eye_closed_relative: [],   // BARU: sesuai definisi training
    };
    let earLog = [], isLogging = true;
    const EAR_LOG_MAX = 50;

    // ══════════════════════════════════════════════════════════════
    // UTIL (sama seperti sebelumnya)
    // ══════════════════════════════════════════════════════════════
    function dist(a, b) { return Math.sqrt((a.x - b.x) ** 2 + (a.y - b.y) ** 2); }
    function pushBuffer(k, v) { frameBuffer[k].push(v); if (frameBuffer[k].length > MAX_WINDOW) frameBuffer[k].shift(); }
    function rollingMean(a, w) { const s = a.slice(-w); return s.reduce((x, y) => x + y, 0) / s.length; }
    function rollingMin(a, w) { return Math.min(...a.slice(-w)); }
    function rollingMax(a, w) { return Math.max(...a.slice(-w)); }
    function rollingStd(a, w) {
      const s = a.slice(-w); if (s.length < 2) return 0; const m = rollingMean(s, s.length);
      return Math.sqrt(s.reduce((x, y) => x + (y - m) ** 2, 0) / (s.length - 1));
    }

    function calcHeadTurnRatio(lm) {
      const nose = lm[1], lc = lm[234], rc = lm[454];
      const dl = dist(nose, lc), dr = dist(nose, rc); return dr === 0 ? 1 : dl / dr;
    }
    function calcEAR6(lm, idx) {
      const A = dist(lm[idx[1]], lm[idx[5]]), B = dist(lm[idx[2]], lm[idx[4]]), C = dist(lm[idx[0]], lm[idx[3]]);
      return C === 0 ? 0 : (A + B) / (2 * C);
    }
    function calcMAR(lm) {
      const v1 = dist(lm[MOUTH_TOP], lm[MOUTH_BOTTOM]), v2 = dist(lm[MOUTH_TOP_OUTER], lm[MOUTH_BOT_OUTER]),
        h = dist(lm[MOUTH_LEFT], lm[MOUTH_RIGHT]); return h === 0 ? 0 : (v1 + v2) / (2 * h);
    }
    function normalizeLandmarks(lm) {
      const nose = lm[NOSE_TIP]; const scale = Math.max(dist(lm[LEFT_EYE_OUT], lm[RIGHT_EYE_OUT]), 1e-6);
      const out = {};
      for (const i of LANDMARK_ORDER) { out[`lx${i}`] = (lm[i].x - nose.x) / scale; out[`ly${i}`] = (lm[i].y - nose.y) / scale; }
      return out;
    }

    // ══════════════════════════════════════════════════════════════
    // LOG (sama)
    // ══════════════════════════════════════════════════════════════
    // Shared -- dipakai di updateUI() DAN addEarLog(), supaya konsisten
    // dan tidak duplikasi angka 0.15/0.45 di 2 tempat berbeda.
    function computeEyeOpennessPct(earVal) {
      return Math.max(0, Math.min(100, (earVal - 0.15) / (0.45 - 0.15) * 100));
    }

    function addEarLog(earAvg, closed, headRatio, perclosPct, frontal) {
      if (!isLogging) return;
      const time = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
      earLog.unshift({
        time, ear: earAvg.toFixed(4), closed, headRatio: headRatio.toFixed(3),
        perclos: perclosPct.toFixed(0), frontal,
      });
      if (earLog.length > EAR_LOG_MAX) earLog.pop();
      document.getElementById('earLog').innerHTML = earLog.map(e => {
        const color = e.closed ? 'var(--accent2)' : 'var(--accent)';
        const label = e.closed ? ' ◀ TERTUTUP' : '';
        const frontalLabel = e.frontal ? '' : ' ⚠NOT-FRONTAL';
        return `<div style="color:${color}">${e.time} &nbsp; EAR: <b>${e.ear}</b> &nbsp; `
          + `PERCLOS: <b>${e.perclos}%</b> &nbsp; HTR: <b>${e.headRatio}</b>${label}${frontalLabel}</div>`;
      }).join('');
    }

    function toggleLog() {
      isLogging = !isLogging; const btn = document.getElementById('btnLog');
      if (isLogging) { btn.textContent = '■ STOP'; btn.style.background = 'rgba(0,255,136,0.1)'; btn.style.borderColor = 'rgba(0,255,136,0.3)'; btn.style.color = 'var(--accent)'; }
      else { btn.textContent = '▶ START'; btn.style.background = 'rgba(255,170,0,0.1)'; btn.style.borderColor = 'rgba(255,170,0,0.3)'; btn.style.color = 'var(--warn)'; }
    }

    function clearLog() { earLog = []; document.getElementById('earLog').innerHTML = '<span style="color:var(--text2); opacity:0.5">— log dikosongkan —</span>'; }

    // ══════════════════════════════════════════════════════════════
    // PROSES FRAME (tanpa kalibrasi, langsung aktif)
    // ══════════════════════════════════════════════════════════════
    function processFrameForModel(lm, htrSmoothed) {
      const earL = calcEAR6(lm, LEFT_EYE_EAR), earR = calcEAR6(lm, RIGHT_EYE_EAR);
      let earAvgRaw = (earL + earR) / 2.0;
      const earDiff = Math.abs(earL - earR);
      const mar = calcMAR(lm);

      if (DEBUG) console.log(`📊 Frame: ${frameCount}, Calibrating: ${isCalibrating}, CalibrationEars: ${calibrationEars.length}`);

      // 🔧 KALIBRASI: kumpulkan EAR sampai cukup
      if (isCalibrating) {
        calibrationEars.push(earAvgRaw);
        calibrationHTRs.push(htrSmoothed);
        console.log(`📊 EAR collected: ${earAvgRaw.toFixed(4)} (${calibrationEars.length}/${CALIBRATION_FRAMES})`);

        // Update UI status kalibrasi
        const progress = Math.min(100, Math.round(calibrationEars.length / CALIBRATION_FRAMES * 100));
        document.getElementById('camStatus').querySelector('span').textContent = `🔧 KALIBRASI ${progress}%`;
        document.getElementById('camStatus').className = 'status-card warning';

        if (calibrationEars.length >= CALIBRATION_FRAMES) {
          baselineEAR = calibrationEars.reduce((a, b) => a + b, 0) / calibrationEars.length;
          baselineHTR = calibrationHTRs.reduce((a, b) => a + b, 0) / calibrationHTRs.length;
          isCalibrating = false;
          calibrationDone = true;

          console.log('✅ Kalibrasi selesai!');
          console.log(`   Baseline EAR (mata terbuka normal): ${baselineEAR.toFixed(4)}`);
          console.log(`   Baseline HTR (posisi kepala frontal personal): ${baselineHTR.toFixed(4)}`);

          document.getElementById('camStatus').querySelector('span').textContent = '● SIAGA';
          document.getElementById('camStatus').className = 'status-card alert';
        }

        return { earAvg: earAvgRaw, closedForLog: false, featureDict: null };
      }

      // ear_avg TETAP MENTAH (persis definisi training -- training TIDAK
      // pernah menskalakan ear_avg secara manual, cuma ear_relative yang
      // dinormalisasi). JANGAN kalikan scale factor apapun di sini.
      const earAvg = earAvgRaw;

      // ear_relative = rasio ke baseline personal -- INI yang persis
      // sama definisinya dengan training (baseline_ear per subject,
      // percentile-90 dari frame alert). Di sini baseline = rata-rata
      // kalibrasi 10 frame pertama (asumsi user dalam kondisi alert
      // saat kalibrasi -- sama seperti asumsi di training).
      const earRelative = earAvgRaw / baselineEAR;
      const eyeClosedRelative = earRelative < REL_CLOSE_THRESH ? 1 : 0;

      // 🛡️ Jaring pengaman rule-based -- dihitung tiap frame (bukan
      // cuma tiap 5 frame seperti model), jadi responsnya lebih cepat.
      if (earRelative < REL_CLOSE_THRESH) {
        consecutiveClosedFrames++;
      } else {
        consecutiveClosedFrames = 0;
      }

      const eyeClosedV5 = earAvg < EAR_CLOSE_THRESH_V5 ? 1 : 0;  // dipakai utk log UI saja
      const mouthOpen = mar > MAR_OPEN_THRESH ? 1 : 0;

      pushBuffer('ear_avg', earAvg);
      pushBuffer('mar', mar);
      pushBuffer('eye_closed_v5', eyeClosedV5);
      pushBuffer('mouth_open', mouthOpen);
      pushBuffer('ear_relative', earRelative);
      pushBuffer('eye_closed_relative', eyeClosedRelative);

      const earMarRatio = earAvg / (mar + 1e-6);

      const featureDict = {
        ...normalizeLandmarks(lm),
        ear_avg: earAvg,
        ear_left: earL,
        ear_right: earR,
        ear_diff: earDiff,
        ear_mar_ratio: earMarRatio,
        eye_closed: earAvg < EAR_CLOSE_THRESH_GENERIC ? 1 : 0,
        mar: mar,
        mouth_open: mouthOpen,
        // BARU: fitur yang tadinya hilang total (selalu ke-default 0)
        ear_relative: earRelative,
        eye_closed_v3: eyeClosedRelative,
      };

      for (const w of WINDOW_SIZES) {
        // perclos_w* & ear_rel_* HARUS dihitung dari ear_relative /
        // eye_closed_relative (sesuai training), BUKAN dari ear_avg
        // mentah / eye_closed_v5 seperti versi lama.
        const earRelWindow = frameBuffer.ear_relative.slice(-w);
        const eyeClosedRelWindow = frameBuffer.eye_closed_relative.slice(-w);
        const marWindow = frameBuffer.mar.slice(-w);
        const mouthOpenWindow = frameBuffer.mouth_open.slice(-w);

        const earRelLen = earRelWindow.length;
        const earRelMean = earRelLen > 0 ? earRelWindow.reduce((a, b) => a + b, 0) / earRelLen : 0;
        const earRelMin = earRelLen > 0 ? Math.min(...earRelWindow) : 0;
        const earRelStd = earRelLen > 1 ? (() => {
          const m = earRelMean;
          return Math.sqrt(earRelWindow.reduce((a, b) => a + (b - m) ** 2, 0) / (earRelLen - 1));
        })() : 0;

        const marMean = marWindow.length > 0 ? marWindow.reduce((a, b) => a + b, 0) / marWindow.length : 0;
        const marMax = marWindow.length > 0 ? Math.max(...marWindow) : 0;
        const mouthOpenRate = mouthOpenWindow.length > 0 ? mouthOpenWindow.reduce((a, b) => a + b, 0) / mouthOpenWindow.length : 0;
        const perclos = eyeClosedRelWindow.length > 0
          ? eyeClosedRelWindow.reduce((a, b) => a + b, 0) / eyeClosedRelWindow.length : 0;

        featureDict[`ear_rel_mean_w${w}`] = earRelMean;
        featureDict[`ear_rel_min_w${w}`] = earRelMin;
        featureDict[`ear_rel_std_w${w}`] = earRelStd;
        featureDict[`mar_mean_w${w}`] = marMean;
        featureDict[`mar_max_w${w}`] = marMax;
        featureDict[`mouth_open_rate_w${w}`] = mouthOpenRate;
        featureDict[`perclos_w${w}`] = perclos;

        if (w === 15) lastPerclosW15 = perclos;  // untuk gauge UI
      }

      return { earAvg, closedForLog: eyeClosedRelative === 1, featureDict };
    }

    async function loadAI() {
      model = await tflite.loadTFLiteModel('/model/7smlp_reflandmarkoff.tflite');
      scaler = await fetch('/model/7scaler_reflandmarkoff.json').then(r => r.json());
      console.log('MLP model loaded!');
    }

    let missingFeatureWarned = false;
    function buildScaledVector(featureDict) {
      const missing = scaler.feature_columns.filter(col => !(col in featureDict));
      if (missing.length > 0 && !missingFeatureWarned) {
        console.error(
          `⚠️ ${missing.length} feature_columns dari scaler TIDAK ADA di featureDict ` +
          `(akan ke-default 0, kemungkinan besar bikin prediksi ngaco): `, missing
        );
        missingFeatureWarned = true;  // cukup 1x supaya console tidak spam
      }
      const raw = scaler.feature_columns.map(col => featureDict[col] ?? 0);
      return raw.map((v, i) => v * scaler.scale[i] + scaler.min[i]);
    }

    // ══════════════════════════════════════════════════════════════
    // GANTI: predictLocal untuk MLP (TFLite)
    // ══════════════════════════════════════════════════════════════
    async function predictLocal(featureDict) {
      if (isPredicting) return;
      isPredicting = true;
      try {
        const scaledVec = buildScaledVector(featureDict);

        if (DEBUG) {
          console.log('Feature values:', {
            ear_avg: featureDict.ear_avg,
            ear_rel_mean_w15: featureDict.ear_rel_mean_w15,
            perclos_w15: featureDict.perclos_w15,
            mar: featureDict.mar,
          });
        }

        if (DEBUG && frameCount % 30 === 0) {
          console.log('=== Rolling Window Values ===');
          console.log('ear_avg buffer length:', frameBuffer.ear_avg.length);
          console.log('ear_avg buffer:', frameBuffer.ear_avg);
          console.log('ear_rel_mean_w15:', featureDict.ear_rel_mean_w15);
          console.log('perclos_w15:', featureDict.perclos_w15);
        }

        // MLP output shape [1, 1] → probability
        const input = tf.tensor([scaledVec], [1, scaledVec.length]);
        const output = model.predict(input);
        confidence = output.dataSync()[0];  // MLP output langsung probability

        input.dispose();
        output.dispose();
        if (confidence >= MODEL_THRESH) {
          modelHighStreak++;
          if (modelHighStreak >= MODEL_STREAK_NEEDED) {
            modelDrowsy = true;
            drowsyOnUntil = Date.now() + DROWSY_HOLD_MS;   // perpanjang hold tiap kali confidence tinggi
          }
        } else {
          modelHighStreak = 0;
          modelDrowsy = Date.now() < drowsyOnUntil;   // tetap ON kalau masih dalam masa hold
        }
      } catch (e) {
        console.error('PREDICT ERROR:', e);
      } finally {
        isPredicting = false;
      }
    }

    // ══════════════════════════════════════════════════════════════
    // MEDIAPIPE
    // ══════════════════════════════════════════════════════════════
    const faceMesh = new FaceMesh({ locateFile: f => `https://cdn.jsdelivr.net/npm/@mediapipe/face_mesh/${f}` });
    faceMesh.setOptions({ maxNumFaces: 1, refineLandmarks: false, minDetectionConfidence: 0.5, minTrackingConfidence: 0.5 });

    faceMesh.onResults(async (results) => {
      frameCount++;
      if (DEBUG) console.log(`🔄 Frame count: ${frameCount}`);

      if (!results.multiFaceLandmarks || results.multiFaceLandmarks.length === 0) {
        faceDetected = false;
        document.getElementById('noFace').style.display = 'block';
        updateUI();
        return;
      }
      faceDetected = true;
      document.getElementById('noFace').style.display = 'none';
      const lm = results.multiFaceLandmarks[0];

      headTurnRatio = calcHeadTurnRatio(lm);
      headTurnHistory.push(headTurnRatio);
      if (headTurnHistory.length > HEAD_TURN_SMOOTH_WINDOW) headTurnHistory.shift();
      const headTurnRatioSmoothed = headTurnHistory.reduce((a, b) => a + b, 0) / headTurnHistory.length;

      // Selama kalibrasi, anggap frontal (belum ada baseline utk dibandingkan)
      if (isCalibrating) {
        isFrontal = true;
      } else {
        const deviasi = Math.abs(headTurnRatioSmoothed - baselineHTR) / baselineHTR;
        isFrontal = Math.abs(headTurnRatioSmoothed - baselineHTR) <= HTR_TOLERANCE;
      }

      const result = processFrameForModel(lm, headTurnRatioSmoothed);

      // 🔧 Alert MURNI dari model, tidak ada jalur rule-based lain.
      drowsyActive = modelDrowsy;

      if (frameCount % 15 === 0) {
        addEarLog(result.earAvg, result.closedForLog, headTurnRatio,
          lastPerclosW15 * 100, isFrontal);
      }

      if (!isFrontal) {
        // Reset semua state drowsy
        modelDrowsy = false;
        drowsyActive = false;
        confidence = 0;
        modelHighStreak = 0;
        drowsyOnUntil = 0;
        // Jangan panggil predictLocal
      } else {
        // Hanya jalankan prediksi jika frontal dan kalibrasi selesai
        if (result.featureDict && frameCount % 5 === 0 && calibrationDone) {
          await predictLocal(result.featureDict);
        }
      }

      // Logging hanya jika frontal
      if (frameCount % 15 === 0 && isFrontal) {
        addEarLog(result.earAvg, result.closedForLog, headTurnRatio,
          lastPerclosW15 * 100, isFrontal);
      }

      if (frameCount % 3 === 0) updateUI();
    });

    // ══════════════════════════════════════════════════════════════
    // UI
    // ══════════════════════════════════════════════════════════════
    function updateUI() {
      let status, statusClass;
      const rightPanel = document.querySelector('.right-panel');

      // Tentukan status
      if (!faceDetected) {
        status = 'TIDAK ADA WAJAH';
        statusClass = 'init';
        rightPanel.classList.add('paused');
      } else if (!isFrontal) {
        status = 'KEPALA TIDAK MENGHADAP KAMERA';
        statusClass = 'init';
        rightPanel.classList.add('paused');
      } else {
        rightPanel.classList.remove('paused');
        if (drowsyActive) {
          status = '⚠ MENGANTUK';
          statusClass = 'drowsy';
        } else if (confidence >= 0.4) {
          status = '△ WASPADA';
          statusClass = 'warning';
        } else {
          status = '● SIAGA';
          statusClass = 'alert';
        }
      }

      // Update status card
      document.getElementById('camStatus').querySelector('span').textContent = status;
      document.getElementById('camStatus').className = `status-card ${statusClass}`;

      // Overlay alert hanya jika frontal dan wajah terdeteksi
      const isActive = faceDetected && isFrontal;
      document.getElementById('alertOverlay').classList.toggle('active', isActive && drowsyActive);

      // Beep hanya jika aktif
      const now = Date.now();
      if (isActive && drowsyActive && now - lastBeep > 1000) {
        playBeep();
        lastBeep = now;
      }

      // --- UPDATE METRIK (hanya jika aktif) ---
      const confPct = isActive ? (confidence * 100).toFixed(1) : '0.0';
      document.getElementById('modelVal').textContent = isActive ? confPct + '%' : '—';
      document.getElementById('modelVal').className = 'metric-value' + (isActive && drowsyActive ? ' danger' : '');
      document.getElementById('confPct').textContent = isActive ? confPct + '%' : '0%';
      document.getElementById('confPct').style.color = isActive && drowsyActive ? 'var(--accent2)' : (isActive ? 'var(--accent)' : 'var(--text2)');
      document.getElementById('confBar').style.width = isActive ? confPct + '%' : '0%';
      document.getElementById('confBar').className = 'bar-fill' + (isActive ? (confidence >= MODEL_THRESH ? ' high' : confidence >= 0.4 ? ' medium' : '') : '');

      const lastEar = isActive ? (frameBuffer.ear_avg.at(-1) ?? 0) : 0;
      document.getElementById('earVal').textContent = isActive && frameBuffer.ear_avg.length ? lastEar.toFixed(3) : '—';
      const earPctInfo = isActive ? computeEyeOpennessPct(lastEar) : 0;
      document.getElementById('earPct').textContent = isActive ? earPctInfo.toFixed(0) + '%' : '0%';
      document.getElementById('earBar').style.width = isActive ? earPctInfo + '%' : '0%';

      const perclosPctVal = isActive ? (lastPerclosW15 * 100).toFixed(0) : '0';
      document.getElementById('perclosPct').textContent = isActive ? perclosPctVal + '%' : '0%';
      document.getElementById('perclosBar').style.width = isActive ? perclosPctVal + '%' : '0%';

      // Closure timer
      const timerEl = document.getElementById('closureTimer');
      if (isActive && consecutiveClosedFrames > 0 && !drowsyActive) {
        const approxSeconds = (consecutiveClosedFrames / 15).toFixed(1);
        timerEl.textContent = `⏱ mata tertutup ~${approxSeconds}s — menganalisis pola...`;
      } else {
        timerEl.textContent = '';
      }

      // EAR Log – pause saat tidak aktif
      if (!isActive) {
        document.getElementById('earLog').innerHTML = '<span style="color:var(--text2); opacity:0.5">⏸ PAUSED — wajah tidak frontal / tidak terdeteksi</span>';
      }

      document.getElementById('modelCard').classList.toggle('warn-active', isActive && drowsyActive);
    }

    function playBeep() {
      try {
        const ctx = new (window.AudioContext || window.webkitAudioContext)();
        const osc = ctx.createOscillator(), gain = ctx.createGain();
        osc.connect(gain); gain.connect(ctx.destination);
        osc.frequency.value = 880; osc.type = 'sine';
        gain.gain.setValueAtTime(0.3, ctx.currentTime);
        gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
        osc.start(ctx.currentTime); osc.stop(ctx.currentTime + 0.4);
      } catch (e) { }
    }

    // ══════════════════════════════════════════════════════════════
    // BOOT (dengan guard meshBusy)
    // ══════════════════════════════════════════════════════════════
    (async () => {
      try {
        document.querySelector('.loading-text').textContent = 'MEMUAT AI...';
        await loadAI();
        document.getElementById('loadingScreen').classList.add('hidden');

        const video = document.getElementById('video');
        const stream = await navigator.mediaDevices.getUserMedia({
          video: { facingMode: 'user', width: { ideal: 320 }, height: { ideal: 240 } }, audio: false
        });
        video.srcObject = stream;
        await video.play();

        let meshBusy = false;
        const cam = new Camera(video, {
          onFrame: async () => {
            if (meshBusy) return;
            meshBusy = true;
            await faceMesh.send({ image: video });
            meshBusy = false;
          },
          width: 320, height: 240
        });
        cam.start();

        document.getElementById('camStatus').querySelector('span').textContent = '● SIAGA';
        document.getElementById('camStatus').className = 'status-card alert';
      } catch (e) {
        document.querySelector('.loading-text').textContent = 'GAGAL LOAD AI / KAMERA';
        console.error(e);
      }
    })();
  </script>
</body>

</html>