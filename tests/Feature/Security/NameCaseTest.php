<?php

namespace Tests\Feature\Security;

use App\Models\User;
use App\Support\NameCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Teacher names: first letter of each word capitalized (rest kept as typed) — App\Support\NameCase,
 * same rule as public/js/name-case.js on the Full Name boxes.
 */
class NameCaseTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_letter_of_each_word_is_capitalized_rest_kept(): void
    {
        $cases = [
            'maria dela cruz'      => 'Maria Dela Cruz',
            "o'connell"            => "O'Connell",
            'john deGuzman'        => 'John DeGuzman',   // rest of the word kept as typed
            'McDonald'             => 'McDonald',
            'ma. teresa santos-reyes' => 'Ma. Teresa Santos-Reyes',
            'niño   añonuevo '     => 'Niño Añonuevo',   // ñ, extra spaces trimmed
            'JUAN'                 => 'JUAN',
        ];
        foreach ($cases as $in => $out) {
            $this->assertSame($out, NameCase::words($in), $in);
        }
    }

    public function test_teacher_names_are_saved_capitalized(): void
    {
        $admin = User::factory()->admin()->create();
        $teacher = User::factory()->teacher()->create(['email' => 'ana.reyes@gmail.com']);
        $this->actingAs($admin, 'web')->putJson("/admin/teachers/{$teacher->id}", ['name' => 'ana dela cruz', 'email' => 'ana.reyes@gmail.com', 'is_active' => true])->assertOk();
        $this->assertSame('Ana Dela Cruz', $teacher->fresh()->name);

        $this->actingAs($teacher->fresh(), 'web')->from('/teacher/dashboard')->put('/teacher/settings/profile', ['name' => 'ana r. dela cruz', 'email' => 'ana.reyes@gmail.com']);
        $this->assertSame('Ana R. Dela Cruz', $teacher->fresh()->name);
    }
}
