#include <WiFi.h>
#include <HTTPClient.h>
#include <Adafruit_Fingerprint.h>

// ===== PINS =====
#define RXD2 16
#define TXD2 17
#define BUTTON_PIN 26
#define BUZZER_PIN 25

// ===== WiFi & API =====
const char* ssid = "Dark";
const char* password = "password2025";
const char* SEND_API  = "https://alsunnah.dreammake-soft.com/fingerprint-send";
const char* CHECK_API = "https://alsunnah.dreammake-soft.com/fingerprint-check";

// ===== Fingerprint =====
HardwareSerial mySerial(2);
Adafruit_Fingerprint finger(&mySerial);

// ===== STATE =====
bool sendMode = false;
bool enrollMode = true;
uint8_t enrollID = 1;
const uint8_t maxFingers = 4;
const int CONFIDENCE_THRESHOLD = 50;

// ===== SETUP =====
void setup() {
  Serial.begin(115200);

  pinMode(BUTTON_PIN, INPUT_PULLUP);
  pinMode(BUZZER_PIN, OUTPUT);

  mySerial.begin(57600, SERIAL_8N1, RXD2, TXD2);
  finger.begin(57600);

  if (!finger.verifyPassword()) {
    Serial.println("❌ Fingerprint sensor not found");
    multiBeep(3000);
    while (1);
  }

  Serial.println("✅ Fingerprint sensor detected");
  finger.getTemplateCount();
  Serial.print("Stored templates: ");
  Serial.println(finger.templateCount);

  WiFi.begin(ssid, password);
  Serial.print("Connecting WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\n✅ WiFi Connected");

  Serial.println("Press button to enroll fingerprints");
}

// ================= LOOP =================
void loop() {
  if (digitalRead(BUTTON_PIN) == LOW) {
    delay(300);

    if (enrollMode && enrollID <= maxFingers) {
      enrollFinger(enrollID);
      enrollID++;
      if (enrollID > maxFingers) {
        enrollMode = false;
        Serial.println("✅ Enrollment completed");
        longBeep(1000);
      }
    } else {
      sendMode = true;
      shortBeep(200);
    }
  }

  // ===== Scan finger =====
  uint8_t p = finger.getImage();
  if (p != FINGERPRINT_OK) return;

  p = finger.image2Tz();
  if (p != FINGERPRINT_OK) {
    Serial.println("❌ Image to template failed");
    multiBeep(1500);
    return;
  }

  p = finger.fingerFastSearch();
  if (p != FINGERPRINT_OK) {
    Serial.println("❌ No match found");
    multiBeep(1500);
    return;
  }

  int fingerID = finger.fingerID;
  int confidence = finger.confidence;

  Serial.println("=== Finger Detected ===");
  Serial.print("Finger ID: "); Serial.println(fingerID);
  Serial.print("Confidence: "); Serial.println(confidence);

  if (confidence < CONFIDENCE_THRESHOLD) {
    Serial.println("❌ Low confidence");
    multiBeep(1500);
    return;
  }

  if (sendMode) {
    sendFingerprint(fingerID, confidence, "scan");
    sendMode = false;
  } else {
    checkFingerprint(fingerID);
  }

  delay(1500);
}

// ================= ENROLL =================
void enrollFinger(uint8_t id) {
  int p = -1;

  Serial.print("Enroll ID "); Serial.println(id);

  // First scan
  while (p != FINGERPRINT_OK) {
    Serial.println("Place finger (1st scan)");
    p = finger.getImage();
    delay(500);
  }

  finger.image2Tz(1);

  // Check duplicate before proceeding
  if (finger.fingerFastSearch() == FINGERPRINT_OK) {
    int existingID = finger.fingerID;
    Serial.println("❌ Finger already exists with ID: " + String(existingID));
    multiBeep(2000);

    // Send as OLD to API
    sendFingerprint(existingID, finger.confidence, "old");
    return;
  }

  Serial.println("Remove finger");
  delay(2000);

  // Second scan
  p = -1;
  while (p != FINGERPRINT_OK) {
    Serial.println("Place same finger (2nd scan)");
    p = finger.getImage();
    delay(500);
  }

  p = finger.image2Tz(2);
  if (p != FINGERPRINT_OK) {
    Serial.println("❌ Failed 2nd template");
    multiBeep(2000);
    return;
  }

  // Create model
  p = finger.createModel();
  if (p != FINGERPRINT_OK) {
    Serial.println("❌ Model creation failed");
    multiBeep(2000);
    return;
  }

  // Store model
  p = finger.storeModel(id);
  if (p == FINGERPRINT_OK) {
    Serial.println("✅ Finger enrolled successfully with ID: " + String(id));
    longBeep(800);
    finger.getTemplateCount();
    Serial.print("Updated templates: ");
    Serial.println(finger.templateCount);

    // Send enrolled ID to server as NEW
    sendFingerprint(id, 100, "new");
  } else {
    Serial.println("❌ Store failed");
    multiBeep(2000);
  }
}

// ================= SEND =================
void sendFingerprint(int id, int conf, String status) {
  if (WiFi.status() != WL_CONNECTED) {
    WiFi.reconnect();
    delay(2000);
    if (WiFi.status() != WL_CONNECTED) {
      Serial.println("❌ WiFi not connected");
      return;
    }
  }

  HTTPClient http;
  http.begin(SEND_API);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  String postData = "finger_id=" + String(id) + "&confidence=" + String(conf) + "&status=" + status;
  int code = http.POST(postData);

  Serial.print("SEND HTTP Code: ");
  Serial.println(code);
  String response = http.getString();
  Serial.println("Response: " + response);

  if (code == 200) longBeep(1000);
  else multiBeep(2000);

  http.end();
}

// ================= CHECK =================
void checkFingerprint(int id) {
  if (WiFi.status() != WL_CONNECTED) {
    WiFi.reconnect();
    delay(2000);
    if (WiFi.status() != WL_CONNECTED) {
      Serial.println("❌ WiFi not connected");
      return;
    }
  }

  HTTPClient http;
  String url = String(CHECK_API) + "?finger_id=" + String(id);
  http.begin(url);

  int code = http.GET();
  Serial.print("CHECK HTTP Code: ");
  Serial.println(code);
  String response = http.getString();
  Serial.println("Response: " + response);

  if (code == 200) shortBeep(1500);
  else multiBeep(2000);

  http.end();
}

// ================= BUZZER =================
void shortBeep(int ms) {
  digitalWrite(BUZZER_PIN, HIGH);
  delay(ms);
  digitalWrite(BUZZER_PIN, LOW);
}

void longBeep(int ms) {
  digitalWrite(BUZZER_PIN, HIGH);
  delay(ms);
  digitalWrite(BUZZER_PIN, LOW);
}

void multiBeep(int ms) {
  int t = 0;
  while (t < ms) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(300);
    digitalWrite(BUZZER_PIN, LOW);
    delay(300);
    t += 600;
  }
}