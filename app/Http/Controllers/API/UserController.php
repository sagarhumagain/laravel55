<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User; //api route controllor from api php file
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api');//constructor for authenticated user or api authentication
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return User::latest()->paginate(10); 
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //validation function
        $this ->validate($request,[

            "name" => 'required|string|max:191',
            "email" => 'required|string|email|max:191|unique:users',
            "password" => 'required|string|min:6'

        ]);
        //save all data in database
        return User::create([
            'name' => $request['name'],
            'type' => $request['type'],
            'email' => $request['email'],
            'bio' => $request['photo'],
            'photo' => $request['email'],
            'password' => Hash::make($request['password'])        
        ]);  
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
    //update info of profile
    public function updateProfile(Request $request)
    {
        $user = auth('api')->user(); //updating data for profile component (image)
        //return ['message' => 'success'];

        $this ->validate($request,[

            "name" => 'required|string|max:191',
            //validate email used previously
            "email" => 'required|string|email|max:191|unique:users,email,'.$user->id,
            "password" => 'sometimes|required|min:6'

        ]);
        //uploading image
        $CurrentPhoto = $user->photo; //assigning current image name to database name(uniquely defined)
        //if user upload diffetent image then it is not going to be the same name and upload it 
        if($request->photo != $CurrentPhoto){
            $name =time().'.'.explode('/', explode(':', substr($request->photo, 0,strpos($request->photo, ';')))[1])[1];//extracting extention of image
            \Image::make($request->photo)->save(public_path('images/profile/').$name);//using image intervention
            $request->merge(['photo' => $name]);

            //defining variable for previous photo  to delete which is (currentphoto)
            $userPhoto = public_path('images/profile/').$CurrentPhoto;
            if(file_exists($userPhoto)){ //delete function
                @unlink($userPhoto);
            }
        }

        if(!empty($request->password)){
            $request->merge(['password'=> Hash::make($request ['password'])]);
        }
        //update user info 
        $user->update($request->all());

        return ['message' => "success"];
    }

    public function profile()
    {
        return auth('api')->user(); //showing data for profile component
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
        //return ['message ' => 'updating'];

        $user = User::findOrFail($id);
        $this ->validate($request,[

            "name" => 'required|string|max:191',
            //validate email used previously
            "email" => 'required|string|email|max:191|unique:users,email,'.$user->id,
            "password" => 'sometimes|string|min:6'

        ]);

        $user->update($request->all());
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        //delete user
        $user-> delete();

        // return ['message' => 'User Deleted'];
    }
}
