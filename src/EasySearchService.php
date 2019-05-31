<?php
/**
 * Created by PhpStorm.
 * User: NUC
 * Date: 2019/5/29
 * Time: 16:18
 */
namespace Easysearsh;

interface EasySearchService
{

    /**
     * 创建索引
     * @param string $indexName
     * @throws \Exception
     */
    public function createEsIndex(string $indexName): void;

    /**
     * 索引文档
     * @param string $indexName
     * @param int $rowId
     * @param array $rowField
     * $rowField = ['字段名称' => '字段值', 'amount' => 33320, 'name' => '仙鹤没有']
     * @throws \Exception
     */
    public function createEsDoc(string $indexName, int $rowId, array $rowField): void;

    /**
     * 批量索引文档
     * @param array $bulkIndexDocParams
     * @throws \Exception
     */
    public function createEsDocs(array $bulkIndexDocParams): void;

    /**
     * 更新部分索引文档
     * @param string $indexName
     * @param int $rowId
     * @param array $doc
     * eg $doc = [ '字段名称' => '字段值', 'new_field' => 'abc' ]
     * @throws \Exception
     */
    public function localUpdateEsDoc(string $indexName, int $rowId, array $doc): void;

    /**
     * 删除索引文档
     * @param string $indexName
     * @param int $rowId
     * @throws \Exception
     */
    public function deleteEsDoc(string $indexName, int $rowId): void;

    /**
     * 获得一个索引文档
     * @param string $indexName
     * @param int $rowId
     * @return array
     */
    public function getIndexDoc(string $indexName, int $rowId): array;

    /**
     * elasticsearch 最基本的搜索操作
     * @return array
     */
    public function search(): array;

    /**
     * 设置查询索引名称
     * @param string $indexName
     * @param bool $source
     * @return EasySearchService
     */
    public function setIndexName(string $indexName, $source = false): EasySearchService;

    /**
     * 聚合相加
     * @param string $field
     * @param string $sumField
     * @return EasySearchService
     */
    public function setSum(string $field, string $sumField = 'sum_sb'): EasySearchService;

    /**
     * 设置查询索引排序body
     * @param string $field
     * @param string $order
     * @return EasySearchService
     */
    public function setBodySort(string $field, string $order): EasySearchService;

    /**
     * 设置查询索引匹配body
     * @param string $field
     * @param string $findStr
     * @return EasySearchService
     */
    public function setBodyQuery(string $field, string $findStr): EasySearchService;

    /**
     * 设置bool查询索引匹配body(must模式[in]  must_not模式[not in] should模式[或] )
     * @param string $field
     * @param string $findStr
     * @param string $type
     * @return EasySearchService
     */
    public function setBodyBoolQuery(string $field, string $findStr, string $type = 'must'): EasySearchService;

    /**
     * 精确in查询
     * @param string $field
     * @param array $values (一维数组)
     * @return EasySearchService
     */
    public function setBodyBoolInQuery(string $field, array $values): EasySearchService;

    /**
     * 设置分页
     * @param int $size
     * @param int $from
     * @return EasySearchService
     */
    public function setPage(int $size, int $from): EasySearchService;


    /**
     * 查询时某个字段为空
     * @param string $field
     * @return EasySearchService
     */
    public function setFieldNull(string $field): EasySearchService;

    /**
     * 查询时某个字段不为空
     * @param string $field
     * @return EasySearchService
     */
    public function setFieldNotNull(string $field): EasySearchService;

    /**
     * 设置查询索引匹配区间body
     * @param string $field
     * @param string $startDate
     * @param string $endDate
     * @return EasySearchService
     */
    public function setBodyQueryTimeBetween(string $field, $startDate, $endDate): EasySearchService;

    /**
     * 设置查询索引匹配区间body
     * @param string $field
     * @param int $min
     * @param int $max
     * @return EasySearchService
     */
    public function setBodyQueryBetween(string $field, $min = null, $max = null): EasySearchService;

    /**
     * 构建批量索引文档数据
     * @param array $bulkIndexDocParams
     * @param string $indexName
     * @param int $rowId
     * @param array $filedValue
     * eg $filedValue = ['fieldName' => 'fieldValue','amount' => 1202,'name' => 'sb']
     */
    public function constructBulkData(array &$bulkIndexDocParams, string $indexName, int $rowId, array $filedValue): void;


    /**
     * 清空查询参数配置
     */
    public function clearSerachConfig(): void;
}