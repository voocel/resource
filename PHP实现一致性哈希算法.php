<?php

class FlexiHash
{
    protected $_node = [];
    protected $_virtualNode  = 64;
    protected $_position = [];

    /**
     * 生成hash
     */
    public function cHash($str){
        return sprintf('%u',crc32($str));
    }

    /**
     * 添加节点
     */
    public function addNode($node){
        if(isset($this->_node[$node])) return;
        for($i = 0; $i < $this->_virtualNode; $i++){
            $pos = $this->cHash($node.'_'.$i);
            $this->_position[$pos] = $node;
            $this->_node[$node][] = $pos;
        }
        $this->sortPos();
    }

    /**
     * 查找节点
     */
    public function lookup($key){
        $point = $this->cHash($key);

        //先取环上最小的一个节点
        $node = current($this->position);

        //循环获取相近节点
        foreach ($this->_position as $key => $value) {
            if($point <= $key){
                $node = $value;
                break;
            }
            reset($this->_position);
        }
        return $node;
    }

    /**
     * 删除节点
     */
    public function delNode($node){
        if(!isset($this->_node[$node])) return;

        //删除虚拟节点
        foreach ($this->_node as $key => $value) {
            unset($this->_position[$value]);
        }

        //删除节点
        unset($this->_node[$node]);
    }

    /**
     * 排序
     */
    public function sortPos(){
        ksort($this->_position,SORT_REGULAR);
    }
}
