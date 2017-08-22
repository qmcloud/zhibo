<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2009 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id: Page.class.php 2806 2012-03-08 03:21:38Z liu21st $

class Page {
    // 分页栏每页显示的页数
    public $rollPage = 5;
    // 页数跳转时要带的参数
    public $parameter  ;
    // 默认列表每页显示行数
    public $listRows = 20;
    // 起始行数
    public $firstRow	;
    // 分页总页面数
    protected $totalPages  ;
    // 总行数
    protected $totalRows  ;
    // 当前页数
    protected $nowPage    ;
    // 分页的栏的总页数
    protected $coolPages   ;
    // 分页显示定制
    //protected $config  =	array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>' %totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %downPage% %first%  %prePage%  %linkPage%  %nextPage% %end%');
	protected $config  =	array('header'=>'条记录','prev'=>'上一页','next'=>'下一页','first'=>'第一页','last'=>'最后一页','theme'=>'%upPage% %linkPage% %end% %downPage%');
    // 默认分页变量名
    protected $varPage;

    /**
     +----------------------------------------------------------
     * 架构函数
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $totalRows  总的记录数
     * @param array $listRows  每页显示记录数
     * @param array $parameter  分页跳转的参数
     +----------------------------------------------------------
     */
    public function __construct($totalRows,$listRows='',$parameter='') {
        $this->totalRows = $totalRows;
        $this->parameter = $parameter;
        $this->varPage = 'p' ;
        if(!empty($listRows)) {
            $this->listRows = intval($listRows);
        }
        $this->totalPages = ceil($this->totalRows/$this->listRows);     //总页数
        $this->coolPages  = ceil($this->totalPages/$this->rollPage);
        $this->nowPage  = !empty($_GET['p'])?intval($_GET['p']):1;
        //if(!empty($this->totalPages) && $this->nowPage>$this->totalPages) {
            //$this->nowPage = $this->totalPages;
        //}
        $this->firstRow = $this->listRows*($this->nowPage-1);
    }

    public function setConfig($name,$value) {
        if(isset($this->config[$name])) {
            $this->config[$name]    =   $value;
        }
    }

    /**
     +----------------------------------------------------------
     * 分页显示输出
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     */
    public function show() {
        if(0 == $this->totalRows) return '';
        $p = $this->varPage;
        $nowCoolPage      = ceil($this->nowPage/$this->rollPage);
        //$url  =  $_SERVER['REQUEST_URI'].(strpos($_SERVER['REQUEST_URI'],'?')?'':"?").$this->parameter;
		$url = $this->parameter;
        $parse = parse_url($url);
        if(isset($parse['query'])) {
            parse_str($parse['query'],$params);
            unset($params[$p]);
            $url   =  $parse['path'].'?'.http_build_query($params);
        }
        //上下翻页字符串
        $upRow   = $this->nowPage-1;
        $downRow = $this->nowPage+1;
        if ($upRow>0){
            //$upPage="<a href='".$url."&".$p."=$upRow'>".$this->config['prev']."</a>";
			$upPage="<a class=\"page-prev\" href=\"".$url."-$upRow\" title=\"$upRow\">上一页</a>";
        }else{
            $upPage="<a  class=\"page-prev-dis\" href=\"javascript:void(0)\" >";
        }

        if ($downRow <= $this->totalPages){
            //$downPage="<a href='".$url."&".$p."=$downRow'>".$this->config['next']."</a>";
			$downPage="<a class=\"page-next\" href=\"".$url."-$downRow\" title=\"$downRow\">下一页</a>";
        }else{
            $downPage="<a class=\"page-next-dis\" href=\"javascript:void(0)\">下一页</a>";
        }
        // << < > >>
        if($nowCoolPage == 1){
            $theFirst = "";
            $prePage = "";
        }else{
            $preRow =  $this->nowPage-$this->rollPage;
            $prePage = "<a href='".$url."-$preRow' >上".$this->rollPage."页</a>";
            $theFirst = "<a href='".$url."-1' >".$this->config['first']."</a>";
        }
        if($nowCoolPage == $this->coolPages){
            $nextPage = "";
            $theEnd="";
        }else{
            $nextRow = $this->nowPage+$this->rollPage;
            $theEndRow = $this->totalPages;
            $nextPage = "<a href='".$url."-$nextRow' >下".$this->rollPage."页</a>";
            //$theEnd = "<a href='".$url."&".$p."=$theEndRow' >".$this->config['last']."</a>";
			$theEnd = "<a title=\"".$theEndRow."\" href=\"".$url."-$theEndRow\">".$theEndRow."</a>";
        }
        // 1 2 3 4 5
        $linkPage = "";
        for($i=1;$i<=$this->rollPage;$i++){
            $page=($nowCoolPage-1)*$this->rollPage+$i;
            if($page!=$this->nowPage){
                if($page<=$this->totalPages){
                    //$linkPage .= "&nbsp;<a href='".$url."&".$p."=$page'>&nbsp;".$page."&nbsp;</a>";
					$linkPage .= "<a title=\"".$page."\" href=\"".$url."-$page\">".$page."</a>";
                }else{
                    break;
                }
            }else{
                if($this->totalPages != 1){
                    //$linkPage .= "&nbsp;<span class='current'>".$page."</span>";
					$linkPage .= "<a class=current>".$page."</a>";
                }
            }
        }
        $pageStr	 =	 str_replace(
            //array('%header%','%nowPage%','%totalRow%','%totalPage%','%upPage%','%downPage%','%first%','%prePage%','%linkPage%','%nextPage%','%end%'),
            //array($this->config['header'],$this->nowPage,$this->totalRows,$this->totalPages,$upPage,$downPage,$theFirst,$prePage,$linkPage,$nextPage,$theEnd),$this->config['theme']);
			array('%upPage%','%linkPage%','%end%','%downPage%'),
            array($upPage,$linkPage,$theEnd,$downPage),$this->config['theme']);
        return $pageStr;
    }

}