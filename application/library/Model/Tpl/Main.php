<?php
/**
 * 博客模板
 *
 * @author chengxuan <i@chengxuan.li>
 */
namespace Model\Tpl;
class Main extends \Model\Abs {
    
    /**
     * 表名
     *
     * @var string
     */
    protected static $_table = 'tpl_main';
    
    /**
     * 每个人最多多少个模板
     * 
     * @var int
     */
    const TOTAL_LIMIT = 100;
    
    /**
     * 获取指定用户的主题 
     * 
     * @param int     $uid          要操作的UID
     * @param boolean $show_default 是否获取默认模板
     * 
     * @return \array
     */
    public function userTpls($uid = false, $show_default = true) {
        $uid || $uid = \Model\User::validateAuth($uid);
        $where_uid = $show_default ? [$uid, 0] : $uid;
        $db = self::db()->wAnd(['user_id'=>$where_uid])->order(['user_id' => SORT_ASC, 'id' => SORT_DESC]);
        return $db->fetchAll();
    }
    
    /**
     * 统计用户的主题数
     * 
     * @param string $uid 要操作的UID
     * 
     * @return \mixed
     */
    public function countUserTpl($uid = false) {
        $uid || $uid = \Model\User::validateAuth($uid);
        return self::db()->wAnd(['user_id'=>$uid])->fetchOne('COUNT(*)');
    }
    
    /**
     * 创建一个模板
     * 
     * @param int    $alias_id 关联模板ID
     * @param string $name     模板名称
     * @param int    $user_id  用户UID
     * @param string $pic      模板图片
     * 
     * @throws \Exception\Msg
     * 
     * @return int
     */
    public function create($alias_id, $name, $user_id, $pic = '') {
        $uid = \Model\User::validateAuth($uid);
        $total_number = self::countUserTpl($uid);
        if($total_number >= self::TOTAL_LIMIT) {
            throw new \Exception\Msg(sprintf(_('模板总数不能超过%s个'), $total_number));
        }
        
        $db = self::db();
        if($db->wAnd(['user_id' => $uid, 'name' => $name])->fetchRow()) {
            throw new \Exception\Msg(sprintf(_('模板名称已存在:%s'), $name));
        }
        
        $db->insert(array(
            'alias_id' => $alias_id,
            'name'     => $name,
            'user_id'  => $user_id,
            'pic'      => $pic, 
        ));
        $id = $db->lastId();

        return $id;
    }
    
    /**
     * 删除一个模板
     * 
     * @param int    $id  主键ID
     * @param string $uid 用户UID
     * 
     * @return \int
     */
    public function delete($id, $uid = false) {
        $uid || $uid = \Model\User::validateAuth($uid);
        return self::db()->wAnd(['id' => $id, 'user_id' => $uid])->delete(true);
    }
    
}
