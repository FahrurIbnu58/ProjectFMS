<?php

namespace App\Repositories;

use App\Models\Folder;

class FolderRepository extends BaseRepository
{
    public function __construct(Folder $model)
    {
        parent::__construct($model);
    }
}
