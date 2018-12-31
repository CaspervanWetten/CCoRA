<?php

namespace Cozp\Controllers;

use Cozp\Utils as Utils;
use Cozp\Models as Models;
use Cozp\Systems as Systems;
use Cozp\Systems\Petrinet as Petrinet;
use Cozp\Converters as Converters;
use Cozp\SystemCheckers as Checkers;
use Cozp\Logger as Logger;
use Slim\Http\UploadedFile as UploadedFile;
use Psr\Http\Message\RequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

class PetrinetController extends Controller
{
    /**
     * Get a petrinet given its id
     *
     * @param Request $request
     * @param Response $response
     * @param [type] $args
     * @return Response with Json
     */
    public function getPetrinet(Request $request, Response $response, $args)
    {
        $id = $args["id"];
        $id = intval(filter_var($id, FILTER_SANITIZE_NUMBER_INT));

        $model = new Models\PetrinetModel($this->container->get('db'));

        $petrinet = $model->getPetrinet($id);
        $converter = new Converters\PetrinetToJson($petrinet);
        $p = $converter->convert();
        
        if(!is_null($petrinet)) {
            return $response->withJson(
                $p
            );
        } else {
            return $this->showErrors($response, "The petrinet could not be found.", 404);
        }
    }
    /**
     * Browse petrinets. Optionally with a limit and offset
     *
     * @param Request $request
     * @param Response $response
     * @param [type] $args
     * @return Response with Json
     */
    public function getPetrinets(Request $request, Response $response, $args)
    {
        $limit = 0;
        $page  = 1;
        if(array_key_exists("limit", $args)) {
            $limit = intval(filter_var(trim($args["limit"]), FILTER_SANITIZE_NUMBER_INT));
        }

        if(array_key_exists("page", $args)) {
            $page = intval(filter_var(trim($args["page"]), FILTER_SANITIZE_NUMBER_INT));
        }

        $model = new Models\PetrinetModel($this->container->get('db'));
        $res = $model->getPetrinets($limit, $page - 1);
        $router = $this->container->get('router');
        foreach($res as $i => $net) {
            $res[$i]["url"] = $router->pathFor("getPetrinet", ["id" => $net["id"]]);
            $res[$i]["imageUrl"] = $router->pathFor("getPetrinetImage", ["id" => $net["id"]]);
        }
        $nextPage = $page + 1;
        $prevPage = max(1, $page - 1);

        return $response->withJson([
            "petrinets" => $res,
            "nextPage"  => $router->pathFor("getPetrinets", ["limit" => $limit, "page" => $nextPage]),
            "prevPage"  => $router->pathFor("getPetrinets", ["limit" => $limit, "page" => $prevPage])
        ]);
    }

    /**
     * Get the image belonging to a petrinet given the petrinet's id
     *
     * @param Request $request
     * @param Response $response
     * @param [type] $args
     * @return Response with Json
     */
    public function getImage(Request $request, Response $response, $args)
    {
        $id = $args["id"];
        $id = intval(filter_var($id, FILTER_SANITIZE_NUMBER_INT));

        $model = new Models\PetrinetModel($this->container->get('db'));
        $petrinet = $model->getPetrinet($id);
        $image = $this->generateImage($petrinet);

        return $response->withJson($image);
    }

    /**
     * Register a petrinet into the database
     *
     * @param Request $request
     * @param Response $response
     * @param [type] $args
     * @return Response with Json
     */
    public function setPetrinet(Request $request, Response $response, $args)
    {
        $userId = $args['id'];
        $file = $request->getUploadedFiles()['petrinet'];

        $model = new Models\UserModel($this->container->get('db'));
        if(!$model->userExists($userId)) {
            return $this->showErrors($response, "This user does not exist", 400);
        }

        if(!is_null($file))
            $error = $file->getError();
        else
            $error = UPLOAD_ERR_NO_FILE;
        // No upload errors
        if ($error === UPLOAD_ERR_OK)
        {
            $fileExtension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
            // wrong file extension
            if ($fileExtension != "lola") {
                return $this->showErrors($response, "Only files with a lola extension are accepted", 400);
            }
            // correct file extension, place in file system
            $filename = $this->moveUploadedFile(
                $file,
                USER_FOLDER . DIRECTORY_SEPARATOR . $userId
            );
            // generate image
            $k = new Converters\LolaToPetrinet(USER_FOLDER . DIRECTORY_SEPARATOR . $userId . DIRECTORY_SEPARATOR . $filename);
            $petrinet = $k->convert();

            $translate = true;

            if($translate) {
                $translator = new Converters\PetrinetTranslator($petrinet);
                $petrinet = $translator->convert();
            }

            $model = new Models\PetrinetModel($this->container->get('db'));
            $petrinetId = $model->setPetrinet($petrinet, $userId);

            $response = $response->withJson([
                "petrinetId" => $petrinetId,
                "petrinetUrl" => $this->container->get('router')->pathFor('getPetrinet', ["id" => $petrinetId])
            ]);

            // cleanup the file system.
            unlink(USER_FOLDER . DIRECTORY_SEPARATOR . $userId . DIRECTORY_SEPARATOR . $filename);
            rmdir(USER_FOLDER . DIRECTORY_SEPARATOR . $userId);

            return $response;
        }
        else {
            return $this->showErrors($response, \Cozp\Utils\FileUploadUtils::getErrorMessage($error), 400);
        }
    }

    public function getFeedback(Request $request, Response $response, $args)
    {
        $user  = filter_var($args['user_id'], FILTER_SANITIZE_NUMBER_INT);
        $pid   = filter_var($args["petrinet_id"], FILTER_SANITIZE_NUMBER_INT);
        $sid   = filter_var($args["session_id"], FILTER_SANITIZE_NUMBER_INT);

        $model = new Models\PetrinetModel($this->container->get('db'));
        $petrinet = $model->getPetrinet($pid);
        $graph = $request->getParsedBody();

        $converter = new Converters\JsonToCoverabilityGraph($graph, $petrinet);
        $graph = $converter->convert();
        
        $checker = new Checkers\CheckCoverabilityGraph($graph, $petrinet);      
        $feedback = $checker->check();
        
        Logger::appendGraph($user, $graph, $sid, $pid);

        return $response->withJson($feedback);
    }

    /**
     * Generate an image given a petrinet object
     *
     * @param PetriNet $petrinet
     * @return string The SVG string
     */
    protected function generateImage($petrinet)
    {
        $converter = new Converters\PetrinetToDot($petrinet, true);

        $tempDot = tempnam(USER_FOLDER, "");
        $handleDot = fopen($tempDot, "w+");

        fwrite($handleDot, $converter->convert());
        fclose($handleDot);

        $tempSvg = tempnam(USER_FOLDER, "");

        $dotCommand = sprintf("%s -Tsvg \"%s\" -o \"%s\" 2>&1", DOT_PATH, $tempDot, $tempSvg);
        $out = exec($dotCommand);

        $res = file_get_contents($tempSvg);

        unlink($tempDot);
        unlink($tempSvg);

        return $res;
    }
    /**
     * Encodes an image given a filename and format in base64.
     * Function was written by "luke", @see http://php.net/manual/en/function.base64-encode.php
     **/
    protected function base64EncodeImage ($filename ,$filetype ) {
        if ($filename) {
            $imgbinary = fread(fopen($filename, "r"), filesize($filename));
            return 'data:image/' . $filetype . ';base64,' . base64_encode($imgbinary);
        }
    }
}
?>
