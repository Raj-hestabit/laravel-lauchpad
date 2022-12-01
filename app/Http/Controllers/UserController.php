<?php

namespace App\Http\Controllers;

use App\Events\RequestApprove;
use App\Http\Resources\UserResource;
use App\Models\User;
use App\Models\UserDetails;
use Illuminate\Auth\Events\Registered;
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
        if(Auth::user()->user_type == 1){
            return response()->json([
                'status'    => 'failure',
                'message'   => 'You are admin'
            ]);
        }
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
            $location   = 'storage/users';
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
                'status'    => 'success',
                'message'   => 'Profile updation request sent to Admin',
                'user'      => new UserResource($user)
            ]);
        }else{
            return response()->json([
                'status'    => 'failure',
                'message'   => 'Something went wrong',
                'user'      => new stdClass()
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
        if(Auth::user()->user_type != 1){
            if($id != Auth::user()->id){
                return response()->json([
                    'status'    => 'failure',
                    'message'   => 'You are not authorized person to view this details',
                ],400);
            }
        }

        $user = User::with('UserType','UserDetails')->find($id);
        if($user){
            return response()->json([
                'status'    => 'success',
                'message'   => 'User details',
                'user'      => new UserResource($user)
            ]);
        }else{
            return response()->json([
                'status'    => 'failure',
                'message'   => 'User not found',
                'user'      => new stdClass()
            ],404);
        }
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
            event(new RequestApprove($user));
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


    public function pendingRequestUsers(){
        $users = User::with(['UserType', 'UserDetails'])->whereHas('UserDetails',function($q){
            $q->where('status', 0);
        })->where('user_type','!=',1)->get();

        if($users){
            return response()->json([
                'status'    => 'success',
                'message'   => 'Pending request users list',
                'users'     => UserResource::collection($users)
            ]);
        } else {
            return response()->json([
                'status'    => 'failure',
                'users'     => [],
                'message'   => 'No request found'
            ]);
        }
    }

    public function notifications(){
        $notifications = auth()->user()->unreadNotifications;
        if($notifications){
            return response()->json([
                'status'        => 'success',
                'message'       => 'Requests notifications',
                'notifications' => $notifications
            ]);
        } else {
            return response()->json([
                'status'        => 'failure',
                'message'       => 'No request found',
                'notifications' => []
            ]);
        }
    }
}
