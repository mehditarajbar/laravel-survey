<?php

namespace App\Enums;


enum SurveysQuestionEnum:string{

    case TYPE_TEXT='text';
    case TYPE_TEXTAREA='textarea';
    case TYPE_RADIO='radio';
    case TYPE_SELECT='select';
    case TYPE_CHECKBOX='checkbox';

    public static function getType(){
        return[
          SurveysQuestionEnum::TYPE_TEXT->value,
          SurveysQuestionEnum::TYPE_CHECKBOX->value,
          SurveysQuestionEnum::TYPE_RADIO->value,
          SurveysQuestionEnum::TYPE_SELECT->value,
          SurveysQuestionEnum::TYPE_TEXTAREA->value,
        ];
    }
}
