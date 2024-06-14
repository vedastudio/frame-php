<?php

namespace Frame\Database;

use Exception;
use Frame\Database;
use stdClass;

/** All Repository classes should extend this class */
abstract class AbstractRepository
{
    /** Table name (required) */
    protected string $table;

    /** Table alias */
    private string $alias;

    /** SELECT query part */
    protected string $select = '*';

    /** FROM query part */
    private string $from;

    /** JOINs query part */
    protected string $joins = '';

    /** WHERE query part */
    protected string $where = '';

    /** ORDER BY query part */
    protected string $orderBy = '';

    /** LIMIT query part */
    private string $limit = '';

    /** Page number */
    private int $page = 1;

    /** Items per page */
    private int $perPage = 100;

    /** Use a specific field from database table as the array key for the result objects */
    private string $primaryKey = '';

    //TODO GROUP BY .. HAVING

    public function __construct(protected readonly Database $db)
    {
        if (!isset($this->table)) throw new Exception('The required value $table was not passed');
        $this->alias = $this->alias ?? $this->table[0];
        $this->from = $this->db->prepare("FROM ?t AS $this->alias", $this->table);
        $this->select = "SELECT $this->select";
        $this->where = "WHERE 1 $this->where";
        $this->limit = "LIMIT 0, $this->perPage";
        $this->orderBy = $this->orderBy ? "ORDER BY $this->orderBy" : '';
    }

    public function create(array $data, bool $cleanData = false): false|string
    {
        if ($cleanData) $data = $this->cleanData($data);
        $this->db->query("INSERT INTO ?t SET ?A", $this->table, $data);

        return $this->db->lastInsertId();
    }

    public function update(int $id, array $data, bool $cleanData = false): int
    {
        if ($cleanData) $data = $this->cleanData($data);
        $this->db->query("UPDATE ?t SET ?A WHERE id = ?i", $this->table, $data, $id);

        return $id;
    }

    public function delete(int $id): int
    {
        $this->db->query("DELETE FROM ?t WHERE id = ?i LIMIT 1", $this->table, $id);

        return $this->db->rowCount();
    }

    public function read(bool $readOne = false): mixed
    {
        $query = "$this->select
        $this->from
        $this->joins 
        $this->where 
        $this->orderBy
        $this->limit";

        $this->db->query($query);

        if ($readOne) return $this->db->result();
        return $this->db->results($this->primaryKey);
    }

    public function readOne(): mixed
    {
        $this->limit = 'LIMIT 1';
        return $this->read(true);
    }

    public function count(): int
    {
        $select = 'SELECT COUNT(*) AS count';
        $query = "$select
        $this->from
        $this->joins 
        $this->where";

        $this->db->query($query);
        return $this->db->result('count');
    }

    private function cleanData(array $data): array
    {
        $this->db->query("DESCRIBE $this->table");
        $cleanedData = [];
        foreach ($this->db->results() as $row) {
            if (array_key_exists($row->Field, $data)) {
                $cleanedData[$row->Field] = $data[$row->Field];
            }
        }
        return $cleanedData;
    }

    public function emptyRecord(): object
    {
        $this->db->query("DESCRIBE $this->table");
        $emptyRecord = new stdClass();
        foreach ($this->db->results() as $row) {
            if ($row->Field === 'id' && $row->Key === 'PRI') continue;
            if ($row->Type === 'datetime' && $row->Default === 'current_timestamp()') continue;
            $emptyRecord->{$row->Field} = $this->convertMySQLType($row->Type, $row->Default);
        }
        return $emptyRecord;
    }

    private function convertMySQLType($type, $value): mixed
    {
        if ($value === null) return null;
        if ($value === '') return '';

        $mainType = strtoupper(explode('(', $type)[0]);
        switch ($mainType) {
            case 'INT':
            case 'TINYINT':
            case 'SMALLINT':
            case 'MEDIUMINT':
            case 'BIGINT':
                return (int)$value;
            case 'DECIMAL':
            case 'NUMERIC':
            case 'FLOAT':
            case 'DOUBLE':
                return (float)$value;
            default:
                return $value;
        }
    }

    public function setFilter(array $filters): self
    {
        $clone = clone $this;
        extract($this->generateSQLQueryFromFilters($filters, $clone->where, $clone->joins));
        $clone->where = $where;
        $clone->joins = $joins;

        return $clone;
    }

    public function setOrderBy(string ...$order): self
    {
        $clone = clone $this;
        foreach ($order as &$s) {
            $s = "$this->alias.$s";
        }
        $clone->orderBy = 'ORDER BY ' . implode(', ', $order);
        return $clone;
    }

    public function setPrimaryKey(string $primaryKey): self
    {
        $clone = clone $this;
        $clone->primaryKey = $primaryKey;

        return $clone;
    }

    public function setLimit(int $page, int $perPage): self
    {
        $clone = clone $this;
        $clone->page = max(1, $page);
        $clone->perPage = max(1, $perPage);
        $clone->limit = $this->db->prepare('LIMIT ?i, ?i', ($clone->page - 1) * $clone->perPage, $clone->perPage);

        return $clone;
    }

    protected function generateSQLQueryFromFilters(array $filters, string $where, string $joins): array
    {
        return ['where' => $this->where, 'joins' => $this->joins];
    }
}