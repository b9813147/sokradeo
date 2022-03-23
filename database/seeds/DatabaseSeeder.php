<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ApplicationSeeder::class);
        $this->call(TagTypeSeeder::class);
        $this->call(GlobalNormRefsTableSeeder::class);
        $this->call(ConfigParametersTableSeeder::class);
        $this->call(EducationalStagesTableSeeder::class);
        $this->call(LocalesTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(SubjectFieldsTableSeeder::class);
        $this->call(TbaAnalysisEventModesTableSeeder::class);
        $this->call(TbaEvaluateEventModesTableSeeder::class);
        $this->call(TbaFeaturesTableSeeder::class);
    }
}
