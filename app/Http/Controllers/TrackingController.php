<?php

namespace App\Http\Controllers;

use App\Models\UrlStatus;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class TrackingController extends BaseController
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){

        $user = Auth::user();
        $url_status = UrlStatus::getUrlsByUserId($user['id']);
        return $this->sendResponse($url_status, 'Url status recovered.');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){

        $validator = Validator::make($request->all(), [
            'url' => 'required',
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
   
        $input = $request->all();

        try {
            $user = Auth::user();
            $input['user_id'] = $user['id'];
            //dd($input);
            $url_status = UrlStatus::create($input);
            return $this->sendResponse($url_status, 'Url register successfully.');
        } catch (\Throwable $th) {
            error_log($th);
            return $this->sendError('Error in Create Url.', ['error'=>'Error 500']);
        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id){
        
        try {
            $user = Auth::user();
            UrlStatus::where('id', $id)
                        ->where('user_id', $user['id'])
                        ->delete();
        } catch (\Throwable $th) {
            error_log($th);
            $this->sendError('Error in delete Url.', ['error'=>'Error 500']);
        }

    }

    /**
     * Check status Urls.
     *
     * @param  void
     * @return void
     */
    public function checkUrls(Request $request){

        if (env('DB_HOST') != $request->ip()) abort(404);

        $current = Carbon::now();
        $urls = UrlStatus::whereTime('status_verified_at', '<=', $current->format('Y-m-d H:i:s'))
                            ->orWhere('status_verified_at', null)
                            ->get();
        foreach($urls as $url){
            $result = $this->getResultCheck($url->url);
            $url->status_code = $result['status_code'];
            $url->body = $result['body'];
            $current_new = Carbon::now()->addMinutes(env('INTERVAL_CHECK_URL'));
            $url->status_verified_at = $current_new->format('Y-m-d H:i:s');
            $url->save();
        }
        return response(200);
    }


    /**
     * Get status_code and body of url.
     *
     * @param  string $url
     * @return array
     */
    public function getResultCheck($url){

        try {
            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_RETURNTRANSFER => 1,
                CURLOPT_URL => $url
            ]);
            $response = curl_exec($curl);
            $http_status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);
            return array(
                'status_code' => $http_status,
                'body' => $response
            );

        } catch (\Throwable $th) {
            return array(
                'status_code' => 400,
                'body' => $th
            );
        }



    }

}
