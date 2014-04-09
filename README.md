SinaCloudStorage-SDK-PHP
========================

PHP SDK for 新浪云存储


### Requirements

* PHP >= 5.2.0
* [PHP cURL]


## Usage

OO method (e,g; $scs->getObject(...)):

```php
$scs = new SCS($scsAccessKey, $scsSecretKey);
```

Statically (e,g; SCS::getObject(...)):

```php
SCS::setAuth($scsAccessKey, $scsSecretKey);
```

### Object Operations

#### Uploading objects

Put an object from a file:

```php
SCS::putObject(SCS::inputFile($file, false), $bucketName, $uploadName, SCS::ACL_PUBLIC_READ)
```

Put an object from a string and set its Content-Type:

```php
SCS::putObject($string, $bucketName, $uploadName, SCS::ACL_PUBLIC_READ, array(), array('Content-Type' => 'text/plain'))
```

Put an object from a resource (buffer/file size is required - note: the resource will be fclose()'d automatically):

```php
SCS::putObject(SCS::inputResource(fopen($file, 'rb'), filesize($file)), $bucketName, $uploadName, SCS::ACL_PUBLIC_READ)
```

Put an object as a string:

```php
SCS::putObjectString($string, $bucket, $uri)
```

#### Retrieving objects

Get an object:

```php
SCS::getObject($bucketName, $uploadName)
```

Save an object to file:

```php
SCS::getObject($bucketName, $uploadName, $saveName)
```

Save an object to a resource of any type:

```php
SCS::getObject($bucketName, $uploadName, fopen('savefile.txt', 'wb'))
```

#### Copying and deleting objects

Copy an object:

```php
SCS::copyObject($srcBucket, $srcName, $bucketName, $saveName, $metaHeaders = array(), $requestHeaders = array())
```

Delete an object:

```php
SCS::deleteObject($bucketName, $uploadName)
```

### Bucket Operations

Get a list of buckets:

```php
SCS::listBuckets()  // Simple bucket list
SCS::listBuckets(true)  // Detailed bucket list
```

Create a bucket:

```php
SCS::putBucket($bucketName)
```

Get the contents of a bucket (list objects):

```php
SCS::getBucket($bucketName)
```

Delete an empty bucket:

```php
SCS::deleteBucket($bucketName)
```

## Examples


#### 基本示例:

* 文件: examples/example.php

#### 表单上传

* 文件: examples/example-form.php

#### 实现一个Wrapper

* 文件: examples/example-wrapper.php

```php

mkdir("scs://{$bucketName}");

file_put_contents("scs://{$bucketName}/test.txt", "http://weibo.com/smcz !");

file_get_contents("scs://{$bucketName}/test.txt")

foreach (new DirectoryIterator("scs://{$bucketName}") as $b) {

	echo "\t" . $b . "\n";
}

unlink("scs://{$bucketName}/test.txt");

rmdir("scs://{$bucketName}");
```

