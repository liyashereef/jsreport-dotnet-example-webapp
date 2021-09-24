<?php

namespace Modules\Osgc\Http\Controllers\Osgc;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Osgc\Repositories\OsgcUserRepository;
class OsgcController extends Controller
{
    protected $osgcUserRepository;

    public function __construct(
    OsgcUserRepository $osgcUserRepository
    ){
       
        $this->osgcUserRepository = $osgcUserRepository;
        
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        return view('osgc::osgc.registered-users');
       
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function getList(Request $request)
    {
        $input['course_completion_status'] = $request->course_completion_status;
        return datatables()->of($this->osgcUserRepository->getRegisteredUsers($input))->addIndexColumn()->toJson();
    }

    
}
