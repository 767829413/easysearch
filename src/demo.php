<?php
/**
 * Created by PhpStorm.
 * User: NUC
 * Date: 2019/5/29
 * Time: 15:51
 */


require __DIR__ . '/../vendor/autoload.php';


/**
 * ES_HOST = your host
 * ES_PORT = your port
 * ES_SCHEME = http
 * ES_USER = your account
 * ES_PASS = your password
 */
$service = new Easysearsh\EasySearchProvider('106.15.179.167', '9200', 'http', 'ewal_dev', '6%sF<2s@KK');

//建立索引
//$service->createEsIndex('fy.index.test');
//
//索引文档
//$service->createEsDoc('fy.index.test', '1', ['testField' => 'abc', 'val' => 10]);
//$service->createEsDoc('fy.index.test', '2', ['testField' => 'def', 'val' => 11]);
//$service->createEsDoc('fy.index.test', '3', ['testField' => 'ghi', 'val' => 12]);
//$service->createEsDoc('fy.index.test', '4', ['testField' => 'jkl', 'val' => 13]);

$data = $service->setIndexName('fy.index.test', true)
    ->setBodyQueryBetween('val', '10', '13')
    ->setBodyQueryBetween('val', '10', '13')
    ->setBodyBoolQuery('testField', 'def')
    ->search();
var_dump($data);
die;

