<?php

namespace Tests\Feature;

use App\Models\CourseGrade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AcademicTrackerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_to_login(): void
    {
        $response = $this->get(route('academic-tracker.index'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_access_academic_tracker(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('academic-tracker.index'));
        $response->assertStatus(200);
        $response->assertViewIs('academic-tracker.index');
    }

    public function test_user_can_add_course_grade(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('academic-tracker.store'), [
            'semester' => 1,
            'mata_kuliah' => 'Matematika Diskrit',
            'sks' => 3,
            'nilai' => 'A',
        ]);

        $response->assertRedirect(route('academic-tracker.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('course_grades', [
            'user_id' => $user->id,
            'semester' => 1,
            'mata_kuliah' => 'Matematika Diskrit',
            'sks' => 3,
            'nilai' => 'A',
        ]);
    }

    public function test_academic_tracker_calculates_ips_and_ipk_correctly(): void
    {
        $user = User::factory()->create();

        // Semester 1: SKS 3 (A=4) & SKS 2 (B=3) -> Points: 12 + 6 = 18. IPS = 18/5 = 3.60
        CourseGrade::create([
            'user_id' => $user->id,
            'semester' => 1,
            'mata_kuliah' => 'Struktur Data',
            'sks' => 3,
            'nilai' => 'A',
        ]);

        CourseGrade::create([
            'user_id' => $user->id,
            'semester' => 1,
            'mata_kuliah' => 'Aljabar Linear',
            'sks' => 2,
            'nilai' => 'B',
        ]);

        // Semester 2: SKS 4 (AB=3.5) -> Points: 14. IPS = 14/4 = 3.50
        // Cumulative Points: 18 + 14 = 32. Cumulative SKS: 9. IPK = 32/9 = 3.56
        CourseGrade::create([
            'user_id' => $user->id,
            'semester' => 2,
            'mata_kuliah' => 'Pemrograman Berorientasi Objek',
            'sks' => 4,
            'nilai' => 'AB',
        ]);

        $response = $this->actingAs($user)->get(route('academic-tracker.index'));

        $response->assertStatus(200);
        $response->assertViewHas('cumulativeSks', 9);
        $response->assertViewHas('ipk', 3.56);

        $stats = $response->viewData('semesterStats');
        $this->assertEquals(3.60, $stats[1]['ips']);
        $this->assertEquals(3.50, $stats[2]['ips']);
    }

    public function test_user_can_delete_course_grade(): void
    {
        $user = User::factory()->create();
        $grade = CourseGrade::create([
            'user_id' => $user->id,
            'semester' => 1,
            'mata_kuliah' => 'Kimia Dasar',
            'sks' => 3,
            'nilai' => 'BC',
        ]);

        $response = $this->actingAs($user)->delete(route('academic-tracker.destroy', $grade));
        $response->assertRedirect(route('academic-tracker.index'));
        
        $this->assertDatabaseMissing('course_grades', [
            'id' => $grade->id,
        ]);
    }

    public function test_user_cannot_delete_other_users_course_grade(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $gradeA = CourseGrade::create([
            'user_id' => $userA->id,
            'semester' => 1,
            'mata_kuliah' => 'Fisika Dasar',
            'sks' => 3,
            'nilai' => 'A',
        ]);

        $response = $this->actingAs($userB)->delete(route('academic-tracker.destroy', $gradeA));
        $response->assertStatus(403);

        $this->assertDatabaseHas('course_grades', [
            'id' => $gradeA->id,
        ]);
    }

    public function test_user_can_update_course_grade(): void
    {
        $user = User::factory()->create();
        $grade = CourseGrade::create([
            'user_id' => $user->id,
            'semester' => 1,
            'mata_kuliah' => 'Kimia Dasar',
            'sks' => 3,
            'nilai' => 'BC',
        ]);

        $response = $this->actingAs($user)->put(route('academic-tracker.update', $grade), [
            'semester' => 2,
            'mata_kuliah' => 'Kimia Dasar Lanjutan',
            'sks' => 4,
            'nilai' => 'A',
        ]);

        $response->assertRedirect(route('academic-tracker.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('course_grades', [
            'id' => $grade->id,
            'semester' => 2,
            'mata_kuliah' => 'Kimia Dasar Lanjutan',
            'sks' => 4,
            'nilai' => 'A',
        ]);
    }

    public function test_user_cannot_update_other_users_course_grade(): void
    {
        $userA = User::factory()->create();
        $userB = User::factory()->create();

        $gradeA = CourseGrade::create([
            'user_id' => $userA->id,
            'semester' => 1,
            'mata_kuliah' => 'Fisika Dasar',
            'sks' => 3,
            'nilai' => 'A',
        ]);

        $response = $this->actingAs($userB)->put(route('academic-tracker.update', $gradeA), [
            'semester' => 1,
            'mata_kuliah' => 'Fisika Dasar',
            'sks' => 3,
            'nilai' => 'E',
        ]);

        $response->assertStatus(403);

        $this->assertDatabaseHas('course_grades', [
            'id' => $gradeA->id,
            'nilai' => 'A',
        ]);
    }
}
