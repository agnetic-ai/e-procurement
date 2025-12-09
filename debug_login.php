<?php
// generate_password.php - Generate password hash untuk testing

// Fungsi untuk generate hash
function generateHash($password)
{
    $hash = password_hash($password, PASSWORD_DEFAULT);
    return $hash;
}

// Fungsi untuk verify password
function verifyPassword($password, $hash)
{
    return password_verify($password, $hash);
}

// Fungsi untuk cek hash info
function getHashInfo($hash)
{
    return password_get_info($hash);
}

echo "<h3>Password Hash Generator & Verifier</h3>";

// Generate hash untuk passwords demo
$passwords = [
    'admin123' => 'Admin password',
    'proc123' => 'Procurement password',
    'password' => 'Default dummy password'
];

echo "<h4>Generated Hashes:</h4>";
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Password</th><th>Description</th><th>Hash</th><th>Verify Test</th><th>Hash Info</th></tr>";

foreach ($passwords as $password => $description) {
    $hash = generateHash($password);
    $verify = verifyPassword($password, $hash) ? '✅ Valid' : '❌ Invalid';
    $info = getHashInfo($hash);

    echo "<tr>";
    echo "<td><code>$password</code></td>";
    echo "<td>$description</td>";
    echo "<td><textarea rows='3' cols='50'>$hash</textarea></td>";
    echo "<td>$verify</td>";
    echo "<td>Algo: {$info['algoName']} (Cost: {$info['options']['cost']})</td>";
    echo "</tr>";
}

echo "</table>";

// Form untuk custom test
echo "<h4>Custom Test:</h4>";
echo "<form method='POST'>";
echo "Password: <input type='text' name='custom_pass' value='admin123'><br>";
echo "Hash to verify: <textarea name='custom_hash' rows='3' cols='50'></textarea><br>";
echo "<button type='submit'>Test</button>";
echo "</form>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customPass = $_POST['custom_pass'] ?? '';
    $customHash = $_POST['custom_hash'] ?? '';

    if ($customPass && $customHash) {
        $result = verifyPassword($customPass, $customHash);
        echo "<p>Verification result: <strong>" . ($result ? '✅ SUCCESS' : '❌ FAILED') . "</strong></p>";

        if ($result) {
            echo "<p style='color:green'>Password matches hash!</p>";
        } else {
            // Generate correct hash
            $correctHash = generateHash($customPass);
            echo "<p style='color:red'>Password does NOT match.</p>";
            echo "<p>Correct hash for '$customPass' would be:</p>";
            echo "<textarea rows='3' cols='50'>$correctHash</textarea>";
        }
    }
}

// SQL untuk update database
echo "<h4>SQL untuk Update Database:</h4>";
echo "<pre>";
foreach ($passwords as $password => $description) {
    $hash = generateHash($password);
    $username = ($password == 'admin123') ? 'admin' : 'procurement';
    echo "-- Update password untuk $username ($description)\n";
    echo "UPDATE users SET password = '$hash' WHERE username = '$username';\n\n";
}
echo "</pre>";
