<?php

namespace OpenForms\Category;

use OpenForms\Utils\MysqlRepository;

final class CategoryRepository
{
    private $repository;
    public function __construct()
    {
        $this->repository = new MysqlRepository();
    }

    public function findAll()
    {
        return $this->repository->select('category',null,'enable=1');
    }
    public function findById($id)
    {
        $data = $this->repository->select('category', null, 'id = :id and enable=1', ['id' => $id]);
        return $data ? $data[0] : $data;
    }

    public function save(Category $new_category)
    {
        return $this->repository->insert('category', (array)$new_category);
    }
    public function edit(Category $category)
    {
        return $this->repository->update('category', (array)$category,'id=:id');
    }
    public function delete($id)
    {
        return $this->repository->delete('category', $id);
    }
}
