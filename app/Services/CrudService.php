<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class CrudService
{
    /**
     * Lấy danh sách bản ghi
     */
    public function all(
        string $modelClass,
        array $with = [],
        array $withCount = [],
        ?string $orderBy = null,
        string $direction = 'asc'
    ) {
        $query = $modelClass::query();

        // Eager loading relationship
        if (!empty($with)) {
            $query->with($with);
        }

        // Đếm relationship
        if (!empty($withCount)) {
            $query->withCount($withCount);
        }

        // Sắp xếp
        if ($orderBy) {
            $query->orderBy($orderBy, $direction);
        }

        return $query->get();
    }


    /**
     * Lấy danh sách bản ghi phân trang
     */
    public function paginate(
        string $modelClass,
        int $perPage = 15,
        array $with = [],
        array $withCount = [],
        ?string $orderBy = null,
        string $direction = 'asc'
    ) {
        $query = $modelClass::query();

        if (!empty($with)) {
            $query->with($with);
        }

        if (!empty($withCount)) {
            $query->withCount($withCount);
        }

        if ($orderBy) {
            $query->orderBy($orderBy, $direction);
        }

        return $query->paginate($perPage);
    }


    /**
     * Tìm một bản ghi hoặc trả về 404
     */
    public function findOrFail(
        string $modelClass,
        int|string $id,
        array $with = []
    ): Model {
        $query = $modelClass::query();

        if (!empty($with)) {
            $query->with($with);
        }

        return $query->findOrFail($id);
    }


    /**
     * Tạo bản ghi
     */
    public function create(
        string $modelClass,
        array $data
    ): Model {
        return $modelClass::create($data);
    }


    /**
     * Cập nhật bản ghi
     */
    public function update(
        Model $model,
        array $data
    ): Model {
        $model->update($data);

        return $model->fresh();
    }


    /**
     * Xóa bản ghi
     */
    public function delete(Model $model): bool
    {
        return (bool) $model->delete();
    }
}