<?php

namespace Frame\Database;

use Exception;
use Frame\Database;

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

    public function create($item): false|int
    {
        $query = $this->db->prepare("INSERT INTO ?t SET ?A", $this->table, $item);
        $this->db->query($query);

        return $this->db->lastInsertId();
    }

    public function update(int $id, array $item): int
    {
        $this->db->query("UPDATE ?t SET ?A WHERE id = ?i", $this->table, $item, $id);

        return $id;
    }

    public function delete(int $id): int
    {
        $this->db->query("DELETE FROM ?t WHERE id = ?i LIMIT 1", $this->table, $id);

        return $this->db->rowCount();
    }

    public function read(bool $readOne = false): array|object|bool
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

    public function readOne(): object|bool
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
        $clone->orderBy= 'ORDER BY '.implode(', ', $order);
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