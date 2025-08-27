<?php

namespace App\Entity;

enum PostState: string
{
    case Published = 'PUBLISHED';
    case Draft = 'DRAFT';
}
