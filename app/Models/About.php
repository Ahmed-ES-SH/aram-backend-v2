<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    protected $fillable = [
        //  first section
        'first_section_image',
        'first_section_title_en',
        'first_section_title_ar',
        'first_section_content_en',
        'first_section_content_ar',
        //  first section
        'second_section_image',
        'second_section_title_en',
        'second_section_title_ar',
        'second_section_content_en',
        'second_section_content_ar',
        //  thired section
        'thired_section_image',
        'thired_section_title_en',
        'thired_section_title_ar',
        'thired_section_content_ar',
        'thired_section_content_en',
        //  fourth section
        'fourth_section_image',
        'fourth_section_title_en',
        'fourth_section_title_ar',
        'fourth_section_content_ar',
        'fourth_section_content_en',
        // others columns
        'cooperation_pdf',
        'main_video',
        'link_video',
        'show_map',
        'address',
    ];
}
