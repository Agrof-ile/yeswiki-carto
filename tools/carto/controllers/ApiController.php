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
     * @Route("/api/carto/map_form_id", options={"acl":{"public"}})
     */
    public function get_map_form_id(Request $request)
    {
        $str = file_get_contents("tools/carto/map_form_id.json");
        $json = json_decode($str, true);
        return new ApiResponse($json);
    }

    /**
     * @Route("/api/carto/ruzip", options={"acl":{"public"}})
     */
    public function remove_underscore_zip(Request $request)
    {
        $output = shell_exec("tools/carto/controllers/ruzip.sh");
        return new ApiResponse($output);
    }
}
