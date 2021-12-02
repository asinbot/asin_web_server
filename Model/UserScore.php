<?php

/**
 * 数据库操作（示例）
 * 先 new 再操作
 */

namespace Model;

use \PHF\Model\Medoo;

class UserScore extends Medoo {

    public function getTableName($id = null) {
        return "asin_userscore";
    }

    public function getTablePK() {
        return "qq";
    }

	/**
	 * 新增数据
	 * @param  [type] $pk    [description]
	 * @param  array  $datas [description]
	 * @return [type]        [description]
	 */
	protected function newData($pk,array $datas) {
		$score = (int)$datas['score'];
		$datas['scorerank'] = $score*10000000000+(10000000000-time());
		if (isset($datas['rank']) && $datas['rank']) $datas['scorerank'] = (100000000-intval($datas['rank']))*10000000000+(10000000000-time());
        if (!isset($datas["qq"])) $datas["qq"] = $pk;
		return parent::insert($datas);
	}

	/**
	 * 更新数据
	 * @param  [type] $pk    [description]
	 * @param  array  $datas [description]
	 * @return [type]        [description]
	 */
	protected function updateData($pk,array $datas) {
		if (isset($datas['score'])) {
			$score = (int)$datas['score'];
			$datas['scorerank'] = $score*10000000000+(10000000000-time());
		}
		if (isset($datas['rank']) && $datas['rank']) $datas['scorerank'] = (100000000-intval($datas['rank']))*10000000000+(10000000000-time());
		return parent::update($pk,$datas);
	}

	/**
	 * 根据 qq 获取排名
	 * @param  [type] $qq [description]
	 * @return [type]     [description]
	 */
	public function getRank($qq) {
		if (!$qq) return false;
		$data = $this->get($qq);
		if (!$data) return false;
		if ($data['rank']) return $data['rank'];
		$rank = $this->fetch('scorerank > '.$data['scorerank'] . " AND rank = ''",'count(qq) AS count');
		$rank = $rank[0]['count'];
		$rank = (int)$rank + 1;
		$rank = $rank + 1000;
		return $rank;
	}

	/**
	 * 获取排行榜列表
	 * @param  [type] $limit 查询的数量
	 * @return [type]        [description]
	 */
	public function getRankList($limit=0) {
		if ($limit) $scoreArr = $this->fetch('','*','scorerank DESC',0,$limit);
		else $scoreArr = $this->fetch();
		$rankList = [];
		for ($i = 0; $i < count($scoreArr); $i++) {
            $data = $scoreArr[$i];
            $mInfo = new \Model\UserInfo();
			$userInfo = $mInfo->get($data['qq']);
			$data['nickname'] = $userInfo['nickname'];
			$data['rank'] = $this->getRank($data['qq'],$this);
			array_push($rankList, $data);
		}
		array_multisort(array_column($rankList,'rank'),SORT_ASC,$rankList);
		return $rankList;
	}

}