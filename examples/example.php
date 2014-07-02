#!/usr/local/bin/php
<?php
/**
* $Id$
*
* SCS class usage
*/

//you can ignore it
@include_once('config.php');

if (!class_exists('SCS')) require_once '../class/SCS.php';

date_default_timezone_set('UTC');

// SCS access info
if (!defined('AccessKey')) define('AccessKey', 'change-this');
if (!defined('SecretKey')) define('SecretKey', 'change-this');

$uploadFile = dirname(__FILE__) . '/../class/SCS.php'; // File to upload, we'll use the SCS class since it exists
$bucketName = uniqid('scs-test'); // Temporary bucket

// If you want to use PECL Fileinfo for MIME types:
//if (!extension_loaded('fileinfo') && @dl('fileinfo.so')) $_ENV['MAGIC'] = '/usr/share/file/magic';


// Check if our upload file exists
if (!file_exists($uploadFile) || !is_file($uploadFile))
	exit("\nERROR: No such file: $uploadFile\n\n");

// Check for CURL
if (!extension_loaded('curl') && !@dl(PHP_SHLIB_SUFFIX == 'so' ? 'curl.so' : 'php_curl.dll'))
	exit("\nERROR: CURL extension not loaded\n\n");

// Pointless without your keys!
if (AccessKey == 'change-this' || SecretKey == 'change-this')
	exit("\nERROR: access information required\n\nPlease edit the following lines in this file:\n\n".
	"define('AccessKey', 'change-me');\ndefine('SecretKey', 'change-me');\n\n");

// Instantiate the class
$scs = new SCS(AccessKey, SecretKey);

echo "SCS::getAuthenticatedURL(): " . SCS::getAuthenticatedURL('sdk', 'snapshot/snapshot.png', 86400000) . "\n";

// List your buckets:
echo "SCS::listBuckets(): ".print_r($scs->listBuckets(), 1) . "\n";


// Create a bucket with public read access
if ($scs->putBucket($bucketName, SCS::ACL_PUBLIC_READ)) {
	echo "Created bucket {$bucketName}" . PHP_EOL;

	// Put our file (also with public read access)
	if ($scs->putObjectFile($uploadFile, $bucketName, baseName($uploadFile), SCS::ACL_PUBLIC_READ)) {
		echo "SCS::putObjectFile(): File copied to {$bucketName}/".baseName($uploadFile).PHP_EOL;


		// Get the contents of our bucket
		$contents = $scs->getBucket($bucketName);
		echo "SCS::getBucket(): Files in bucket {$bucketName}: ".print_r($contents, 1);


		// Get object info
		$info = $scs->getObjectInfo($bucketName, baseName($uploadFile));
		echo "SCS::getObjectInfo(): Info for {$bucketName}/".baseName($uploadFile).': '.print_r($info, 1);


		// You can also fetch the object into memory
		// var_dump("SCS::getObject() to memory", $scs->getObject($bucketName, baseName($uploadFile)));

		// Or save it into a file (write stream)
		// var_dump("SCS::getObject() to savefile.txt", $scs->getObject($bucketName, baseName($uploadFile), 'savefile.txt'));

		// Or write it to a resource (write stream)
		// var_dump("SCS::getObject() to resource", $scs->getObject($bucketName, baseName($uploadFile), fopen('savefile.txt', 'wb')));



		// Get the access control policy for a bucket:
		// $acp = $scs->getAccessControlPolicy($bucketName);
		// echo "SCS::getAccessControlPolicy(): {$bucketName}: ".print_r($acp, 1);

		// Update an access control policy ($acp should be the same as the data returned by SCS::getAccessControlPolicy())
		// $scs->setAccessControlPolicy($bucketName, '', $acp);
		// $acp = $scs->getAccessControlPolicy($bucketName);
		// echo "SCS::getAccessControlPolicy(): {$bucketName}: ".print_r($acp, 1);


		// Enable logging for a bucket:
		// $scs->setBucketLogging($bucketName, 'logbucket', 'prefix');

		// if (($logging = $scs->getBucketLogging($bucketName)) !== false) {
		// 	echo "SCS::getBucketLogging(): Logging for {$bucketName}: ".print_r($contents, 1);
		// } else {
		// 	echo "SCS::getBucketLogging(): Logging for {$bucketName} not enabled\n";
		// }

		// Disable bucket logging:
		// var_dump($scs->disableBucketLogging($bucketName));


		// Delete our file
		if ($scs->deleteObject($bucketName, baseName($uploadFile))) {
			echo "SCS::deleteObject(): Deleted file\n";

			// Delete the bucket we created (a bucket has to be empty to be deleted)
			if ($scs->deleteBucket($bucketName)) {
				echo "SCS::deleteBucket(): Deleted bucket {$bucketName}\n";
			} else {
				echo "SCS::deleteBucket(): Failed to delete bucket (it probably isn't empty)\n";
			}

		} else {
			echo "SCS::deleteObject(): Failed to delete file\n";
		}
	} else {
		echo "SCS::putObjectFile(): Failed to copy file\n";
	}
} else {
	echo "SCS::putBucket(): Unable to create bucket (it may already exist and/or be owned by someone else)\n";
}
