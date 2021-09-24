<?php

namespace Modules\Recruitment\Http\Controllers\Auth;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Modules\Recruitment\Models\RecCandidate;
use Modules\Recruitment\Repositories\RecCandidateRepository;


class LoginController extends Controller
{

    protected $recCandidateRepository;

    public function __construct(
        RecCandidateRepository $recCandidateRepository
    )
    {
        $this->recCandidateRepository = $recCandidateRepository;
        $this->middleware('guest:rec_candidate', ['except' => ['logout']]);
    }

    public function login(Request $request)
    {
        return $this->recCandidateRepository->candidateLogin($request);

    }

    public function resetPassword(Request $request)
    {
        $success = false;
        $candidateId = $request->get('id');
        $password = bcrypt($request->get('password'));
        $token = $request->get('token');
        try {
            if ($candidateId) {
                $candidate = RecCandidate::where('id', $candidateId)
                    ->where('reset_token', $token)
                    ->update([
                        'password' => $password,
                        'reset_token' => null,
                    ]);
                if ($candidate == 0) {
                    $status = 401;
                    $msg = 'Invalid request';
                } else {
                    $status = 200;
                    $msg = 'Success';
                }
            } else {
                $status = 400;
                $msg = 'Invalid email or password.';
            }
        } catch (\Exception $e) {
            $status = 406;
            $msg = 'Something went wrong';
            if(config('app.debug')){
                $msg = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            }
        }
        return response()->json(['message' => $msg, 'status' => $status, 'success' => $success]);

    }

    public function forgotPassword(Request $request)
    {
        $success = false;
        $candidateEmail = $request->get('email');
        $candidate = RecCandidate::where('email', $candidateEmail)->get()->first();
        try {
            if (!empty($candidateEmail) && !empty($candidate)) {
                $response = $this->recCandidateRepository->sendPasswordResetMail(
                    array('candidate-id' => $candidate->id)
                );
                $status = 200;
                $success = $response->getData()->success;
                $msg = $response->getData()->message ?? 'success';
            } else {
                $status = 400;
                $msg = 'Invalid email or password.';
            }
        } catch (\Exception $e) {
            $status = 406;
            $msg = 'Something went wrong';
            if(config('app.debug')){
                $msg = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            }
        }
        return response()->json(['message' => $msg, 'status' => $status, 'success' => $success]);

    }

    public function logout(Request $request)
    {
        try {
            RecCandidate::where('id', $request->user()->id)->update(['remember_token' => null]);
            $token = $request->user()->token();
            $token->revoke();
            Auth::guard('rec_candidate')->logout();
            $status = 200;
            $msg = 'Success';
        } catch (\Exception $e) {
            $status = 406;
            $msg = 'Something went wrong';
            if(config('app.debug')){
                $msg = $e->getMessage() . " at " . $e->getLine() . " in " . $e->getFile();
            }
        }
        return response()->json(['message' => $msg, 'status' => $status]);

    }

}
