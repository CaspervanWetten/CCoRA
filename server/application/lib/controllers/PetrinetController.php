<?php

namespace Cora\Controllers;

use \Cora\Utils as Utils;
use \Cora\Models as Models;
use \Cora\Systems as Systems;
use \Cora\Systems\Petrinet as Petrinet;
use \Cora\Converters as Converters;
use \Cora\SystemCheckers as Checkers;
use \Cora\Exceptions\CoraException as CoraException;

use \Slim\Http\UploadedFile as UploadedFile;
use \Psr\Http\Message\RequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;

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
        
        if(!is_null($petrinet)) {
            $converter = new Converters\PetrinetToJson($petrinet);
            $p = $converter->convert();
            return $response->withJson(
                $p
            );
        } else {
            throw new CoraException("The Petri net could not be found", 404);
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
        $limit = isset($args["limit"]) ?
            min(MAX_PETRINET_RESULT_SIZE,
                filter_var($args["limit"], FILTER_SANITIZE_NUMBER_INT)) :
            MAX_PETRINET_RESULT_SIZE;
        $page = isset($args["page"]) ?
            filter_var($args["page"], FILTER_SANITIZE_NUMBER_INT) :
            1;
        $paginator = new Utils\Paginator($limit, $page);
        $model = new Models\PetrinetModel($this->container->get('db'));
        $petrinets = $model->getPetrinets($paginator->limit(), $paginator->offset());
        // set up the response
        $router = $this->container->get('router');
        foreach($petrinets as $i => $petrinet) {
            $pid = $petrinet["id"];
            $petrinets[$i]["url"] = $router->pathFor("getPetrinet", ["id" => $pid]);
            $petrinets[$i]["image_url"] = $router->pathFor("getPetrinetImage", ["id" => $pid]);
        }
        $nextPage = $paginator->next()->page();
        $prevPage = $paginator->prev()->page();
        return $response->withJson([
            "petrinets" => $petrinets,
            "next_page" => $router->pathFor("getPetrinets", ["limit" => $limit, "page" => $nextPage]),
            "prev_page" => $router->pathFor("getPetrinets", ["limit" => $limit, "page" => $prevPage])
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
        if(!$model->petrinetExists($id)) {
            throw new CoraException("A Petri net with this id does not exist", 404);
        }
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
            throw new CoraException("This user does not exist", 400);
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
                throw new CoraException("Only files with a lola extension are accepted", 400);
            }
            // correct file extension, place in file system
            try {
                // file name for temp file
                $lolaFilename = USER_FOLDER
                    . DIRECTORY_SEPARATOR
                    . $userId
                    . DIRECTORY_SEPARATOR
                    . date("Y-m-d-H:i:s");
                // directory name for the temp file
                $userDir = USER_FOLDER . DIRECTORY_SEPARATOR . $userId;
                // create directory
                Utils\FileUtils::mkdir($userDir, 0711);
                // move uploaded file to directory with new file name
                $file->moveTo($lolaFilename);
                $k = new Converters\LolaToPetrinet($lolaFilename);
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
            } finally {
                // cleanup the file system.
                unlink($lolaFilename);
                rmdir($userDir);
            }

            return $response;
        }
        else {
            throw new CoraException(\Cora\Utils\FileUploadUtils::getErrorMessage($error), 400);
        }
    }

    public function getFeedback(Request $request, Response $response, $args)
    {
        $user  = filter_var($args['user_id'],     FILTER_SANITIZE_NUMBER_INT);
        $pid   = filter_var($args["petrinet_id"], FILTER_SANITIZE_NUMBER_INT);
        $sid   = filter_var($args["session_id"],  FILTER_SANITIZE_NUMBER_INT);

        $userModel = new Models\UserModel($this->container->get('db'));
        if(!$userModel->userExists($user)) {
            throw new CoraException("Could not receive feedback as the user does not exist", 404);
        }
        $petrinetModel = new Models\PetrinetModel($this->container->get('db'));
        if(!$petrinetModel->petrinetExists($pid)) {
            throw new CoraException("Could not receive feedback for Petri net as it does not exist", 404);
        }
        $petrinet = $petrinetModel->getPetrinet($pid);
        $jsonGraph = $request->getParsedBody();

        $converter = new Converters\JsonToGraph($jsonGraph, $petrinet);
        $graph = $converter->convert();

        $checker = new Checkers\CheckCoverabilityGraph($graph, $petrinet);      
        $feedback = $checker->check();

        $sessionModel = new Models\SessionModel();
        if($sessionModel->appendGraph($user, $sid, $graph) === FALSE) {
            throw new CoraException("Could not log graph", 500);
        }

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
}
?>
