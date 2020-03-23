<?php

namespace Zhyu\Tools;


class UrlMakeQuery{
    private $divide ='#';

    public function __construct(string $divide = null)
    {
        if(!empty($divide)) $this->divide = $divide;
    }

    public function encode(array $params = []) : string{
        if(count($params)==0){

            return '';
        }
        $query = [];
        foreach($params as $key => $val){
            array_push($query, $this->parseValKey2string($key, $val));
        }

        if(is_array($query) &&count($query)) {

            return join('*', $query);
        }

        return '';
    }

    public function decode(string $query) : array{
        if(strlen($query)==0){

            return [];
        }
        $vars = [];
        $query = urldecode($query);
        //dump($query);
        $rows = explode('*', $query);
        foreach($rows as $row){
            $array = $this->parse2Array($row);
            if(count($array)==0) continue;

            $key = key($array);
            $vars[$key] = $array[$key];
        }
        //dump($rows);

        return $vars;
    }

    private function parse2Array($string) : array{
        $rs = explode($this->divide, $string);
        $ess = explode('.', $rs[0]);
        if(count($ess)>1){
            $key = $ess[1];
        }else{
            $key = $ess[0];
        }

        if(count($rs)==3) {
            if(strstr($rs[2], '[') && strstr($rs[2], ']') ){
                $rs[2] = str_replace('[', '', $rs[2]);
                $rs[2] = str_replace(']', '', $rs[2]);
                $rs[2] = explode(',', $rs[2]);
            }
            $var = [ $key => $rs[2]];
        }elseif(count($rs)==2) {
            $var = [ $key => $rs[0]];
        }else{
            $var = [];
        }

        return $var;
    }

    /**
     * @return array
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    private function parseValKey2string($key, $val){
        $puzzles = [];
        if(!empty($key)){
            array_push($puzzles, $key);
        }
        if(is_array($val)) {
            if(!empty($val['eg'])){
                array_push($puzzles, $val['eg']);
            }

            if(is_array($val['value'])){
                $vv = '['.join(',', $val['value']).']';
                array_push($puzzles, $vv);
            }else{
                if(!empty($val['value'])){
                    array_push($puzzles, $val['value']);
                }
            }
        }else{
            array_push($puzzles, '=');
            array_push($puzzles, $val);
        }

        return join($this->divide, $puzzles);
    }

}