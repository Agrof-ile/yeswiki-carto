<?php

namespace YesWiki\Carto\Controller;

use Symfony\Component\HttpFoundation\Request;
// use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use YesWiki\Core\ApiResponse;
use YesWiki\Core\YesWikiController;

class ApiController extends YesWikiController
{
    /**
     * @Route("/api/carto/form", options={"acl":{"public"}})
     */
    public function sayHello2form(Request $request)
    {
        // $form = $_GET["/data/form_res.json"];
        $str = file_get_contents("data/form_res.json");
        $json = json_decode($str, true);
        return new ApiResponse($json);
        // echo("Flûte");
        // $action = $request->get('action') ?? 'hello';
        // return new ApiResponse([$action => $name]);
    }

    /**
     * @Route("/api/carto/shp/{name}", options={"acl":{"public"}})
     */
    public function sayHello2shp(Request $request, $name)
    {
        // $form = $_GET["/data/form_res.json"];
        $str = file_get_contents(sprintf("data/shp/%s.zip", $name));
        $json = json_decode($str, true);
        return new ApiResponse($json);
        // echo("Flûte");
        // $action = $request->get('action') ?? 'hello';
        // return new ApiResponse([$action => $name]);
    }
}
