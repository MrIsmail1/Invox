<?php

namespace App\Components\common;

use App\Entity\Media;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\UX\LiveComponent\Attribute\AsLiveComponent;
use Symfony\UX\LiveComponent\Attribute\LiveAction;
use Symfony\UX\LiveComponent\Attribute\LiveProp;
use Symfony\UX\LiveComponent\DefaultActionTrait;

#[AsLiveComponent(template: 'components/common/file_upload.html.twig')]
class FileUpload
{
    use DefaultActionTrait;

    #[LiveProp]
    public ?string $singleUploadFilename = null;
    #[LiveProp]
    public ?string $singleFileUploadError = null;

    private ?User $user = null;

    public function __construct(private ValidatorInterface $validator)
    {
    }

    #[LiveAction]
    public function uploadFiles(Request $request): void
    {
        $singleFileUpload = $request->files->get('file');

        if ($singleFileUpload) {
            $this->validateSingleFile($singleFileUpload);
        }
        if ($singleFileUpload instanceof UploadedFile) {
            [$this->singleUploadFilename] = $this->processFileUpload($singleFileUpload);
        }

    }

    private function validateSingleFile(UploadedFile $singleFileUpload): void
    {
        $errors = $this->validator->validate($singleFileUpload, [
            new Assert\File([
                'maxSize' => '2048k',
                'mimeTypes' => [
                    'image/jpeg',
                    'image/png',
                ],
                'mimeTypesMessage' => 'Veuillez télécharger une image au format JPEG ou PNG',
                'maxSizeMessage' => 'Le fichier est trop volumineux ({{ size }} {{ suffix }}). La taille maximale autorisée est de 2Mo.',
            ]),
        ]);

        if (0 === \count($errors)) {
            return;
        }

        $this->singleFileUploadError = $errors->get(0)->getMessage();

        // causes the component to re-render
        throw new UnprocessableEntityHttpException('Validation failed');
    }

    private function processFileUpload(UploadedFile $file): void
    {

        $media = new Media();
        $media->setUploadedFile($file);
        $media->setUploadedBy($this->user);
        dump($media);
        /*// in a real app, move this file somewhere
        // $file->move(...);

        $file = new UploadedFile('chemin_du_fichier', 'nom_du_fichier');
        $uploadResult = $this->processFileUpload($file);*/
    }
}