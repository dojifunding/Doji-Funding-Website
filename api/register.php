<?php
/**
 * Doji Funding - Register API
 * POST /api/register.php
 * Defensive: auto-detects which columns exist in the users table.
 */

require_once __DIR__ . "/../config/app.php";
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../includes/auth.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    jsonResponse(["error" => "Method not allowed"], 405);
}

if (!verifyCsrf($_POST["csrf"] ?? "")) {
    jsonResponse(["error" => "Invalid session. Please refresh and try again."], 403);
}

$firstName = trim($_POST["first_name"] ?? "");
$lastName  = trim($_POST["last_name"] ?? "");
$email     = trim(strtolower($_POST["email"] ?? ""));
$password  = $_POST["password"] ?? "";
$address   = trim($_POST["address"] ?? "");
$city      = trim($_POST["city"] ?? "");
$zipcode   = trim($_POST["zipcode"] ?? "");
$country   = trim($_POST["country"] ?? "");
$region    = trim($_POST["region"] ?? "");
$phoneCode = trim($_POST["phone_code"] ?? "+1");
$phone     = trim($_POST["phone"] ?? "");
$referral  = trim($_POST["referral"] ?? "");
$marketing = isset($_POST["marketing"]) ? 1 : 0;
$fullPhone = $phoneCode . " " . $phone;

$errors = [];
if (strlen($firstName) < 1 || strlen($firstName) > 50) $errors[] = "First name is required.";
if (strlen($lastName) < 1 || strlen($lastName) > 50) $errors[] = "Last name is required.";
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Please enter a valid email address.";
if (strlen($password) < 8) $errors[] = "Password must be at least 8 characters.";
if (strlen($address) < 2) $errors[] = "Address is required.";
if (strlen($city) < 1) $errors[] = "City is required.";
if (strlen($zipcode) < 1) $errors[] = "Zipcode is required.";
if (strlen($country) < 1) $errors[] = "Country is required.";
if (strlen($phone) < 4) $errors[] = "Phone number is required.";
if (!isset($_POST["terms"])) $errors[] = "You must agree to the Privacy Policy and Terms.";
if (!isset($_POST["identity_confirm"])) $errors[] = "You must confirm that your information is correct.";

if (!empty($errors)) {
    jsonResponse(["error" => implode(" ", $errors)], 400);
}

$db = getDB();
if (!$db) {
    jsonResponse(["error" => "Service temporarily unavailable. Please try again later."], 500);
}

try {
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        jsonResponse(["error" => "This email is already registered. Please log in instead."], 409);
    }

    $hash = password_hash($password, PASSWORD_BCRYPT, ["cost" => 12]);
    $userReferralCode = strtoupper(substr($firstName, 0, 2) . substr($lastName, 0, 2)) . rand(1000, 9999);

    $referredBy = null;
    if (!empty($referral)) {
        $stmt = $db->prepare("SELECT id FROM users WHERE referral_code = ?");
        $stmt->execute([$referral]);
        $referrer = $stmt->fetch();
        if ($referrer) $referredBy = $referrer["id"];
    }

    // Detect available columns in users table
    $stmt = $db->query("SHOW COLUMNS FROM users");
    $existingCols = array_column($stmt->fetchAll(), "Field");

    // Core fields (always present)
    $fields = ["email", "password_hash", "first_name", "last_name"];
    $values = [$email, $hash, $firstName, $lastName];

    // Optional fields - only inserted if column exists
    $optional = [
        "phone"              => $fullPhone,
        "address"            => $address,
        "city"               => $city,
        "zipcode"            => $zipcode,
        "country"            => $country,
        "region"             => $region ?: null,
        "marketing_consent"  => $marketing,
        "referral_code"      => $userReferralCode,
        "referred_by"        => $referredBy,
    ];

    foreach ($optional as $col => $val) {
        if (in_array($col, $existingCols)) {
            $fields[] = $col;
            $values[] = $val;
        }
    }

    $placeholders = implode(", ", array_fill(0, count($fields), "?"));
    $fieldList = implode(", ", $fields);

    $stmt = $db->prepare("INSERT INTO users (" . $fieldList . ") VALUES (" . $placeholders . ")");
    $stmt->execute($values);

    $userId = $db->lastInsertId();

    loginUser([
        "id"         => $userId,
        "email"      => $email,
        "first_name" => $firstName,
        "last_name"  => $lastName,
        "created_at" => date("Y-m-d H:i:s"),
    ]);

    jsonResponse(["success" => true, "message" => "Account created successfully."]);

} catch (PDOException $e) {
    error_log("Registration error: " . $e->getMessage());
    jsonResponse(["error" => "DB Error: " . $e->getMessage()], 500);
}
