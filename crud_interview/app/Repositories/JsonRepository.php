<?php

namespace App\Repositories;

use Illuminate\Support\Collection;

class JsonRepository
{
    protected string $filePath;
    protected array $data;

    public function __construct()
    {
        $this->filePath = storage_path('app/data.json');
        $this->loadData();
    }

    /**
     * Carregar dados do arquivo JSON
     */
    protected function loadData(): void
    {
        if (!file_exists($this->filePath)) {
            $this->createDefaultFile();
        }
        $content = file_get_contents($this->filePath);
        $this->data = json_decode($content, true);
    }

    /**
     * Criar arquivo padrão se não existir
     */
    protected function createDefaultFile(): void
    {
        $defaultData = [
            'profiles' => [],
            'users' => [],
            'addresses' => [],
            'address_user' => []
        ];
        $this->saveData($defaultData);
    }

    /**
     * Salvar dados no arquivo JSON
     */
    protected function saveData($data = null): bool
    {
        $dataToSave = $data ?? $this->data;
        return file_put_contents(
            $this->filePath,
            json_encode($dataToSave, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
        ) !== false;
    }

    /**
     * Obter todos os registros de uma tabela
     */
    public function all(string $table): Collection
    {
        return collect($this->data[$table] ?? []);
    }

    /**
     * Buscar registro por ID
     */
    public function find(string $table, int $id): ?array
    {
        $records = $this->all($table);
        return $records->firstWhere('id', $id);
    }

    /**
     * Criar novo registro
     */
    public function create(string $table, array $data): array
    {
        // Gerar novo ID
        $records = $this->all($table);
        $newId = $records->max('id') + 1;
        $data['id'] = $newId;
        $data['created_at'] = now()->toIso8601String();
        $data['updated_at'] = now()->toIso8601String();

        $this->data[$table][] = $data;
        $this->saveData();
        return $data;
    }

    /**
     * Atualizar registro
     */
    public function update(string $table, int $id, array $data): ?array
    {
        $index = $this->findIndex($table, $id);
        if ($index === null) {
            return null;
        }

        $data['updated_at'] = now()->toIso8601String();
        $this->data[$table][$index] = array_merge(
            $this->data[$table][$index],
            $data
        );

        $this->saveData();
        return $this->data[$table][$index];
    }

    /**
     * Deletar registro
     */
    public function delete(string $table, int $id): bool
    {
        $index = $this->findIndex($table, $id);
        if ($index === null) {
            return false;
        }
        array_splice($this->data[$table], $index, 1);
        $this->saveData();
        return true;
    }

    /**
     * Buscar índice do registro
     */
    protected function findIndex(string $table, int $id): ?int
    {
        foreach ($this->data[$table] as $index => $record) {
            if ($record['id'] == $id) {
                return $index;
            }
        }
        return null;
    }

    /**
     * Filtrar registros
     */
    public function where(string $table, array $conditions): Collection
    {
        $records = $this->all($table);
        foreach ($conditions as $field => $value) {
            if (is_array($value)) {
                // Suporte para operadores especiais
                $operator = $value[0] ?? '=';
                $searchValue = $value[1] ?? null;

                switch ($operator) {
                    case 'LIKE':
                        $records = $records->filter(function ($record) use ($field, $searchValue) {
                            return stripos($record[$field] ?? '', str_replace('%', '', $searchValue)) !== false;
                        });
                        break;
                    case '>=':
                        $records = $records->filter(fn($record) => ($record[$field] ?? null) >= $searchValue);
                        break;
                    case '<=':
                        $records = $records->filter(fn($record) => ($record[$field] ?? null) <= $searchValue);
                        break;
                }
            } else {
                $records = $records->where($field, $value);
            }
        }
        return $records;
    }

    /**
     * Vincular relacionamento Many-to-Many
     */
    public function attach(string $pivotTable, int $firstId, int $secondId, string $firstKey, string $secondKey): bool
    {
        // Verificar se já existe
        $exists = collect($this->data[$pivotTable] ?? [])
            ->where($firstKey, $firstId)
            ->where($secondKey, $secondId)
            ->isNotEmpty();

        if ($exists)
            return false;

        $newId = collect($this->data[$pivotTable] ?? [])->max('id') + 1;
        $this->data[$pivotTable][] = [
            'id' => $newId,
            $firstKey => $firstId,
            $secondKey => $secondId,
            'created_at' => now()->toIso8601String(),
        ];
        $this->saveData();
        return true;
    }

    /**
     * Desvincular relacionamento Many-to-Many
     */
    public function detach(string $pivotTable, int $firstId, int $secondId, string $firstKey, string $secondKey): bool
    {
        $initialCount = count($this->data[$pivotTable] ?? []);
        $this->data[$pivotTable] = array_values(array_filter(
            $this->data[$pivotTable] ?? [],
            fn($record) => !($record[$firstKey] == $firstId && $record[$secondKey] == $secondId)
        ));

        $finalCount = count($this->data[$pivotTable]);
        if ($initialCount > $finalCount) {
            $this->saveData();
            return true;
        }
        return false;
    }

    /**
     * Obter relacionamentos Many-to-Many
     */
    public function getRelated(string $pivotTable, int $id, string $idKey, string $relatedKey): Collection
    {
        return collect($this->data[$pivotTable] ?? [])
            ->where($idKey, $id)
            ->pluck($relatedKey);
    }

    /**
     * Verificar se registro existe
     */
    public function exists(string $table, array $conditions): bool
    {
        return $this->where($table, $conditions)->isNotEmpty();
    }

    /**
     * Contar registros
     */
    public function count(string $table, array $conditions = []): int
    {
        if (empty($conditions)) {
            return count($this->data[$table] ?? []);
        }
        return $this->where($table, $conditions)->count();
    }

    /**
     * Paginação
     */
    public function paginate(Collection $items, int $page = 1, int $perPage = 15): array
    {
        $total = $items->count();
        $lastPage = (int) ceil($total / $perPage);
        $offset = ($page - 1) * $perPage;
        return [
            'current_page' => $page,
            'data' => $items->slice($offset, $perPage)->values()->toArray(),
            'first_page_url' => "?page=1",
            'from' => $offset + 1,
            'last_page' => $lastPage,
            'last_page_url' => "?page={$lastPage}",
            'next_page_url' => $page < $lastPage ? "?page=" . ($page + 1) : null,
            'path' => request()->url(),
            'per_page' => $perPage,
            'prev_page_url' => $page > 1 ? "?page=" . ($page - 1) : null,
            'to' => min($offset + $perPage, $total),
            'total' => $total,
        ];
    }
}