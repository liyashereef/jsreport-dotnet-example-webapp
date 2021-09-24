<?php

namespace Modules\Documents\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Admin\Models\DocumentCategory;
use Modules\Admin\Models\DocumentNameDetail;

class AddDocumentNamesSignedEmployeeContractTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $documentNameDetailsCount = DocumentNameDetail::where('name','Signed Employee Contract')
            ->count();
        if($documentNameDetailsCount > 0) {
            throw new \Exception("Signed Employee Contract name already exists");
        }


        \DB::table('document_name_details')->insert(array(
            0 => array(
                'document_type_id' =>1,
                'document_category_id' =>49,
                'name' =>'Signed Employee Contract',
                'answer_type' => 'Modules\Admin\Models\DocumentCategory',
                'is_editable' => 0,
                'is_valid' => 1,
                'is_auto_archive' => 1,
                'created_by' => null,
                'created_at' => \Carbon\Carbon::now(),
                'updated_at' => \Carbon\Carbon::now(),

            )
        ));
        // $this->call("OthersTableSeeder");
    }
}
