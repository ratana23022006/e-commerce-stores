<?php
    function apiResponse($data=null,$status=0,$msg=""){
        return response()->json([
            'data'=>$data,
            'status'=>$status,
            'msg'=>$msg
        ]);
    }