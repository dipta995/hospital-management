#include <WiFi.h>
#include <HTTPClient.h>
#include <Adafruit_Fingerprint.h>

// ================= PINS =================
#define RXD2 16
#define TXD2 17
#define BUTTON_PIN 26
#define BUZZER_PIN 25

// ================= WIFI =================
const char* ssid     = "Dark";
const char* password = "password2025";

// ================= API =================
const char* SEND_API  = "https://alsunnah.dreammake-soft.com/fingerprint-send";
const char* CHECK_API = "https://alsunnah.dreammake-soft.com/fingerprint-check";

// ================= FINGERPRINT =================
HardwareSerial mySerial(2);
Adafruit_Fingerprint finger(&mySerial);

// ================= STATE =================
uint8_t enrollID = 1;
bool enrollMode = true;
const uint8_t MAX_FINGER = 127;
const int CONFIDENCE_THRESHOLD = 50;

// ================= SETUP =================
void setup() {
  Serial.begin(115200);

  pinMode(BUTTON_PIN, INPUT_PULLUP);
  pinMode(BUZZER_PIN, OUTPUT);

  mySerial.begin(57600, SERIAL_8N1, RXD2, TXD2);
  finger.begin(57600);

  if (!finger.verifyPassword()) {
    Serial.println("❌ Fingerprint sensor not found");
    while (1);
  }

  Serial.println("✅ Fingerprint sensor ready");

  // ---- WIFI ----
  WiFi.begin(ssid, password);
  Serial.print("Connecting WiFi");
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  Serial.println("\n✅ WiFi Connected");

  // ---- IMPORTANT PART ----
  finger.getTemplateCount();
  enrollID = finger.templateCount + 1;

  Serial.print("Stored Fingers: ");
  Serial.println(finger.templateCount);

  Serial.print("Next Enroll ID: ");
  Serial.println(enrollID);

  Serial.println("Press BUTTON to enroll new finger");
}

// ================= LOOP =================
void loop() {

  // ---- ENROLL BUTTON ----
  if (digitalRead(BUTTON_PIN) == LOW) {
    delay(300);
    if (enrollID <= MAX_FINGER) {
      enrollFinger(enrollID);
      enrollID++;
    }
  }

  // ---- SCAN MODE ----
  if (finger.getImage() != FINGERPRINT_OK) return;
  if (finger.image2Tz() != FINGERPRINT_OK) return;
  if (finger.fingerFastSearch() != FINGERPRINT_OK) {
    errorBeep();
    return;
  }

  if (finger.confidence < CONFIDENCE_THRESHOLD) {
    errorBeep();
    return;
  }

  Serial.print("Finger ID: ");
  Serial.println(finger.fingerID);

  sendToServer(finger.fingerID, finger.confidence, "scan");
  successBeep();
  delay(1500);
}

// ================= ENROLL =================
void enrollFinger(uint8_t id) {
  Serial.print("Enrolling ID: ");
  Serial.println(id);

  int p = -1;
  while (p != FINGERPRINT_OK) {
    Serial.println("Place finger");
    p = finger.getImage();
    delay(500);
  }

  finger.image2Tz(1);

  if (finger.fingerFastSearch() == FINGERPRINT_OK) {
    Serial.println("❌ Finger already exists");
    errorBeep();
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
    errorBeep();
    return;
  }

  if (finger.storeModel(id) == FINGERPRINT_OK) {
    Serial.println("✅ Enroll success");
    sendToServer(id, 100, "new");
    successBeep();
  } else {
    errorBeep();
  }
}

// ================= API SEND =================
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
}

// ================= BUZZER =================
void successBeep() {
  digitalWrite(BUZZER_PIN, HIGH);
  delay(200);
  digitalWrite(BUZZER_PIN, LOW);
}

void errorBeep() {
  for (int i = 0; i < 3; i++) {
    digitalWrite(BUZZER_PIN, HIGH);
    delay(200);
    digitalWrite(BUZZER_PIN, LOW);
    delay(200);
  }
}
