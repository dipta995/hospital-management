#include <WiFi.h>
#include <HTTPClient.h>
#include <Adafruit_Fingerprint.h>

// ================= PINS =================
#define RXD2 16
#define TXD2 17
#define BUTTON_PIN 26
#define BUZZER_PIN 25

// ================= WIFI =================
const char* ssid     = "ZTE_2.4G_2bRXdx";
const char* password = "ESyhRuPc";

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

  // ---- Fingerprint ----
  mySerial.begin(57600, SERIAL_8N1, RXD2, TXD2);
  finger.begin(57600);

  if (!finger.verifyPassword()) {
    Serial.println("❌ Fingerprint sensor not detected");
    while (1);
  }
  Serial.println("✅ Fingerprint sensor ready");

  // ---- WIFI ----
  Serial.print("Connecting to WiFi");
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\n✅ WiFi Connected");

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
}

// ================= LOOP =================
void loop() {

  // -------- BUTTON HANDLING --------
  if (digitalRead(BUTTON_PIN) == LOW) {
    if (!buttonHolding) {
      buttonHolding = true;
      buttonPressTime = millis();
    }

    // ---- LONG PRESS (10 sec) ----
    if (buttonHolding && millis() - buttonPressTime >= 10000) {
      Serial.println("⚠️ LONG PRESS (10s) DETECTED");
      Serial.println("⚠️ FORMATTING ALL FINGERPRINT DATA");

      warningBeeps();
      formatAllFingerprints();

      buttonHolding = false;
      delay(2000);
      return;
    }
  } else {
    // ---- BUTTON RELEASE ----
    if (buttonHolding) {
      unsigned long pressDuration = millis() - buttonPressTime;

      // ---- SHORT PRESS ----
      if (pressDuration < 10000) {
        Serial.println("Enroll mode requested");
        enrollRequested = true;
        beep(200);
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

  if (finger.image2Tz() != FINGERPRINT_OK) {
    Serial.println("Image conversion failed");
    errorBeeps();
    return;
  }

  if (finger.fingerFastSearch() != FINGERPRINT_OK) {
    Serial.println("No matching fingerprint");
    errorBeeps();
    return;
  }

  if (finger.confidence < CONFIDENCE_THRESHOLD) {
    Serial.println("Low confidence");
    errorBeeps();
    return;
  }

  Serial.print("Match Found | ID: ");
  Serial.print(finger.fingerID);
  Serial.print(" | Confidence: ");
  Serial.println(finger.confidence);

  sendToServer(finger.fingerID, finger.confidence, "scan");

  // Now check with server for confirmation and beep only if matched
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String url = String(CHECK_API) + "?finger_id=" + String(finger.fingerID);
    http.begin(url);
    int httpCode = http.GET();
    if (httpCode > 0) {
      String payload = http.getString();
      // Look for '"status":true' in response
      if (payload.indexOf("\"status\":true") != -1) {
        beep(2000); // confirmation beep only if matched with employee
      } else {
        errorBeeps(); // server did not confirm match
      }
    } else {
      Serial.println("Server check failed");
      errorBeeps();
    }
    http.end();
  } else {
    Serial.println("WiFi not connected for check");
    errorBeeps();
  }
  delay(1500);
}

// ================= ENROLL =================
void enrollFinger(uint8_t id) {
  Serial.print("Starting enrollment for ID ");
  Serial.println(id);

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
    return;
  }

  if (finger.storeModel(id) == FINGERPRINT_OK) {
    Serial.println("Enrollment successful");
    sendToServer(id, 100, "new");
    beep(2000);
  } else {
    Serial.println("Failed to store fingerprint");
    errorBeeps();
  }
}

// ================= FORMAT =================
void formatAllFingerprints() {
  Serial.println("Deleting all fingerprint templates...");

  if (finger.emptyDatabase() == FINGERPRINT_OK) {
    Serial.println("✅ All fingerprint data erased");

    enrollID = 1;   // reset ID
    beep(4000);     // long confirmation beep
  } else {
    Serial.println("❌ Failed to erase database");
    errorBeeps();
  }
}

// ================= API =================
void sendToServer(int id, int conf, String status) {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  http.begin(SEND_API);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  String data = "finger_id=" + String(id) +
                "&confidence=" + String(conf) +
                "&status=" + status;

  http.POST(data);
  http.end();

  Serial.println("Data sent to server");
}
