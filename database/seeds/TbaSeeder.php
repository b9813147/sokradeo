<?php

use App\Models\Tba;
use Illuminate\Database\Seeder;

class TbaSeeder extends Seeder
{
    public function run()
    {
        $this->add_exno_and_serialnumber_to_tbas();
    }

    public function add_exno_and_serialnumber_to_tbas()
    {
        $result = [
            'exno_serialnumber_cn1.json',
            'exno_serialnumber_cn2.json',
            'exno_serialnumber_cn3.json',
            'exno_serialnumber_cn4.json',
            'exno_serialnumber_cn5.json',
            'exno_serialnumber_cn6.json',
            'exno_serialnumber_cn7.json',
            'exno_serialnumber_org.json',
        ];
        foreach ($result as $item) {
            $data = collect(json_decode(file_get_contents(public_path($item))));
            $data->each(function ($q) {
                Tba::query()->where(['mark' => $q->ExNO, 'locale_id' => $q->locale])->update(['binding_number' => $q->SERIALNUMBER]);
            });
        }

    }

}
