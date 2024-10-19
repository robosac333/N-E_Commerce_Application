
<?php
require 'vendor/autoload.php';

use Aws\SecretsManager\SecretsManagerClient;
use Aws\Exception\AwsException;

// Function to retrieve secret from AWS Secrets Manager
function getSecret($secretName) {
    $client = new SecretsManagerClient([
        'version' => 'latest',
        'region' => 'us-west-2' // Replace with your region
    ]);

    try {
        $result = $client->getSecretValue([
            'SecretId' => $secretName,
        ]);

        if (isset($result['SecretString'])) {
            return json_decode($result['SecretString'], true);
        } else {
            return base64_decode($result['SecretBinary']);
        }
    } catch (AwsException $e) {
        echo "Error retrieving secret: " . $e->getMessage();
        return null;
    }
}

// Retrieve the RDS secret
$secretName = 'MyRDSSecret'; // Replace with your secret name
$secret = getSecret($secretName);

if ($secret) {
    $username = $secret['username'];
    $password = $secret['password'];
    $dbHost = 'rds-mydbinstance-ohzhgoflg8ov.ctq0oqggufq5.us-east-2.rds.amazonaws.com'; // Replace with your RDS endpoint
    $dbName = 'ecommerce_1'; // Replace with your database name

    // Create connection
    $con = new mysqli($localhost, $username, $password, $dbName);

    // Check connection
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }
    echo "Connected successfully to the RDS database.";
} else {
    echo "Failed to retrieve database credentials.";
}

?>
