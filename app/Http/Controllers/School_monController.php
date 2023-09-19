<?php

namespace App\Http\Controllers;

use App\Models\School_money;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Auth\AuthManager;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\Console\Input\Input;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;
use Carbon\Carbon;

class School_monController extends Controller
{
    public function index()
    {
        // $user = auth()->user();
        // if ($user['isadmin'] == true) {
        $School_money = School_money::all();
        return response()->json([
            'success' => true,
            'message' => 'All School_money',
            'School_money' => $School_money
        ]);
        // } else return response()->json([
        //     'success' => false,
        //     'message' => 'access denied (Not admin)',

        // ]);
    }
    // if($request->file('child_attachements')){
    //     $file= $request->file('child_attachements');
    //     $filename= date('YmdHi').$file->getClientOriginalName();
    //     $file-> move(public_path('public/Image'), $filename);
    //     $data->child_attachements= $filename;
    // }
    // $input['child_attachements'] = $filename;
    // public function store(Request $request)
    // {
    //     $input = $request->all();
    //     $validator = Validator::make($input, [
    //         'employee_name' => 'required',
    //         'performance_num' => 'required',
    //         'child_name' => 'required',
    //         'child_BD' => 'required',
    //         'relative_exists' => 'required',
    //         'performance_num_relative'=>'nullable',
    //         'child_attachements',
    //         'status'
    //     ]);
    //     $data = new School_money();
    //     $School_money = new School_money();
    //     if ($input['relative_exists']) 
    //     {
    //         if (!(isset($input['performance_num_relative']))) {
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'you have to enter the performance of your relative ',

    //             ]);
    //         }
    //         $School_money->performance_num_relative = $request->input('performance_num_relative');
    //     }else {
    //         $School_money->performance_num_relative=0;
    //     }
    //     //will continue if no relatives or theres is relatives and the performance num exists
    //     if ($validator->fails()) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'sorry not stored ',
    //             'error' => $validator->errors()
    //         ]);
    //     }


    //     $School_money->employee_name = $request->input('employee_name');
    //     $School_money->child_name = $request->input('child_name');
    //     $School_money->performance_num = $request->input('performance_num');
    //     $School_money->child_BD = $request->input('child_BD');
    //     $School_money->relative_exists = $request->input('relative_exists');

    //     if ($request->hasFile('file')) {
    //         $file = $request->file('file');
    //         $name = time() . '.' . $file->getClientOriginalExtension();
    //         $filePath = $file->move('uploads', $name);
    //         $data->child_attachements = $name;

    //         $input['child_attachements'] = $name;
    //     }
    //     $School_money = School_money::create($input);
    //     return response()->json([
    //         'success' => true,
    //         'message' => 'School_money saved successfully ',
    //         'School_money' => $School_money
    //     ]);
    // }
    public function store(Request $request)
    {
        $input = $request->all();
        $validator = Validator::make($request->all(), [
            'employee_name' => '',
            'performance_num' => 'required',
            'child_name' => 'required',
            'child_BD' => 'required',
            'relative_exists' => 'required',
            'performance_num_relative' => 'nullable', // Add 'nullable' rule
            'file' => 'required',
            'status',
            'within_age'=>''
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Sorry, data validation failed',
                'error' => $validator->errors()
            ],404);
        }

        $School_money = new School_money();
        $School_money->performance_num = $input['performance_num'];
        $School_money->child_name = $input['child_name'];
        $School_money->child_BD = $input['child_BD'];
        $School_money->relative_exists = $input['relative_exists'];
        $School_money->within_age =  1;
        $input['within_age']=1;
        //to get the user name


        $varx=new PassportAuthController();
        $input['employee_name']=$varx->getName($input['performance_num']);
         $School_money->employee_name = $input['employee_name'];


        $birth_date = $input['child_BD'];
        $currentDate = Carbon::now();
        $dateFromDatabase = Carbon::parse($birth_date);
        $diffInDays = $dateFromDatabase->diffInDays($currentDate);
        $diffInMonths = $dateFromDatabase->diffInMonths($currentDate);
        $diffInYears = $dateFromDatabase->diffInYears($currentDate);
        //the limit for the age is between 4 and 23
        if (!(isset($input['status']))&&($diffInDays > 0 && $diffInMonths > 0 && $diffInYears >= 23) || ($diffInYears <= 4)) {
            $School_money->status = "Attention";
            $input['status'] = "Attention";
        }

        // Check if relative exists and if performance_num_relative is provided
        if ($input['relative_exists'] && !$request->has('performance_num_relative')) {
            return response()->json([
                'success' => false,
                'message' => 'You have to enter the performance of your relative',
            ],404);
        }

        // If relative exists, set performance_num_relative; otherwise, set to 0
        // $School_money->performance_num_relative = $request->input('relative_exists')
        //     ? $request->input('performance_num_relative')
        //     : NULL;

            if($request->input('relative_exists'))
            {
                $School_money->performance_num_relative = $request->input('performance_num_relative');
            }else{
                $School_money->performance_num_relative =null;
                $input['performance_num_relative']=null;
            }
            // return response()->json([
            //     'success' => false,
            //     'performance_num_relative' => $School_money->performance_num_relative,
            // ]);
        

        // If relative exists and performance_num_relative is provided, check if the relative has applied before
        if ($request->input('relative_exists') && $request->has('performance_num_relative')) {
            $performance_num_relative = $request->input('performance_num_relative');
            $partner = School_money::where('performance_num', $performance_num_relative)->first();

            if ($partner !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your relative has applied before',
                ],404);
            }
        }


        // Handle file upload
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $name = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->move('uploads', $name);
            $School_money->child_attachements = $name;
        }
        $input['child_attachements'] = $name;

        $School_money = School_money::create($input);

        return response()->json([
            'success' => true,
            'message' => 'School_money saved successfully',
            'School_money' => $School_money
        ]);
    }
    //  $partner = User::where('performance_num', $requests['partner_id'])->get()->first();

    public function checkrelative(School_money $School_money)
    {
        //check if there is a record that has the relative_id as id
        $performance_num_relative = $School_money['performance_num_relative'];
        $partner = School_money::where('performance_num', $performance_num_relative)->get()->first();
    }
    public function upload(Request $request)
    {
        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $name = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->move('uploads', $name);

            // Return response
            return response()->json([
                'success' => true,
                'message' => 'File successfully uploaded',
                'file_path' => $filePath
            ], 200);
        }

        // If no file was passed...
        return response()->json([
            'error' => true,
            'message' => 'No file uploaded'
        ], 400);
    }
    public function update(Request $request, $id)
    {
        //$user = auth()->user();
        // if ($user['isadmin'] == true) {
        $input = $request->all();
        $validator = Validator::make($input, [
           
            'performance_num' => 'required',
            'child_name' => 'required',
            'child_BD' => 'required',
            'relative_exists' => 'required',
            'performance_num_relative' => 'nullable', // Add 'nullable' rule
            'file' => 'required',
            'status',
            'within_age' => 'nullable'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'sorry not updated ',
                'error' => $validator->errors()
            ], 404);
        }
        $School_money = School_money::find($id);
        $School_money->performance_num = $input['performance_num'];
        $School_money->child_name = $input['child_name'];
        $School_money->child_BD = $input['child_BD'];
        $School_money->relative_exists = $input['relative_exists'];
        $School_money->performance_num_relative = $input['performance_num_relative'];
        $School_money->status = $input['status'];
        $School_money['child_attachements'] = $input['file'];
        $School_money->within_age = 1;

        $varx=new PassportAuthController();
        $input['employee_name']=$varx->getName($input['performance_num']);
         $School_money->employee_name = $input['employee_name'];
         
        //check if the age within the specific
        $birth_date = $input['child_BD'];
        $currentDate = Carbon::now();
        $dateFromDatabase = Carbon::parse($birth_date);
        $diffInDays = $dateFromDatabase->diffInDays($currentDate);
        $diffInMonths = $dateFromDatabase->diffInMonths($currentDate);
        $diffInYears = $dateFromDatabase->diffInYears($currentDate);
        //the limit for the age is between 4 and 23
        if (!(isset($input['status']))&&(($diffInDays > 0 && $diffInMonths > 0 && $diffInYears >= 23) || ($diffInYears <= 4))) {
            $School_money->status = "Attention";
        }


        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $name = time() . '.' . $file->getClientOriginalExtension();
            $filePath = $file->move('uploads', $name);
            $School_money->child_attachements = $name;
            $School_money['child_attachements'] = $name;
        }
        $input['file'] = $School_money->child_attachements;

        // Check if relative exists and if performance_num_relative is provided
        if ($input['relative_exists'] && !$request->has('performance_num_relative')) {
            return response()->json([
                'success' => false,
                'message' => 'You have to enter the performance of your relative',
            ],404);
        }

        // If relative exists, set performance_num_relative; otherwise, set to 0
        $School_money->performance_num_relative = $request->input('relative_exists')
            ? $request->input('performance_num_relative')
            : NULL;

        // If relative exists and performance_num_relative is provided, check if the relative has applied before
        if ($request->input('relative_exists') && $request->has('performance_num_relative')) {
            $performance_num_relative = $request->input('performance_num_relative');
            $partner = School_money::where('performance_num', $performance_num_relative)->first();

            if ($partner !== null) {
                return response()->json([
                    'success' => false,
                    'message' => 'Your relative has applied before',
                ],404);
            }
        }

        //you have to make save to update it in the database
        $School_money->save();

        return response()->json([
            'success' => true,
            'message' => 'School_money update successfully ',
            'School_money' => $School_money
        ], 200);
    }
    public function confirmed($performance)
    {
        $School_money_req = School_money::where('performance_num', $performance)->get()->first();

        // $user=auth()->user();
        // if($user['isadmin']){
        $School_money_req->status = 'confirmed';
        $School_money_req->save();
        return response()->json([
            'success' => true,
            'message' => 'Status of the request has been changed',

        ], 200);
        // }else {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Access denied (not admin)',

        //     ]);
        // }
    }
    public function rejected($performance)
    {
        // $user=auth()->user();
        $School_money_req = new School_money();
        $School_money_req = School_money::where('performance_num', $performance)->get()->first();
        // if($user['isadmin']){
        $School_money_req->status = 'rejected';
        $School_money_req->save();
        return response()->json([
            'success' => true,
            'message' => 'Status of the request has been changed',

        ], 200);
        // }else {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'Access denied (not admin)',

        //     ]);
        // }
    }
    public function destroy(School_money $School_money, $id)
    {
        // $user = auth()->user();
        // if ($user['isadmin'] == true) {
        $School_money = School_money::find($id);
        $School_money->delete();

        return response()->json([
            'success' => true,
            'message' => 'School_money has been deleted successfully ',
            'School_money' => $School_money
        ], 200);
        // } else {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'access denied (Not admin)',

        //     ]);
        // }
    }
    //get the files of the school money 
    public function sendfile(Request $request, $filename)
    {

        $imagePath = public_path('uploads/' . $filename);

        if (file_exists($imagePath)) {
            $fileContents = file_get_contents($imagePath);

            // Determine the image MIME type (e.g., 'image/jpeg', 'image/png', etc.).
            $mimeType = mime_content_type($imagePath);

            // Set appropriate headers to indicate the image type.
            $headers = [
                'Content-Type' => $mimeType,
            ];

            // Return the image as a response with headers.
            return Response::make($fileContents, 200, $headers);
        } else {
            abort(404);
        }
    }

    // Helper function to get the content type from file extension
    private function getContentTypeFromExtension($fileExtension)
    {
        switch ($fileExtension) {
            case 'jpg':
            case 'jpeg':
                return 'image/jpeg';
            case 'png':
                return 'image/png';
            case 'gif':
                return 'image/gif';
                // Add more cases for other file types if needed
            default:
                return 'application/octet-stream'; // Default to binary data
        }
    }
    public function latest_schoolmoney($pn){
        return School_money::where('created_at',School_money::where('performance_num',$pn)->where('status','confirmed')->max('created_at'))->first();
    }
}