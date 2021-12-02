<?php

namespace Domain;

class UserSkill {

    private $model;
    public function __construct()
    {
        $this->model = new \Model\UserSkill();
    }

    public function getData($qq) {
        return $this->model->get($qq);
    }

    public function setData($qq, $datas) {
        $data = $this->getData($qq);
        if ($data) return $this->model->update($qq, $datas);
        if (!isset($datas['qq'])) $datas['qq'] = $qq;
        return $this->model->insert($datas);
    }

}