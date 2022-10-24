<?php

namespace App\Console\Traits;

use Illuminate\Support\Pluralizer;

trait helpersJsonApi
{

    public function getPluralClassName($nameClass):string{
        return ucwords(Pluralizer::plural($nameClass));
    }

    public function getSingularClassName($nameClass):string{
        return ucwords(Pluralizer::singular($nameClass));
    }

    /**
     * Get the stub path and the stub variables
     *
     * @return bool|mixed|string
     *
     */
    public function getSourceFile():string{
        return $this->getStubContents($this->getStubPath(), $this->getStubVariables());
    }

    public function getNewSourceRegisterLines($searchLineInStub = "// [EndOfLineMethodRegister]"): string{
        return $this->getStubContentsWithNewLines($this->getSourceFilePath(), $this->getStubNewLines(), $searchLineInStub);
    }
    public function getNewSourceRegisterLinesMaster(string $pathStub , array $stubNewLines = [], array $searchLineInStub = []): string{
        return $this->getStubContentsWithArray($pathStub, $stubNewLines, $searchLineInStub);
    }


    /**
     * Replace the stub variables(key) with the desire value
     *
     * @param string $stub
     * @param array $stubVariables
     * @return bool|mixed|string
     */
    public function getStubContents(String $stub, array $stubVariables = []):string{
        $contentsStub = file_get_contents($stub);

        foreach ($stubVariables as $keyVar => $valueReplace)
        {
            $contentsStub = str_replace('{{ '.$keyVar.' }}' , $valueReplace, $contentsStub);
        }

        return $contentsStub;
    }

    public function getStubContentsWithArray(string $stub, array $stubNewLines = [], $linesSearchForReplace = []):string{
            $contentsStub = file_get_contents($stub);

            foreach ($linesSearchForReplace as $keyIndex => $searchLine)
            {
                $contentsStub = str_replace( $searchLine , $stubNewLines[$keyIndex], $contentsStub);
            }

            return $contentsStub;
    }
    /**
     * Replace the stub variables(key) with the desire value
     *
     * @param string $stub
     * @param array $stubVariables
     * @return bool|mixed|string
     */
    public function getStubContentsWithNewLines(string $stub, string $newLines = "// [EndOfLineMethodRegister]", string $searchAndReplaceLine = "// [EndOfLineMethodRegister]"):string{
        $contentsStub = file_get_contents($stub);

        $contentsStub = str_replace($searchAndReplaceLine , $newLines, $contentsStub);

        return $contentsStub;
    }

    /**
     * Build the directory for the class if necessary.
     *
     * @param  string  $path
     * @return string
     */
    public function makeDirectory(String $path):string
    {
        if (! $this->filesystem->isDirectory($path)) {
            $this->filesystem->makeDirectory($path, 0777, true, true);
        }

        return $path;
    }

}
