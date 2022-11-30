<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\URL;
use stdClass;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        dd(Auth::user());
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $userId = Auth::user()->id;
        if(empty($userId)) {
            return response()->json([
                'user'      => new stdClass(),
                'status'    => 'failure',
                'message'   => 'Please login first'
            ]);
        }

        $fileUrl = null;
        if($request->hasFile('profile_picture')){
            $file       = $request->file('profile_picture');
            $filename   = time().'_'.$file->getClientOriginalName();
            $location   = 'users';
            $file->move($location,$filename);
            $fileUrl    = URL::to('/').'/'.$location.'/'.$filename;
            $request->request->add(['profile_picture_url' => $fileUrl]);
        }
        $request->request->add(['status' => 0]);

        $userDetails = UserDetails::updateOrCreate(['user_id' => $userId],
                        $request->all());

        if($userDetails){
            $user = User::with('UserDetails', 'UserType')->find($userId);
            $user->name = $request->name;
            $user->save();
            return response()->json([
                'user'      => new UserResource($user),
                'status'    => 'success',
                'message'   => 'Profile updation request sent to Admin'
            ]);
        }else{
            return response()->json([
                'user'      => new stdClass(),
                'status'    => 'failure',
                'message'   => 'Something went wrong'
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        if(!$user){
            return response()->json([
                'status'    => 'failure',
                'message'   => 'User not found'
            ]);
        }

        DB::beginTransaction();
        try {
            $user = User::find($id)->delete();
            $userDetails = UserDetails::where('user_id',$id)->delete();
            DB::commit();
            if($userDetails && $user){
                return response()->json([
                    'status'    => 'success',
                    'message'   => 'User deleted successfully'
                ]);
            } else {
                return response()->json([
                    'status'    => 'failure',
                    'message'   => 'something went wrong'
                ]);
            }
        } catch (\Exception $ex) {
            DB::rollback();
            return response()->json([
                'status'    => 'failure',
                'message'   => 'something went wrong',
                'error'     => $ex->getMessage()
            ]);
        }


    }

    public function approveRequest(Request $request, $id){
        $user = User::find($id);
        if(!$user){
            return response()->json([
                'status'    => 'failure',
                'message'   => 'User not found'
            ]);
        }
        $userDetails = UserDetails::where('user_id', $id)->where('status', 0)->first();
        if(!$userDetails){
            return response()->json([
                'status'    => 'failure',
                'message'   => 'Request not found for this user'
            ]);
        }

        $approve = UserDetails::updateOrCreate(['user_id' => $id],
                        $request->all());

        if($approve){
            return response()->json([
                'status'    => 'success',
                'message'   => 'Request approve successfully'
            ]);
        } else {
            return response()->json([
                'status'    => 'failure',
                'message'   => 'something went wrong'
            ]);
        }

    }
}
