<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

abstract class BaseRepository
{
    public function __construct(protected Model $model)
    {
    }

    public function getAll(?int $perPage = null, array $relations = []): LengthAwarePaginator|Collection
    {
        $query = $this->model->with($relations);

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    public function findById(int $id, array $relations = []): ?Model
    {
        return $this->model->with($relations)->find($id);
    }

    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    public function update(Model $model, array $data): Model
    {
        $model->update($data);
        return $model;
    }

    public function delete(Model $model): void
    {
        $model->delete();
    }

    public function search(string $search, array $fields, ?int $perPage = null, array $relations = []): LengthAwarePaginator|Collection
    {
        $query = $this->model->where(function ($query) use ($search, $fields) {
            foreach ($fields as $field) {
                $query->orWhere($field, 'LIKE', "%{$search}%");
            }
        })->with($relations);

        return $perPage ? $query->paginate($perPage)->appends(['search' => $search]) : $query->get();
    }
}
