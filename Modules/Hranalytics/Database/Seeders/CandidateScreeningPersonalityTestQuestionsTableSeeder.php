<?php

namespace Modules\Hranalytics\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class CandidateScreeningPersonalityTestQuestionsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        // $this->call("OthersTableSeeder");

        \DB::table('candidate_screening_personality_test_questions')->delete();

        \DB::table('candidate_screening_personality_test_questions')->insert(
            [
                0 => [
                    'id' => 1,
                    'question' => 'At a social event, do you:',
                    'row' => 1,
                    'column' => 1,

                ],
                1 => [
                    'id' => 2,
                    'question' => 'Which best describes you:',
                    'row' => 1,
                    'column' => 2,

                ],
                2 => [
                    'id' => 3,
                    'question' => 'In your personal view, which is worse?',
                    'row' => 1,
                    'column' => 3,

                ],
                3 => [
                    'id' => 4,
                    'question' => 'Are you more impressed by:',
                    'row' => 1,
                    'column' => 4,

                ],
                4 => [
                    'id' => 5,
                    'question' => 'Are you more likely to change your opinion based on:',
                    'row' => 1,
                    'column' => 5,

                ],
                5 => [
                    'id' => 6,
                    'question' => 'When doing maintenance work at your home or residence,  do you prefer:',
                    'row' => 1,
                    'column' => 6,

                ],
                6 => [
                    'id' => 7,
                    'question' => 'You are at a  store and see a product that catches your eye.  Do you:',
                    'row' => 1,
                    'column' => 7,

                ],
                7 => [
                    'id' => 8,
                    'question' => 'At a social function, you:',
                    'row' => 2,
                    'column' => 1,

                ],
                8 => [
                    'id' => 9,
                    'question' => 'You tend to gravitate towards:',
                    'row' => 2,
                    'column' => 2,

                ],
                9 => [
                    'id' => 10,
                    'question' => 'I am more interested in spending time on things that:',
                    'row' => 2,
                    'column' => 3,

                ],
                10 => [
                    'id' => 11,
                    'question' => 'You own a variety store.  A person has not eaten in 2 days and stole some bread, milk, and some money from the till.  What do you do:',
                    'row' => 2,
                    'column' => 4,

                ],
                11 => [
                    'id' => 12,
                    'question' => 'You approach a stranger at a social function.  You tend to be somewhat:',
                    'row' => 2,
                    'column' => 5,

                ],
                12 => [
                    'id' => 13,
                    'question' => 'You tend to be more:',
                    'row' => 2,
                    'column' => 6,

                ],
                13 => [
                    'id' => 14,
                    'question' => 'Which answer best reflects you?',
                    'row' => 2,
                    'column' => 7,

                ],
                14 => [
                    'id' => 15,
                    'question' => 'You\'re in the lunch room sharing your break with a group work colleagues.  Do you:',
                    'row' => 3,
                    'column' => 1,

                ],
                15 => [
                    'id' => 16,
                    'question' => 'In doing ordinary things are you more likely to:',
                    'row' => 3,
                    'column' => 2,

                ],
                16 => [
                    'id' => 17,
                    'question' => 'When presenting a difficult topic to a group, it is best to:',
                    'row' => 3,
                    'column' => 3,

                ],
                17 => [
                    'id' => 18,
                    'question' => 'Your partner or spouse asks you "how do I look in this outfit?".  How should you respond?',
                    'row' => 3,
                    'column' => 4,

                ],
                18 => [
                    'id' => 19,
                    'question' => 'Your are on a jury about to render a verdict on a person who robbed a bank to pay for a life saving surgery for their daughter. What do you do?',
                    'row' => 3,
                    'column' => 5,

                ],
                19 => [
                    'id' => 20,
                    'question' => 'You are that individual who stole money for food.  You\'ve been in prison for 5 days awaiting a jury trial and verdict.  Do you:',
                    'row' => 3,
                    'column' => 6,

                ],
                20 => [
                    'id' => 21,
                    'question' => 'Would you say you are more:',
                    'row' => 3,
                    'column' => 7,

                ],
                21 => [
                    'id' => 22,
                    'question' => 'In preparing for a job interview, do you:',
                    'row' => 4,
                    'column' => 1,

                ],
                22 => [
                    'id' => 23,
                    'question' => 'Choose one that best fits your opinion:',
                    'row' => 4,
                    'column' => 2,

                ],
                23 => [
                    'id' => 24,
                    'question' => 'Steve Jobs was a visionary who pioneered a new age of digital communication at Apple despite being accused of treating employees poorly at times.  Choose a statement that best fits your opinion about Steve Jobs.',
                    'row' => 4,
                    'column' => 3,

                ],
                24 => [
                    'id' => 25,
                    'question' => 'Are you more often:',
                    'row' => 4,
                    'column' => 4,

                ],
                25 => [
                    'id' => 26,
                    'question' => 'It is worse to be:',
                    'row' => 4,
                    'column' => 5,

                ],
                26 => [
                    'id' => 27,
                    'question' => 'You are in high school planning your career.  Do you:',
                    'row' => 4,
                    'column' => 6,

                ],
                27 => [
                    'id' => 28,
                    'question' => 'Which do you prefer?',
                    'row' => 4,
                    'column' => 7,

                ],
                28 => [
                    'id' => 29,
                    'question' => 'When you are in a social setting, do you:',
                    'row' => 5,
                    'column' => 1,

                ],
                29 => [
                    'id' => 30,
                    'question' => 'People talk a lot about having "common sense".  In your view:',
                    'row' => 5,
                    'column' => 2,

                ],
                30 => [
                    'id' => 31,
                    'question' => 'Children often do NOT:',
                    'row' => 5,
                    'column' => 3,

                ],
                31 => [
                    'id' => 32,
                    'question' => 'In making a decision, do you feel more comfortable with:',
                    'row' => 5,
                    'column' => 4,

                ],
                32 => [
                    'id' => 33,
                    'question' => 'Are you more:',
                    'row' => 5,
                    'column' => 5,

                ],
                33 => [
                    'id' => 34,
                    'question' => 'Which is more admirable:',
                    'row' => 5,
                    'column' => 6,

                ],
                34 => [
                    'id' => 35,
                    'question' => 'Do you put more value on being:',
                    'row' => 5,
                    'column' => 7,

                ],
                35 => [
                    'id' => 36,
                    'question' => 'Does new and non-routine interaction with others:',
                    'row' => 6,
                    'column' => 1,

                ],
                36 => [
                    'id' => 37,
                    'question' => 'Are you more frequently:',
                    'row' => 6,
                    'column' => 2,

                ],
                37 => [
                    'id' => 38,
                    'question' => 'You are a site supervisor in charge of a complex site.  Five guards come to you to complain about another fellow guard.  Which statement applies to you:',
                    'row' => 6,
                    'column' => 3,

                ],
                38 => [
                    'id' => 39,
                    'question' => 'Which is more satisfying?',
                    'row' => 6,
                    'column' => 4,

                ],
                39 => [
                    'id' => 40,
                    'question' => 'How do you make decisions?',
                    'row' => 6,
                    'column' => 5,

                ],
                40 => [
                    'id' => 41,
                    'question' => 'If given the option, would you prefer a work schedule that:',
                    'row' => 6,
                    'column' => 6,

                ],
                41 => [
                    'id' => 42,
                    'question' => 'Assume you are on the spares list for casual part-time security work.  Do you prefer:',
                    'row' => 6,
                    'column' => 7,

                ],
                42 => [
                    'id' => 43,
                    'question' => 'Do you prefer:',
                    'row' => 7,
                    'column' => 1,

                ],
                43 => [
                    'id' => 44,
                    'question' => 'Do you go more by:',
                    'row' => 7,
                    'column' => 2,

                ],
                44 => [
                    'id' => 45,
                    'question' => 'Are you more interested in:',
                    'row' => 7,
                    'column' => 3,

                ],
                45 => [
                    'id' => 46,
                    'question' => 'Which is more of a compliment to you:',
                    'row' => 7,
                    'column' => 4,

                ],
                46 => [
                    'id' => 47,
                    'question' => 'What do you value more about yourself?',
                    'row' => 7,
                    'column' => 5,

                ],
                47 => [
                    'id' => 48,
                    'question' => 'Which do you prefer more often?',
                    'row' => 7,
                    'column' => 6,

                ],
                48 => [
                    'id' => 49,
                    'question' => 'You have just purchased a car.  Are you more comfortable:',
                    'row' => 7,
                    'column' => 7,

                ],
                49 => [
                    'id' => 50,
                    'question' => 'Do you:',
                    'row' => 8,
                    'column' => 1,

                ],
                50 => [
                    'id' => 51,
                    'question' => 'Are you more likely to trust your:',
                    'row' => 8,
                    'column' => 2,

                ],
                51 => [
                    'id' => 52,
                    'question' => 'Do you  often feel:',
                    'row' => 8,
                    'column' => 3,

                ],
                52 => [
                    'id' => 53,
                    'question' => 'Who would make a better supervisor.  One who displays:',
                    'row' => 8,
                    'column' => 4,

                ],
                53 => [
                    'id' => 54,
                    'question' => 'Are you more inclined to be:',
                    'row' => 8,
                    'column' => 5,

                ],
                54 => [
                    'id' => 55,
                    'question' => 'Choose a statement that best suits you.  It is preferable mostly to:',
                    'row' => 8,
                    'column' => 6,

                ],
                55 => [
                    'id' => 56,
                    'question' => 'Pick a statement that resonates with you:',
                    'row' => 8,
                    'column' => 7,

                ],
                56 => [
                    'id' => 57,
                    'question' => 'You\'re at home with your family.  The phone in your kitchen rings. Do you:',
                    'row' => 9,
                    'column' => 1,

                ],
                57 => [
                    'id' => 58,
                    'question' => 'Which do you prize more about yourself?',
                    'row' => 9,
                    'column' => 2,

                ],
                58 => [
                    'id' => 59,
                    'question' => 'Are you more drawn to:',
                    'row' => 9,
                    'column' => 3,

                ],
                59 => [
                    'id' => 60,
                    'question' => 'Which seems to be the greater error in judgement?',
                    'row' => 9,
                    'column' => 4,

                ],
                60 => [
                    'id' => 61,
                    'question' => 'Do you see yourself to be:',
                    'row' => 9,
                    'column' => 5,

                ],
                61 => [
                    'id' => 62,
                    'question' => 'Which situation appeals to you more:',
                    'row' => 9,
                    'column' => 6,

                ],
                62 => [
                    'id' => 63,
                    'question' => 'In high school, you were:',
                    'row' => 9,
                    'column' => 7,

                ],
                63 => [
                    'id' => 64,
                    'question' => 'Are you more inclined to be:',
                    'row' => 10,
                    'column' => 1,

                ],
                64 => [
                    'id' => 65,
                    'question' => 'In writings, do you prefer:',
                    'row' => 10,
                    'column' => 2,

                ],
                65 => [
                    'id' => 66,
                    'question' => 'It is harder for you to:',
                    'row' => 10,
                    'column' => 3,

                ],
                66 => [
                    'id' => 67,
                    'question' => 'Which do you wish more for yourself:',
                    'row' => 10,
                    'column' => 4,

                ],
                67 => [
                    'id' => 68,
                    'question' => 'Which is the greater fault?',
                    'row' => 10,
                    'column' => 5,

                ],
                68 => [
                    'id' => 69,
                    'question' => 'Do you prefer:',
                    'row' => 10,
                    'column' => 6,

                ],
                69 => [
                    'id' => 70,
                    'question' => 'Do you tend to be:',
                    'row' => 10,
                    'column' => 7,

                ],

            ]
        );

    }
}
