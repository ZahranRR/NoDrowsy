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
      /* margin-top: 10px; */
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
      z-index: 100;
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
        max-width: 1400px;
        padding-bottom: 40px;
      }

      .header {
        padding: 24px 24px 16px;
      }

      .main-grid {
        display: grid;
        grid-template-columns: 0.9fr 1.1fr 0.9fr;
        /* left, middle(kamera), right */
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
        order: 1;
      }

      .middle-panel {
        display: flex;
        flex-direction: column;
        gap: 10px;
        order: 2;
      }

      .middle-panel #btnStartCalibration {
        display: block;
        width: 100%;
      }

      .right-panel {
        display: flex;
        flex-direction: column;
        gap: 10px;
        order: 3;
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
      <div class="logo">NoDrowsy<span></span></div>
      <div class="status-dot" id="statusDot"></div>
    </div>

    <!-- PC: wrap in grid -->
    <div class="main-grid">

      <!-- Middle panel: kamera -->
      <div class="middle-panel">
        <div class="camera-wrap" id="cameraWrap">
          <video id="video" autoplay playsinline muted></video>
          <canvas id="canvas"></canvas>
          <div class="camera-overlay">
            <div class="corner tl"></div>
            <div class="corner tr"></div>
            <div class="corner bl"></div>
            <div class="corner br"></div>
            <div class="no-face" id="noFace">WAJAH TIDAK TERDETEKSI</div>
            <img src="/assets/outline.png" class="face-guide-img" id="faceGuide" alt="">
            <div class="distance-hint" id="distanceHint">
              <div class="dot"></div>
              <span id="distanceText">Posisikan wajah Anda di dalam outline untuk hasil terbaik</span>
            </div>
          </div>
        </div>

        <div class="status-card init" id="camStatus"><span>MEMUAT...</span></div>
      </div>

      <!-- Left panel: metrics + confidence -->
      <div class="left-panel">
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

          <div class="conf-header" style="margin-top:10px">
            <span class="conf-label">PERCLOS (1 detik terakhir)</span>
            <span class="conf-value" id="perclosPct" style="color:var(--warn)">0%</span>
          </div>
          <div class="bar-track">
            <div class="bar-fill" id="perclosBar" style="background:var(--warn)"></div>
          </div>

          <div id="closureTimer" style="
            margin-top:8px; font-family:var(--font-mono); font-size:10px;
            color:var(--text2); text-align:center; min-height:14px;
          "></div>

          <button id="btnStartCalibration" onclick="startCalibration()" style="
            display:none;
            background:rgba(0,255,136,0.1); border:1px solid rgba(0,255,136,0.3);
            color:var(--accent); padding:12px; border-radius:12px;
            font-family:var(--font-mono); font-size:12px; letter-spacing:1px;
            cursor:pointer; text-transform:uppercase;
            ">Mulai Kalibrasi</button>
        </div>
      </div>

      <!-- Right panel: IoT -->
      <div class="right-panel">
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

          <div style="margin-top: 14px; padding-top: 14px; border-top: 1px solid var(--border);">
            <div style="display:flex; justify-content:space-between; align-items:center;">
              <div style="flex:1;">
                <div class="iot-metric-label" style="font-size:8px; margin-bottom:4px;">CARDIO BASELINE</div>
                <div id="baselineVal"
                  style="font-family: var(--font-mono); font-size: 20px; font-weight: 700; color: var(--accent);">--
                </div>
                <div id="baselineCountdown"
                  style="font-family: var(--font-mono); font-size: 11px; color: var(--text2); margin-top: 4px; display: none;">
                </div>
              </div>
              <button onclick="resetBaseline()" style="
                background: rgba(255, 68, 102, 0.1); border: 1px solid rgba(255, 68, 102, 0.3);
                color: var(--accent2); padding: 6px 10px; border-radius: 6px;
                font-family: var(--font-mono); font-size: 9px; font-weight: 700;
                cursor: pointer; transition: all 0.2s; display: flex; align-items: center; gap: 4px;
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

        <div class="iot-section" id="hrChartSection">
          <div class="iot-header">
            <div class="iot-dot"></div>
            <div class="iot-title">Heart Rate History</div>
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
    const LARAVEL_URL = `${window.location.protocol}//${window.location.hostname}:8000`;
    const MODEL_THRESH = 0.3;
    const MODEL_STREAK_MS = 900;
    const DROWSY_HOLD_MS = 800;
    const HEAD_TURN_SMOOTH_WINDOW = 5
    const CALIBRATION_FRAMES = 30;
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

    const MAX_WINDOW = 15;

    let confidence = 0;
    let faceDetected = false;
    let isFrontal = true;
    let headTurnRatio = 1;

    let lastBeep = 0;
    let frameCount = 0;
    let model, scaler;
    let isPredicting = false;
    let cameraActive = false;
    let cameraInstance = null;

    // HR state (diisi dari fetchIoT)
    let hrLow = false;
    let hrChart = null;
    let currentRange = 30;

    let confidenceHighSince = 0, modelDrowsy = false;
    let drowsyOnUntil = 0;
    let consecutiveClosedFrames = 0;
    let lastPerclosW15 = 0;

    let isCalibrating = false;
    let calibrationStarted = false;
    let calibrationEars = [];
    let baselineEAR = 1.0;
    let calibrationDone = false;

    let calibrationHTRs = [];
    let baselineHTR = 1.0;
    const HTR_TOLERANCE = 0.25;

    let headTurnHistory = [];

    const frameBuffer = {
      ear_avg: [], mar: [], eye_closed_v5: [], mouth_open: [],
      ear_relative: [], eye_closed_relative: [],
    };

    function dist(a, b) {
      return Math.sqrt((a.x - b.x) ** 2 + (a.y - b.y) ** 2);
    }

    function pushBuffer(k, v) {
      frameBuffer[k].push(v);
      if (frameBuffer[k].length > MAX_WINDOW) frameBuffer[k].shift();
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
      const v1 = dist(lm[MOUTH_TOP], lm[MOUTH_BOTTOM]);
      const v2 = dist(lm[MOUTH_TOP_OUTER], lm[MOUTH_BOT_OUTER]);
      const h = dist(lm[MOUTH_LEFT], lm[MOUTH_RIGHT]);
      return h === 0 ? 0 : (v1 + v2) / (2 * h);
    }

    function normalizeLandmarks(lm) {
      const nose = lm[NOSE_TIP];
      const scale = Math.max(dist(lm[LEFT_EYE_OUT], lm[RIGHT_EYE_OUT]), 1e-6);
      const out = {};
      for (const i of LANDMARK_ORDER) {
        out[`lx${i}`] = (lm[i].x - nose.x) / scale;
        out[`ly${i}`] = (lm[i].y - nose.y) / scale;
      }
      return out;
    }

    function computeDrowsinessLevel() {
      const hrLevelValue = hrLow ? 2 : 0;
      const modelLevel = modelDrowsy ? 2 : (confidence >= 0.4 ? 1 : 0);

      if (modelLevel === 2 && hrLevelValue >= 1) return 3;
      if (modelLevel === 2) return 2;
      if (modelLevel === 1 && hrLevelValue >= 1) return 2;
      if (modelLevel === 1 || hrLevelValue >= 1) return 1;
      return 0;
    }

    function startCalibration() {
      calibrationEars = [];
      calibrationHTRs = [];
      calibrationStarted = true;
      isCalibrating = true;
      document.getElementById('btnStartCalibration').style.display = 'none';
      document.getElementById('camStatus').querySelector('span').textContent = '🔧 KALIBRASI 0%';
      document.getElementById('camStatus').className = 'status-card warning';
    }

    function processFrameForModel(lm, htrSmoothed) {
      const earL = calcEAR6(lm, LEFT_EYE_EAR), earR = calcEAR6(lm, RIGHT_EYE_EAR);
      const earAvgRaw = (earL + earR) / 2.0;
      const mar = calcMAR(lm);

      if (isCalibrating) {
        calibrationEars.push(earAvgRaw);
        calibrationHTRs.push(htrSmoothed);

        const progress = Math.min(100, Math.round(calibrationEars.length / CALIBRATION_FRAMES * 100));
        document.getElementById('camStatus').querySelector('span').textContent = `🔧 KALIBRASI ${progress}%`;
        document.getElementById('camStatus').className = 'status-card warning';

        if (calibrationEars.length >= CALIBRATION_FRAMES) {
          baselineEAR = calibrationEars.reduce((a, b) => a + b, 0) / calibrationEars.length;
          baselineHTR = calibrationHTRs.reduce((a, b) => a + b, 0) / calibrationHTRs.length;
          isCalibrating = false;
          calibrationDone = true;
          document.getElementById('camStatus').querySelector('span').textContent = '● SIAGA';
          document.getElementById('camStatus').className = 'status-card alert';
        }
        return;
      }

      const earAvg = earAvgRaw;
      const earRelative = earAvgRaw / baselineEAR;
      const eyeClosedRelative = earRelative < REL_CLOSE_THRESH ? 1 : 0;

      if (earRelative < REL_CLOSE_THRESH) consecutiveClosedFrames++;
      else consecutiveClosedFrames = 0;

      const mouthOpen = mar > 0.5 ? 1 : 0;

      pushBuffer('ear_avg', earAvg);
      pushBuffer('mar', mar);
      pushBuffer('mouth_open', mouthOpen);
      pushBuffer('ear_relative', earRelative);
      pushBuffer('eye_closed_relative', eyeClosedRelative);

      const featureDict = {
        ...normalizeLandmarks(lm),
        ear_avg: earAvg,
        ear_left: earL,
        ear_right: earR,
        ear_diff: Math.abs(earL - earR),
        mar: mar,
        mouth_open: mouthOpen,
        ear_relative: earRelative,
        eye_closed_v3: eyeClosedRelative,
      };

      for (const w of [5, 10, 15]) {
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

        const marMean = marWindow.length ? marWindow.reduce((a, b) => a + b, 0) / marWindow.length : 0;
        const marMax = marWindow.length ? Math.max(...marWindow) : 0;
        const mouthOpenRate = mouthOpenWindow.length ? mouthOpenWindow.reduce((a, b) => a + b, 0) / mouthOpenWindow.length : 0;
        const perclos = eyeClosedRelWindow.length ? eyeClosedRelWindow.reduce((a, b) => a + b, 0) / eyeClosedRelWindow.length : 0;

        featureDict[`ear_rel_mean_w${w}`] = earRelMean;
        featureDict[`ear_rel_min_w${w}`] = earRelMin;
        featureDict[`ear_rel_std_w${w}`] = earRelStd;
        featureDict[`mar_mean_w${w}`] = marMean;
        featureDict[`mar_max_w${w}`] = marMax;
        featureDict[`mouth_open_rate_w${w}`] = mouthOpenRate;
        featureDict[`perclos_w${w}`] = perclos;
        if (w === 15) lastPerclosW15 = perclos;
      }

      if (frameCount % 5 === 0 && calibrationDone) {
        predictLocal(featureDict);
      }
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
        document.getElementById('noFace').style.display = 'block';
        updateUI();
        return;
      }

      faceDetected = true;
      document.getElementById('noFace').style.display = 'none';
      const lm = results.multiFaceLandmarks[0];

      if (!calibrationStarted) { updateUI(); return; }

      headTurnRatio = calcHeadTurnRatio(lm);
      headTurnHistory.push(headTurnRatio);
      if (headTurnHistory.length > HEAD_TURN_SMOOTH_WINDOW) headTurnHistory.shift();
      const headTurnRatioSmoothed = headTurnHistory.reduce((a, b) => a + b, 0) / headTurnHistory.length;

      if (isCalibrating) {
        isFrontal = true;
      } else {
        isFrontal = Math.abs(headTurnRatioSmoothed - baselineHTR) <= HTR_TOLERANCE;
      }

      processFrameForModel(lm, headTurnRatioSmoothed);

      if (!isFrontal) {
        modelDrowsy = false;
        confidence = 0;
        confidenceHighSince = 0;
        drowsyOnUntil = 0;
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

        // Update state HR global — dipakai computeDrowsinessLevel()
        hrLow = data.hr_low === true;

      } catch (e) { }
    }

    // ── Reset baseline ────────────────────────────────────────────────
    async function resetBaseline() {
      await fetch(`${LARAVEL_URL}/api/baseline/reset`, { method: 'POST' }).catch(() => { });
      resetDrowsinessState();
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

    function resetDrowsinessState() {
      confidence = 0;
      confidenceHighSince = 0;
      modelDrowsy = false;
      drowsyOnUntil = 0;
      consecutiveClosedFrames = 0;
    }

    // ── Update UI ─────────────────────────────────────────────────────
    function updateUI() {
      if (!cameraActive) return;

      if (isCalibrating) return;

      const level = computeDrowsinessLevel();

      let status, statusClass;
      if (!faceDetected) {
        status = 'TIDAK ADA WAJAH'; statusClass = 'init';
      } else if (level === 3) {
        status = '⚠ MICROSLEEP ALERT'; statusClass = 'drowsy';
      } else if (level === 2) {
        status = '⚠ MENGANTUK'; statusClass = 'drowsy';
      } else if (level === 1) {
        status = '△ WASPADA'; statusClass = 'warning';
      } else {
        status = '● SIAGA'; statusClass = 'alert';
      }

      document.getElementById('camStatus').querySelector('span').textContent = status;
      document.getElementById('camStatus').className = `status-card ${statusClass}`;
      document.getElementById('alertOverlay').classList.toggle('active', level >= 2);

      const now = Date.now();
      if (level >= 2 && now - lastBeep > 1000) { playBeep(); lastBeep = now; }

      const lastEar = frameBuffer.ear_avg.at(-1) ?? 0;
      document.getElementById('earVal').textContent = frameBuffer.ear_avg.length ? lastEar.toFixed(3) : '—';
      document.getElementById('earVal').className = 'metric-value' + (modelDrowsy ? ' danger' : ' good');
      document.getElementById('earCard').classList.toggle('warn-active', modelDrowsy);

      // ★ Cek null karena confidence section di-comment di HTML
      const earBarEl = document.getElementById('earBar');
      const earPctEl = document.getElementById('earPct');
      if (earBarEl) {
        const earPct = Math.max(0, Math.min(100, (lastEar - 0.15) / (0.45 - 0.15) * 100));
        earBarEl.style.width = earPct + '%';
        earBarEl.className = 'ear-bar-fill' + (modelDrowsy ? ' low' : '');
        if (earPctEl) earPctEl.textContent = earPct.toFixed(0) + '%';
      }

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

      // PERCLOS gauge
      const perclosPctVal = (lastPerclosW15 * 100).toFixed(0);
      document.getElementById('perclosPct').textContent = perclosPctVal + '%';
      document.getElementById('perclosBar').style.width = perclosPctVal + '%';

      // Closure timer (informatif, tidak mempengaruhi alert)
      const timerEl = document.getElementById('closureTimer');
      if (consecutiveClosedFrames > 0 && !modelDrowsy) {
        const approxSeconds = (consecutiveClosedFrames / 15).toFixed(1);
        timerEl.textContent = `⏱ mata tertutup ~${approxSeconds}s — menganalisis pola...`;
      } else {
        timerEl.textContent = '';
      }
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
      model = await tflite.loadTFLiteModel('/model/7smlp_reflandmarkoff.tflite');
      scaler = await fetch('/model/7scaler_reflandmarkoff.json').then(r => r.json());
      console.log('MLP Model Loaded');
    }

    function buildScaledVector(featureDict) {
      const raw = scaler.feature_columns.map(col => featureDict[col] ?? 0);
      return raw.map((v, i) => v * scaler.scale[i] + scaler.min[i]);
    }

    async function predictLocal(featureDict) {
      if (isPredicting) return;
      isPredicting = true;
      try {
        const scaledVec = buildScaledVector(featureDict);
        const input = tf.tensor([scaledVec], [1, scaledVec.length]);
        const output = model.predict(input);
        confidence = output.dataSync()[0];
        input.dispose();
        output.dispose();

        if (confidence >= MODEL_THRESH) {
          if (confidenceHighSince === 0) confidenceHighSince = Date.now();
          if (Date.now() - confidenceHighSince >= MODEL_STREAK_MS) {
            modelDrowsy = true;
            drowsyOnUntil = Date.now() + DROWSY_HOLD_MS;
          }
        } else {
          confidenceHighSince = 0;
          modelDrowsy = Date.now() < drowsyOnUntil;
        }
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

        initChart();
        loadChartData(currentRange);

        // Kamera langsung aktif, tidak menunggu HR
        await activateCamera();

        document.getElementById('btnStartCalibration').style.display = 'block';

        document.getElementById('loadingScreen').classList.add('hidden');
      } catch (e) {
        document.querySelector('.loading-text').textContent = 'GAGAL LOAD AI / KAMERA';
      }
    })();

    // Polling IoT setiap 2 detik
    setInterval(fetchIoT, 2000);
    fetchIoT();
  </script>
</body>

</html>