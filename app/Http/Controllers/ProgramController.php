<?php

namespace App\Http\Controllers;

use App\Models\SchoolSetting;
use Illuminate\View\View;

class ProgramController extends Controller
{
    public function show(string $program): View
    {
        $programs = [
            'play-group' => [
                'name' => 'Play Group',
                'image' => 'playgroup.png',
                'intro' => 'A joyful first step into school life through play, stories, music, movement and guided social interaction.',
                'focus' => ['Comfort with school routines','Communication and listening','Fine and gross motor activities','Sharing, confidence and social habits','Colours, shapes, sounds and early concepts'],
            ],
            'nursery' => [
                'name' => 'Nursery',
                'image' => 'nursery.png',
                'intro' => 'A balanced program that develops language, motor skills, observation, creativity and early academic readiness.',
                'focus' => ['Pre-reading and vocabulary','Pre-writing and pencil readiness','Number awareness and basic concepts','Rhymes, stories, art and craft','Good habits and independent classroom routines'],
            ],
            'lkg' => [
                'name' => 'LKG',
                'image' => 'lkg.png',
                'intro' => 'A structured but child-friendly learning stage that strengthens literacy, numeracy, confidence and classroom participation.',
                'focus' => ['Letter sounds and early reading','Writing practice and language development','Numbers and basic mathematical thinking','Environmental awareness and general knowledge','Creative activities, conversation and confidence'],
            ],
            'ukg' => [
                'name' => 'UKG',
                'image' => 'ukg.png',
                'intro' => 'A school-readiness program designed to strengthen core concepts, communication, independence and learning confidence.',
                'focus' => ['Reading and sentence formation','Writing, spelling and comprehension basics','Number operations and problem-solving readiness','General awareness and concept-based learning','Independent work, discipline and classroom confidence'],
            ],
        ];

        abort_unless(isset($programs[$program]), 404);
        $details = $programs[$program];
        $settings = SchoolSetting::first();

        return view('programs.show', compact('details', 'settings'));
    }
}
