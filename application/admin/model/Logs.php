<?php

namespace app\admin\model;

use think\Model;

class Logs extends Model
{
        protected $table='web_role';
    //
        public static function portbility(){
            $resutl=action('home/register/random_str',7);
            return $resutl;
        }
}
