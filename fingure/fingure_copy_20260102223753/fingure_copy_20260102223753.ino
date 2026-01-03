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
// Main cloud "SD card" endpoint: saves each fingerprint event to a file
const char* SEND_API           = "https://alsunnah.dreammake-soft.com/fingerprint-send";
// Check API used when scanning in normal mode
const char* CHECK_API          = "https://alsunnah.dreammake-soft.com/fingerprint-check";
// Store template info in DB table fingerprint_templates
const char* TEMPLATE_STORE_API = "https://alsunnah.dreammake-soft.com/api/fingerprint-store";
// List all templates from DB on boot
const char* TEMPLATE_LIST_API  = "https://alsunnah.dreammake-soft.com/api/fingerprint-list";

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
    Serial.println("ERROR: Fingerprint sensor not found");
    multiBeep(3000);
    while (1);
  }

  Serial.println("OK: Fingerprint sensor detected");
  finger.getTemplateCount();
  Serial.print("Stored templates: ");
  Serial.println(finger.templateCount);

  WiFi.begin(ssid, password);
  Serial.print("Connecting WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\nOK: WiFi Connected");

  // Try to restore data/state from API (use API like SD card)
  restoreFromApi();

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
        Serial.println("OK: Enrollment completed");
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
    Serial.println("ERROR: Image to template failed");
    multiBeep(1500);
    return;
  }

  p = finger.fingerFastSearch();
  if (p != FINGERPRINT_OK) {
    Serial.println("ERROR: No match found");
    multiBeep(1500);
    return;
  }

  int fingerID = finger.fingerID;
  int confidence = finger.confidence;

  Serial.println("=== Finger Detected ===");
  Serial.print("Finger ID: "); Serial.println(fingerID);
  Serial.print("Confidence: "); Serial.println(confidence);

  if (confidence < CONFIDENCE_THRESHOLD) {
    Serial.println("ERROR: Low confidence");
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
    Serial.println("ERROR: Finger already exists with ID: " + String(existingID));
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
    Serial.println("ERROR: Failed 2nd template");
    multiBeep(2000);
    return;
  }

  // Create model
  p = finger.createModel();
  if (p != FINGERPRINT_OK) {
    Serial.println("ERROR: Model creation failed");
    multiBeep(2000);
    return;
  }

  // Store model
  p = finger.storeModel(id);
  if (p == FINGERPRINT_OK) {
    Serial.println("OK: Finger enrolled successfully with ID: " + String(id));
    longBeep(800);
    finger.getTemplateCount();
    Serial.print("Updated templates: ");
    Serial.println(finger.templateCount);

    // Send enrolled ID to server as NEW
    sendFingerprint(id, 100, "new");

    // Also save template record in cloud DB table fingerprint_templates
    saveTemplateToCloud(id);
  } else {
    Serial.println("ERROR: Store failed");
    multiBeep(2000);
  }
}

// ================= SEND =================
void sendFingerprint(int id, int conf, String status) {
  if (WiFi.status() != WL_CONNECTED) {
    WiFi.reconnect();
    delay(2000);
    if (WiFi.status() != WL_CONNECTED) {
      Serial.println("ERROR: WiFi not connected");
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
  Serial.println("SEND Response: " + response);

  if (code == 200) longBeep(1000);
  else multiBeep(2000);

  http.end();
}

// ================= TEMPLATE SAVE (TEMPLATE_STORE_API) =================
void saveTemplateToCloud(int id) {
  if (WiFi.status() != WL_CONNECTED) {
    WiFi.reconnect();
    delay(2000);
    if (WiFi.status() != WL_CONNECTED) {
      Serial.println("ERROR: WiFi not connected (template save)");
      return;
    }
  }

  HTTPClient http;
  http.begin(TEMPLATE_STORE_API);
  http.addHeader("Content-Type", "application/x-www-form-urlencoded");

  // NOTE: Laravel API expects: finger_id (int), template (string)
  // We don't have raw template bytes here, so we send a marker string.
  String postData = "finger_id=" + String(id) + "&template=stored_in_sensor";
  int code = http.POST(postData);

  Serial.print("TEMPLATE SAVE HTTP Code: ");
  Serial.println(code);
  String response = http.getString();
  Serial.println("TEMPLATE SAVE Response: " + response);

  http.end();
}

// ================= RESTORE (TEMPLATE_LIST_API) =================
void restoreFromApi() {
  if (WiFi.status() != WL_CONNECTED) {
    WiFi.reconnect();
    delay(2000);
    if (WiFi.status() != WL_CONNECTED) {
      Serial.println("ERROR: WiFi not connected (restore)");
      return;
    }
  }

  HTTPClient http;
  http.begin(TEMPLATE_LIST_API);

  int code = http.GET();
  Serial.print("RESTORE HTTP Code: ");
  Serial.println(code);
  String response = http.getString();
  Serial.println("RESTORE Response: " + response);

  if (code == 200) {
    // If server returns a non-empty list (not just "[]"),
    // assume backup exists and signal with a long beep.
    if (response.length() > 2) {
      longBeep(3000); // backup found on server
    } else {
      // No backup data yet – short info beep instead of error
      shortBeep(300);
    }
  } else {
    multiBeep(2000);
  }

  http.end();
}

// ================= CHECK =================
void checkFingerprint(int id) {
  if (WiFi.status() != WL_CONNECTED) {
    WiFi.reconnect();
    delay(2000);
    if (WiFi.status() != WL_CONNECTED) {
      Serial.println("ERROR: WiFi not connected");
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
  Serial.println("CHECK Response: " + response);

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
