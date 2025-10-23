<?php

namespace Fmk\Facades\Database;

use Exception;
use Fmk\Facades\Database\Drivers\Driver;

class Query
{

    protected $driver;

    protected $table;

    protected Builder $builder;

    protected $limit = null;

    protected $offset = null;

    protected $orders = [];

    protected array $columns = ['*'];

    public function __construct(Driver $driver, $table)
    {
        $this->driver = $driver;
        $this->table = $table;
        $this->builder = new Builder();
    }


    public function select($columns)
    {
        $this->columns = (is_array($columns)) ? $columns : func_get_args();
        return $this;
    }

    public function get()
    {
        $stm = $this->driver->select(
            $this->table,
            $this->columns,
            $this->builder,
            $this->orders,
            $this->limit,
            $this->offset
        )->exec();
        return $stm->fetchAll(\PDO::FETCH_ASSOC);
    }

    public function first()
    {
        $stm = $this->driver->select(
            $this->table,
            $this->columns,
            $this->builder,
            $this->orders,
            $this->limit,
            $this->offset
        )->exec();
        return $stm->fetch(\PDO::FETCH_ASSOC);
    }


    public function insert(array $data)
    {
        $this->driver->insert($this->table, $data)->exec();
        return $this->lastInsertId();
    }

    public function lastInsertId()
    {
        return $this->driver->lastInsertId($this->table);
    }

    private function order($column, $order = 'asc')
    {
        $this->orders[$column] = $order;
        return $this;
    }

    public function orderAsc($column)
    {
        return $this->order($column);
    }

    public function orderDesc($column)
    {
        return $this->order($column, 'desc');
    }

    public function limit($limit, $offset = null)
    {
        $this->limit = $limit;
        $this->offset = $offset;
        return $this;
    }

    public function where($column, $operator, $value)
    {
        $this->builder->where($column, $operator, $value);
        return $this;
    }

    public function orWhere($column, $operator, $value)
    {
        $this->builder->orWhere($column, $operator, $value);
        return $this;
    }


    public function update(array $data)
    {
        if ($this->builder->isEmpty()) {
            throw new Exception("Não se pode executar update sem where");
        }
        return $this->driver->update($this->table, $data, $this->builder)->exec();
    }
    public function delete()
    {
        if ($this->builder->isEmpty()) {
            throw new Exception("Não se pode executar delete sem where");
        }
        return $this->driver->delete($this->table, $this->builder)->exec();
    }


}