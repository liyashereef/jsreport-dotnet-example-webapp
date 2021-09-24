<?php

namespace Modules\Documents\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Documents\Models\Document;
use Modules\Hranalytics\Models\CandidateEmployee;
use Modules\Admin\Models\DocumentCategory;
use Modules\Admin\Models\DocumentNameDetail;
use Modules\Documents\Repositories\DocumentsRepository;
use Illuminate\Support\Facades\Auth;

class AddTransitionAttachmentToDocumentTableSeeder extends Seeder
{
    protected $documentRepository;

    /**
     * Create a new CustomerIncidentPriority instance.
     *
     * @param \App\Models\CustomerIncidentPriority $customerIncidentPriorityModel
     */
    public function __construct(DocumentsRepository $documentRepository)
    {
        $this->documentRepository = $documentRepository;
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $userDetails = CandidateEmployee::with(['user', 'attachment'])->get();
        $documentCategoryDetails = DocumentCategory::where('document_category', 'Agreement');
        $documentNameDetails = DocumentNameDetail::where('name', 'Signed Employee Contract');
        if (count($documentCategoryDetails->get()) > 1 || count($documentNameDetails->get()) > 1) {
            throw new \Exception("Invalid category or name data found");
        } else {
            $documentCategoryDetailsId = $documentCategoryDetails->first()->id;
            $documentNameDetailsId = $documentNameDetails->first()->id;
            $attachmentIdArr = Document::where('document_type_id', EMPLOYEE)
                ->where('document_category_id', $documentCategoryDetailsId)
                ->where('document_name_id', $documentNameDetailsId)
                ->pluck('attachment_id')
                ->toArray();

            foreach ($userDetails as $userData) {
                if (in_array($userData->attachment->attachment_id, $attachmentIdArr)) {
                    continue;
                }
                $data['document_type_id'] = EMPLOYEE;
                $data['document_category_id'] = $documentCategoryDetailsId;
                $data['document_name_id'] = $documentNameDetailsId;
                $data['user_id'] = $userData->user_id;
                $data['answer_type'] = $this->documentRepository->getCategoryModels(null, $data['document_category_id']);
                $data['attachment_id'] = $userData->attachment->attachment_id;
                $data['created_by'] = 1;
                $data['created_at'] = \Carbon\Carbon::now();
                $data['updated_at'] = \Carbon\Carbon::now();
                \DB::table('documents')->insert($data);
            }

        }

    }
}
