<?php
/**
 * Created by PhpStorm.
 * User: mingming6
 * Date: 2018/5/4
 * Time: 15:17
 */
//you can ignore it
@include_once('config.php');

if (!class_exists('SCS')) require_once '../class/SCS.php';

date_default_timezone_set('UTC');

// SCS access info
if (!defined('AccessKey')) define('AccessKey', 'change-this');
if (!defined('SecretKey')) define('SecretKey', 'change-this');
if (!defined('Bucket')) define('Bucket', 'change-this');
/*待删除的目录，如果是根目录，可以留空*/
$delete_file_path = 'aaa';

SCS::setAuth(AccessKey, SecretKey);
SCS::setExceptions(true);

echo(sprintf("start delete bucket: %s\n", Bucket));
// list file
$marker = null;
try {
    while ($ret = SCS::getBucket(Bucket, $delete_file_path, $marker, 1000)) {
        $send_to_redis = array();
        foreach ($ret as $s) {
            $uri = $s['name'];
            $marker = $uri;
            echo(sprintf("start delete file: %s\n", $uri));
            $ret = SCS::deleteObject(Bucket, $uri);
        }
    }
} catch (Exception $e) {
    /*程序出现异常*/
    var_dump($e);
}