<?php
/**
 * Custom validator for lib-validator
 * @package lib-model
 * @version 0.0.1
 */

namespace LibModel\Validator;

class Model
{
    static function unique($value, $options, $object, $field, $rules): ?array{
        if(is_null($value))
            return null;

        $model  = $options->model;
        $mfield = $options->field;
        $mself  = $options->self ?? null;

        $row = $model::getOne([$mfield => $value]);
        if(!$row)
            return null;

        if(!$mself)
            return ['14.0'];

        $obj = \Mim::$app;
        $mself_serv = explode('.', $mself->service);
        foreach($mself_serv as $prop){
            $obj = $obj->$prop ?? null;
            if(is_null($obj))
                break;
        }

        $row_val = $row->{$mself->field};

        if($row_val == $obj)
            return null;
        return ['14.0'];
    }
}