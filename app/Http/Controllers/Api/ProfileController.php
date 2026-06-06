<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Student;
use App\Models\StudentLearningTopic;
use App\Models\StudentSkillProgress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProfileController extends Controller
{
    public function completeProfile(Request $request)
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthenticated',
            ], 401);
        }

        if (Student::where('user_id', $user->id)->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Profile already completed',
            ], 409);
        }

        $avatarPath = null;
        $avatarDisk = (string) config('filesystems.avatar_disk', 'public');

        try {
            $validated = $request->validate([
                'name' => ['required', 'string', 'min:2', 'max:255'],
                'gender' => ['required', 'in:male,female'],
                'birth_date' => ['nullable', 'date', 'before:today'],
                'grade_id' => ['required', 'exists:grades,id'],
                'avatar' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
                
                'interests' => ['nullable', 'array'],
                'interests.*' => ['integer', 'exists:interests,id'],

                'learning_topics' => ['nullable', 'array'],
                'learning_topics.*' => ['integer', 'exists:learning_topics,id'],
            ]);

            $student = DB::transaction(function () use ($request, $user, $validated, &$avatarPath, $avatarDisk) {
                if ($request->hasFile('avatar')) {
                    $avatarPath = $request->file('avatar')->store('students/avatars', $avatarDisk);
                }

                $student = Student::create([
                    'user_id' => $user->id,
                    'name' => $validated['name'],
                    'gender' => $validated['gender'],
                    'birth_date' => $validated['birth_date'] ?? null,
                    'current_grade_id' => $validated['grade_id'],
                    'avatar' => $avatarPath,
                ]);

                $student->studentprofile()->create([
                    'current_level_id' => 1,
                    'current_points' => 0,
                    'longest_streak' => 0,
                    'total_games_played' => 0,
                ]);

                if (! empty($validated['interests'])) {
                    $student->interests()->sync($validated['interests']);
                }

                if (! empty($validated['learning_topics'])) {
                    $now = now();

                    $topicRows = collect($validated['learning_topics'])
                        ->values()
                        ->map(fn ($topicId, $index) => [
                            'student_id' => $student->id,
                            'learning_topic_id' => $topicId,
                            'priority' => $index + 1,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ])->all();

                    StudentLearningTopic::insert($topicRows);
                }

                return $student;
            });

            $student->load([
                'grade',
                'interests',
                'studentprofile',
                'skillProgress.skill',
                'learningGoals.topic',
            ]);

            $user->setRelation('student', $student);

            return response()->json([
                'success' => true,
                'message' => 'Profile completed successfully',
                'data' => [
                    'user' => new UserResource($user),
                    'profile_completed' => true,
                ],
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (Throwable $e) {
            if (! empty($avatarPath)) {
                Storage::disk($avatarDisk)->delete($avatarPath);
            }

            Log::error('Complete profile error', [
                'message' => $e->getMessage(),
                'user_id' => $user?->id,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to complete profile',
                'error' => config('app.debug') ? $e->getMessage() : 'An error occurred',
            ], 500);
        }
    }
}