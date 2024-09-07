<?php


namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Auth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    protected $firebaseAuth;

    public function __construct(Auth $firebaseAuth)
    {
        $this->firebaseAuth = $firebaseAuth;
    }

    public function otpSend(Request $request)
    {
        $validatedData = validate($request, [
            'phone' => 'required|string',
        ]);
        
        $phoneNumber = $request->input('phone');
        
        
        
        try {
            return response()->json(['message' => 'OTP sent successfully']);
            die('in');
            // $this->firebaseAuth->signInWithPhoneNumber($phoneNumber);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }


    public function otpVerify(Request $request)
    {
        $validatedData = validate($request, [
            'otp' => 'required|string',
        ]);

        $otp = $request->input('otp');

        try {
            $verifiedIdToken = $this->firebaseAuth->verifyIdToken($otp);
            $uid = $verifiedIdToken->claims()->get('sub');

            // business logic

            return response()->json(['uid' => $uid], 200);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 400);
        }
    }
}

