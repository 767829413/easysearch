<?php
/**
 * Created by PhpStorm.
 * User: NUC
 * Date: 2019/5/29
 * Time: 16:37
 */

namespace Easysearsh;

use Elasticsearch\Client;
use Elasticsearch\Common\Exceptions\Missing404Exception;

class EsApi
{
    /** @var Client */
    private $client;

    public function __construct(string $host, string $port, string $scheme, string $user, string $pass, string $retries = '2')
    {
        $init = [
            'host' => $host,
            'port' => $port,
            'scheme' => $scheme,
            'user' => $user,
            'pass' => $pass,
        ];
        $this->client = \Elasticsearch\ClientBuilder::create()
            ->setHosts([$init])
            ->setRetries($retries)
            ->build();
    }

    /**
     * 创建索引
     * @param string $indexName
     */
    public function createEsIndex(string $indexName): void
    {
        $params = [
            'index' => $indexName
        ];
        try {
            $this->client->indices()->get($params);
        } catch (Missing404Exception $e) {
            $this->client->indices()->create($params);
        }
    }

    /**
     * 索引文档
     * @param string $indexName
     * @param int $rowId
     * @param array $rowField
     * $rowField = ['字段名称' => '字段值', 'amount' => 33320, 'name' => '仙鹤没有']
     * @throws \Exception
     */
    public function createEsDoc(string $indexName, int $rowId, array $rowField): void
    {
        $params = [
            'index' => $indexName,
            'type' => '_doc',
            'id' => $rowId,
            'body' => $rowField
        ];
        $this->createEsIndex($indexName);
        $this->client->index($params);
    }

    /**
     * 批量索引文档
     * @param array $bulkIndexDocParams
     * @throws \Exception
     */
    public function createEsDocs(array $bulkIndexDocParams): void
    {
        if (empty($bulkIndexDocParams)) {
            throw new \Exception('传入参数为空数组', 500);
        }
        $this->client->bulk($bulkIndexDocParams);
    }

    /**
     * 更新部分索引文档
     * @param string $indexName
     * @param int $rowId
     * @param array $doc
     * eg $doc = [ '字段名称' => '字段值', 'new_field' => 'abc' ]
     * @throws \Exception
     */
    public function localUpdateEsDoc(string $indexName, int $rowId, array $doc): void
    {
        $params = [
            'index' => $indexName,
            'type' => '_doc',
            'id' => $rowId,
            'body' => [
                'doc' => $doc
            ]
        ];
        $this->client->update($params);
    }

    /**
     * 删除索引文档
     * @param string $indexName
     * @param int $rowId
     * @throws \Exception
     */
    public function deleteEsDoc(string $indexName, int $rowId): void
    {
        $params = [
            'index' => $indexName,
            'type' => '_doc',
            'id' => $rowId
        ];
        $this->client->delete($params);
    }

    /**
     * elasticsearch 最基本的搜索操作
     * @param array $params
     * @return array
     * @throws \Exception
     */
    public function search(array $params): array
    {
        return $this->client->search($params);
    }


    /**
     * 构建批量索引文档数据
     * @param array $bulkIndexDocParams
     * @param string $indexName
     * @param int $rowId
     * @param array $filedValue
     * eg $filedValue = ['fieldName' => 'fieldValue','amount' => 1202,'name' => 'sb']
     */
    public function constructBulkData(array &$bulkIndexDocParams, string $indexName, int $rowId, array $filedValue): void
    {
        $bulkIndexDocParams['body'][] = [
            'index' => [
                '_index' => $indexName,
                '_type' => '_doc',
                '_id' => $rowId
            ],
        ];
        $bulkIndexDocParams['body'][] = $filedValue;
    }

    /**
     * 获得一个索引文档
     * @param string $indexName
     * @param int $rowId
     * @return array
     */
    public function getIndexDoc(string $indexName, int $rowId): array
    {
        $params = [
            'index' => $indexName,
            'type' => '_doc',
            'id' => $rowId
        ];
        try {
            return $this->client->get($params);
        } catch (\Exception $e) {
            return [];
        }
    }
}