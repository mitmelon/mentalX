<?php
namespace Manomite\Engine;

class Zipper {
    public $zip;
    public function __construct($output){
        $this->zip = new \ZipStream\ZipStream(
            outputName: $output,
            sendHttpHeaders: true,
        );
    }

    public function addFileFromPath(string $filename, string $filePath){
        $this->zip->addFileFromPath(
            fileName: $filename,
            path: $filePath,
        );
        return $this;
    }

    public function addFileFromRaw(string $filename, string $data, string $comment = null){
        $this->zip->addFile(
            fileName: $filename,
            data: $data,
            comment: $comment
          );
          return $this;
    }

    public function complete(){
        try{
            $this->zip->finish();
        } catch(\Throwable $e){
            throw new \Exception($e->getMessage());
        }
    }
}