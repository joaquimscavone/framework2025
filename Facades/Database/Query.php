<?php

namespace Fmk\Facades\Database;

use Exception;
use Fmk\Facades\Database\Drivers\Driver;
use Fmk\Facades\Model;

class Query
{

    protected $driver;

    protected $table;

    protected Builder $builder;

    protected $limit = null;

    protected $offset = null;

    protected $orders = [];

    protected array $columns = ['*'];

    protected $class;

    //procedimento de execução padrão.
    protected $callback;

    public function __construct(Driver $driver, $table)
    {
        $this->driver = $driver;
        $this->table = $table;
        if(class_exists($table) && is_subclass_of($table, Model::class)){
            $this->class = $table; //App/Models/User
            $this->table = $table::getTableName(); //users
        }
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
        if($this->class){
            return $stm->fetchAll(\PDO::FETCH_CLASS, $this->class);
        }
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
          if($this->class){
            return $stm->fetchObject($this->class);
        }
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

    public function setCallback($callback){
        $this->callback = $callback;
        return $this;
    }

    public function exec(){
        if($this->callback){
            return call_user_func([$this,$this->callback]);
        }
        return null;
    }


}