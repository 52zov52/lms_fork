<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MyPublication extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'status',
        'title',
        'output_data',
        'year_publishing',
        'volume_full_in_printed_sheets',
        'volume_full_on_pages',
        'volume_author_in_printed_sheets',
        'volume_author_on_pages',
        'name_co_authors',
        'bibliographic_description',
        'file_path',
        'link_elibrary',
        'is_university_affiliation',
        'user_id'
    ];
}
