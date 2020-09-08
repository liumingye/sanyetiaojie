<?php

namespace app\common\model\order;

use app\common\model\BaseModel;
use app\common\model\order\Mediate as MediateModel;

class MediateInfo extends BaseModel
{
    /**
     * 添加
     */
    public function add(array $data)
    {
        if (!isset($data['times'])) {
            $mediate = (new MediateModel)->field('times')->where('id', $data['mid'])->find();
            if (!isset($mediate['times'])) {
                return false;
            }
            $data['times'] = $mediate['times'];
        }
        $data['app_id'] = self::$app_id;
        // 开启事务
        $this->startTrans();
        try {
            $this->save($data);
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->error = $e->getMessage();
            $this->rollback();
            return false;
        }
    }
    
    /**
     * 删除
     */
    public function remove()
    {
        return $this->delete();
    }

    public static function detail($id)
    {
        return self::find($id);
    }
}
