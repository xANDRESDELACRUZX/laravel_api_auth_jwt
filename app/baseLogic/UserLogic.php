<?php

namespace App\baseLogic;

use Illuminate\Http\Request;

class UserLogic
{
    public static function processCommand(Request $request)
    {
        switch ($request->typeRequest) {
           /* case '/user/list':
                return UserLogic::getList($request);

            case 'login':
                return UserLogic::create($request);

            case 'login':
                return UserLogic::create($request);

            case 'login':
                return UserLogic::create($request);
            
            case 'login':
            return UserLogic::create($request);*/
        }
    }
}