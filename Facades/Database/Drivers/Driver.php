<?php


namespace Fmk\Facades\Database\Drivers;

use Exception;
use Fmk\Facades\Database\Builder;
use Fmk\Facades\Database\Connection;



abstract class Driver{

    protected array $parameters;

    protected array $parameters_default = [];

    private $connection;

    private $sql;

    private $data;


    public function __construct(array $parameters){
        $this->parameters = array_merge($this->parameters_default, $parameters);
    }


    public function getConnection(){
        if(is_null($this->connection)){
            $this->connection = Connection::getConnection(
                $this->getDns(),
                $this->username,
                $this->password,
                $this->options
            );
        }
        return $this->connection;
    }

    public function __get($name){
        return $this->parameters[$name] ?? null;
    }


    abstract protected function getDns():string;

    public function insert($tabela, array $data){
        $columns = implode(',', array_keys($data));
        $values = implode(',:', array_keys($data));
        $this->sql = "INSERT INTO $tabela ($columns) values (:$values); ";
        $this->data  = $data;
        return $this;
    }

    //name=>asc|desc
    public function select($tabela, array $columns = ['*'], Builder|null $builder = null, array $orders = [], int|null $limit = null, int|null $offset = null){
        //SELECT * FROM TABELA WHERE NAME = JOAQUIM ORDER BY NOME LIMIT 10;
        $columns = implode(',', $columns);
        [$where, $wdata] = $this->compilerBuilder($builder);
        $order = $this->compilerOrders($orders);
        $limit = $this->compilerLimit($limit, $offset);
        $this->sql = "SELECT $columns FROM $tabela$where$order$limit;";
        $this->data = $wdata;
        return $this;
    }


     public function update($tabela, array $data, Builder|null $builder = null){   
        $sql = "UPDATE $tabela SET ";
        $comma = "";
        foreach($data as $key => $value){
            $sql.="$comma$key = :$key";
            $comma = ', ';
        }
        [$where, $wdata] = $this->compilerBuilder($builder);
        $this->sql = "$sql$where;";
        $this->data = array_merge($data, $wdata);
        return $this;
    }
     public function delete($tabela, Builder|null $builder = null){   
        $sql = "DELETE FROM $tabela";
        [$where, $wdata] = $this->compilerBuilder($builder);
        $this->sql = "$sql$where;";
        $this->data = $wdata;
        return $this;
    }

    private function compilerOrders(array $orders){
        if(empty($orders)){
            return "";
        }

        $sql = " ORDER BY ";
        $comma = "";
        foreach($orders as $name => $order){
            $sql.=$comma.$name;
            $sql.=(strtoupper($order) == 'DESC')?" DESC":" ASC";
            $comma=", ";
        }
        return $sql; 

    }

    private function compilerLimit(?int $limit = null, ?int $offset = null ){
        //LIMIT 5 OFFSET 5
        if(is_null($limit)){
            return "";
        }
        $sql = " LIMIT $limit";
        return $sql.=(is_null($offset))?"":" OFFSET $offset";
    }   

    private function compilerBuilder(Builder|null $builder = null){
        if($builder){
            return $builder->compiler();
        }
        return ['',[]];
    }

    public function exec(){
        if(empty($this->sql)){
            throw new Exception('SQL empty!');
        }
        $stm = $this->getConnection()->prepare($this->sql);
        $stm->execute($this->data);
        return $stm;
    }

    public function toSql(){
        $query = $this->sql ?? '';
        foreach($this->data ?? [] as $key => $value){
            $query = str_replace(":$key", "'".addslashes(string: $value)."'", $query);
        }
        return $query;
    }


    public function lastInsertId(string $tabela){
        return $this->getConnection()->lastInsertId($tabela);
    }







}