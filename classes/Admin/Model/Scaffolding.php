<?php

interface Admin_Model_Scaffolding
{
    public function getAll($page, $limit, $filters);
    public function countAll($filters);
    public function getOne($id);
    public function create($data);
    public function save($id, $data);
    public function delete($id);
}