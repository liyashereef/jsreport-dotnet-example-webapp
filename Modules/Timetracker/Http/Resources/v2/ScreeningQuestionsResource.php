<?php

namespace Modules\Timetracker\Http\Resources\v2;

use Illuminate\Http\Resources\Json\ResourceCollection;

class ScreeningQuestionsResource extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {

        return $this->collection->transform(function($question){
                return [
                    'id' => $question->id,
                    'title' => $question->question,
                    'expectedAnswer' => (($question->answer == 1))? true : false,
                ];
            });
    }
}
