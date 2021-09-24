<?php

namespace Modules\Contracts\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class AddDefaultContractWrittenTemplateSeederTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('contract_written_template_parties')->truncate();
        \DB::table('contract_written_template_parties')->insert([
            0=>[
                "id"=>1,
                'templateparty'=>'Client',
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                
            ],
            1=>[
                "id"=>2,
                'templateparty'=>'Security Provider',
                'created_at' => '2019-10-15 06:04:47',
                'updated_at' => '2019-10-15 06:04:47',
                
            ]
        ]);
        // $this->call("OthersTableSeeder");
    }
}
