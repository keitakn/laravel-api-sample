<?php
/**
 * アカウントコントローラー
 *
 * @author keita-nishimoto
 * @since 2016-09-06
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 * @link https://readouble.com/laravel/5.3/ja/controllers.html
 */

// App\Http\Controllers\V1\AccountController

namespace App\Http\Controllers\V1;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Domain;

/**
 * Class AccountsController
 *
 * @category laravel-api-sample
 * @package App\Http\Controllers\V1
 * @author keita-nishimoto
 * @since 2016-09-06
 * @link https://github.com/keita-nishimoto/laravel-api-sample
 */
class AccountsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $result = [
            'controller' => 'AccountController',
            'method'     => 'index',
        ];

        return response()->json($result);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $result = [
            'controller' => 'AccountController',
            'method'     => 'create',
        ];

        return response()->json($result);
    }

    /**
     * アカウント作成
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $serviceFacade = new Domain\ServiceFacade(
            $request,
            'Account',
            'create'
        );
        $responseEntity = $serviceFacade->execute();

        return response()->json(
            $responseEntity->getBody(),
            $responseEntity->getHttpStatusCode(),
            $responseEntity->getHeader()
        );
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
