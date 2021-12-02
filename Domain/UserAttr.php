<?php

namespace Domain;

class UserAttr {

    private $model;

    public function __construct()
    {
        $this->model = new \Model\UserAttr();
    }

    public function getUserAttr($qq) {
        return $this->model->get($qq);
    }

    public function setData($qq, $datas) {
        $data = $this->getUserAttr($qq);
        if ($data) return $this->model->update($qq, $datas);
        if (!isset($datas['qq'])) $datas['qq'] = $qq;
        return $this->model->insert($datas);
    }

	/**
	 * 增加角色属性
	 *
	 * @param mixed $pk
	 * @param array $datas
	 * @return void
	 */
	public function addAttr($pk,array $datas) {
		$userAttr = $this->getUserAttr($pk);
		if (!$userAttr) return -2;
		$free = $userAttr['free'];
		if (isset($datas['qq'])) unset($datas['qq']);
		foreach ($datas as $key => $value) {
			if ($key != 'free') $free -= $value;
			if ($free < 0) return -1;
			$datas[$key] = max(0,$userAttr[$key]+$value);
			$datas['free'] = ($key == 'free') ? $datas[$key] : $free;
		}
		return $this->setData($pk,$datas);
    }
    
    /** 洗点 */
    public function resetAttr($pk) {
        $userAttr = $this->getUserAttr($pk);
        if (!$userAttr) return -1;
        $DScore = new UserScore();
        $userScore = $DScore->getData($pk);
        if ($userScore['credit'] < 2000) return -2;
        $DScore->setData($pk, array('credit'=>$userScore['credit']-2000));
        $attr = 0;
        foreach ($userAttr as $key => $value) {
            if ($key === 'qq') continue;
            if ($key === 'free') continue;
            // if ($value >= 20) {
            //     $attr += $value-20;
            // } else {
            //     $attr -= 20-$value;
            // }
            // $userAttr[$key] = 20;
            // 洗点全部洗成 0
            $attr += $value;
            $userAttr[$key] = 0;
        }
        $userAttr['free'] += $attr;
        $qq = $userAttr['qq'];
        unset($userAttr['qq']);
        return $this->model->update($userAttr, array('qq'=>$qq));
    }

    public function getUserAttrWithInfo($qq) {
        $dinfo = new \Domain\UserInfo();
        $user = $dinfo->getData($qq);
        if (!$user) return false;
        $info = $this->getUserAttr($qq);
        foreach ($info as $key => $value) {
            $user[$key] = $value;
        }
        return $user;
    }

    public function getUserAttrWithFight($qq) {
        $user = $this->getUserAttrWithInfo($qq);
        if (!$user) return false;
        $user['nickName'] = $user['nickname'];
        // 血量上限
        $user['maxBld'] = $user['bld'] = 50+floor(log10($user['con']+1)*50);
        // 攻击力
        $user['atk'] = 20+floor(log10($user['str']+1)*20);
        // 暴击率
        $user['crit'] = floor(log10($user['dex']+1)*15);
        // 感知判定系数
        $user['rat'] = floor(log10($user['wis']+1)*20);
        // 减伤系数
        $user['shc'] = floor(log10($user['cha']+1)*16.35);
        // 回血率
        $user['abr'] = floor(log10($user['ine']+1)*12);
        return $user;
    }

}