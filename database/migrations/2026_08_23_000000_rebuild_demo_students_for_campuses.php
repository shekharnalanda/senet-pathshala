<?php

use App\Models\Branch;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            $legacyStudentUserIds = Student::query()
                ->whereNull('branch_id')
                ->orWhere('admission_no', 'like', 'DEMO-%')
                ->pluck('user_id')
                ->filter()
                ->values();

            if ($legacyStudentUserIds->isNotEmpty()) {
                User::whereIn('id', $legacyStudentUserIds)->delete();
            }

            User::where('email', 'like', 'demo.b%.c%.%@cnet.local')->delete();

            $branches = Branch::query()
                ->where('is_active', true)
                ->orderByDesc('is_main')
                ->orderBy('id')
                ->get();

            foreach ($branches as $branch) {
                $classes = SchoolClass::query()
                    ->where('is_active', true)
                    ->where(function ($query) use ($branch) {
                        $query->whereNull('branch_id')->orWhere('branch_id', $branch->id);
                    })
                    ->orderBy('sort_order')
                    ->orderBy('name')
                    ->get()
                    ->groupBy('name')
                    ->map(function ($items) use ($branch) {
                        return $items->sortByDesc(fn ($class) => (int) $class->branch_id === (int) $branch->id)->first();
                    })
                    ->values();

                foreach ($classes as $class) {
                    $sections = $class->section_list;

                    for ($number = 1; $number <= 2; $number++) {
                        $suffix = str_pad((string) $number, 2, '0', STR_PAD_LEFT);
                        $email = "demo.b{$branch->id}.c{$class->id}.{$suffix}@cnet.local";
                        $admissionNo = "DEMO-B{$branch->id}-C{$class->id}-{$suffix}";
                        $section = ! empty($sections) ? $sections[($number - 1) % count($sections)] : null;
                        $gender = $number === 1 ? 'Male' : 'Female';
                        $studentName = "Demo {$class->name} Student {$number}";

                        $user = User::create([
                            'branch_id' => $branch->id,
                            'name' => $studentName,
                            'email' => $email,
                            'password' => Hash::make('Demo@12345'),
                            'is_admin' => false,
                            'role' => null,
                            'permissions' => null,
                        ]);

                        Student::create([
                            'user_id' => $user->id,
                            'branch_id' => $branch->id,
                            'admission_no' => $admissionNo,
                            'class_name' => $class->name,
                            'section' => $section,
                            'roll_no' => (string) $number,
                            'date_of_birth' => now()->subYears(6 + max(0, (int) $class->sort_order))->subMonths($number)->toDateString(),
                            'gender' => $gender,
                            'father_name' => "Demo Father {$number}",
                            'mother_name' => "Demo Mother {$number}",
                            'guardian_name' => "Demo Guardian {$number}",
                            'guardian_phone' => '900000'.str_pad((string) ($branch->id * 100 + $class->id * 2 + $number), 4, '0', STR_PAD_LEFT),
                            'address' => ($branch->address ?: $branch->name).' - Demo Student',
                            'is_active' => true,
                        ]);
                    }
                }
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function () {
            $demoUserIds = Student::query()
                ->where('admission_no', 'like', 'DEMO-%')
                ->pluck('user_id')
                ->filter()
                ->values();

            if ($demoUserIds->isNotEmpty()) {
                User::whereIn('id', $demoUserIds)->delete();
            }
        });
    }
};
