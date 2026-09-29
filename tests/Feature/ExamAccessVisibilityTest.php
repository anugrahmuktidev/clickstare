<?php

namespace Tests\Feature;

use App\Models\ExamSession;
use App\Models\Sekolah;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ExamAccessVisibilityTest extends TestCase
{
    use RefreshDatabase;

    public function test_pretest_routes_are_blocked_when_school_pretest_is_disabled(): void
    {
        $sekolah = Sekolah::create([
            'nama' => 'SMA Tes 1',
            'npsn' => '10000001',
            'is_pretest_enabled' => false,
            'is_posttest_enabled' => false,
        ]);

        $student = $this->makeValidatedStudent($sekolah->id, '2000000001');

        $this->actingAs($student)
            ->get(route('exam.pretest'))
            ->assertForbidden();
    }

    public function test_posttest_routes_are_blocked_when_school_posttest_is_disabled(): void
    {
        $sekolah = Sekolah::create([
            'nama' => 'SMA Tes 2',
            'npsn' => '10000002',
            'is_pretest_enabled' => true,
            'is_posttest_enabled' => false,
        ]);

        $student = $this->makeValidatedStudent($sekolah->id, '2000000002');

        $this->actingAs($student)
            ->get(route('exam.posttest'))
            ->assertForbidden();
    }

    public function test_pretest_route_can_be_opened_when_school_pretest_is_enabled(): void
    {
        $sekolah = Sekolah::create([
            'nama' => 'SMA Tes 3',
            'npsn' => '10000003',
            'is_pretest_enabled' => true,
            'is_posttest_enabled' => false,
        ]);

        $student = $this->makeValidatedStudent($sekolah->id, '2000000003');
        $session = ExamSession::create([
            'sekolah_id' => $sekolah->id,
            'nama' => 'Sesi 1',
            'is_active' => true,
        ]);

        $this->actingAs($student)
            ->withSession(['active_exam_session_id' => $session->id])
            ->get(route('exam.pretest'))
            ->assertOk();
    }

    public function test_validation_status_redirects_validated_student_to_education_page(): void
    {
        $sekolah = Sekolah::create([
            'nama' => 'SMA Tes 4',
            'npsn' => '10000004',
            'is_pretest_enabled' => false,
            'is_posttest_enabled' => false,
        ]);

        $student = $this->makeValidatedStudent($sekolah->id, '2000000004');

        $this->actingAs($student)
            ->getJson(route('validation.status'))
            ->assertOk()
            ->assertJson([
                'validated' => true,
                'redirect' => route('education.index'),
            ]);
    }

    private function makeValidatedStudent(int $sekolahId, string $nisn): User
    {
        return User::create([
            'username' => $nisn,
            'role' => 'siswa',
            'name' => 'Siswa Uji',
            'nisn' => $nisn,
            'password' => Hash::make('password123'),
            'sekolah_id' => $sekolahId,
            'is_validated' => true,
            'validated_at' => now(),
        ]);
    }
}
