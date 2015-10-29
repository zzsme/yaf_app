<?php

/**
 *      [CodeJm!] Author CodeJm[codejm@163.com].
 *
 *      新闻 管理类
 *      $Id: News.php 2014-09-26 14:54:06 codejm $
 */

class NewsController extends \Core_BackendCtl {

    /**
     * 新闻列表
     *
     */
    public function indexAction() {
        // 分页
        $page = intval($this->getg('page', 1));
        $pageSize = 10;
        // 排序
        $orderby = $this->getg('sort');
        if($orderby) {
            $orderby = str_replace('.', ' ', $orderby);
        } else {
            $orderby = 'id asc';
        }

        // 实例化Model
        $news = new NewsModel();
        // 查询条件
        $params = array(
            'field' => array(),
            'where' => array('status>'=>0),
            'order' => $orderby,
            'page' => $page,
            'per' => $pageSize,
        );
        // 列表
        $result = $news->getLists($params);
        // 数据总条数
        $total = $news->getCount($params);

        // 分页url
        $url = Tools_help::url('backend/news/index').'?page=';
        $pagestr = Tools_help::pager($page, $total, $pageSize, $url);

        // 模版分配数据
        $this->_view->assign('pagestr', $pagestr);
        $this->_view->assign('result', $result);
        $this->_view->assign("pageTitle", '新闻列表');
    }

    /**
     * 添加新闻
     *
     */
    public function addAction() {
        // 实例化Model
        $news = new NewsModel();
        // 处理post数据
        if($this->getRequest()->isPost()) {
            // 获取所有post数据
            $pdata = $this->getAllPost();
            // 处理图片等特殊数据
           $imageInfo = Tools_help::upload('img', 'news');
           if(!empty($imageInfo)) {
               $pdata['img'] = $imageInfo;
           } else {
               unset($pdata['img']);
           }

            // 验证
            $result = $news->validation->validate($pdata, 'add');
            $news->parseAttributes($pdata);

            // 通过验证
            if($result) {
                // 入库前数据处理

   $pdata['dateline'] = Tools_help::htime($news->dateline);

   $pdata['updatetime'] = Tools_help::htime($news->updatetime);

                // Model转换成数组
                $data = $news->toArray($pdata);
                $result = $news->insert($data);
                if($result) {
                    // 提示信息并跳转到列表
                    Tools_help::setSession('Message', '添加成功！');
                    $this->redirect('/backend/news/index');
                } else {
                    // 验证失败
                    $this->_view->assign('ErrorMessage', '添加失败！');
                    $this->_view->assign("errors", $news->validation->getErrorSummary());
                }
            } else {
                // 验证失败
                $this->_view->assign('ErrorMessage', '添加失败！');
                $this->_view->assign("errors", $news->validation->getErrorSummary());
            }
        }

        // 格式化表单数据


        // 模版分配数据
        $this->_view->assign("news", $news);
        $this->_view->assign("pageTitle", '添加新闻');
    }

    /**
     * 编辑新闻
     *
     */
    public function editAction() {
        // 获取主键
        $id = $this->getg('id', 0);
        if(empty($id)) {
            $this->error('id 不能为空!');
        }

        // 实例化Model
        $news = new NewsModel();

        // 处理Post
        if($this->getRequest()->isPost()) {
            // 获取所有post数据
            $pdata = $this->getAllPost();
            // 处理图片等特殊数据
           $imageInfo = Tools_help::upload('img', 'news');
           if(!empty($imageInfo)) {
               $pdata['img'] = $imageInfo;
           } else {
               unset($pdata['img']);
           }

            // 验证
            $result = $news->validation->validate($pdata, 'edit');
            $news->parseAttributes($pdata);

            // 通过验证
            if($result) {
                // 入库前数据处理

   $pdata['dateline'] = Tools_help::htime($news->dateline);

   $pdata['updatetime'] = Tools_help::htime($news->updatetime);


                // Model转换成数组
                $data = $news->toArray($pdata);
                $result = $news->update(array('id'=>$id), $data);

                if($result) {
                    // 提示信息并跳转到列表
                    Tools_help::setSession('Message', '修改成功！');
                    $this->redirect('/backend/news/index');
                } else {
                    // 出错
                    Tools_help::setSession('ErrorMessage', '修改失败, 请确定已修改了某项！');
                    $this->_view->assign("errors", $news->validation->getErrorSummary());
                }
                $news->id = $id;
            } else {
                // 验证失败
                Tools_help::setSession('ErrorMessage', '修改失败, 请检查错误项');
                $this->_view->assign("errors", $news->validation->getErrorSummary());
            }
        }

        // 如果Model数据为空，则获取
        if(!empty($id) && empty($news->id)) {
            $data = $news->select(array('where'=>array('id'=>$id)));
            $news->parseAttributes($data);
        }

        // 格式化表单数据

       // 图片处理
       if($news->img){
           $news->img = Tools_help::fbu($news->img);
       }


        // 模版分配数据
        $this->_view->assign("news", $news);
        $this->_view->assign("pageTitle", '修改新闻');
    }

    /**
     * 单个新闻删除
     *
     */
    public function delAction() {
        $id = $this->getg('id', 0);
        if(empty($id)) {
            $this->error('id 不能为空!');
        }
        // 实例化Model
        $news = new NewsModel();
        $row = $news->update(array('id'=>$id), array('status'=>-1));
        if($row) {
            $this->error('恭喜，删除成功', 'Message');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 批量删除新闻或者调整顺序
     *
     */
    public function batchAction() {
        $ids = $this->getp('dels');
        if(empty($ids)) {
            $this->error('id 不能为空!');
        }
        // 实例化Model
        $news = new NewsModel();
        $row = $news->delNewss($ids);
        if($row) {
            $this->error('恭喜，删除成功', 'Message');
        } else {
            $this->error('删除失败');
        }
    }

    /**
     * 修改新闻状态
     *
     *
     */
    public function statusAction() {
        $id = $this->getg('id', 0);
        if(empty($id)) {
            $this->error('id 不能为空!');
        }
        $status = $this->getg('status', 0);
        $status = $status ? 0 : 1;
        // 实例化Model
        $news = new NewsModel();
        $row = $news->update(array('id'=>$id), array('status'=>$status));
        if($row) {
            $this->error('恭喜，操作成功', 'Message');
        } else {
            $this->error('操作失败');
        }

    }

}

?>
