<?php

namespace app\shop\model\auth;

use app\common\model\BaseModel;

class Group extends BaseModel
{
    /**
     * 隐藏字段
     */
    protected $hidden = [
        'app_id',
        'update_time'
    ];

    //表名
    protected $name = 'shop_user_group';
    //主键字段名
    protected $pk = 'id';

    public function getList()
    {
        return $this
            ->order(['sort' => 'desc', 'create_time' => 'desc'])
            ->paginate(20, false, [
                'query' => \request()->request()
            ]);
    }

    /**
     * 详情
     */
    public static function detail($id)
    {
        return self::find($id);
    }

    /**
     * 添加
     */
    public function add($data)
    {
        $data['app_id'] = self::$app_id;
        return $this->save($data);
    }

    /**
     * 编辑
     */
    public function edit($data)
    {
        return $this->save($data) !== false;
    }

    /**
     * 删除
     */
    public function remove()
    {
        return $this->delete();
    }
}
