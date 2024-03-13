<?php

namespace App\Repositories;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use App\Exceptions\ApiException;

/**
 * Base Repository
 */
abstract class BaseRepository
{
    /** @var Illuminate\Database\Eloquent\Model */
    protected $model;

    abstract protected function listFields(): array;

    public function list(): ?Collection
    {
        $fields = $this->listFields() ?? '*';

        return $this->query()->select($fields)->get();
    }

    /**
     * Returns a list of all records
     */
    public function listAll(): ?Collection
    {
        $fields = $this->listFields() ?? '*';

        return $this->query()->withTrashed()->select($fields)->get();
    }

    /**
     * Returns a single record by given ID value
     */
    public function get( $id )
    {
        $primary_key = $this->getPrimaryKey();

        return $this->make()->where($primary_key, '=', $id)->first();
    }

    /**
     * Returns a filtered list with given array of filters
     */
    public function filteredList(array $filters)
    {
        $fields = $this->listFields() ?? '*';
        $model = $this->query()->select($fields);

        foreach( $filters as $column => $value )
        {
            $model = $model->where($column, '=', $value);
        }

        //$res = $model->toRawSql();
        //return $res;

        return $model->get();
    }

    /**
     * @return Model
     */
    protected function make(): Model
    {
        return new $this->model;
    }

    /**
     * Returns the Primary Key name
     * @return string
     */
    protected function getPrimaryKey(): string
    {
        return $this->make()->getKeyName();
    }

    /**
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function query(): Builder
    {
        return $this->make()->newQuery();
    }

    /**
     * Accepts either the id or model. It's a safety method so that you can just pass arguments in
     * and receive the id back.
     * @param $model
     * @return mixed
     */
    protected function getId($model): int
    {
        return $model instanceof Model ? $model->getKey() : $model;
    }

    /**
     * Persist the model data.
     * Pass in an array of input, and id. Passing null to the
     * second argument will create a new instance.
     *
     * @param array    $input
     * @param int|null $id
     *
     * @return \Illuminate\Database\Eloquent\Model|mixed|null
     */
    public function store(array $input, $id = null)
    {
        $model = $id ? $this->get($id) : $this->make();

        if( ! $model instanceof $this->model )
        {
            throw new ApiException( Lang::get('general.record_not_found') );
        }

        DB::beginTransaction();

        try
        {
            $model->fill($input);
            $result = $model->save();
            DB::commit();

            return $model;
        }
        catch( Exception $e )
        {
            DB::rollBack();
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * Delete the model.
     * @param int $id
     * @return bool|null
     * @throws \Exception
     */
    public function delete($id): ?bool
    {
        $model = $this->get($id);

        if( ! $model instanceof $this->model )
        {
            throw new ApiException( Lang::get('general.record_not_found') );
        }

        DB::beginTransaction();

        try
        {
            $result = $model->delete();
            DB::commit();

            return $result;
        }
        catch( Exception $e )
        {
            DB::rollBack();
            throw new ApiException($e->getMessage());
        }
    }

    /**
     * Perform a transaction.
     *
     * @param \Closure    $callback
     * @param int         $attempts
     * @param string|null $connection
     *
     * @return mixed
     * @throws \Exception|\Throwable
     */
    public static function transaction(\Closure $callback, int $attempts = 1, string $connection = null)
    {
        if( $connection )
        {
            return DB::connection($connection)->transaction($callback, $attempts);
        }

        return DB::transaction($callback, $attempts);
    }
}