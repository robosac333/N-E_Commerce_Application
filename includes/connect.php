<?php 
// Include the AWS SDK for PHP
require __DIR__ . '/../vendor/autoload.php';
use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

function getSecret($secretName) {
    $client = new SecretsManagerClient([
        'version' => 'latest',
        'region' => 'us-east-2' // e.g., 'us-east-1'
    ]);

    try {
        $result = $client->getSecretValue([
            'SecretId' => $secretName,
        ]);

        if (isset($result['SecretString'])) {
            return json_decode($result['SecretString'], true);
        } else {
            throw new Exception('Secret is not a string');
        }
    } catch (AwsException $e) {
        echo "Error retrieving secret: " . $e->getMessage();
        return null;
    }
}

// Retrieve the RDS secret for database credentials
$secretName = 'MyRDSSecret'; // Replace with your secret name
$secret = getSecret($secretName);

// Retrieve the CA certificate
$caSecretName = 'MyRDSCACert'; // Replace with your CA certificate secret name
$caSecret = getSecret($caSecretName);
$caCertificate = $caSecret['SecretString']; // Assuming the secret is stored as plain string

if ($secret && $caCertificate) {
    // Database connection details
    $username = $secret['username'];
    $password = $secret['password'];
    $dbHost = $secret['endpoint'];
    $dbName = "ecommerce_1";

    // Create connection
    $con = new mysqli($dbHost, $username, $password, $dbName, null, null, [
        // Set SSL options
        'ssl' => [
            'verify_server_cert' => true,
            'CAfile' => 'data://text/plain;base64,' . base64_encode($caCertificate)
        ]
    ]);

    // Check connection
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }
    echo "Connected successfully to the database with SSL.";
} else {
    echo "Failed to retrieve database credentials or CA certificate.";
}
?>
