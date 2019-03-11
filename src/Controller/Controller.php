<?php
/**
 * Created by PhpStorm.
 * User: david
 * Date: 2019-03-02
 * Time: 05:31
 */

namespace Zhyu\Controller;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Zhyu\Repositories\Eloquents\RepositoryApp;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected $title = null;
    protected $id = null;
    protected $table = null;

    protected $columns;
    protected $limit;

    public function __construct()
    {
        RepositoryApp::bind((new \ReflectionClass($this))->getShortName());
    }

    /**
     * @return mixed
     */
    public function getColumns()
    {
        return $this->columns;
    }

    /**
     * @param mixed $columns
     */
    public function setColumns($columns)
    {
        $this->columns = $columns;
    }


    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }

    /**
     * @return null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param null $id
     */
    public function setId($id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getTable(): string
    {
        return $this->table;
    }

    /**
     * @param string $table
     */
    public function setTable(string $table): void
    {
        $this->table = $table;
    }



    public function returnClassBaseName($class){
        return  strtolower(class_basename($class));
    }
    protected function view($view, Model $model = null, $params = null){
        $model_name = $this->returnClassBaseName($model);
        ${$model_name} = $model;

        if(isset($params['table']) && strlen($params['table'])>0){
            $table = $params['table'];
        }else{
            if(!is_null($this->table)) {
                $table = $this->table;
            }else{
                $table = $model->getTable();
            }
        }
        if(isset($params['title']) && strlen($params['title'])>0){
            $title = $params['title'];
        }else{
            $title = $this->title;
        }
        if(isset($model->id)){
            $id = $model->id;
        }


        return view($view, compact('table', 'title', 'id', $model_name));
    }
}