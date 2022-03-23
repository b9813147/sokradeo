<?php

namespace App\Repositories;

use App\Models\SubjectField;

class SubjectFieldRepository
{
    //
    public function __construct()
    {
        
    }
    
    //
    public function getSubjectFields()
    {
        return SubjectField::all();
    }
    
    //
    public function getSubjectField($subjectFieldId)
    {
        return SubjectField::findOrFail($subjectFieldId);
    }
}
