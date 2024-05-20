<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InfoController extends Controller
{
    public function getServerInfo(Request $request) {
    	return response()->json(phpinfo());
    }

    public function getClientInfo(Request $request) {
    	return response()->json(['ip' => $request->ip(), 'useragent' => $request->useragent()]);
    }

    public function getDatabaseInfo(Request $request) {
    	$connection = DB::connection();
		$databaseConfig = $connection->getConfig();
		return response()->json($databaseConfig);
    }
}
