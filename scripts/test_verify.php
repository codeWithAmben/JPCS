<?php
require_once __DIR__ . '/../config.php';

$xml = loadXML(DB_VERIFICATIONS);
if (!$xml) { echo "No verifications file\n"; exit(1); }

$last = null;
foreach ($xml->verification as $v) { $last = $v; }
if (!$last) { echo "No verification records\n"; exit(1); }
$token = (string)$last->token;
$email = (string)$last->email;
$code = (string)$last->code;

echo "Using token for $email\n";

$result = verifyToken($token);
var_export($result);
echo PHP_EOL;