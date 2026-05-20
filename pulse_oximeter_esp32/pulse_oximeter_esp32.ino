/*
 * Pulse Oximeter - ESP32 Version (MQTT)
 * ================================
 * Hardware:
 *   - ESP32
 *   - MAX30102 Sensor  : SDA=GPIO21, SCL=GPIO22
 *   - OLED 128x64 SH1106: SDA=GPIO21, SCL=GPIO22
 *
 * Library yang dibutuhkan (install via Library Manager):
 *   - Adafruit SH110X  (by Adafruit)
 *   - Adafruit GFX Library (by Adafruit)
 *   - PubSubClient     (by Nick O'Leary)  ← BARU
 *
 * File lokal (taruh satu folder dengan .ino ini):
 *   - MAX30102.h / MAX30102.cpp
 *   - Pulse.h / Pulse.cpp
 */

#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SH110X.h>
#include "MAX30102.h"
#include "Pulse.h"
#include <Preferences.h>
#include <esp_sleep.h>
#include <pgmspace.h>
#include <WiFi.h>
#include <PubSubClient.h>  // ← ganti HTTPClient & ArduinoJson

// ─── WiFi & MQTT Config ───────────────────────────────────────────
const char* WIFI_SSID     = "watermelons";
const char* WIFI_PASSWORD = "watermelons";
const char* MQTT_SERVER   = "192.168.18.11";  // IP laptop
const int   MQTT_PORT     = 1883;
const char* MQTT_TOPIC    = "oximeter/data";
const char* MQTT_CLIENT   = "ESP32-Oximeter";

// ─── Pin & Layar ─────────────────────────────────────────────────
#define I2C_SDA      21
#define I2C_SCL      22
#define OLED_ADDR    0x3C
#define SCREEN_W     128
#define SCREEN_H     64

// ─── Objects ─────────────────────────────────────────────────────
Adafruit_SH1106G oled(SCREEN_W, SCREEN_H, &Wire, -1);
MAX30102          sensor;
Pulse             pulseIR;
Pulse             pulseRed;
MAFilter          bpm;
WiFiClient        wifiClient;
PubSubClient      mqtt(wifiClient);

// ─── BPM Smoothing ───────────────────────────────────────────────
#define BPM_SAMPLES 8
int     bpmBuffer[BPM_SAMPLES] = {0};
uint8_t bpmIdx = 0;

int smoothBPM(int newVal) {
  if (newVal < 30 || newVal > 200) return bpmBuffer[(bpmIdx + BPM_SAMPLES - 1) % BPM_SAMPLES];
  bpmBuffer[bpmIdx] = newVal;
  bpmIdx = (bpmIdx + 1) % BPM_SAMPLES;
  int sum = 0, count = 0;
  for (int i = 0; i < BPM_SAMPLES; i++) {
    if (bpmBuffer[i] > 0) { sum += bpmBuffer[i]; count++; }
  }
  return count > 0 ? sum / count : newVal;
}

Preferences prefs;

// ─── SpO2 Lookup Table ───────────────────────────────────────────
const uint8_t spo2_table[184] PROGMEM = {
   95, 95, 95, 96, 96, 96, 97, 97, 97, 97, 97, 98, 98, 98, 98, 98,
   99, 99, 99, 99, 99, 99, 99, 99,100,100,100,100,100,100,100,100,
  100,100,100,100,100,100,100,100,100,100,100,100, 99, 99, 99, 99,
   99, 99, 99, 99, 98, 98, 98, 98, 98, 98, 97, 97, 97, 97, 96, 96,
   96, 96, 95, 95, 95, 94, 94, 94, 93, 93, 93, 92, 92, 92, 91, 91,
   90, 90, 89, 89, 89, 88, 88, 87, 87, 86, 86, 85, 85, 84, 84, 83,
   82, 82, 81, 81, 80, 80, 79, 78, 78, 77, 76, 76, 75, 74, 74, 73,
   72, 72, 71, 70, 69, 69, 68, 67, 66, 66, 65, 64, 63, 62, 62, 61,
   60, 59, 58, 57, 56, 56, 55, 54, 53, 52, 51, 50, 49, 48, 47, 46,
   45, 44, 43, 42, 41, 40, 39, 38, 37, 36, 35, 34, 33, 31, 30, 29,
   28, 27, 26, 25, 23, 22, 21, 20, 19, 17, 16, 15, 14, 12, 11, 10,
    9,  7,  6,  5,  3,  2,  1
};

// ─── Waveform ────────────────────────────────────────────────────
const uint8_t MAXWAVE = 72;

class Waveform {
public:
  Waveform() : wavep(0) {}

  void record(int waveval) {
    waveval = waveval / 8 + 128;
    waveval = constrain(waveval, 0, 255);
    waveform[wavep] = (uint8_t)waveval;
    wavep = (wavep + 1) % MAXWAVE;
  }

  void scale() {
    uint8_t maxw = 0, minw = 255;
    for (int i = 0; i < MAXWAVE; i++) {
      if (waveform[i] > maxw) maxw = waveform[i];
      if (waveform[i] < minw) minw = waveform[i];
    }
    uint8_t scale8 = (maxw - minw) / 4 + 1;
    uint8_t index = wavep;
    for (int i = 0; i < MAXWAVE; i++) {
      disp_wave[i] = 31 - ((uint16_t)(waveform[index] - minw) * 8) / scale8;
      index = (index + 1) % MAXWAVE;
    }
  }

  void draw(uint8_t X) {
    for (int i = 0; i < MAXWAVE; i++) {
      uint8_t y = disp_wave[i];
      oled.drawPixel(X + i, y, SH110X_WHITE);
      if (i < MAXWAVE - 1) {
        uint8_t nexty = disp_wave[i + 1];
        if (nexty > y)
          for (uint8_t iy = y + 1; iy < nexty; iy++)
            oled.drawPixel(X + i, iy, SH110X_WHITE);
        else if (nexty < y)
          for (uint8_t iy = nexty + 1; iy < y; iy++)
            oled.drawPixel(X + i, iy, SH110X_WHITE);
      }
    }
  }

private:
  uint8_t waveform[MAXWAVE];
  uint8_t disp_wave[MAXWAVE];
  uint8_t wavep;
} wave;

// ─── Global State ────────────────────────────────────────────────
int     beatAvg          = 0;
int     SPO2             = 0;
int     SPO2f            = 0;
bool    filter_for_graph = false;
bool    draw_Red         = false;
uint8_t sleep_counter    = 0;
long    lastBeat         = 0;
long    displaytime      = 0;

// ─── Helper: cetak angka multi-digit di OLED ─────────────────────
void print_digit(int x, int y, long val, char c = ' ', uint8_t field = 3, uint8_t sz = 2) {
  uint8_t ff = field;
  do {
    char ch = (val != 0) ? (char)(val % 10 + '0') : c;
    oled.setCursor(x + sz * (ff - 1) * 6, y);
    oled.setTextSize(sz);
    oled.setTextColor(SH110X_WHITE);
    oled.print(ch);
    val /= 10;
    --ff;
  } while (ff > 0);
}

// ─── Deep Sleep ──────────────────────────────────────────────────
void go_sleep() {
  oled.clearDisplay();
  oled.display();
  oled.oled_command(SH110X_DISPLAYOFF);
  delay(10);
  sensor.off();
  delay(10);
  esp_deep_sleep_start();
}

// ─── Draw OLED ───────────────────────────────────────────────────
void draw_oled(int msg) {
  oled.clearDisplay();

  switch (msg) {
    case 0:
      oled.setTextSize(1);
      oled.setTextColor(SH110X_WHITE);
      oled.setCursor(10, 28);
      oled.print(F("Device error"));
      break;

    case 1:
      oled.setTextSize(2);
      oled.setTextColor(SH110X_WHITE);
      oled.setCursor(0, 10);
      oled.print(F("PLACE YOUR"));
      oled.setCursor(25, 34);
      oled.print(F("FINGER"));
      break;

    case 2:
      wave.draw(0);
      oled.drawFastHLine(0, 33, 128, SH110X_WHITE);
      oled.setTextSize(1);
      oled.setTextColor(SH110X_WHITE);
      oled.setCursor(0, 36);
      oled.print(F("PULSE RATE"));
      oled.setTextSize(2);
      oled.setCursor(0, 48);
      if (beatAvg > 0) oled.print(beatAvg);
      else oled.print(F("---"));
      oled.setTextSize(1);
      oled.setCursor(68, 36);
      oled.print(F("OXYGEN SAT"));
      oled.setTextSize(2);
      oled.setCursor(68, 48);
      if (SPO2f > 0) {
        oled.print(SPO2f);
        oled.setTextSize(1);
        oled.print('%');
      } else {
        oled.print(F("---"));
      }
      break;

    case 3:
      oled.setTextSize(2);
      oled.setTextColor(SH110X_WHITE);
      oled.setCursor(20, 10);
      oled.print(F("  Pulse"));
      oled.setCursor(10, 34);
      oled.print(F("Oximeter"));
      break;

    case 4:
      oled.setTextSize(1);
      oled.setTextColor(SH110X_WHITE);
      oled.setCursor(20, 24);
      oled.print(F("Sleeping in "));
      oled.print((char)('0' + (10 - sleep_counter / 10)));
      oled.print('s');
      break;

    case 5:
      oled.setTextSize(1);
      oled.setTextColor(SH110X_WHITE);
      oled.setCursor(0, 0);
      oled.print(F("Avg Pulse Rate"));
      oled.setTextSize(2);
      oled.setCursor(0, 12);
      oled.print(beatAvg);
      oled.setTextSize(1);
      oled.setCursor(0, 35);
      oled.print(F("Avg SpO2"));
      oled.setTextSize(2);
      oled.setCursor(0, 47);
      oled.print(SPO2);
      oled.setTextSize(1);
      oled.print('%');
      break;
  }

  oled.display();
}

// ─── Koneksi WiFi ─────────────────────────────────────────────────
void connectWiFi() {
  Serial.print("Connecting to WiFi");
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  int retry = 0;
  while (WiFi.status() != WL_CONNECTED && retry < 20) {
    delay(500);
    Serial.print(".");
    retry++;
  }
  if (WiFi.status() == WL_CONNECTED) {
    Serial.println("\nWiFi connected! IP: " + WiFi.localIP().toString());
  } else {
    Serial.println("\nWiFi gagal, lanjut tanpa internet.");
  }
}

// ─── Koneksi MQTT ────────────────────────────────────────────────
void connectMQTT() {
  if (WiFi.status() != WL_CONNECTED) return;
  if (mqtt.connected()) return;

  Serial.print("Connecting MQTT...");
  if (mqtt.connect(MQTT_CLIENT)) {
    Serial.println("connected!");
  } else {
    Serial.printf("failed rc=%d\n", mqtt.state());
  }
}

// ─── Setup ───────────────────────────────────────────────────────
void setup() {
  Serial.begin(115200);

  Wire.begin(I2C_SDA, I2C_SCL);
  Wire.setClock(400000);

  connectWiFi();

  // Setup MQTT
  mqtt.setServer(MQTT_SERVER, MQTT_PORT);
  connectMQTT();

  prefs.begin("oximeter", false);
  filter_for_graph = prefs.getBool("filter", false);
  draw_Red         = prefs.getBool("drawRed", false);

  if (!oled.begin(OLED_ADDR, true)) {
    Serial.println(F("OLED tidak ditemukan!"));
    while (1) delay(500);
  }
  oled.clearDisplay();
  oled.display();

  draw_oled(3);
  delay(3000);

  if (!sensor.begin()) {
    draw_oled(0);
    Serial.println(F("MAX30102 tidak ditemukan!"));
    while (1) delay(500);
  }
  sensor.setup();
  lastBeat = millis();

  Serial.println(F("Siap!"));
}

// ─── Loop ────────────────────────────────────────────────────────
unsigned long lastSend = 0;
const int SEND_INTERVAL = 2000;

void loop() {
  // MQTT keep-alive (non-blocking)
  if (!mqtt.connected()) connectMQTT();
  mqtt.loop();

  sensor.check();
  long now = millis();

  if (!sensor.available()) return;

  uint32_t irValue  = sensor.getIR();
  uint32_t redValue = sensor.getRed();
  sensor.nextSample();

  // ── Jari tidak diletakkan ──────────────────────────────────────
  if (irValue < 5000) {
    draw_oled(sleep_counter <= 50 ? 1 : 4);
    delay(200);
    ++sleep_counter;
    if (sleep_counter > 100) {
      go_sleep();
      sleep_counter = 0;
    }
    return;
  }

  // ── Jari terdeteksi ───────────────────────────────────────────
  sleep_counter = 0;

  int16_t IR_signal, Red_signal;
  bool    beatRed, beatIR;

  if (!filter_for_graph) {
    IR_signal  = pulseIR.dc_filter(irValue);
    Red_signal = pulseRed.dc_filter(redValue);
    beatRed    = pulseRed.isBeat(pulseRed.ma_filter(Red_signal));
    beatIR     = pulseIR.isBeat(pulseIR.ma_filter(IR_signal));
  } else {
    IR_signal  = pulseIR.ma_filter(pulseIR.dc_filter(irValue));
    Red_signal = pulseRed.ma_filter(pulseRed.dc_filter(redValue));
    beatRed    = pulseRed.isBeat(Red_signal);
    beatIR     = pulseIR.isBeat(IR_signal);
  }

  wave.record(draw_Red ? -Red_signal : -IR_signal);

  // ── Deteksi heartbeat & hitung BPM ────────────────────────────
  if (draw_Red ? beatRed : beatIR) {
    long btpm = 60000L / (now - lastBeat);
    if (btpm > 0 && btpm < 200)
      beatAvg = smoothBPM((int)btpm);
    lastBeat = now;

    long numerator   = (pulseRed.avgAC() * pulseIR.avgDC()) / 256;
    long denominator = (pulseRed.avgDC() * pulseIR.avgAC()) / 256;
    int  RX100       = (denominator > 0) ? (numerator * 100) / denominator : 999;

    SPO2f = (10400 - RX100 * 17 + 50) / 100;
    if (RX100 >= 0 && RX100 < 184)
      SPO2 = pgm_read_byte_near(&spo2_table[RX100]);

    Serial.print(F("BPM: "));          Serial.print(beatAvg);
    Serial.print(F("  |  SpO2: "));    Serial.print(SPO2);    Serial.print('%');
    Serial.print(F("  |  SpO2(f): ")); Serial.print(SPO2f);   Serial.print('%');
    Serial.print(F("  |  IR: "));      Serial.print(irValue);
    Serial.print(F("  |  Red: "));     Serial.println(redValue);
  }

  // ── Kirim via MQTT setiap 2 detik ─────────────────────────────
  if (now - lastSend >= SEND_INTERVAL) {
    lastSend = now;
    if (mqtt.connected() && beatAvg > 0 && SPO2f > 0) {
      String payload = "{\"hr\":" + String(beatAvg) + ",\"spo2\":" + String(SPO2f) + "}";
      bool ok = mqtt.publish(MQTT_TOPIC, payload.c_str());
      Serial.println(ok ? "MQTT published: " + payload : "MQTT publish failed");
    }
  }

  // ── Update display setiap 50ms ────────────────────────────────
  if (now - displaytime > 50) {
    displaytime = now;
    wave.scale();
    draw_oled(2);
  }
}
