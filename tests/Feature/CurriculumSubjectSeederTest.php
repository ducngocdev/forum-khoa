<?php

namespace Tests\Feature;

use App\Models\Subject;
use Database\Seeders\CurriculumSubjectSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CurriculumSubjectSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_curriculum_courses_are_added_and_seeding_is_idempotent(): void
    {
        $seeder = new CurriculumSubjectSeeder;
        $seeder->run();
        $firstCount = Subject::count();

        $seeder->run();

        $this->assertSame($firstCount, Subject::count());
        $this->assertGreaterThan(130, $firstCount);
        $this->assertSame('Lập trình Web ASPX', Subject::where('code', 'IN4309N')->value('name'));
        $this->assertSame('Tiếng Anh 1', Subject::where('code', 'GE4410')->value('name'));
        $this->assertSame('Bảo mật Website', Subject::where('code', 'IN4518')->value('name'));
    }
}
