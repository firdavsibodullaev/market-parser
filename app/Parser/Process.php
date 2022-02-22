<?php

namespace App\Parser;

use App\Models\Address;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class Process
{

    public function start()
    {
        $subjects = $this->getSubjectsList();
        foreach ($subjects as $subject) {
            $class = $subject->class;

            dump("------- {$class} --------");

            $subj = (new $class($subject->url));
            $this->putDataToDatabase($subj->getData());
        }
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
}
