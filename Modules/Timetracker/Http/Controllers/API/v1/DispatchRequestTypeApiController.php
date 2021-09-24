<?php

namespace Modules\Timetracker\Http\Controllers\API\v1;

use Illuminate\Routing\Controller;
use Modules\Timetracker\Repositories\DispatchRequestTypeRepository;

class DispatchRequestTypeApiController extends Controller
{
    protected $repository;

    public function __construct(DispatchRequestTypeRepository $repository)
    {
        $this->repository = $repository;
    }

    public function list()
    {
        return response()->json([
            "success" => true,
            "content" => $this->repository->getAll()],200);
    }
}
