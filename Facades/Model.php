<?php

namespace Fmk\Facades;

use Exception;
use Fmk\Facades\Database\DB;
use Fmk\Facades\Database\Query;

class Model
{
    protected static $table;

    protected static $connection_name;


    protected static $pk = 'id';


    protected static $columns = ['*'];

    protected $relationships = [];
    protected $data = [];

    protected $old = [];

    protected $exists = false;

    public function __construct()
    {
        $this->exists = !empty($this->data);
        $this->old = $this->data;
    }
    //$this->nome = 'Joaquim';
    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }
    public function __get($name)
    {
        
        return $this->data[$name] ?? $this->checkRelationship($name);
    }


    public static function getTableName()
    {
        if (static::$table) {
            return static::$table;
        }
        return strtolower(basename(static::class)) . "s";
    }
    public static function getConnectionName()
    {
        if (static::$connection_name) {
            return static::$connection_name;
        }
        return Config::get('database.connection_default');
    }

    public static function __callStatic($method, $args){
        if(method_exists(Query::class, $method)){
            return call_user_func_array([static::query(),$method],$args);
        }
        throw new Exception("Método $method não encontrado");
    }


    public static function query()
    {
        return (new DB(static::getConnectionName()))
            ->table(static::class);
    }


    public static function find($id)
    {
        return static::query()->select(static::$columns)
            ->where(static::$pk, '=', $id)->first();
    }

    public static function all()
    {
        return static::query()->select(static::$columns)
            ->get();
    }

    public static function create($data){
        $id = static::query()->insert($data);
        return static::find($id);
    }

    public function isStorage(){
        return $this->exists;
    }

    public function save(array $data = []){
        $this->data = array_merge($this->data, $data);
        $pk = static::$pk;
        $data = $this->data;
        unset($data[$pk]);
        if($this->isStorage()){
            return $this->update($pk, $data);
        }
        return $this->insert($pk,$data);
    }

    protected function update($pk, array $data){
        static::query()->where($pk, '=', $this->$pk)->update($data);
        return true;
    }
    protected function insert($pk, array $data){
        $id = static::query()->insert($data);
        $this->$pk = $id;
        return $this->exists = true;
    }

    public function delete(){
        $pk = static::$pk;
        if($this->isStorage()){
            //$this->pk $this->id
            static::query()->where($pk, '=', $this->$pk)->delete();
        }
        $this->exists = false;
        return true;
    }

    public function old($name = null){
        if(isset($name)){
            return $this->old[$name] ?? null;
        }
        return $this->old;
    }
    // cria um relacionamento de 1 - 1 onde a chave estrangeira está na outra classe;
    public function hasOne($related_class, $foreign_key){
        return $related_class::where($foreign_key, '=', $this->{static::$pk})->setCallback('first');
    }

    // cria um relacionamento de 1 - n onde a chave estrangeira está na outra classe;
    public function hasMany($related_class, $foreign_key){
        return $related_class::where($foreign_key, '=', $this->{static::$pk})->setCallback('get');
    }

      // cria um relacionamento de 1 - 1 onde a chave estrangeira está na prorpia classe;
      public function belongsTo($related_class, $local_key){
        return $related_class::where($related_class::$pk, '=', $this->$local_key)->setCallback('first');
      }


      public function checkRelationship($name){
            if(array_key_exists($name,$this->relationships)){
                return $this->relationships[$name];
            }

            if(method_exists($this, $name)){ 
               $reflectionMethod = \ReflectionMethod::createFromMethodName(static::class."::".$name); 
               if($reflectionMethod->getReturnType() == Query::class){
                    $this->relationships[$name] = $this->$name()->exec();
                    return $this->relationships[$name];
               }
            }

            return null;
      }




}