<?php

namespace Cora\Services;

use Cora\Converters\LolaToPetrinet;
use Cora\Converters\PetrinetTranslator;
use Cora\Domain\User\UserRepository as UserRepo;
use Cora\Repositories\PetrinetRepository as PetriRepo;
use Cora\Utils\FileUploadUtils;
use Psr\Http\Message\UploadedFileInterface as File;

use Exception;

class RegisterPetrinetService {
    public function register(
        $uid,
        File $file,
        UserRepo $userRepo,
        PetriRepo $petriRepo)
    {
        $uid = filter_var($uid, FILTER_SANITIZE_NUMBER_INT);
        if (!$userRepo->userExists("id", $uid))
            throw new Exception("No user with this id exists");
        $error = $file->getError();
        if ($error != UPLOAD_ERR_OK)
            throw new Exception(FileUploadUtils::getErrorMessage($error));
        $extension = pathinfo($file->getClientFilename(), PATHINFO_EXTENSION);
        if ($extension != "lola")
            throw new Exception("Only files with a lola extension are accepted");
        $lola = $file->getStream()->getContents();
        $converter = new LolaToPetrinet($lola);
        $marked = $converter->convert();
        $translate = true;
        if ($translate) {
            $translator = new PetrinetTranslator($marked);
            $marked = $translator->convert();
        }
        $petrinetId = $petriRepo->savePetrinet($marked, $uid);
        return $petrinetId;
    }
}
