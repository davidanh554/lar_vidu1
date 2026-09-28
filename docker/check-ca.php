<?php

$caFile = getenv('MYSQL_ATTR_SSL_CA');
if (! $caFile) {
    exit(0);
}

if (! file_exists($caFile) || ! is_readable($caFile)) {
    fwrite(STDERR, "SSL CA certificate file does not exist or is not readable: {$caFile}\n");
    exit(1);
}

$content = file_get_contents($caFile);
if (strpos($content, '-----BEGIN CERTIFICATE-----') === false) {
    fwrite(STDERR, "Invalid CA certificate format in {$caFile}\n");
    exit(1);
}

echo "MySQL CA certificate verified successfully.\n";
exit(0);
