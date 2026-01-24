#include <WiFi.h>
#include <HTTPClient.h>
#include <WiFiManager.h>
#include <Adafruit_Fingerprint.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>

// ================= PINS =================
#define RXD2 16
#define TXD2 17
#define BUTTON_PIN 26
#define BUZZER_PIN 25

// ================= OLED (SSD1306) =================
#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 64
#define OLED_RESET   -1
Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, OLED_RESET);

// ================= WIFI (WiFiManager) =================
WiFiManager wm; // manages WiFi credentials via web portal

// ================= API =================
const char* SEND_API = "https://alsunnah.dreammake-soft.com/fingerprint-send";
const char* CHECK_API = "https://alsunnah.dreammake-soft.com/fingerprint-check";

// ================= FINGERPRINT =================
HardwareSerial mySerial(2);
Adafruit_Fingerprint finger(&mySerial);

// ================= STATE =================
uint8_t enrollID = 1;
bool enrollRequested = false;
const int CONFIDENCE_THRESHOLD = 50;

// Button long-press
unsigned long buttonPressTime = 0;
bool buttonHolding = false;
// thresholds (ms): 10s for WiFi config, 20s for full reset
const unsigned long WIFI_CONFIG_PRESS_MS = 10000UL;
const unsigned long FULL_RESET_PRESS_MS  = 20000UL;

// Forward declarations for helpers used before their definitions
void showWifiConnecting();
void showWifiConnected(const String &ip);
void showWifiError(const String &msg);
void showStatusScreen(const String &title, const String &subtitle, const String &icon);
void beep(int onMs);
void errorBeeps();
void warningBeeps();
void formatAllFingerprints();

// ================= WIFI HELPERS =================
void connectToWiFi() {
  WiFi.mode(WIFI_STA);
  Serial.println("Connecting using WiFiManager (autoConnect)...");

  // This will try saved credentials; if none/invalid, it opens AP "FP-Config"
  wm.setConfigPortalTimeout(180); // 3 minutes config portal timeout at boot

  showWifiConnecting();

  bool res = wm.autoConnect("FP-Config");
  if (!res) {
    Serial.println("[WiFi] AutoConnect failed or timed out. Continuing without WiFi.");
    showWifiError("No WiFi");
  } else {
    Serial.println("[WiFi] Connected!");
    showWifiConnected(WiFi.localIP().toString());
  }
}

void startWifiConfigPortal() {
  WiFi.mode(WIFI_STA);
  Serial.println("[WiFi] Starting config portal (10s button press)...");
  showStatusScreen("WIFI CFG", "Open AP", "~");
  beep(300);

  wm.setConfigPortalTimeout(300); // 5 minutes when manually triggered
  bool res = wm.startConfigPortal("FP-Config");

  if (res) {
    Serial.println("[WiFi] Config saved, connected.");
    beep(800);
    showWifiConnected(WiFi.localIP().toString());
  } else {
    Serial.println("[WiFi] Config portal timeout / closed without connect.");
    errorBeeps();
    showWifiError("Cfg timeout");
  }
}

void resetAllData() {
  Serial.println("[RESET] 20s button press detected. Resetting ALL data...");
  showStatusScreen("RESET", "All data", "!");
  warningBeeps();

  // Erase fingerprint database
  formatAllFingerprints();

  // Clear WiFi credentials stored by WiFiManager / WiFi stack
  Serial.println("[RESET] Clearing WiFi settings...");
  wm.resetSettings();

  showStatusScreen("RESET", "Restarting", "!");
  delay(2000);
  ESP.restart();
}

// ================= OLED HELPERS =================
void drawCenteredText(const String &text, int16_t y, uint8_t size) {
  int16_t x1, y1;
  uint16_t w, h;
  display.setTextSize(size);
  display.setTextColor(SSD1306_WHITE);
  display.getTextBounds(text, 0, 0, &x1, &y1, &w, &h);
  int16_t x = (SCREEN_WIDTH - w) / 2;
  display.setCursor(x, y);
  display.print(text);
}

void drawWifiStatusIcon() {
  display.setTextSize(1);
  display.setTextColor(SSD1306_WHITE);
  display.setCursor(0, 0);

  if (WiFi.status() == WL_CONNECTED) {
    display.print("Wi");   // Wi-Fi connected indicator
  } else {
    display.print("X");    // Disconnected indicator (cross)
  }
}

void showStatusScreen(const String &title, const String &subtitle = "", const String &icon = "") {
  display.clearDisplay();

  // Always show WiFi status at top-left
  drawWifiStatusIcon();

  // Optional main icon (small), slightly lower to avoid WiFi icon
  if (icon.length() > 0) {
    drawCenteredText(icon, 10, 1);
  }

  // Main title (big)
  drawCenteredText(title, 20, 2);

  // Subtitle (medium)
  if (subtitle.length() > 0) {
    drawCenteredText(subtitle, 44, 2);
  }

  display.display();
}

void showBootScreen() {
  showStatusScreen("BOOTING", "Fingerprint System", "");
}

void showWifiConnecting() {
  showStatusScreen("WIFI", "Connecting...", "~");
}

void showWifiConnected(const String &ip) {
  showStatusScreen("WIFI OK", ip, "");
}

void showWifiError(const String &msg) {
  showStatusScreen("WIFI ERR", msg, "!");
}

void showReady() {
  showStatusScreen("READY", "Place finger", "");
}

void showEnrollRequested(uint8_t id) {
  showStatusScreen("ENROLL", "ID: " + String(id), "+");
}

void showEnrollStep(const char *stepMsg) {
  showStatusScreen("ENROLL", stepMsg, "+");
}

void showEnrollSuccess(uint8_t id) {
  showStatusScreen("ENROLLED", "ID: " + String(id), "✔");
}

void showEnrollError(const char *msg) {
  showStatusScreen("ENROLL ERR", msg, "!");
}

void showScanWaiting() {
  showStatusScreen("SCAN", "Place finger", "");
}

void showScanProcessing() {
  showStatusScreen("SCAN", "Processing...", "");
}

void showScanNoMatch() {
  showStatusScreen("NO MATCH", "Not registered", "x");
}

void showScanLowConfidence() {
  showStatusScreen("TRY AGAIN", "Low confidence", "!");
}

void showScanMatch(int id) {
  showStatusScreen("MATCH", "ID: " + String(id), "✔");
}

void showAttendanceOK() {
  showStatusScreen("ATTEND", "Success", "✔");
}

void showAttendanceRejected() {
  showStatusScreen("ATTEND", "Rejected", "x");
}

void showAttendanceServerError() {
  showStatusScreen("SERVER", "Error", "!");
}

void showSensorError() {
  showStatusScreen("SENSOR", "Not found", "!");
}

// ================= BUZZER =================
void beep(int onMs) {
  digitalWrite(BUZZER_PIN, HIGH);
  delay(onMs);
  digitalWrite(BUZZER_PIN, LOW);
}

void errorBeeps() {
  for (int i = 0; i < 5; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(80);
    digitalWrite(BUZZER_PIN, LOW);
    delay(80);
  }
}

void warningBeeps() {
  for (int i = 0; i < 3; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(700);
    digitalWrite(BUZZER_PIN, LOW);
    delay(300);
  }
}

// ================= SETUP =================
void setup() {
  Serial.begin(115200);

  pinMode(BUTTON_PIN, INPUT_PULLUP);
  pinMode(BUZZER_PIN, OUTPUT);

  Serial.println("Booting device...");

  // ---- OLED INIT ----
  Wire.begin(21, 22); // SDA=21, SCL=22
  if (!display.begin(SSD1306_SWITCHCAPVCC, 0x3C)) {
    Serial.println("SSD1306 allocation failed");
  } else {
    display.clearDisplay();
    showBootScreen();
  }

  // ---- Fingerprint ----
  mySerial.begin(57600, SERIAL_8N1, RXD2, TXD2);
  finger.begin(57600);

  if (!finger.verifyPassword()) {
    Serial.println("❌ Fingerprint sensor not detected");
    showSensorError();
    while (1);
  }
  Serial.println("✅ Fingerprint sensor ready");

  // ---- WIFI (WiFiManager) ----
  connectToWiFi();

  // ---- LOAD ENROLL ID ----
  finger.getTemplateCount();
  enrollID = finger.templateCount + 1;

  Serial.print("Stored fingerprints: ");
  Serial.println(finger.templateCount);
  Serial.print("Next Enroll ID: ");
  Serial.println(enrollID);

  // ---- READY ----
  Serial.println("System Ready. Attendance mode active.");
  beep(3000); // 3 sec ready beep
  showReady();
}

// ================= LOOP =================
void loop() {

  // -------- BUTTON HANDLING --------
  int buttonState = digitalRead(BUTTON_PIN);

  if (buttonState == LOW) {
    // button pressed (active low)
    if (!buttonHolding) {
      buttonHolding = true;
      buttonPressTime = millis();
    }
  } else {
    // ---- BUTTON RELEASE ----
    if (buttonHolding) {
      unsigned long pressDuration = millis() - buttonPressTime;
      Serial.print("[BUTTON] Press duration (ms): ");
      Serial.println(pressDuration);

      if (pressDuration >= FULL_RESET_PRESS_MS) {
        // 20s: full reset (WiFi + fingerprints) and restart
        resetAllData();
        buttonHolding = false;
        return;
      } else if (pressDuration >= WIFI_CONFIG_PRESS_MS) {
        // 10–20s: WiFi config portal
        startWifiConfigPortal();
        buttonHolding = false;
        return;
      } else {
        // short press: enrollment mode
        Serial.println("Enroll mode requested (short press)");
        enrollRequested = true;
        beep(200);
        showEnrollRequested(enrollID);
      }
    }
    buttonHolding = false;
  }

  // -------- ENROLL MODE --------
  if (enrollRequested) {
    enrollRequested = false;
    enrollFinger(enrollID);
    enrollID++;
    return;
  }

  // -------- ATTENDANCE MODE --------
  uint8_t p = finger.getImage();
  if (p != FINGERPRINT_OK) return;

  Serial.println("Finger detected");
  showScanProcessing();

  if (finger.image2Tz() != FINGERPRINT_OK) {
    Serial.println("Image conversion failed");
    showScanLowConfidence();
    errorBeeps();
    return;
  }

  if (finger.fingerFastSearch() != FINGERPRINT_OK) {
    Serial.println("No matching fingerprint");
    showScanNoMatch();
    errorBeeps();
    return;
  }

  if (finger.confidence < CONFIDENCE_THRESHOLD) {
    Serial.println("Low confidence");
    showScanLowConfidence();
    errorBeeps();
    return;
  }

  Serial.print("Match Found | ID: ");
  Serial.print(finger.fingerID);
  Serial.print(" | Confidence: ");
  Serial.println(finger.confidence);
  showScanMatch(finger.fingerID);

  sendToServer(finger.fingerID, finger.confidence, "scan");

  // Now check with server for confirmation and beep only if matched
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = String(CHECK_API) + "?finger_id=" + String(finger.fingerID);
    Serial.print("CHECK_API URL: ");
    Serial.println(url);
    http.begin(url);
    int httpCode = http.GET();
    Serial.print("CHECK_API HTTP code: ");
    Serial.println(httpCode);

    if (httpCode > 0) {
      String payload = http.getString();
      Serial.print("CHECK_API payload: ");
      Serial.println(payload);

      // Look for '"status":true' in response
      if (payload.indexOf("\"status\":true") != -1) {
        Serial.println("CHECK_API: status=true (match)");
        beep(2000); // confirmation beep only if matched with employee
        showAttendanceOK();
      } else {
        Serial.println("CHECK_API: status!=true (NOT MATCH from server)");
        Serial.print("CHECK_API: Local finger ID: ");
        Serial.println(finger.fingerID);
        errorBeeps(); // server did not confirm match
        showAttendanceRejected();
      }
    } else {
      Serial.println("Server check failed");
      errorBeeps();
      showAttendanceServerError();
    }
    http.end();
  } else {
    Serial.println("WiFi not connected for check");
    errorBeeps();
    showWifiError("No connection");
  }
  delay(1500);
}

// ================= ENROLL =================
void enrollFinger(uint8_t id) {
  Serial.print("Starting enrollment for ID ");
  Serial.println(id);

  showEnrollStep("Place finger");

  int p = -1;
  while (p != FINGERPRINT_OK) {
    Serial.println("Place finger");
    p = finger.getImage();
    delay(500);
  }

  finger.image2Tz(1);

  if (finger.fingerFastSearch() == FINGERPRINT_OK) {
    Serial.println("Finger already exists. Enrollment cancelled.");
    errorBeeps();
    showEnrollError("Already exists");
    return;
  }

  Serial.println("Remove finger");
  delay(2000);

  p = -1;
  while (p != FINGERPRINT_OK) {
    Serial.println("Place same finger again");
    p = finger.getImage();
    delay(500);
  }

  finger.image2Tz(2);

  if (finger.createModel() != FINGERPRINT_OK) {
    Serial.println("Model creation failed");
    errorBeeps();
    showEnrollError("Model failed");
    return;
  }

  if (finger.storeModel(id) == FINGERPRINT_OK) {
    Serial.println("Enrollment successful");
    sendToServer(id, 100, "new");
    beep(2000);
    showEnrollSuccess(id);
  } else {
    Serial.println("Failed to store fingerprint");
    errorBeeps();
    showEnrollError("Store failed");
  }
}

// ================= FORMAT =================
void formatAllFingerprints() {
  Serial.println("Deleting all fingerprint templates...");

  if (finger.emptyDatabase() == FINGERPRINT_OK) {
    Serial.println("✅ All fingerprint data erased");

    enrollID = 1;   // reset ID
    beep(4000);     // long confirmation beep
    showStatusScreen("ERASE", "Done", "✔");
  } else {
    Serial.println("❌ Failed to erase database");
    errorBeeps();
    showStatusScreen("ERASE", "Failed", "!");
  }
}

// ================= API =================
void sendToServer(int id, int conf, String status) {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("SEND_API: WiFi not connected, skipping POST");
    return;
  }

  HTTPClient http;
  http.begin(SEND_API);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  String data = "finger_id=" + String(id) +
                "&confidence=" + String(conf) +
                "&status=" + status;
  Serial.print("SEND_API URL: ");
  Serial.println(SEND_API);
  Serial.print("SEND_API POST data: ");
  Serial.println(data);

  int httpCode = http.POST(data);
  Serial.print("SEND_API HTTP code: ");
  Serial.println(httpCode);

  if (httpCode > 0) {
    String resp = http.getString();
    Serial.print("SEND_API response: ");
    Serial.println(resp);
  } else {
    Serial.println("SEND_API: request failed");
  }
  http.end();

  Serial.println("Data sent to server");
}
