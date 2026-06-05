<?php
declare(strict_types=1);

namespace CuCustomField\Service;

use Cake\ORM\Query\SelectQuery;

interface CuCustomFieldSearchServiceInterface
{
    /**
     * 公開中のカスタムフィールド名を取得
     *
     * @return array<string>
     */
    public function getAvailableFieldNames(): array;

    /**
     * リクエストクエリからカスタムフィールド検索条件を抽出
     *
     * @param array $queryParams
     * @param array<string> $availableFieldNames
     * @return array<int, array{field:string,value:string,like:bool}>
     */
    public function parseSearchFilters(array $queryParams, array $availableFieldNames): array;

    /**
     * ブログ記事検索クエリにカスタムフィールド条件を適用
     *
     * @param SelectQuery $query
     * @param array<int, array{field:string,value:string,like:bool}> $filters
     * @return SelectQuery
     */
    public function applyToBlogPostsQuery(SelectQuery $query, array $filters): SelectQuery;
}
