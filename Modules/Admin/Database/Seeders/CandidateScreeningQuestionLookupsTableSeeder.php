<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Seeder;

class CandidateScreeningQuestionLookupsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('candidate_screening_question_lookups')->delete();
        
        \DB::table('candidate_screening_question_lookups')->insert(array (
            0 => 
            array (
                'id' => 1,
                'category' => 'Initiative',
                'screening_question' => 'Have you ever recognized a problem before your boss or others in the organization? How did you handle it?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => NULL,
            ),
            1 => 
            array (
                'id' => 2,
                'category' => 'Initiative',
                'screening_question' => 'What do you do in your job that would be considered "above and beyond" your typical job description?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => NULL,
            ),
            2 => 
            array (
                'id' => 3,
                'category' => 'Stress Tolerance',
            'screening_question' => 'Describe the last time a person at work (customer, co-worker, Supervisor) became irritated or lost his/her temper. What did they do? How did you respond? What was the outcome?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => NULL,
            ),
            3 => 
            array (
                'id' => 4,
                'category' => 'Stress Tolerance',
                'screening_question' => 'Give me an example of when your ideas were strongly opposed by a co-worker or supervisor. What was the situation? What was your reaction? What was the result?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => NULL,
            ),
            4 => 
            array (
                'id' => 5,
                'category' => 'Teamwork / Interpersonal Group Dynamics',
                'screening_question' => 'Tell me, specifically, what you have done to show you are a team player.',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => NULL,
            ),
            5 => 
            array (
                'id' => 6,
                'category' => 'Teamwork / Interpersonal Group Dynamics',
                'screening_question' => 'We all have ways of showing consideration for others. What are some things you\'ve done to show concern or consideration for a co-worker?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => NULL,
            ),
            6 => 
            array (
                'id' => 7,
                'category' => 'Scenarios / Problem Solving',
                'screening_question' => 'You are dispatched to a disturbance in one of the parking lots. A group of males was causing a disturbance earlier in the bar. One of the bouncers was offended by one of the males in the group. The bouncer went home and got his friends to come back with him to confront the group of males and \'get even\'. The bouncers all have weapons with them. What do you do and why?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => NULL,
            ),
            7 => 
            array (
                'id' => 8,
                'category' => 'Scenarios / Problem Solving',
                'screening_question' => 'There are reports of a male in a parking lot partially unclothed, yelling, screaming and waiving chains in each hand. The individual may be experiencing delirium. You are working your shift. What do you do and why?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => NULL,
            ),
            8 => 
            array (
                'id' => 9,
                'category' => 'Scenarios / Problem Solving',
                'screening_question' => 'You notice a group of six males walking together. You notice two of the males yelling at one of the males. Then one of the two males punches the other male in the back of the head causing him to fall forward face first into the sidewalk. The two males jump on top of the other male and continue the assault. You are alone when watching this and on foot. What do you do and why?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => NULL,
            ),
            9 => 
            array (
                'id' => 10,
                'category' => 'Scenarios / Problem Solving',
                'screening_question' => 'You are dispatched for a report of a student refusing to leave a computer lab when instructed to do so by the Lab Tech. The student is distraught and threatening the Lab Tech. Multiple people in the lab have reported the disturbance. The student is accompanied by others. What do you do and why?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => NULL,
            ),
            10 => 
            array (
                'id' => 11,
                'category' => 'Scenarios / Problem Solving',
                'screening_question' => 'A Dispatcher from a taxi company has reported that one of their drivers is being assaulted by four males over a fare payment. The driver later reports that he was robbed by two of the males. The males are still outside and are refusing to listen to commands. What do you do and why?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => NULL,
            ),
            11 => 
            array (
                'id' => 12,
                'category' => 'Scenarios / Problem Solving',
                'screening_question' => 'You get a report of two older males with a young female who appears to be impaired by alcohol. The two males are attempting to get the female into their truck. The two males don\'t appear to be students. What do you do and why?',
                'created_at' => '2018-01-02 18:30:00',
                'updated_at' => '2018-01-02 18:30:00',
                'deleted_at' => NULL,
            ),
        ));
        
        
    }
}