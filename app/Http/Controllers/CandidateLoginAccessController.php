<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Modules\Recruitment\Models\RecCandidate;
use Modules\Recruitment\Repositories\RecCandidateRepository;

class CandidateLoginAccessController extends Controller
{

    /**
     * @var RecCandidateRepository
     */
    private $recCandidateRepository;

    public function __construct(RecCandidateRepository $recCandidateRepository)
    {
        $this->recCandidateRepository = $recCandidateRepository;
    }

    /**
     * To update the lattitud and longitude
     *
     * @param Request $request
     * @return void
     */
    public function loginAccess(Request $request)
    {
        try {
            if ($request->hasValidSignature()) {
                $candidate = RecCandidate::where([
                    'id' => $request->get('user'),
                    'reset_token' => $request->get('token'),
                ])->first();
                if ($candidate->id) {
//                    $resetToken = $this->recCandidateRepository->generateResetToken($candidate->id);
                    $resetToken = $request->get('token');
                    RecCandidate::where('id', $candidate->id)->update(
                        ['is_activated' => 1]
                    );
                   // dd(Redirect::to(config('globals.recruitmentUrl') . '/reset-password/' . $request->get('user') . '/' . $resetToken));
                    return Redirect::to(config('globals.recruitmentUrl') . '/reset-password/' . $request->get('user') . '/' . $resetToken);
                } else {
                    return Redirect::to(config('globals.recruitmentUrl') . '/error');
                }
            } else {
                return Redirect::to(config('globals.recruitmentUrl') . '/error');
            }
        } catch (\Exception $e) {
            \DB::rollBack();
            return Redirect::to(config('globals.recruitmentUrl') . '/error');
          //  return response()->json(array('success' => false, 'message' => $e->getMessage()));
        }
    }
}
