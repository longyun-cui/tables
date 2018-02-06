<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

/*
 * TEST
 */
Route::group(['prefix' => 'test'], function () {

    $controller = "TestController";

    Route::get('/', $controller.'@index');
    Route::get('/index', $controller.'@index');
});

/*
 * auth
 */
Route::match(['get','post'], 'login', 'AuthController@user_login');
Route::match(['get','post'], 'logout', 'AuthController@user_logout');
Route::match(['get','post'], 'register', 'AuthController@user_register');
Route::match(['get','post'], 'email/activation', 'AuthController@activation');

/*Common 通用功能*/
Route::group(['prefix' => 'common'], function () {

    $controller = "CommonController";

    Route::match(['get','post'], 'change_captcha', $controller.'@change_captcha');
});


Route::get('/', 'ChartController@index');
Route::get('charts', 'ChartController@index');
Route::get('chart', 'TableController@view_chart');


Route::get('admin/i18n', function () {
    return trans('pagination.i18n');
});


/*
 * Home Backend
 */
Route::group(['prefix' => 'home', 'middleware' => 'home'], function () {

    $homeController = 'HomeController';
    Route::get('/', $homeController.'@index');

    // 表格
    Route::group(['prefix' => 'table'], function () {
        $controller = "TableController";

        Route::get('/create', $controller.'@createAction');
        Route::match(['get','post'], '/edit', $controller.'@editAction');
        Route::match(['get','post'], 'list', $controller.'@viewList');

        // 数据
        Route::group(['prefix' => 'data'], function () {
            $controller = "TableController";

            Route::get('/', $controller.'@data_index');
            Route::match(['get','post'], '/edit', $controller.'@data_edit');
            Route::post('/get/add', $controller.'@data_get_add');
            Route::post('/get/edit', $controller.'@data_get_edit');
            Route::post('/delete', $controller.'@data_delete');
        });

        // 图
        Route::group(['prefix' => 'chart'], function () {
            $controller = "TableController";

            Route::get('/', $controller.'@chart_index');
            Route::match(['get','post'], '/edit', $controller.'@chart_edit');
            Route::post('/get/add', $controller.'@chart_get_add');
            Route::post('/get/edit', $controller.'@chart_get_edit');
            Route::post('/delete', $controller.'@chart_delete');
        });
    });

    // 列
    Route::group(['prefix' => 'column'], function () {
        $controller = "ColumnController";

        Route::get('/create', $controller.'@createAction');
        Route::match(['get','post'], '/edit', $controller.'@editAction');
        Route::post('delete', $controller.'@deleteAction');
        Route::post('sort', $controller.'@sortAction');
    });

    // 行
    Route::group(['prefix' => 'row'], function () {
        $controller = "RowController";

        Route::get('/create', $controller.'@createAction');
        Route::match(['get','post'], '/edit', $controller.'@editAction');
    });



});
