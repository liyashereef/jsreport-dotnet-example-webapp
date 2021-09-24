<?php

namespace Modules\Documents\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DocumentsDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        $this->call(DocumentTypesTableSeeder::class);
        $this->call(DocumentCategoriesTableSeeder::class);
        $this->call(AddDocumentCategoriesAgreementAttachmentTableSeeder::class);
        $this->call(AddDocumentNamesSignedEmployeeContractTableSeeder::class);
        $this->call(AddTransitionAttachmentToDocumentTableSeeder::class);
    }
}
