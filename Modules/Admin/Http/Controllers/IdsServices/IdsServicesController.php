<?php

namespace Modules\Admin\Http\Controllers\IdsServices;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;

use App\Services\HelperService;
use Modules\Admin\Models\IdsOfficeServiceAllocation;
use Modules\Admin\Models\IdsOffice;

use Modules\Admin\Repositories\IdsServicesRepository;
use Modules\Admin\Http\Requests\IdsServicesRequest;
use Modules\IdsScheduling\Repositories\IdsEntriesRepositories;
use Modules\Expense\Repositories\TaxMasterRepository;
class IdsServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */

    public function __construct(
        IdsServicesRepository $idsServicesRepository,
        HelperService $helperService,
        IdsEntriesRepositories $idsEntriesRepositories,
        TaxMasterRepository $taxMasterRepository
        )
    {
        $this->repository = $idsServicesRepository;
        $this->helperService = $helperService;
        $this->idsEntriesRepositories = $idsEntriesRepositories;
        $this->taxMasterRepository = $taxMasterRepository;
    }
    public function index()
    {
        $office = IdsOffice::all()->pluck('name', 'id')->toArray();
        $taxes = $this->taxMasterRepository->getList()->toArray();
        return view('admin::ids-scheduling.service',compact('office','taxes'));
    }

    /**
     * list all data.
     * @return Response
     */
    public function getAll()
    {
        return datatables()->of($this->repository->getAll())->addIndexColumn()->toJson();
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(IdsServicesRequest $request)
    {
        try {
            \DB::beginTransaction();
            $inputs = $request->all();
            unset($inputs['office_ids']);
            $result = $this->repository->store($inputs);
            $data['ids_service_id'] = $result->id;

            if($request->filled('id')){ //-- On service edit ---- Office Allocation and Remove

                $allocatedOfficeIds = IdsOfficeServiceAllocation::where('ids_service_id',$request->input('id'))
                ->pluck('ids_office_id')
                ->toArray();

               //Create new office allocation
               $newOfficeAllocation =array_diff($request->input('office_ids'),$allocatedOfficeIds);
               foreach($newOfficeAllocation as $office){
                $data['ids_office_id']= $office;
                IdsOfficeServiceAllocation::create($data);
                }
               //Remove office allocation
               $removeOfficeAllocation =array_diff($allocatedOfficeIds,$request->input('office_ids'));
               if(!empty($removeOfficeAllocation)){
                IdsOfficeServiceAllocation::where('ids_service_id',$request->input('id'))
                ->whereIn('ids_office_id',$removeOfficeAllocation)
                ->delete();
               }

            }else{ //-- On service creation ---- Office Allocation
                foreach($request->input('office_ids') as $office){
                    $data['ids_office_id']= $office;
                    IdsOfficeServiceAllocation::create($data);
                }
            }

            \DB::commit();
         return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        // return view('admin::show');
        return $this->repository->getAll();
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        return view('admin::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            \DB::beginTransaction();
            $inputs['ids_service_id'] = $id;
            $booking = $this->idsEntriesRepositories->getBookings($inputs);
            if($booking >= 1){
                $return = array('warning' => true,'message'=>"Service booking exists");
                return response()->json($return);
            }else{
                $this->repository->destroy($id);
                IdsOfficeServiceAllocation::where('ids_service_id',$id)->delete();
            }

            \DB::commit();
            return response()->json($this->helperService->returnTrueResponse());
        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json($this->helperService->returnFalseResponse());
        }

    }

    public function getById($id){
        return $this->repository->getById($id);
    }
}
