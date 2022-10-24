<?php

namespace App\Observers;

class GenericObserver
{
    public function creating($model)
    {
        $model->created_by = auth()->user()->id;
        $model->updated_by = auth()->user()->id;
    }

    public function updating($model)
    {
        $model->updated_by = auth()->user()->id;
    }
}
