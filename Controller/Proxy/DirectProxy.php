<?php
$requestHeaders = getallheaders();
$apiKeyRaw = $requestHeaders['X-Auth-Key'];
$apiUrl = $requestHeaders['X-Remote-Api-Url'];
$content = file_get_contents('php://input');

$keysToRemove = [
    'X-Auth-Key',
    'x-auth-key',
    'x-remote-api-url',
    'X-Remote-Api-Url',
    'Content-Type',
    'Content-Length',
];
foreach ($keysToRemove as $key) {
    if(array_key_exists($key, $requestHeaders)) {
        unset($requestHeaders[$key]);
    }
}

header('Content-Type: application/json');
if (empty($apiUrl) || empty($apiKeyRaw)) {
    http_response_code(400);
    print_r(json_encode(['error' => 'Missing required headers', 'headers' => $requestHeaders]));
} else {

    $headersToSend = [];
    foreach ($requestHeaders as $name => $value) {
        $headersToSend[] = $name . ': ' . $value;
    }

    $headersToSend[] = 'X-Auth-Key: ' . $apiKeyRaw;
    $headersToSend[] = 'Content-Type: application/json';

    $curlHandle = curl_init($apiUrl);
	// Fetch the config value for TCP Fast Open from the backend
	$magentoRootPath = realpath(__DIR__ . '/../');
	$configFilePath = $magentoRootPath . '/var/tcp_config_value.txt';
	$useTcpFastOpen = 1;
	if (file_exists($configFilePath)) {
		$configValue = file_get_contents($configFilePath);
		if ($configValue !== false) {
			$parts = explode("=", $configValue);
			$useTcpFastOpen = isset($parts[1]) ? trim($parts[1]) : $useTcpFastOpen;
		}
	}
	$curlOptions = [
		CURLOPT_SSL_VERIFYHOST => 0,
		CURLOPT_RETURNTRANSFER => true,
		CURLOPT_POST => true,
		CURLOPT_POSTFIELDS => $content,
		CURLOPT_HTTPHEADER => $headersToSend
	];
	// Add the CURLOPT_TCP_FASTOPEN option if enabled in the backend
	if ($useTcpFastOpen) {
		$curlOptions[CURLOPT_TCP_FASTOPEN] = true;
	}
    curl_setopt_array($curlHandle, $curlOptions);

    $time = microtime(true);
    $response = curl_exec($curlHandle);
    $time = microtime(true) - $time;

    /** Return status 203 */
    http_response_code(curl_getinfo($curlHandle, CURLINFO_HTTP_CODE));
    curl_close($curlHandle);
    print_r($response);
}
