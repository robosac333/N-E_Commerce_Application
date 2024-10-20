<?php 
// $con=mysqli_connect('localhost','root','','ecommerce_1');
//$con = new mysqli('rds-mydbinstance-ohzhgoflg8ov.ctq0oqggufq5.us-east-2.rds.amazonaws.com','admin',"56Z\>'sFTBn<t-o9",'ecommerce_1');
//if(!$con){
  //  die(mysqli_error($con));
//}

//require '../vendor/autoload.php';
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

// Retrieve the secret
$secretName = 'MyRDSSecret'; // Replace with your secret name
$secret = getSecret($secretName);

if ($secret) {
    // Database connection details
    $username = $secret['username'];
    $password = $secret['password'];
    $dbHost = "rds-mydbinstance-ohzhgoflg8ov.ctq0oqggufq5.us-east-2.rds.amazonaws.com";
    $dbName = "ecommerce_1";

    // Create connection
    $con = new mysqli($dbHost, $username, $password, $dbName);

    // Check connection
    if ($con->connect_error) {
        die("Connection failed: " . $con->connect_error);
    }
    echo "Connected successfully to the database.";
} else {
    echo "Failed to retrieve database credentials.";
}



?>
