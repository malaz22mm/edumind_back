<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\InterestResource;
use App\Models\Grade;
use App\Models\Interest;
use App\Models\Skill;
use App\Models\LearningTopic;
use App\Http\Resources\GradeResource;
use App\Http\Resources\SkillResource;
use App\Http\Resources\LearningTopicResource;

class SetupController extends Controller
{
    /**
     * Get all grades
     */
    public function getGrades()
    {
        $grades = Grade::orderBy('id')->get();

        return response()->json([
            'success' => true,
            'data' => GradeResource::collection($grades),
        ]);
    }

    /**
     * Get all Interests
     */
    public function getInterests()
    {
        $interests = Interest::orderBy('id')->get();

        return response()->json([
            'success' => true,
            'data' => InterestResource::collection($interests),
        ]);
    }

    /**
     * Get all learning topics
     */
    public function GetLearningTopics()
    {
        $topics = LearningTopic::orderBy('order_index')->get();

        return response()->json([
            'success' => true,
            'data' => LearningTopicResource::collection($topics),
        ]);
    }
}