<?php

namespace Modules\Admin\Database\Seeders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class CompetencyMatrixLookupTableSeeder extends Seeder
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
        \DB::table('competency_matrix_lookups')->delete();
        \DB::table('competency_matrix_lookups')->insert([
            0 => [
                'id' => 1,
                'competency_matrix_category_id' => 1,
                'competency' => 'Adaptability',
                'definition' => 'Changes behavioral style or method of approach when necessary to achieve a goal. Adjusts style as appropriate to the needs of the situation. Responds to change with a positive attitude and a willingness to learn new ways to accomplish work activities and objectives.',
                'behavior' => '1) Looks for ways to make changes work rather than only identifying why change will not work.
2) Adapts to change quickly and easily.
3) Makes suggestions for increasing the effectiveness of changes.
4) Shows willingness to learn new methods, procedures, or techniques, resulting from departmental or Commissionaires-wide change.
5) Shifts strategy or approach in response to the demands of a situation.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            1 => [
                'id' => 2,
                'competency_matrix_category_id' => 1,
                'competency' => 'Attention To Detail',
                'definition' => 'Thoroughness in accomplishing a task through concern for all the areas involved, no matter how small. Monitors and checks work or information. Plans and organizes time and resources efficiently.',
                'behavior' => '1) Double-checks the accuracy of information and work product to provide accurate and consistent work.
2) Provides information on a timely basis and in a usable form to others who need to act on it.
3) Carefully monitors the details and quality of own and others\' work.
4) Expresses concern that things be done right, thoroughly, or precisely.
5) Completes all work according to procedures and standards.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            2 => [
                'id' => 3,
                'competency_matrix_category_id' => 1,
                'competency' => 'Collaboration',
                'definition' => 'Develops cooperation and teamwork while participating in a group, working toward solutions which generally benefit all involved parties.',
                'behavior' => '1) Demonstrates respect for the opinions of others.
2) Identifies and pushes for solutions in which all parties can benefit.
3) Helps and supports fellow employees in their work to contribute to overall Commissionaires success.
4) Keeps people informed and up-to-date.
5) Shares information and own expertise with others to enable them to accomplish group goals.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            3 => [
                'id' => 4,
                'competency_matrix_category_id' => 1,
                'competency' => 'Communication - Open',
                'definition' => 'Creates an atmosphere in which timely and high-quality information flows smoothly up and down, inside and outside of Commissionaires. Encourages open expression of ideas and opinions.',
                'behavior' => '1) Asks open-ended questions that encourage others to give their points of view and is approachable at all times.
2) Keeps relevant people accurately informed and up-to-date of both positive and potentially negative information.
3) Appropriately expresses one\'s own opinion.
4) Refrains from immediate judgment and criticism of others\' ideas. Delivers criticism in a way that demonstrates sensitivity to the feelings of others and waits for others to finish their intended message before responding.
5) Encourages response and dissent to ideas and issues.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            4 => [
                'id' => 5,
                'competency_matrix_category_id' => 1,
                'competency' => 'Communication - Oral And Written',
                'definition' => 'Effectively transfers thoughts and expresses ideas orally or verbally in individual or group situations.',
                'behavior' => '1) Presents oneself clearly and articulately when speaking with an individual or before a group assuring that others fully comprehend the intended message.
2) Checks for understanding of the communication by asking open-ended questions that draw out the listener\'s understanding.
3) Effectively uses appropriate literature or visual aids during product/service demonstrations or when giving presentations.
4) Thinks through material for presentations in advance and organizes presentations in a logical flow.
5) Repeats message back to speaker in a way that it is clear that the message is understood.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            5 => [
                'id' => 6,
                'competency_matrix_category_id' => 1,
                'competency' => 'Continuous Learning',
                'definition' => 'Demonstrates eagerness to acquire necessary technical knowledge, skills, and judgment to accomplish a result or to serve a customer\'s needs effectively. Has desire and drive to acquire knowledge and skills necessary to perform job more effectively.',
                'behavior' => '1) Keeps up-to-date on current research and technology in one\'s work focus. Identifies and pursues areas for development. Pursues training that will enhance job performance.
2) Takes responsibility for one\'s own development.
3) Maintains fluency in appropriate work applications, software, or tools.
4) Reviews, selects, and disseminates information regarding key technologies, best practices, and tools to others in the group.
5) Continually looks for ways to expand job capabilities.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            6 => [
                'id' => 7,
                'competency_matrix_category_id' => 1,
                'competency' => 'Crisis Management',
                'definition' => 'Ability to handle crisis situations on projects in a calm and rational way.  Projects leadership and direction on troubled projects and instills a sense of confidence to client sponsors that issues are under control and proactively being managed.',
                'behavior' => '1) Calm demeanor under pressure.
2) Continually commuincates to stakeholders the latest status on crisis situations
3) Is proactive versus reactive
4) Provides leadership and direction, and takes accountability for actions - provides go to green plan
5) Instills confidence with project sponsors',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            7 => [
                'id' => 8,
                'competency_matrix_category_id' => 1,
                'competency' => 'Judgement',
                'definition' => 'Makes decisions authoritatively and wisely, after adequately contemplating various available courses of action.  Consistently strives to use analytics and data to make fact based decisions.',
                'behavior' => '1) Considers alternative available actions, resources, and constraints before selecting a method for accomplishing a task or project.
2) Refrains from "jumping to conclusions" based on no, or minimal, evidence.Takes time to collect facts before decision-making.
3) Considers cost and efficiency when making decisions establishing or changing work procedures.
4) Considers the long-term as well as immediate short-term outcomes and actions.
5) Appropriately balances needs and desires with available resources and constraints.
6) Recognizes when to escalate appropriate or specific situations to the next higher level of expertise.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            8 => [
                'id' => 9,
                'competency_matrix_category_id' => 1,
                'competency' => 'Diversity',
                'definition' => 'Supports and promotes an environment that holds opportunities for all, regardless of race, gender, culture, and age.',
                'behavior' => '1) Enthusiastically works with all employees at all levels, capitalizing on their strengths.
2) Actively seeks opinions and ideas from people of varied background and experiences to improve decisions.
3) Values and incorporates contributions of people from diverse backgrounds.
4) Seeks information from many different sources before deciding on own approach.
5) Demonstrates respect for opinions and ideas of others.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            9 => [
                'id' => 10,
                'competency_matrix_category_id' => 1,
                'competency' => 'Drive For Results',
                'definition' => 'Demonstrates concern for achieving or surpassing results against an internal or external standard of excellence. Shows a passion for improving the delivery of services with a commitment to continuous improvement.',
                'behavior' => '1) Recognizes and capitalizes on opportunities.
2) Sets and maintains high performance standards for self and others that support Commissionaires\'s strategic plan and holds self and other team members accountable for achieving results.
3) Tries new things to reach challenging goals and persists until personal and team goals are achieved and commitments met.
4) Works to meet individual and Commissionaires goals with positive regard, acknowledgment of, and cooperation with the achievement of others\' goals.
5) Motivates others to translate ideas into actions and results.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            10 => [
                'id' => 11,
                'competency_matrix_category_id' => 1,
                'competency' => 'Initiative',
                'definition' => 'Does more than is required or expected in the job; does things that no one has requested that will improve or enhance products and services, avoid problems, or develop entrepreneurial opportunities. Plans ahead for upcoming problems or opportunities and takes appropriate action.',
                'behavior' => '1) Goes beyond expectations in the assignment, task, or job description without being asked.
2) Demonstrates a sincere positive attitude towards getting things done.
3) Digs beneath the obvious to get at the facts, even when not asked to do so.
4) Creates opportunities or minimizes potential problems by anticipating and preparing for these in advance.
5) Seeks out and/or accepts additional responsibilities in the context of the job.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            11 => [
                'id' => 12,
                'competency_matrix_category_id' => 1,
                'competency' => 'Innovation',
                'definition' => 'Applies original thinking in approach to job responsibilities and to improve processes, methods, systems, or services.',
                'behavior' => '1) Keeps up-to-date on current research and technology in the industry.
2) Identifies novel approaches for completing work assignments more effectively or efficiently and works within the "established" system to push for "a better way."
3) Reviews, selects and disseminates information regarding key technologies, best practices, and tools to others in the group.
4) Understands technical aspects of one\'s job and uses appropriate technology for the situation at hand.
5) Tries new approaches when problem solving, seeking ideas, or suggestions from others as appropriate.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            12 => [
                'id' => 13,
                'competency_matrix_category_id' => 1,
                'competency' => 'Negotiation',
                'definition' => 'Explores positions and alternatives to reach outcomes that gain acceptance of all parties.',
                'behavior' => '1) Determines minimal or ideal conditions of the other party during negotiations.
2) Develops a strategy for giving on some points and standing firm on others to achieve desired outcomes.
3) Responds to opposing views in a non-defensive manner.
4) Keeps arguments issue-oriented.
5) Offers compromises and trade-offs to others, as necessary, in exchange for cooperation.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            13 => [
                'id' => 14,
                'competency_matrix_category_id' => 1,
                'competency' => 'Organizational Understanding',
                'definition' => 'Understands agendas and perspectives of others, recognizing and effectively balancing the interests and needs of one\'s own group with those of the broader organization.',
                'behavior' => '1) Knowledgeable about one\'s own department and about the Commissionaires in general.
2) Demonstrates awareness of Commissionaires\'s general goals and makes requests or decisions to support this awareness.
3) Knows how to use the Commissionaires\'s formal and informal systems to get things done.
4) Maintains cross-functional focus and uses the most appropriate channels to communicate within and between departments/divisions.
5) Keeps objectives related to Commissionaires priorities at the top of one\'s own priorities and the priorities of one\'s work department or group.
6) Works to build a sense of common purpose across all work groups, avoiding a "we versus them" attitude.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            14 => [
                'id' => 15,
                'competency_matrix_category_id' => 1,
                'competency' => 'Planning, Organization, and Time Management',
                'definition' => 'Establishes a systematic course of action for self or others to ensure accomplishment of a specific objective. Sets priorities, goals, and timetables to achieve maximum productivity.',
                'behavior' => '1) Develops or uses systems to organize and keep track of information (e.g., "to-do" lists, appointment calendars, follow-up file systems).
2) Sets priorities with an appropriate sense of what is most important and plans with an appropriate and realistic sense of the time demand involved.
3) Keeps track of activities completed and yet to do, to accomplish stated objectives.
4) Keeps clear, detailed records of activities related to accomplishing stated objectives.
5) Knows status of one\'s own work at all times.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            15 => [
                'id' => 16,
                'competency_matrix_category_id' => 1,
                'competency' => 'Problem Solving',
                'definition' => 'Builds a logical approach to address problems or opportunities or manage the situation at hand by drawing on one\'s knowledge and experience base, and calling on other references and resources as necessary.',
                'behavior' => '1) Undertakes a complex task by breaking it down into manageable parts in a systematic, detailed way.
2) Thinks of several possible explanations or alternatives for a situation and anticipates potential obstacles and develops contingency plans to overcome them.
3) Identifies the information needed to solve a problem effectively.
4) Presents problem analysis and recommended solution to others rather than just identifying or describing the problem itself.
5) Acknowledges when one doesn\'t know something and takes steps to find out.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            16 => [
                'id' => 17,
                'competency_matrix_category_id' => 1,
                'competency' => 'Professionalism',
                'definition' => 'Thinks carefully about the likely effects on others of one\'s words, actions, appearance, and mode of behavior. Selects the words or actions most likely to have the desired effect on the individual or group in question.',
                'behavior' => '1) Practices good hygiene and presents an appropriate professional appearance.
2) Understands how one is perceived by others.
3) Takes actions calculated to have a positive effect on others.
4) Works to make a friendly impression on others by using good eye contact and using names whenever possible.
5) Works to develop and maintain positive working relationships with co-workers by being punctual, keeping personal telephone calls to a minimum, and maintaining a pleasant work attitude.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            17 => [
                'id' => 18,
                'competency_matrix_category_id' => 1,
                'competency' => 'Quality',
                'definition' => 'Produces results or provides service that meets or exceeds Commissionaires standards.',
                'behavior' => '1) Shows concern for quality, accuracy, and completeness of work activities.
2) Plans own work activities in advance to insure that all assignments are completed in a timely and quality manner.
3) Uses established systems (i.e. software) to organize and efficiently keep track of information, data, time, and resources.
4) Personally seeks to add value in every work assignment.
5) Notices opportunities to improve quality and takes action to do so.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            18 => [
                'id' => 19,
                'competency_matrix_category_id' => 1,
                'competency' => 'Reliability',
                'definition' => 'Demonstrates a high level of dependability in all aspects of the job.',
                'behavior' => '1) Shows commitment/dedication and accountability in one\'s work, and follows through on all projects, goals, aspects of one\'s work.
2) Completes all assigned tasks on time and with minimal supervision.
3) Arrives at work on time every day.
4) Fulfills all commitments made to peers, co-workers, and supervisor.
5) Works to achieve agreement (by offering alternatives, etc.) on time frames or objectives that can be realistically met.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            19 => [
                'id' => 20,
                'competency_matrix_category_id' => 1,
                'competency' => 'Service',
                'definition' => 'Demonstrates strong commitment to meeting the needs of co-workers, employees and clients striving to ensure their full satisfaction.',
                'behavior' => '1) Asks questions to identify the needs or expectations of others.
2) Considers the impact on the external or internal customer when taking action, or carrying out one\'s own job responsibilities.
3) Looks for creative approaches to providing or improving services that may increase efficiency and decrease cost.
4) Finds opportunities to pass on knowledge and transfer skills to others.
5) Takes personal responsibility for resolving service problems brought to one\'s attention.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            20 => [
                'id' => 21,
                'competency_matrix_category_id' => 1,
                'competency' => 'Technical Expertise',
                'definition' => 'Applies and improves extensive or in-depth specialized knowledge, skills and judgment to accomplish a result or to accomplish one\'s job effectively.',
                'behavior' => '1) Understands technical aspects of one\'s job and continuously builds knowledge, keeping up-to-date on the technical or procedural aspects of the job.
2) Makes oneself available to others to help solve technical or procedural problems or issues.
3) Thinks of ways to apply new developments to improve organizational performance or customer service.
4) Applies technical/procedural knowledge to correctly address a situation in a timely manner.
5) Recognizes trends in theory and practice of one\'s own technical area and effectively prepares for anticipated changes.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            21 => [
                'id' => 22,
                'competency_matrix_category_id' => 2,
                'competency' => 'Change Leadership',
                'definition' => 'Initiates and/or manages the change process and energizes it on an ongoing basis, taking steps to remove barriers or accelerate its pace.',
                'behavior' => '1) Communicates a compelling vision and need for change within one\'s department/group/Commissionaires that generates excitement, enthusiasm, and commitment to the process.
2) Obtains and provides resources to implement change initiatives and works to make others feel ownership of the change.
3) Clearly communicates the direction, required performance, and challenges of change to all involved parties.
4) Identifies and enlists the support of key individuals and groups to move the change forward.
5) Serves as a personal model of the change that one expects of others by demonstrating commitment to innovation and continuous improvement in organizational performance.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            22 => [
                'id' => 23,
                'competency_matrix_category_id' => 2,
                'competency' => 'Coaching',
                'definition' => 'Works to improve and reinforce performance of others. Facilitates their skill development by providing clear, behaviorally specific performance feedback, and making or eliciting specific suggestions for improvement in a manner that builds confidence and maintains self-esteem.',
                'behavior' => '1) Listens actively and effectively.
2) Provides feedback that is clear and direct. Describes the impact of actions and checks for understanding.
3) Establishes an effective, professional, and positive relationship with staff.
4) Establishes trust.
5) Focuses on staff member\'s behaviors.
6) Creates an environment that allows staff to feel motivated to work and interact.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            23 => [
                'id' => 24,
                'competency_matrix_category_id' => 2,
                'competency' => 'Collaborative Leadership',
                'definition' => 'Promotes and generates cooperation among one\'s peers in leadership to achieve a collective outcome; fosters the development of a common vision and fully participates in creating a unified leadership team that get results.',
                'behavior' => '1) Takes into account the Commissionaires as a whole when making decisions. Separates one\'s own interests from Commissionaires interests to make the best possible judgments for the Commissionaires.
2) Identifies and pushes for solutions in which all parts of the Commissionaires can benefit.
3) Builds consensus among one\'s peers in leadership.
4) Communicates key Commissionaires priorities and how one\'s division or department contributes to achieving those priorities.
5) Shares goals with peers in the Commissionaires to increase alignment, cooperation, and opportunities to collaborate.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            24 => [
                'id' => 25,
                'competency_matrix_category_id' => 2,
                'competency' => 'Conflict Management',
                'definition' => 'Brings substantial conflicts and disagreements into the open and attempts to manage them collaboratively, building consensus, keeping the best interests of the organization in mind, not only one\'s own interest.',
                'behavior' => '1) Recognizes conflict and identifies ways to help involved parties work through conflict.
2) Identifies areas of agreement when working with conflicting individuals or groups.
3) Maintains awareness of broad, longer-term objectives and works to ensure that all parties share this awareness while seeking solutions.
4) States own point-of-view without criticizing the other person\'s.
5) Responds to opposing views in a non-defensive manner.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            25 => [
                'id' => 26,
                'competency_matrix_category_id' => 2,
                'competency' => 'Influence',
                'definition' => 'Asserts own ideas and persuades others, gaining support and commitment from others; mobilizes people to take action, using creative approaches to motivate others to meet Commissionaires goals.',
                'behavior' => '1) Able to make a good/persuasive argument to persuade/influence audience.
2) Develops and uses subtle strategies to influence others.
3) Works to make others feel ownership in one\'s own solutions.
4) Identifies key decision-makers on issues of concern.
5) Develops and effectively uses networks, inside and outside the Commissionaires.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
            26 => [
                'id' => 27,
                'competency_matrix_category_id' => 2,
                'competency' => 'Team Leadership',
                'definition' => 'Willingly cooperates and works collaboratively toward solutions that generally benefit all involved parties; works cooperatively with others to accomplish company objectives.',
                'behavior' => '1) Participates willingly in activities as a good role player that works well with others.
2) Puts goals of the group ahead of one\'s own agenda, and supports and acts in accordance with final group decisions even when such decisions may not entirely reflect one\'s own position.
3) Solicits the input of others who are affected by plans or actions and gives credit and recognition to others who have contributed.
4) Works to build consensus within the group/department/Commissionaires.
5) Demonstrates concern for treating people fairly and equitably.',
                'created_at' => date('Y-m-d'),
                'updated_at' => date('Y-m-d'),
            ],
        ]);

    }
}
