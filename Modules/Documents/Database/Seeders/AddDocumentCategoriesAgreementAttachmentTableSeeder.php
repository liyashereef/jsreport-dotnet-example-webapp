<?php

namespace Modules\Documents\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Models\DocumentCategory;

class AddDocumentCategoriesAgreementAttachmentTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     * @throws \Exception
     */
    public function run()
    {
        $documentCategoryDetailsCount = DocumentCategory::where('document_category','Agreement')->count();
        if($documentCategoryDetailsCount > 0) {
            throw new \Exception("Agreement Category already exists");
        }

        \DB::table('document_categories')->insert(array(
            0 => array(
                'document_type_id' =>1,
                'document_category' =>'Agreement',
                'is_editable' => 0,
                'created_by' => null,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),

            )
        ));
        // $this->call("OthersTableSeeder");
    }
}
