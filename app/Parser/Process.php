<?php

namespace App\Parser;

use App\Models\Address;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class Process
{

    public function start()
    {
        if ($this->getParsedData()->isNotEmpty()) {
            return dump('Parsing was released');
        }
        $subjects = $this->getSubjectsList();
        foreach ($subjects as $subject) {
            $class = $subject->class;
            $subj = (new $class($subject->url));
            dump("------- {$subj->getBrand()} --------");
            $this->putDataToDatabase($subj->getData());
        }
        dump("Parsing Finished");
    }

    /**
     * @return Collection
     */
    public function getSubjectsList(): Collection
    {
        return DB::table('subjects')->get();
    }

    private function putDataToDatabase(array $data)
    {
        if (!empty($data)) {
            Address::query()->insert($data);
        }
    }

    /**
     * @return Collection
     */
    private function getParsedData(): Collection
    {
        return DB::table('addresses')->get();
    }
}
