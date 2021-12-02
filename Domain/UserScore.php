<?php

namespace Domain;

class UserScore {

    private $model;
    public function __construct()
    {
        $this->model = new \Model\UserScore();
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

    public function add($qq, $score = 0, $credit = 0) {
        $userScore = $this->getData($qq);
        if (!$userScore) return -1;
		$_score = max(0,intval($userScore['score'])+$score);
		$_credit = max(0,intval($userScore['credit'])+$credit);
		if ($userScore['rank'] > 0) {
			return $this->setData($qq,array(
				'credit'=>$_credit
			));
		} else {
            return $this->setData($qq,array(
                'score'=>$_score,
                'credit'=>$_credit
            ));
        }
    }

    public function getRank($qq) {
        return $this->model->getRank($qq);
    }

    public function getRankList($limit = 0) {
        return $this->model->getRankList($limit);
    }

}