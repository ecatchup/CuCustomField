<?php
declare(strict_types=1);

namespace CuCustomField\Service;

use Cake\ORM\Query\SelectQuery;
use Cake\ORM\TableRegistry;

class CuCustomFieldSearchService implements CuCustomFieldSearchServiceInterface
{
    public function getAvailableFieldNames(): array
    {
        $definitions = TableRegistry::getTableLocator()->get('CuCustomField.CuCustomFieldDefinitions');
        $rows = $definitions->find()
            ->select(['field_name'])
            ->where(['status' => true])
            ->all();

        $names = [];
        foreach ($rows as $row) {
            if (!empty($row->field_name)) {
                $names[] = $row->field_name;
            }
        }
        return array_values(array_unique($names));
    }

    public function parseSearchFilters(array $queryParams, array $availableFieldNames): array
    {
        if (!$queryParams || !$availableFieldNames) {
            return [];
        }

        $filters = [];
        $allow = array_flip($availableFieldNames);

        foreach ($queryParams as $key => $value) {
            if ($key === 'preview' || $key === 'page') {
                continue;
            }
            if (is_array($value)) {
                continue;
            }
            $this->appendFilter($filters, (string) $key, $value, $allow);
        }

        if (!empty($queryParams['fieldValue']) && is_array($queryParams['fieldValue'])) {
            foreach ($queryParams['fieldValue'] as $fieldKey => $value) {
                if (is_array($value)) {
                    continue;
                }
                $this->appendFilter($filters, (string) $fieldKey, $value, $allow);
            }
        }

        return $filters;
    }

    public function applyToBlogPostsQuery(SelectQuery $query, array $filters): SelectQuery
    {
        if (!$filters) {
            return $query;
        }

        $alias = $query->getRepository()->getAlias();

        foreach ($filters as $index => $filter) {
            $valueAlias = 'CuCustomFieldValueFilter' . $index;
            $query->innerJoin([
                $valueAlias => 'cu_custom_field_values'
            ], [
                $alias . '.id = ' . $valueAlias . '.relate_id',
                $valueAlias . '.key' => 'CuCustomFieldValue.' . $filter['field'],
            ]);

            if ($filter['like']) {
                $query->where([
                    $valueAlias . '.value LIKE' => '%' . $filter['value'] . '%'
                ]);
            } else {
                $query->where([
                    $valueAlias . '.value' => $filter['value']
                ]);
            }
        }

        return $query->distinct([$alias . '.id']);
    }

    private function appendFilter(array &$filters, string $key, mixed $value, array $allow): void
    {
        $value = trim((string) $value);
        if ($value === '') {
            return;
        }

        $targetKey = $key;
        $isLike = false;
        if (str_ends_with($targetKey, ':like')) {
            $targetKey = substr($targetKey, 0, -5);
            $isLike = true;
        }
        if (str_starts_with($targetKey, 'fieldValue.')) {
            $targetKey = substr($targetKey, strlen('fieldValue.'));
        }

        if (!isset($allow[$targetKey])) {
            return;
        }

        $filters[] = [
            'field' => $targetKey,
            'value' => $value,
            'like' => $isLike,
        ];
    }
}
