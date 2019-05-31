<?php
/**
 * Created by PhpStorm.
 * User: NUC
 * Date: 2019/5/29
 * Time: 16:32
 */

namespace Easysearsh;

class EasySearchProvider implements EasySearchService
{
    private $params = [];

    /** @var EsApi client */
    private $client;

    public function __construct(string $host, string $port, string $scheme, string $user, string $pass, string $retries = '2')
    {
        $this->client = new EsApi($host, $port, $scheme, $user, $pass, $retries);

    }


    /**
     * 创建索引
     * @param string $indexName
     * @throws \Exception
     */
    public function createEsIndex(string $indexName): void
    {
        $this->client->createEsIndex($indexName);
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
        $this->client->createEsDoc($indexName, $rowId, $rowField);
    }

    /**
     * 批量索引文档
     * @param array $bulkIndexDocParams
     * @throws \Exception
     */
    public function createEsDocs(array $bulkIndexDocParams): void
    {
        $this->client->createEsDocs($bulkIndexDocParams);
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
        $this->client->localUpdateEsDoc($indexName, $rowId, $doc);
    }

    /**
     * 删除索引文档
     * @param string $indexName
     * @param int $rowId
     * @throws \Exception
     */
    public function deleteEsDoc(string $indexName, int $rowId): void
    {
        $this->client->deleteEsDoc($indexName, $rowId);
    }

    /**
     * 获得一个索引文档
     * @param string $indexName
     * @param int $rowId
     * @return array
     */
    public function getIndexDoc(string $indexName, int $rowId): array
    {
        return $this->client->getIndexDoc($indexName, $rowId);
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
        $this->client->constructBulkData($bulkIndexDocParams, $indexName, $rowId, $filedValue);
    }

    /**
     * elasticsearch 最基本的搜索操作
     * @return array
     */
    public function search(): array
    {
        try {
            return $this->client->search($this->params);
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * 设置查询索引名称
     * @param string $indexName
     * @param mixed $source (是否开启元数据) ["字段名1","字段名2",...] 还支持["includes" => ["字段名1",..],"excludes" => ['字段名2',...]]
     * @return EasySearchService
     */
    public function setIndexName(string $indexName, $source = false): EasySearchService
    {
        $this->params['index'] = $indexName;
        $this->params['type'] = '_doc';
        $this->params['_source'] = $source;
        return $this;
    }

    /**
     * 聚合相加
     * @param string $field
     * @param string $sumField
     * @return EasySearchService
     */
    public function setSum(string $field, string $sumField = 'sum_sb'): EasySearchService
    {
        $this->params['body']['aggs'][$sumField] = ['sum' => ['field' => $field]];
        return $this;
    }

    /**
     * 设置查询索引排序body
     * @param string $field
     * @param string $order
     * @return EasySearchService
     */
    public function setBodySort(string $field, string $order): EasySearchService
    {
        $order = strtolower($order);
        if (empty(array_get($this->params, 'body.sort._score'))) {
            $this->params['body']['sort']['_score'] = 'desc';
        }
        $this->params['body']['sort'][$field] = $order;
        return $this;
    }

    /**
     * 设置查询索引匹配时间区间body
     * @param string $field
     * @param string $startDate
     * @param string $endDate
     * @return EasySearchService
     */
    public function setBodyQueryTimeBetween(string $field, $startDate, $endDate): EasySearchService
    {
        if (empty($startDate)) {
            $startDate = '1999-01-01';
        }
        if (empty($endDate)) {
            $endDate = '9999-12-30';
        }
        $startDate = $this->dateFormat($startDate);
        $endDate = $this->dateFormat($endDate);
        $this->params['body']['query']['bool']['must'][] = [
            'range' => [$field => ['gte' => $startDate, 'lte' => $endDate, 'format' => 'yyyy-MM-dd']]];
        return $this;
    }

    /**
     * 设置查询索引匹配区间body
     * @param string $field
     * @param int $min
     * @param int $max
     * @return EasySearchService
     */
    public function setBodyQueryBetween(string $field, $min = null, $max = null): EasySearchService
    {
        switch (true) {
            case !empty($min) && empty($max):
                $this->params['body']['query']['bool']['must'][] = ['range' => [$field => ['gte' => $min]]];
                break;
            case empty($min) && !empty($max):
                $this->params['body']['query']['bool']['must'][] = ['range' => [$field => ['lte' => $max]]];
                break;
            case !empty($min) && !empty($max):
                $this->params['body']['query']['bool']['must'][] = ['range' => [$field => ['gte' => $min, 'lte' => $max]]];
                break;
            default:
                break;
        }
        return $this;
    }

    /**
     * 设置match查询索引匹配body
     * @param string $field
     * @param string $findStr
     * @return EasySearchService
     */
    public function setBodyQuery(string $field, string $findStr): EasySearchService
    {
        $this->params['body']['query']['constant_score']['filter']['term'][$field] = $findStr;
        return $this;
    }

    /**
     * 设置bool查询索引匹配body(must模式[in]  must_not模式[not in] should模式[或] )
     * @param string $field
     * @param string $findStr
     * @param string $type
     * @return EasySearchService
     */
    public function setBodyBoolQuery(string $field, string $findStr, string $type = 'must'): EasySearchService
    {
        switch ($type) {
            case 'must':
                $this->params['body']['query']['bool']['must'][] = ['match' => [$field => $findStr]];
                break;
            case 'must_not':
                $this->params['body']['query']['bool']['must_not'][] = ['match' => [$field => $findStr]];
                break;
            case 'should':
                $this->params['body']['query']['bool']['should'][] = ['match' => [$field => $findStr]];
                break;
            default:
                return $this;
        }
        return $this;
    }

    /**
     * 精确in查询
     * @param string $field
     * @param array $values (一维数组)
     * @return EasySearchService
     */
    public function setBodyBoolInQuery(string $field, array $values): EasySearchService
    {
        $this->params['body']['query']['bool']['must'][] = ['terms' => [$field => $values]];
        return $this;
    }

    /**
     * 查询时某个字段为空
     * @param string $field
     * @return EasySearchService
     */
    public function setFieldNull(string $field): EasySearchService
    {
        $this->params['body']['query']['bool']['must_not'][] = ['exists' => ['field' => $field]];
        return $this;
    }

    /**
     * 查询时某个字段不为空
     * @param string $field
     * @return EasySearchService
     */
    public function setFieldNotNull(string $field): EasySearchService
    {
        $this->params['body']['query']['bool']['must'][] = ['exists' => ['field' => $field]];
        return $this;
    }

    /**
     * 设置分页
     * @param int $size
     * @param int $from
     * @return EasySearchService
     */
    public function setPage(int $size, int $from): EasySearchService
    {
        $this->params['body']['size'] = $size;
        $this->params['body']['from'] = $from;
        return $this;
    }

    /**
     * 清空查询参数配置
     */
    public function clearSerachConfig(): void
    {
        $this->params = [];
    }

    private function dateFormat(string $date): string
    {
        if (strtotime($date) === false) {
            return '';
        }
        return date_create($date)->format('Y-m-d');
    }
}