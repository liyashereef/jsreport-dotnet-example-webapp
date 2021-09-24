<?php

namespace Modules\Recruitment\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class RecCandidateScreeningQuestionLookupsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        \DB::connection('mysql_rec')->table('rec_candidate_screening_question_lookups')->delete();

        \DB::connection('mysql_rec')->table('rec_candidate_screening_question_lookups')->insert(array (
            0 =>
            array (
                'id' => 1,
                'category' => 'Initiative',
                'screening_question' => 'Describe a time when you identified a problem at a client site. What proactive steps did you take to resolve the issue? How did you communicate this with your chain command?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            1 =>
            array (
                'id' => 2,
                'category' => 'Initiative',
                'screening_question' => 'What have you done in your current or previous job(s), that would be considered going "above and beyond" your normal job function?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            2 =>
            array (
                'id' => 3,
                'category' => 'Stress Tolerance',
            'screening_question' => 'Describe the last time a person at work (customer, co-worker, Supervisor) became irritated or lost his/her temper. What did they do? How did you respond? What was the outcome?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            3 =>
            array (
                'id' => 4,
                'category' => 'Stress Tolerance',
                'screening_question' => 'Give me an example of an idea you came up with that was strongly opposed by a co-worker or supervisor. What was the situation? What was your reaction? What was the result?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            4 =>
            array (
                'id' => 5,
                'category' => 'Teamwork / Interpersonal Group Dynamics',
                'screening_question' => 'Are you a team player? Would your colleagues agree with your assessment? Give examples where you have put the interests of your team ahead of your own.',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            5 =>
            array (
                'id' => 6,
                'category' => 'Teamwork / Interpersonal Group Dynamics',
                'screening_question' => 'We all have ways of showing consideration for others. What are some things you\'ve done to show concern or consideration for a co-worker?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            6 =>
            array (
                'id' => 7,
                'category' => 'Scenarios / Problem Solving',
                'screening_question' => 'You are dispatched to a disturbance at a college parking lot. A rowdy group of individuals had caused a disturbance earlier at the campus bar. One of the "bouncers" was offended by an individual in the group. The "bouncer" subsequently called his friends over to the campus bar to confront the individuals and "settle the score". The bouncer and his friends have weapons. What do you do and why?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            7 =>
            array (
                'id' => 8,
                'category' => 'Scenarios / Problem Solving',
                'screening_question' => 'There are reports of a partially nude male at the site parking lot who seems disoriented. He is screaming and waiving a chain in each hand. The individual may be experiencing delirium. You are the guard currently on shift. What do you do and why?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            8 =>
            array (
                'id' => 9,
                'category' => 'Scenarios / Problem Solving',
                'screening_question' => 'You notice 6 males walking together. Two individuals start yelling at another individual in the group. Suddenly, one of the individuals strikes the individual from the back of the head causing him to fall forward face first into the sidewalk. The two males continue kicking and beating the individual. You are alone and watching this while on foot patrol. What do you do and why?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            9 =>
            array (
                'id' => 10,
                'category' => 'Scenarios / Problem Solving',
                'screening_question' => 'You receive notification that a student is refusing to leave the computer lab when instructed to do so. The student is distraught and threatening to harm himself. What do you do and why?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            10 =>
            array (
                'id' => 11,
                'category' => 'Scenarios / Problem Solving',
                'screening_question' => 'You have witnessed a robbery. What do you do and why?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
            11 =>
            array (
                'id' => 12,
                'category' => 'Scenarios / Problem Solving',
                'screening_question' => 'You are guarding a highly restricted site when a visitor at the concierge desk requests to see an individual in the building. The person is not authorized to enter the building and you ask him to leave. He is getting belligerent and starts hurling insults at you. Things are getting out of control. The site is not authorized for use of force. However, you are tempted to remove the individual by applying force. What do you do and why? ',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => null,
            ),
        ));
    }
}
