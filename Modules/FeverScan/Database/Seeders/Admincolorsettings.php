<?php
namespace Modules\FeverScan\Database\Seeders;

use Illuminate\Database\Seeder;

class Admincolorsettings extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('admin_colorsettings')->truncate();
        \DB::table('admin_colorsettings')->insert(array (
            0=>array(
                "id"=>1,
                "title"=>"0-97",
                "colorhexacode"=>"#008000",
                "fieldIdentifier"=>1,
                "rangebegin"=>0,
                "rangeend"=>97.99,
                "status"=>true,
                "created_by"=>1,
                "created_at"=>"2020-03-01 09:00"
            ),
            1 =>
            array(
                "id"=>2,
                "title"=>"98.0-99.0",
                "colorhexacode"=>"#ffff00",
                "fieldIdentifier"=>1,
                "rangebegin"=>98,
                "rangeend"=>99.00,
                "status"=>true,
                "created_by"=>1,
                "created_at"=>"2020-03-01 09:00"
            ),
            
            2=>array(
                "id"=>3,
                "title"=>"99.1-101.0",
                "colorhexacode"=>"#FF0000",
                "fieldIdentifier"=>1,
                "rangebegin"=>99.1,
                "rangeend"=>101.0,
                "status"=>true,
                "created_by"=>1,
                "created_at"=>"2020-03-01 09:00"
            ),
            
            3=>array(
                "id"=>4,
                "title"=>"Under 25",
                "colorhexacode"=>"#ffff",
                "fieldIdentifier"=>2,
                "rangebegin"=>0,
                "rangeend"=>25,
                "status"=>true,
                "created_by"=>1,
                "created_at"=>"2020-03-01 09:00"
            ),
            
            4=>array(
                "id"=>5,
                "title"=>"25-35",
                "colorhexacode"=>"#008000",
                "fieldIdentifier"=>2,
                "rangebegin"=>25.01,
                "rangeend"=>35,
                "status"=>true,
                "created_by"=>1,
                "created_at"=>"2020-03-01 09:00"
            ),
            
            5=>array(
                "id"=>6,
                "title"=>"35-50",
                "colorhexacode"=>"#ffff00",
                "fieldIdentifier"=>2,
                "rangebegin"=>35.01,
                "rangeend"=>50,
                "status"=>true,
                "created_by"=>1,
                "created_at"=>"2020-03-01 09:00"
            ),
            
            6=>array(
                "id"=>7,
                "title"=>"50-60",
                "colorhexacode"=>"#0000FF",
                "fieldIdentifier"=>2,
                "rangebegin"=>50.01,
                "rangeend"=>60,
                "status"=>true,
                "created_by"=>1,
                "created_at"=>"2020-03-01 09:00"
            )
            ,
            
            7=>array(
                "id"=>8,
                "title"=>"60-70",
                "colorhexacode"=>"#A52A2A",
                "fieldIdentifier"=>2,
                "rangebegin"=>60.01,
                "rangeend"=>70,
                "status"=>true,
                "created_by"=>1,
                "created_at"=>"2020-03-01 09:00"
            )
            ,
            
            8=>array(
                "id"=>9,
                "title"=>"70plus",
                "colorhexacode"=>"#FF0000",
                "fieldIdentifier"=>2,
                "rangebegin"=>70.01,
                "rangeend"=>125,
                "status"=>true,
                "created_by"=>1,
                "created_at"=>"2020-03-01 09:00"
            ),
            
            
            
            9=>array(
                "id"=>11,
                "title"=>"101.1-103",
                "colorhexacode"=>"#FF0000",
                "fieldIdentifier"=>1,
                "rangebegin"=>101.1,
                "rangeend"=>103.0,
                "status"=>true,
                "created_by"=>1,
                "created_at"=>"2020-03-01 09:00"
            ),
            
            10=>array(
                "id"=>12,
                "title"=>"Over103",
                "colorhexacode"=>"#FF0000",
                "fieldIdentifier"=>1,
                "rangebegin"=>103.1,
                "rangeend"=>107.0,
                "status"=>true,
                "created_by"=>1,
                "created_at"=>"2020-03-01 09:00"
            )
            
        ));

    }
}
